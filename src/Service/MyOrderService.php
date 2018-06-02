<?php
namespace App\Service;

error_reporting(E_STRICT);
/**
 * 배송지 조회 서비스 인터페이스
 * User: Makun
 * Date: 16. 2. 1.
 * Time: 오후 1:54
 */

use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Network\Email\Email;
use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Controller\Component;


/**
 * Interface AddressInterface
 * 배송지 조회 인터 페이스
 * @package App\Service
 */
interface MyOrderInterface
{
    /**
     * 내 주문정보 가져오기
     * @param $userId 사용자 아이디
     * @return array
     */
    public function getMyOrders($userId);

    /**
     * 내 주문 상세
     * @param $orderCode  주문번호
     * @param $userInfo 사용자 정보
     * @return array
     */
    public function orderDetail($orderCode,$userInfo);

    /**
     * 주문 취소 요청
     * @param $orderCode 주문번호
     * @param $userEmail 사용자 이메일
     * @param $userId 사용자 아이디
     * @return array
     */
    public function orderCancelTotal($orderCode,$userEmail,$userId);

    /**
     * 주문 환불 요청
     * @param $data 환불 요청 데이터
     * @param $userId 사용자 아이디
     * @param $userEmail 사용자 이메일
     * @return array
     */
    public function refundReason($data,$userId,$userEmail);

    /**
     * 사용자 클레임 정보 질문 사항
     * @param null $code 코드 정보
     * @return array
     */
    public function getClaimOpenType($code = null);
}

/**
 * 사용자 조회 서비스
 * Class UserService
 * @package App\Service
 */
class MyOrderService implements MyOrderInterface {

    private $ChOrder;

    private $ChPurchase;

    private $ChPayment;

    private $ChOrderClaim;

    private $ChRefund;

    private $ChCode;

    private $productService;

    private $orderService;

    private $ChProductOption;

    private $ChImageFile;

    private function __construct() {
        $this->ChOrder = TableRegistry::get("ChOrder");
        $this->ChPurchase = TableRegistry::get("ChPurchase");
        $this->ChPayment = TableRegistry::get("ChPayment");
        $this->ChOrderClaim = TableRegistry::get("ChOrderClaim");
        $this->ChRefund = TableRegistry::get("ChRefund");
        $this->ChCode = TableRegistry::get("ChCode");
        $this->ChProductOption = TableRegistry::get("ChProductOption");
        $this->ChImageFile = TableRegistry::get("ChImageFile");
        $this->productService = ProductService::Instance();
        $this->orderService = OrderService::Instance();
    }

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new MyOrderService();
        }
        return $inst;
    }

    /** 내 주문 이력  */
    public function getMyOrders($userId){
        $contain = array(
            'ChPurchase' => array(
                'ChProduct' => array(
                    'ChImage' => array(
                        'ChImageFile'
                    )
                ),
                'ChProductOption'
            ),
        );

        $orderStatusArr = ['purchased','shipped','delivered','cancel','cancelled','completed','refunded'];

        $myorderList = $this->ChOrder->find()
            ->contain($contain)
            ->where(["ChOrder.creator"=>$userId,'ChOrder.status in'=>$orderStatusArr])
            ->order(['ChOrder.modified' => 'DESC']);

        $priority = array(
            'completed' => 1,
            'delivered' => 2,
            'shipped' =>3,
            'cancelled' =>4,
            'cancel' =>5,
            'refunded' => 6,
            'purchased'=>7,
            'ordered' =>8,
        );


        $orderListByDay = array();
        foreach($myorderList as $orderObj){

            $items = array();
            $totalAmount = 0;
            $order_date = "";

            $i=0;
            $orderStatus = "";
            foreach($orderObj->ch_purchase as $purchase){
                $totalAmount += $purchase->amount;
                $totalAmount += $purchase->shipping_price;
                $status = $purchase->status;
                $order_date = date_format($orderObj->modified,"n j Y");

                if($i==0){
                    $orderStatus = $status;
                }

                if($priority[$status] < $priority[$orderStatus]){
                    $orderStatus = $status;
                }

                $productInfo = array(
                    'sellerName' => $purchase->ch_product->designer_name,
                    'productName' => $purchase->ch_product->name,
                    'option' => explode(";",$purchase->ch_product_option->name),
                    'quantity' => $purchase->quantity,
                    'mainImageUrl' => $purchase->ch_product->ch_image->ch_image_file[0]->surl,
                );

                $items[] = $productInfo;
                $i++;
            }

            $order = array(
                'orderCode' => $orderObj->order_code,
                'status' => $orderStatus,
                'orderDate' =>$order_date,
                'totalAmount' => $totalAmount,
                'items' => $items
            );

            $orderDay = date_format($orderObj->created,"n j Y");
//            $orderDay = strtotime($orderDay);
            if (isset($orderListByDay) && isset($orderListByDay[$orderDay])) {
                $dayList = $orderListByDay[$orderDay];
                array_push($dayList, $order);
                $orderListByDay[$orderDay] = $dayList;
            } else {
                $orderListByDay[$orderDay] = array($order);
            }
        }
//        krsort($orderListByDay);

        return $orderListByDay;
    }

    /** 주문 상세 */
    public function orderDetail($orderCode,$userInfo){
        $userId = $userInfo['id'];
        $contain = array(
            'ChPurchase' => array(
                'ChProduct' => array(
                    'ChImage' => array(
                        'ChImageFile'
                    )
                ),
                'ChProductOption',
                'ChShipping',
            ),
            'ChPayment'
        );

        $orderStatusArr = ['purchased','shipped','delivered','cancel','cancelled','completed','refunded'];

        $myorderList = $this->ChOrder->find()->contain($contain)->where(["ChOrder.order_code"=>$orderCode,"ChOrder.creator"=>$userId,'ChOrder.status in'=>$orderStatusArr])->first();

        if($myorderList==null){
            return false;
        }

        $subTotal = 0;
        $shippingPrice = 0;
        $items = array();



        $trackingUrl = array();
        $orderStatus = "";

        $transctionId = $myorderList->ch_payment->transaction_id;
        $purchaseCodeArr = array();

        $i = 0;
        $priority = array(
            'completed' => 1,
            'delivered' => 2,
            'shipped' =>3,
            'cancelled' =>4,
            'cancel' =>5,
            'refunded' => 6,
            'purchased'=>7,
            'ordered' =>8,
        );

        foreach($myorderList->ch_purchase as $purchase) {
            $subTotal += $purchase->amount;
            $shippingPrice += $purchase->shipping_price;
            $nowStatus = $purchase->status;
            $purchaseCodeArr[] = $purchase->purchase_code;

            if($i==0){
                $orderStatus = $nowStatus;
            }

            if($priority[$nowStatus] < $priority[$orderStatus]){
                $orderStatus = $nowStatus;
            }

            $productInfo = array(
                'purchaseCode' => $purchase->purchase_code,
                'amount' => $purchase->amount,
                'sellerName' => $purchase->ch_product->desinger_name,
                'productName' => $purchase->ch_product->name,
                'option' => explode(";",$purchase->ch_product_option->name),
                'quantity' => $purchase->quantity,
                'mainImageUrl' => $purchase->ch_product->ch_image->ch_image_file[0]->url,
                'trackingNum1' => (isset($purchase->ch_shipping->tracking_number1))?$purchase->ch_shipping->tracking_number1:"",
                'status' => $purchase->status,
            );
            $items[] = $productInfo;
            if($purchase->status != "purchased" && isset($purchase->ch_shipping->tracking_number1)) {
                $trackingUrl[$purchase->ch_shipping->tracking_number1] = (isset($purchase->ch_shipping->tracking_url)) ? $purchase->ch_shipping->tracking_url : "";
            }

            $i++;
        }

        $isRefund = $this->isRefund($purchaseCodeArr,$transctionId,$userId);

        $isRefundRequest = $this->isClaim($purchaseCodeArr);


        $claimItems = array();
        foreach($isRefundRequest as $claim){
            $claimItems[] = $claim->purchase_code;
        }

        $refundItems = array();

        foreach($isRefund as $refund){
            if(isset($refundItems[$refund->purchase_code])){
                $refundItems[$refund->purchase_code] += $refund->amount;
            }else {
                $refundItems[$refund->purchase_code] = $refund->amount;
            }
        }

        $delivName = $myorderList->ch_purchase[0]->ch_shipping->deliv_last_name." ".$myorderList->ch_purchase[0]->ch_shipping->deliv_first_name;

        $orderDetail = array(
            'orderDate' => date_format($myorderList->modified,"n j Y"),
            'orderCode' => $myorderList->order_code,
            'transId' => $myorderList->transaction_id,
            'status' => $orderStatus,
            'shippingAddr' => array(
                'dilivName' => $delivName,
                'zipcode' => $myorderList->ch_purchase[0]->ch_shipping->zipcode,
                'address' => $myorderList->ch_purchase[0]->ch_shipping->address,
                'address2' => $myorderList->ch_purchase[0]->ch_shipping->address2,
                'state' => $myorderList->ch_purchase[0]->ch_shipping->state,
                'phone' => $myorderList->ch_purchase[0]->ch_shipping->phone_decrypt,
            ),
            'subTotal' =>$subTotal,
            'shippingPrice' =>$shippingPrice,
            'tax' =>$myorderList->ch_payment->tax,
            'totalAmount' => $myorderList->ch_payment->total,
            'trackingUrl' => $trackingUrl,
            'items' => $items,
            "refundItems" =>$refundItems,
            "claimItems" =>$claimItems,
        );

        return $orderDetail;
    }

    /** 환불 요청 확인 */
    private function isClaim($purchaseCode){
        return $this->ChOrderClaim->find()->where(['purchase_code in'=>$purchaseCode]);
    }

    /** 환불 여부 확인 */
    private function isRefund($purchaseCode,$transactionId,$userId){
        return $this->ChRefund->find()->where([
            'purchase_code in'=>$purchaseCode,
//            'transaction_id'=> $transactionId,
//            'creator' => $userId
        ]);
    }

    /** 오더 테이블 단일 조회 */
    private  function orderSimple($orderCode){
        return $this->ChOrder->find()->where(["order_code"=>$orderCode])->first();
    }

    /** 주문 취소 등록  */
    public function orderCancelTotal($orderCode,$userEmail,$userId){

        $order = $this->orderSimple($orderCode);

        if($order->creator!=$userId){
            return array(
                'result' => false,
                'msg' => 'is not yours'
            );
        }

        $this->orderService->UpdateCancelOrder($order);

        $data['order_code'] = $orderCode;
        $data['email'] = $userEmail;
        $data['subject'] = "주문 취소 신청 안내";
        $data['template'] = "cancelOrder";

        $this->sendConfirmEmail($data);
        return array(
            'result' => true,
            'msg' => 'success'
        );
    }

    /** 확인 이메일 전송 */
    private function sendConfirmEmail($data){
        $data['domain'] = Router::url('/', true);

        $encriptService = EncryptService::Instance();
        $data['email'] = $encriptService->decrypt(trim($data['email']));

        $email = new Email();
        try{
            $email->transport('brick')
                ->to($data['email'])
                ->from(FROMFCCTVMAIL)
                ->emailFormat('html')
                ->subject($data['subject'])
                ->viewVars(array(
                    'data' => $data
                ))
                ->template($data['template']);
            if(!$email->send()){
                Debugger::log($email);
                throw new InternalErrorException();
            }
        }catch (Exception $e){
            Debugger::log($e->getMessage());
            return 'fail';
        }
        return 'success';
    }

    /** 주문 DB Status 변경 처리
     * 2016.03.11 사용 중지
     */
    private  function orderCancel($orderCode,$status){
        $orderEntity = $this->orderSimple($orderCode);
        $orderNewEntity = $this->ChOrder->patchEntity($orderEntity,['status'=>$status]);
        $orderRtn = $this->ChOrder->save($orderNewEntity);
        if(!$orderRtn){
            Debugger::log($orderNewEntity);
            return false;
        }
        $purchaseList = $this->ChPurchase->find()->where(['order_code'=>$orderCode]);
        foreach($purchaseList as $purchase){
            $purchaseEntity = $this->ChPurchase->patchEntity($purchase,['status'=>$status]);
            if(!$this->ChPurchase->save($purchaseEntity)){
                Debugger::log($purchaseEntity);
                return false;
            }
        }
        return true;
    }
    /** claim 정보 입력 */
    public function refundReason($data,$userId,$userEmail){

        $isPurchase = $this->ChPurchase->find()->where(['buyer_id'=>$userId,'purchase_code'=>$data['purchase_code'],'order_code'=>$data['order_code']])->first();
        if($isPurchase==null){
            return array(
                'result' => false,
                'msg' => 'is not yours'
            );
        }

        $openType = $data["open_type"];

        $claimType= $this->getClaimOpenType($openType);

        $data['email'] = $userEmail;
        $data['subject'] = "환불 신청 안내";
        $data['reason'] = $claimType->name;

        $productInfo = $this->ChProductOption->find()->contain("ChProduct")->where(["ChProductOption.product_option_code"=>$isPurchase->product_option_code])->first();

        $mainImageId = $productInfo->ch_product->main_image_id;

        $mainImageFile = $this->ChImageFile->find()->where(['image_id'=>$mainImageId,"type"=>"image"])->first();

        $data['productName'] = $productInfo->ch_product->name;
        $data['image'] = $mainImageFile->murl;
        $optionArr = explode(";",$productInfo->name);
        $data['color'] = $optionArr[1];
        $data['size'] = $optionArr[2];
        $data['quantity'] = $isPurchase->quantity;
        $data['unitAmount'] = $isPurchase->unit_price;
        $data['sumPrice'] = ($isPurchase->quantity * $isPurchase->unit_price);
        $data['shipPrice'] = $isPurchase->shipping_price;
        $data['template'] = 'refundRequest';

        $data['users_id'] = $userId;
        $data['creator'] = $userId;
        $data['modifier'] = $userId;
        $data['order_claim_code'] = $this->productService->generateCode("ORDER_CLAIM");
        $data['status'] = "requested";
        $data['seller_close_yn'] = 'N';
        $data['buyer_close_yn'] = 'N';


        $refundChk = $this->ChRefund->find()->where([
//            'transaction_id' => $data['transaction_id'],
//            'creator' => $userId,
            'purchase_code' => $data['purchase_code']
        ])->first();

        if($refundChk!=null){
            return array(
                'result' => false,
                'msg' => 'already'
            );
        }

        //Hold Transaction
        $connection = ConnectionManager::get('default');
        $connection->begin();
        try {
            $chOrderEntity = $this->ChOrderClaim->newEntity($data);
            $claimSave = $this->ChOrderClaim->save($chOrderEntity);
            if ($claimSave) {
//                $chRefundEntity = $this->ChRefund->newEntity($data);
//                $refunSave = $this->ChRefund->save($chRefundEntity);
//                if ($refunSave) {
                $connection->commit();

                $this->sendConfirmEmail($data);
                return array(
                    'result' => true,
                    'msg' => 'success'
                );
//                }else {
//                    Debugger::log($chRefundEntity, 'error');
//                throw new InternalErrorException('ch_refund cannot be saved : ' . $chRefundEntity);
//                }
            }else {
            Debugger::log($chOrderEntity, 'error');
            throw new InternalErrorException('chorderClaim cannot be saved : ' . $chOrderEntity);
            }



        }catch(Exception $e) {
            $connection->rollback();
            return array(
                'result' => false,
                'msg' => 'fail'
            );

        }
    }

    /** 클레임 오픈 타입 가져오기 */
    public function getClaimOpenType($code = null){
        if($code == null) {
            return $this->ChCode->find()->where(['cds_kind' => "CLAIM_OPEN_TYPE", "use_flag" => "Y", "del_flag" => 'N'])->order(["seq" => 'ASC']);
        }else{
            return $this->ChCode->find()->where(['cds_kind' => "CLAIM_OPEN_TYPE", "use_flag" => "Y", "del_flag" => 'N',"code"=>$code])->first();
        }
    }
}
