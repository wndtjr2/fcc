<?php
/**
 * Created by PhpStorm.
 * User: brickandmon
 * Date: 2016. 1. 15.
 * Time: 오후 5:06
 */

namespace App\Controller;

use App\Service\AddressService;
use App\Service\EncryptService;
use App\Service\OrderService;
use App\Service\ProductService;
use App\Service\SmsService;
use App\Service\UserService;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;
use stdClass;

class PurchasesController extends AppController
{
    /**
     * @var \App\Model\Table\ChProductTable
     */
    private $ChProduct;

    /**
     * @var \App\Service\OrderService
     */
    private $OrderService;

    /**
     * @var \App\Service\ProductService
     */
    private $ProductService;

    /**
     * @var \App\Service\AddressService
     */
    private $AddressService;

    private $ChCart;


    /**
     * @var \App\Service\UserService
     */
    private $UserService;

    public function initialize(){
        parent::initialize();
        $this->loadComponent('RequestHandler');

        $this->ChProduct = TableRegistry::get('ChProduct');
        $this->OrderService = OrderService::Instance();
        $this->ProductService = ProductService::Instance();
        $this->AddressService = AddressService::Instance();
        $this->ChCart = TableRegistry::get("ChCart");
        $this->UserService = UserService::Instance();

        $this->Auth->allow(['sendPurchaseInfo', 'saveSmsForPurchase', 'sendUrgentStockWarning']);

        //login redirect
        $parsedUrl = parse_url($this->referer());
        $explodedUrl = explode('/',$parsedUrl['path']);
        if(!$this->Auth->user() and $this->request->params['action'] == 'orderInfo'){
            if($explodedUrl[1] == 'fccTv' and $explodedUrl[2] == 'detail'){
                $this->redirect([
                    'controller' => 'Auth',
                    'action' => 'login',
                    '?' => [
                        'redirect' => $this->referer()
                    ]
                ]);
            }
        }
    }

    /**
     * @return bool
     */
    public function isAuthorized(){
        return true;
    }

    /**
     * show order data
     * @return \Cake\Network\Response|void
     */
    public function orderInfo(){

        $userId = $this->Auth->user('id');
        //from product detail
        if($this->request->is('POST')){
            $requestData = $this->request->data();
        }
        //from cart
        else{
            $data=[];
            $carts = $this->ChCart->find()->select(['product_option_code','quantity'])->where(['users_id'=>$userId])->toArray();
            if(is_null($carts) || empty($carts)){
                return $this->redirect('/');
            }
            foreach($carts as $row){
                $data[] = [
                    'product_option_code' => $row->product_option_code,
                    'quantity' => $row->quantity,
                ];
            }
            //print_r($data);
            $requestData=[
                'data' => $data
            ];
        }

        //handling given data exception
        if(!isset($requestData['data'][0]['product_option_code'])
            && !isset($requestData['data'][0]['quantity'])
            && !array_key_exists("product_option_code", $requestData['data'][0])
            && !array_key_exists("quantity", $requestData['data'][0])) {

            $message = 'No Purchase Infomation Given. Please try again.';
            $this->set('alert', $message);
            Debugger::log($requestData);
        }else{
            $data = $requestData['data'];

            $productOptionArray = array();
            $quantityArray = array();
            for ($i = 0; $i < sizeof($data); $i++) {
                $productOptionArray[] = $data[$i]['product_option_code'];
                $quantityArray[$data[$i]['product_option_code']] = $data[$i]['quantity'];
            }
            if (sizeof($productOptionArray) > 0) {
                $products = $this->OrderService->getProductWithProductOption($productOptionArray);
            }

            //check product status
            $rtn = $this->OrderService->checkProductStatus($products, $quantityArray);
            if($rtn === false){
                //첫번째 결제시도
                $orderCode = $this->OrderService->createOrder($products, $quantityArray, $userId);

                $this->request->session()->write('token', EncryptService::Instance()->encrypt($orderCode));

                $this->set('orderCode', $orderCode);
            }else{
                $this->set('productStatus', $rtn);
            }

            //get country code from user address
            $address = $this->OrderService->getShipping($this->Auth->user('id'))->toArray();

            for($i = 0;$i < sizeof($data); $i++){
                $productOptionArray[] = $data[$i]['product_option_code'];
            }

            $products = $this->OrderService->getProductWithProductOption($productOptionArray);

            //product_code 어레이에 담기
            $productCodeArray = array();

            foreach($products as $prd){
                $productCodeArray[] = $prd->product_code;
            }
            //배송지가 등록되어있으면
            $shippingTotal = 0;
            if ($address) {
                $shippingPrice = $this->OrderService->getProductShippingChargeArrayWithProductCode($productCodeArray);
                foreach ($products as $prd) {
                    if(strtolower($prd->ch_product->type) == 'product') {
                        if (isset($shippingPrice[$prd->product_code][$address[0]->country_code])) {
                            $shippingTotal += $shippingPrice[$prd->product_code][$address[0]->country_code];
                        } else {
                            $shippingTotal += 0;
                        }
                    }else{
                        $shippingTotal += 0;
                    }
                }
            } else {
                //배송지 등록되어있지 않으면
                $addressService = AddressService::Instance();

                $countryList = $addressService->selectCountryCode();
                $countryArr = array();
                foreach ($countryList as $country) {
                    $countryArr[$country->country_code] = $country->country_name;
                }
                $this->set('countryList', $countryArr);
            }

            //calculation
            $grandTotal = 0;
            $productTotalPrice =0;

            $orderQuantity = array();
            foreach($products as $key => $prdOption){
                $eachPrice = $prdOption->price;
                foreach($data as $optionProduct){
                    if($optionProduct['product_option_code']==$prdOption->product_option_code){
                        $productTotalPrice += $eachPrice * $optionProduct['quantity'];
                        $orderQuantity[$key] = $optionProduct['quantity'];
                    }
                }
            }
            $grandTotal += $productTotalPrice;

            $this->set("userInfo",$this->Auth->user());
            $this->set(compact('products', 'address','orderQuantity'));
            $this->set(compact('grandTotal', 'shippingTotal', 'data'));
            $this->set('payment_test', Configure::read('PAYMENT_TEST'));
        }
    }

    /**
     * Save data before eximbay payment success
     * @return \Cake\Network\Response|void
     */
    public function orderBefore(){

        $this->autoRender = false;
        if($this->request->is('POST')) {
            $countryCode = 'KR';
            $buyer = $this->Auth->user();
            $userId = $buyer['id'];
            $data = $this->request->data();
            //Debugger::log($data);
            $datas = $data['data'];
            $shippingId = $data['shipping_id'];

            //exception handling with given data
            if(is_null($datas[0]['product_option_code']) or empty($datas[0]['product_option_code'])){
                $msg = 'No Product Option Code is given.';
                Debugger::log($msg, 'error');
                throw new BadRequestException($msg);
                exit;
            }
            if(is_null($datas[0]['quantity']) or empty($datas[0]['quantity'])){
                $msg = 'No Product quantity is given.';
                Debugger::log($msg, 'error');
                throw new BadRequestException($msg);
                exit;
            }
            if(is_null($data['orderCode']) or empty($data['orderCode'])){
                $msg = 'No Order Code is given.';
                Debugger::log($msg, 'error');
                throw new BadRequestException($msg);
                exit;
            }
            if(!isset($_SESSION['token'])){
                $msg = "결제 가능 시간이 초과 하였습니다.";
                Debugger::log($msg);
                throw new BadRequestException($msg);
                exit;
            }
            if(isset($data['P_OID']) && isset($data['P_NOTI'])){

                if($data['P_OID'] != $data['P_NOTI']){
                    $msg = "Unauthorized Action was detected.";
                    Debugger::log($msg . ', users_id : ' . $userId . ',' . ' oid and noti do not match.', 'error');
                    throw new BadRequestException($msg);
                    exit;
                }
                $decrypted = EncryptService::Instance()->decrypt(trim($_SESSION['token']));
                if($decrypted != $data['P_OID'] || $data['orderCode'] != $data['P_OID']){
                    $msg = "Unauthorized Action was detected.";
                    Debugger::log($msg . ', users_id : ' . $userId . ',' . ' session\'s token and oid do not match.', 'error');
                    throw new BadRequestException($msg);
                    exit;
                }
            }elseif(isset($data['oid'])){
                $decrypted = EncryptService::Instance()->decrypt(trim($_SESSION['token']));
                if($decrypted != $data['oid'] || $data['orderCode'] != $data['oid']){
                    $msg = "Unauthorized Action was detected.";
                    Debugger::log($msg . ', users_id : ' . $userId . ',' . ' session\'s token and oid do not match.', 'error');
                    throw new BadRequestException($msg);
                    exit;
                }
            }else{
                $msg = "No Oid was given.";
                Debugger::log('users_id : ' . $userId . ',' . $msg, 'error');
                throw new BadRequestException($msg);
                exit;
            }
            $orderCode = $data['orderCode'];

            $productOptionArray = array();
            $quantityArray = array();
            for ($i = 0; $i < sizeof($datas); $i++) {
                $productOptionArray[] = $datas[$i]['product_option_code'];
                $quantityArray[$datas[$i]['product_option_code']] = $datas[$i]['quantity'];
            }
            if (sizeof($productOptionArray) > 0) {
                $products = $this->OrderService->getProductWithProductOption($productOptionArray);
            }

            //상품 상태 체크
            $rtn = $this->OrderService->checkProductStatus($products, $quantityArray);
            if($rtn === false){
                $this->OrderService->createShipping($shippingId, $orderCode, $countryCode);
                $rtn = $orderCode;
            }
            echo json_encode($rtn);
        }
    }

    public function checkOutShipping(){

        if($this->request->is('POST')){
            $data = $this->request->data();
            $productCode = $data['product_code'];
            $countryCode = $data['country_code'];

            //exception handling with given data
            if(is_null($productCode) or empty($productCode)){
                return $this->redirect(['controller' => 'Products', 'action' => 'index']);
                throw new BadRequestException('No Product Code is given.');
            }
            if(is_null($countryCode) or empty($countryCode)){
                return $this->redirect(['controller' => 'Products', 'action' => 'index']);
                throw new BadRequestException('No Country Code is given.');
            }

            $shippingFee = $this->OrderService->getProductShippingChargeWithProductCodeAndCountryCode($productCode, $countryCode);
            echo $shippingFee;
        }

    }

    public function sendPurchaseInfo(){
        $this->autoRender = false;

        if($this->request->is('POST')){
            $data = $this->request->data();
            if(!isset($data['order_code'])){
                throw new BadRequestException('No order_code was given.');
            }

            if(!isset($data['users_id'])){
                throw new BadRequestException('No user id was given.');
            }

            $orderCode = $data['order_code'];
            //check authentication
            $order = $this->OrderService->getOrderWithOrderCode($orderCode)->first();

            //권한 체크
            if($data['order_code'] != $order->order_code){
                throw new BadRequestException('You are not authorized to access.');
            }


            $userAccount = $this->UserService->getUsersAndUserAccountsByUsersId($order->users_id);
            
            $email = $userAccount->user_account->emailDecrypt;
            $buyerName = $userAccount->last_name. " " . $userAccount->first_name;


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

            $chPayment = TableRegistry::get('ChPayment');
            $payment = $chPayment->find()->where(['order_code' => $orderCode])->first();
            $total = $payment->total;


            $rtn = $this->OrderService->sendPurchaseInfo($email, $order, $subtotal, $shipping, $total, $isShip, $payment, $buyerName);

            echo json_encode($rtn);
        }
    }

    public function saveSmsForPurchase(){
        $this->autoRender = false;

        if($this->request->is('POST')){
            $data = $this->request->data();

            if(!isset($data['order_code'])){
                throw new BadRequestException('No order_code was given.');
            }
            if(!isset($data['users_id'])){
                throw new BadRequestException('No users_id was given.');
            }

            $orderCode = $data['order_code'];
            $usersId = $data['users_id'];

            $order = $this->OrderService->getOrderWithOrderCodeForSimple($orderCode)->first();
            if($order->users_id != $usersId){
                throw new BadRequestException("You are not authorized to access this action.");
            }

            $type = 'buy';
            $shopName = $order->ch_purchase[0]->ch_product->designer_name;
            $buyer = $this->UserService->getUserByUsersId($usersId);
            $userName = $buyer->last_name.$buyer->first_name;
            $recvPhoneNumber = $buyer->phoneDecrypt;

            $quantity = count($order->ch_purchase);
            $stringQuantity = $quantity > 1?" 외 ".($quantity - 1)."개":"";
            $productName = $order->ch_purchase[0]->ch_product->name.$stringQuantity;

            $data = [
                '{shopName}' => $shopName,
                '{userName}' => $userName,
                '{orderCode}' => $orderCode,
                '{productName}' => $productName
            ];

            $rtn = SmsService::Instance()->insertSmsMessage($type,$recvPhoneNumber,$data);
            echo $rtn;
        }
    }
    public function childwin(){}


    public function sendUrgentStockWarning(){
        $this->autoRender = false;
        if($this->request->is('POST')){
            $data = $this->request->data();

            //data given exception handling
            if(!isset($data['product_code'])){
                throw new BadRequestException("No Product Code is given to send urgent stock warning email.");
            }
            if(!isset($data['product_name'])){
                throw new BadRequestException("No Product name is given to send urgent stock warning email.");
            }
            if(!isset($data['product_option_name'])){
                throw new BadRequestException("No Product option name is given to send urgent stock warning email.");
            }
            $this->OrderService->sendUrgentStockWarning($data['product_code'], $data['product_name'], $data['product_option_name']);
        }
    }













}