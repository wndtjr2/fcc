<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 2016. 1. 21.
 * Time: 오전 10:00
 */

namespace App\Controller;


use App\Service\AddressService;
use App\Service\CartService;
use App\Service\OrderService;
use App\Service\ProductService;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use stdClass;
use Symfony\Component\Config\Definition\Exception\Exception;


class OrdersController extends AppController
{

    /**
     * @var \App\Model\Table\ChPaymentTable
     */
    private $ChPayment;

    /**
     * @var \App\Model\Table\ChProductTable
     */
    private $ChProduct;

    /**
     * @var \App\Service\ProductService
     */
    private $ProductService;

    /**
     * @var \App\Service\OrderService
     */
    private $OrderService;

    /**
     * @var \App\Model\Table\ChOrderTable
     */
    private $ChOrder;

    /**
     * @var \App\Model\Table\ChPurchaseTable
     */
    private $ChPurchase;

    /**
     * @var \App\Model\Table\ChCartTable
     */
    private $ChCart;

    /**
     * @var \App\Service\AddressService
     */
    private $AddressService;

    /**
     * @var \App\Model\Table\ChProductOptionTable
     */
    private $ChProductOption;

    private $ErrorMsg = [
        1 => "결제중 오류가 발생하였습니다.",
        2 => "결제가능 시간이 초과하였습니다. 로그인 후 다시 시도해 주시기 바랍니다.",
        3 => "매진 상품이 포함되어 있습니다.<br/>상품 정보 확인 후 다시 주문해 주시기 바랍니다.",
    ];

    public function initialize(){
        parent::initialize();

        $this->ChPayment = TableRegistry::get('ChPayment');
        $this->ProductService = ProductService::Instance();
        $this->OrderService = OrderService::Instance();
        $this->ChOrder = TableRegistry::get('ChOrder');
        $this->ChPurchase = TableRegistry::get('ChPurchase');
        $this->ChCart = TableRegistry::get('ChCart');
        $this->AddressService = AddressService::Instance();
        $this->ChProduct = TableRegistry::get('ChProduct');
        $this->ChProductOption = TableRegistry::get('ChProductOption');

        $this->Auth->allow(['status', 'cancelOrder', 'sendCommonEmail', 'returnOrderError']);
    }

    public function isAuthorized(){
        return true;
    }

    public function status(){
        $this->autoRender = false;
        $data = $this->request->data();

        //check data and get order object
        $order = $this->OrderService->checkMerchatIdAndOrderCodeFromInicis($data['mid'], $data['ref']);

        //check data and get purchase object
        $purchases = $this->OrderService->checkTotalAmountAndPaymentTable($data['ref'], $data['amt']);

        $payment = $this->OrderService->setPayment($data['ref'], $data['transid'], $data['txntype'], $data['paymethod'], $data['rescode'], $data['amt']);

        //트랜잭션
        $connection = ConnectionManager::get('default');
        $connection->begin();

        try{

            //Payment테이블에 데이터 넣기
            $this->OrderService->createPayment($payment);

            if($data['rescode'] == '0000'){

                //update transaction_id in Order table
                $this->OrderService->updateOrder($order, $payment->transaction_id, 'purchased');

                //get cart list
                $productCarts = $this->ChCart->findByUsersId($order->users_id);

                //update status in Purchase table
                $purchaseStatus = 'purchased';
                $productCartArray = $this->OrderService->createProductCartArrayAndUpdatePurchase($purchases, $productCarts, $purchaseStatus);

                //카트 삭제
                if(isset($productCartArray) and !is_null($productCartArray)){
                    $cartReturn = CartService::Instance()->removeCartOfProductionCode($productCartArray, $order->users_id);
                    if ($cartReturn['result'] != true or $cartReturn['msg'] == 'lack') {
                        throw new BadRequestException('Cart Deletion has failed. : ' . $cartReturn['msg'], 'error');
                    }
                }
            }else{
                //recover stock
                foreach($purchases as $purchase){
                    $this->OrderService->recoverStock($purchase->product_option_code, $purchase->quantity);
                }
            }

            $connection->commit();

        }catch(Exception $e){
            $connection->rollback();
            throw new InternalErrorException($e->getMessage());
        }

    }

    public function returnOrderInfo($orderCode){

        //check authentication
        $order = $this->OrderService->getOrderWithOrderCode($orderCode)->first();

        //주문정보가 없을시
        if(is_null($order)){
            return $this->redirect('/');
        }

        //권한체크
        if($order->users_id != $this->Auth->user('id')){
            return $this->redirect('/');
        }

        $subtotal = 0;
        $shipping = 0;
        foreach($order->ch_purchase as $purchase){
            //get price
            $amount = $purchase->unit_price * $purchase->quantity;
            $subtotal += $amount;
            $ship = $purchase->shipping_price;
            $shipping += $ship;

            //check is shipped
            $productCodeArray[] = $purchase->product_code;
        }

        //check if one of the ordered product is product not service
        $isShip = $this->OrderService->checkToShipByProductCode($productCodeArray);

//        $total = $subtotal + $shipping;
        $payment = $this->ChPayment->find()->where(['order_code' => $orderCode])->first();
        if(is_null($payment)){
            //payment table에 데이터 해당 컬럼 없음
            $msg = 'No Payment infomation according to Order Code : '.$orderCode;
            Debugger::log($msg, 'error');
            $error = 'Internal server error. Please contact with us.';
            $this->set(compact('error'));
        }else{

            $total = $payment->total;

            $date = date_format($order->created, 'n j Y');

            $this->set(compact('order', 'subtotal', 'shipping', 'total', 'isShip', 'date'));
        }

    }

    public function returnOrderError(){

        if($this->request->is("POST")){
            $data = $this->request->data();
            $error = 'Internal server error. Please contact with us.';
            if(!isset($data['msg'])){
                $this->set('error', $error);
            }
            if(!empty($data['order_code'])){
                $order = $this->OrderService->getOrderByOrderCodeForError($data['order_code']);
                $this->set('order', $order);
            }
            $this->set('msg', __($data['msg']));
        }else{
            return $this->redirect("/");
        }
    }

    public function returnOrder(){}

    public function cancelOrder(){
        $this->autoRender = false;
        $status = 'error';
        if($this->request->is('POST')){
            $data = $this->request->data();
            $orderCode = $data['order_code'];
            $usersId = $data['users_id'];
            $order = $this->ChOrder->findByOrderCode($orderCode)->first();
            if($order->users_id != $usersId){
                throw new BadRequestException('The request is prohibited.');
            }
            $connection = ConnectionManager::get('default');
            $connection->begin();
            try{
                $this->OrderService->updateCancelOrder($order);
                $status = 'success';
                $connection->commit();
            }catch (Exception $e){
                $connection->rollback();
                throw new InternalErrorException($e->getMessage());
            }
        }
        echo $status;
    }

    public function addAddressBeforeCheckOut(){
        $this->autoRender = false;

        $userId = $this->Auth->user('id');
        $data = $this->request->data();

        $rtn = $this->AddressService->saveAddress($data,$userId);

        echo json_encode(array('result'=>$rtn));
    }
    public function isTrueToShip(){
        $this->autoRender = false;

        $data = $this->request->data();
        $productOptionCodesArray = $data['products'];
        $addrId = $data['addrId'];

        $isShip = $this->OrderService->checkToShipByProductOpionCode($productOptionCodesArray);
        $products = $this->ChProductOption->find()->contain(['ChProduct'])->where(['product_option_code in' => $productOptionCodesArray]);

        foreach($products as $product){
            $productCodeArray[] = $product->product_code;
        }

        $shippingFee = 0;
        if($isShip){
            $countryCode = $this->OrderService->getCountryCodeByMcUserAddrInfoId($addrId);
            $shippingFee = $this->OrderService->getProductShippingChargeWithProductCodeAndCountryCode($productCodeArray, $countryCode);
        }
        echo $shippingFee;

    }

    public function sendCancelFailEmail(){
        $this->autoRender = false;
        if($this->request->is("POST")){
            $data = $this->request->data();
            if(!isset($data['msg'])){
                $rtn = ['result' => false];
                echo json_encode($rtn);
            }
            if(!isset($data['order_code'])){
                $rtn = ['result' => false];
                echo json_encode($rtn);
            }
            $this->OrderService->sendCancelFailEmail($data['msg'], $data['order_code']);
        }
    }
    public function sendCommonEmail(){

        $this->autoRender = false;
        if($this->request->is("POST")){

            $data = $this->request->data();
            if(!isset($data['subject'])){
                $rtn = ['result' => false];
                echo json_encode($rtn);
                exit;
            }
            if(!isset($data['entry'])){
                $rtn = ['result' => false];
                echo json_encode($rtn);
                exit;
            }
            if(!isset($data['message'])){
                $rtn = ['result' => false];
                echo json_encode($rtn);
                exit;
            }
            if(isset($data['to'])){
                $to = $data['to'];
            }else{
                $to = null;
            }
            if(isset($data['from'])){
                $from = $data['from'];
            }else{
                $from = null;
            }
            $rtn = $this->OrderService->sendCommonEmail($data['subject'], $data['entry'], $data['message'], $to, $from);
            echo json_encode($rtn);
            exit;
        }
    }

}