<?php

/**
 * Created by PhpStorm.
 * User: eric
 * Date: 5/30/16
 * Time: 10:05 AM
 */

/*
 * afterpayment.php에서 사용되는 메세지 객체
 * name : messages
 */
class messages
{
    /*
     * 클라이언트단 메세지 또는 예외처리 메세지
     */
    
    public $CommonOne = "결제중 오류가 발생하였습니다.";

    public $CommonTwo = "결제가능 시간이 초과하였습니다. 로그인 후 다시 시도해 주시기 바랍니다.";

    public $CommonThree = "매진 상품이 포함되어 있습니다.<br/>상품 정보 확인 후 다시 주문해 주시기 바랍니다.";

    public $ErrorMsg = [
        1 => "결제중 오류가 발생하였습니다.",
        2 => "결제가능 시간이 초과하였습니다. 로그인 후 다시 시도해 주시기 바랍니다.",
        3 => "매진 상품이 포함되어 있습니다.<br/>상품 정보 확인 후 다시 주문해 주시기 바랍니다.",
    ];

    public $One = "unknown error has occurred.";

    public $Two = "check out timed out. please try it again after log in";

    public $Three = "some of the product is sold out. Please check the status of the product.";

    public $UnknownError = "알 수 없는 에러가 발생하였습니다. 다시 시도해 주세요.";

    public $NoOrderInfo = "주문정보가 존재하지 않습니다.";

    public $NoPurchaseInfo = "구매정보가 존재하지 않습니다.";

    public $NoPaymentInfo = "결제 정보가 존재하지 않습니다.";

    public $NotSavePayment = "Payment테이블 정보를 가져올 수 없습니다.";

    public $NotUpdateOrder = "Order데이터를 저장할 수 없습니다.";

    public $NotUpdatePurchase = "Purchase데이터를 저장할 수 없습니다.";

    public $FailDeleteCart = "카트삭제가 실패하였습니다 : {msg}";

    public $PDOException = "PDO Exception : {msg}";

    public $Error500 = "알수 없는 오류가 발생하였습니다. 관리자에게 문의 하세요.";

    public $GetCartError = "카트리스트를 가져오는데 실패하였습니다";

    public $NotMatchUser = "사용자가 일치 하지 않습니다.";

    public $NotSaveSms = "order code : {order_code}, mc_send_sms테이블에 정보를 저장하는데 실패 하였습니다";

    public $NotGetOrder = "Order테이블을 가져오는데 실패하였습니다.";

    public $NotGetProductOption = "Product Option테이블을 가져오는데 실패하였습니다.";

    public $NotUpdateProductOption = "Product Option테이블을 업데이트하는데 실패하였습니다.";

    public $SessionExpired = "결제가능시간이 초과하였습니다. 다시 시도해주세요.";

    public $NotMatchToken = "잘못된 접근입니다.";

    public $NotAvailable = "해당 상품이 매진되었습니다.";

    public $OutOfStock = "해당 상품이 매진되었습니다.";

    public $LessThan = "{product_option_code}에 대한 재고가 0입니다.";

    public $NoToken = "결제 가능시간을 초과 하였습니다. 다시 시도해 주세요.";

    public $NoMatchPaymentEmailSubject = "Warning!! 결제금액 불일치";

    public $NoMatchPaymentEmailEntry = "No Match";

    public $UpdateSms = "{shopName} {userName}님의 주문번호 : {orderCode}, 상품명 : {productName}의 입금이 확인되었습니다";

    public $UpdateSmsPrefix = "[FCC TV]";

    public $NoOid = "주문코드를 전달 받지 못했습니다.";

    /*
     * 에러로그 기록 메세지
     */

    public $UnknownErrorLog = "상점아이디가 일치하지 않습니다. mid in FCCTV : {inicis_mid}, mid from Inicis : {fcctv_mid}";

    public $NoOrderInfoLog = "order code : {order_code} 에 대한 ch_order 정보가 존재하지 않습니다.";

    public $NoPurchaseInfoLog = "order code : {order_code}에 대한 ch_purchase 정보가 존재하지 않습니다.";

    public $NotMatchPriceLog = "주문코드 : {order_code}의 실제 결제 금액 ({total_price})과 결제 예상 금액({amount})이 일치하지 않습니다.";

    public $NoPaymentInfoLog = "order code : {order_code}에 대한 ch_payment 정보가 존재하지 않습니다.";

    public $Error500Log = "결제가 성공하였으나 order code : {order_code}에 대한 정보를 디비에 저장하는데 실패하였습니다, {msg}";

    public $EmailErrorLog = "usersId : {users_id}. 주문성공 이메일을 전송하는데 실패하였습니다.";

    public $SmsSaveErrorLog = "order_code : {order_code} 주문성공 SMS를 저장하는데 실패하였습니다.";

    public $SessionExpiredLog = "order_code : {order_code}의 세션이 만료되었습니다.";

    public $NotMatchTokenLog = "이니시스토큰 : {token_inicis}와 세션토큰 : {token_fcctv} 이 일치하지 않습니다.";

    public $SaveCancelPaymentLog = "mc_cancel_payment에 저장이 실패하였습니다. tid = {tid}, order_code = {order_code}, message = {msg}, error_message = {errMsg}, status = {status}";

    public $CartDeleteFail = "users_id : {users_id}, product_option_code : {product_option_code}의 카트 삭제가 실패하였습니다.";

    /*
     * 에러메세지 분류
     */

    public $Inicis = 1;

    public $Fcctv = 2;

    public $Server = 3;

    public $Stock = 4;

    public $Session = 5;

    /*
     * 알수없는 에러 메세지
     * name : getUnknownErrorLog
     * @param iniMid : 이니시스로부터 받은 상점 아이디, fccMid : configure에서 가지고온 상점 아이디
     * return string
     */
    public function getUnknownErrorLog($iniMid, $fccMid){
        $strArr = [
            '{inicis_mid}' => $iniMid,
            '{fcctv_mid}' => $fccMid
        ];
        return $this->strReplace($strArr, $this->UnknownErrorLog);

    }
    /*
     * ch_order 에서 정보를 가지고오지 못하는 메세지
     * name : getNoOrderInfoLog
     * @param order_code : 주문코드
     * return string
     */
    public function getNoOrderInfoLog($order_code){
        $strArr = [
            '{order_code}' => $order_code
        ];
        return $this->strReplace($strArr, $this->NoOrderInfoLog);
    }
    /*
     * ch_purchase에서 정보를 가지고오지 못하는 메세지
     * name : getNoPurchaseInfoLog
     * @param order_code : 주문코드
     * return string
     */
    public function getNoPurchaseInfoLog($order_code){
        $strArr = [
            '{order_code}' => $order_code
        ];
        return $this->strReplace($strArr, $this->NoPurchaseInfoLog);
    }
    /*
     * 결제 금액이 맞지않는 메세지
     * name : getNotMatchPaymentLog
     * @param order_code : 주문코드, total_price : 결제된 금액, amount : 결제예상 금액
     * return string
     */
    public function getNotMatchPaymentLog($order_code, $total_price, $amount){
        $strArr = [
            '{order_code}' => $order_code,
            '{total_price}' => $total_price,
            '{amount}' => $amount
        ];
        return $this->strReplace($strArr, $this->NotMatchPriceLog);
    }
    /*
     * ch_payment테이블 정보를 가지고 오지 못하는 메세지
     * name : getNoPaymentInfoLog
     * @param order_code : 주문코드
     * return string
     */
    public function getNoPaymentInfoLog($order_code){
        $strArr = [
            '{order_code}' => $order_code
        ];
        return $this->strReplace($strArr, $this->NoPaymentInfoLog);
    }
    /*
     * ch_cart 삭제 실패 메세지
     * name : getNotDeleteCartLog
     * @param msg : 로그에 쌓을 메세지
     * return string
     */
    public function getNotDeleteCartLog($msg){
        $strArr = [
            '{msg}' => $msg
        ];
        return $this->strReplace($strArr, $this->FailDeleteCart);
    }
    /*
     * PDO에러 메세지
     * name : getPDOLog
     * @param msg : 로그에 쌓을 메세지
     * return string
     */
    public function getPDOLog($msg){
        $strArr = [
            '{msg}' => $msg
        ];
        return $this->strReplace($strArr, $this->PDOException);
    }
    /*
     * 500에러 메세지
     * name : get500Log
     * @param order_code : 주문코드, msg : 로그에 기록할 메세지
     * return string
     */
    public function get500Log($order_code, $msg){
        $strArr = [
            '{order_code}' => $order_code,
            '{msg}' => $msg
        ];
        return $this->strReplace($strArr, $this->Error500Log);
    }
    /*
     * 이메일 전송 실패 메세지
     * name : getEmailErrorLog
     * @param users_id : 구매자 users_id
     * return string
     */
    public function getEmailErrorLog($users_id){
        $strArr = [
            '{users_id}' => $users_id
        ];
        return $this->strReplace($strArr, $this->EmailErrorLog);
    }
    /*
     * mc_send_sms테이블에 정보 저장 실패 메세지
     * name : getSmsErrorLog
     * @param order_code : 주문코드
     * return string
     */
    public function getSmsErrorLog($order_code){
        $strArr = [
            '{order_code}' => $order_code
        ];
        return $this->strReplace($strArr, $this->SmsSaveErrorLog);
    }
    /*
     * 루프내 카트삭제 메세지
     * name : getCartDeleteFailLog
     * @param users_id : 구매자 아이디, product_option_code : 삭제할 상품옵션코드
     * return string
     */
    public function getCartDeleteFailLog($users_id, $product_option_code){
        $strArr = [
            '{users_id}' => $users_id,
            '{product_option_code}' => $product_option_code
        ];
        return $this->strReplace($strArr, $this->CartDeleteFail);
    }
    /*
     * mc_send_sms에 저장할 메세지
     * name : getSmsSaveMsg
     * @param designer_name : 디자이너명, user_name : 구매자명, order_code : 주문코드, product_name : 상품명
     * return string
     */
    public function getSmsSaveMsg($designer_name, $user_name, $order_code, $product_name){
        $strArr = [
            '{shopName}' => $designer_name,
            '{userName}' => $user_name,
            '{orderCode}' => $order_code,
            '{productName}' => $product_name
        ];
        return $this->strReplace($strArr, $this->UpdateSms);
    }
    /*
     * mc_send_sms테이블에 저장 실패 메세지
     * name : getNotSaveSmsLog
     * @param order_code : 주문코드
     * return string
     */
    public function getNotSaveSmsLog($order_code){
        $strArr = [
            '{order_code}' => $order_code
        ];
        return $this->strReplace($strArr, $this->SmsSaveErrorLog);
    }
    /*
     * 세션 만료 에러 메세지
     * name : getSessionExpiredLog
     * @param order_code : 주문코드
     * return string
     */
    public function getSessionExpiredLog($order_code){
        $strArr = [
            '{order_code}' => $order_code
        ];
        return $this->strReplace($strArr, $this->SessionExpiredLog);
    }
    /*
     * 세션의 토큰이 일치하지 않는 메세지
     * name : getSessionNotMatchLog
     * @param iniToken : 이니시스로부터 받은 토큰, fccToken : fcc 세션에 저장된 토큰
     * return string
     */
    public function getSessionNotMatchLog($iniToken, $fccToken){
        $strArr = [
            '{token_inicis}' => $iniToken,
            '{token_fcctv}' => $fccToken
        ];
        return $this->strReplace($strArr, $this->NotMatchTokenLog);
    }
    /*
     * 재고 부족 메세지
     * name : getLessThanStock
     * @param productOptionCode : 재고부족상품의 product_option_code
     * return string
     */
    public function getLessThanStock($productOptionCode){
        $strArr = [
            '{product_option_code}' => $productOptionCode
        ];
        return $this->strReplace($strArr, $this->LessThan);
    }
    /*
     * 주문취소테이블 저장 실패 메세지
     * name : getSaveCancelFailLog
     * @param tid : 트랜잭션아이디, orderCode : 주문코드, msg : 메세지, errMsg : 에러 메세지, status : 상태
     * return string
     */
    public function getSaveCancelFailLog($tid, $orderCode, $msg, $errMsg, $status){
        $strArr = [
            '{tid}' => $tid,
            '{order_code}' => $orderCode,
            '{msg}' => $msg,
            '{errMsg}' => $errMsg,
            '{status}' => $status
        ];
        return $this->strReplace($strArr, $this->SaveCancelPaymentLog);
    }
    /*
     * 메세지에 일치하는 단어로 교체
     * name : strReplace
     * @param strArray : 교체할 단어가 들어있는 어레이, message : 교체해야할 메세지
     * return string
     */
    public function strReplace($strArray, $message){
        $msg = $message;
        foreach($strArray as $k => $v){
            $msg = str_replace($k, $v, $msg);
        }
        return $msg;
    }
}