<?php
include_once "messages.php";
include_once "../../../src/Service/EncryptService.php";
include_once "../../../config/bootstrap.php";
/*
 * Created by PhpStorm.
 * User: Eric Lee
 * Date: 2016. 3. 7.
 * Time: 오후 3:08
 */

/*Declare Constants*/
//ch_order, ch_purchase, ch_payment의 status컬럼
define("ORDERED", "ordered");
define("PURCHASED", "purchased");
define("FAIL", "fail");
define("SUCCESS", "success");
//ch_product의 status컬럼
define("OPEN", "open");
//ch_payment의 gateway컬럼
define("GATEWAY", "inicis");
//ch_payment의 type컬럼
define("TYPE", "payment");
//서버 타임존
define("TIME_ZONE", "Asia/Seoul");
//이니시스 웹 결제 요청 타입
define("WEBPAYTYPE", "securepay");
//이니시스 모바일 결제 요청 타입
define("MOBPAYTYPE", "PAY");
//엔코딩 from
define("ENCOD_FROM", "euc-kr");
//엔코딩 to
define("ENCOD_TO", "utf-8");

class afterPayment
{
    //MySQL PDO
    private $pdo;

    //Configuration
    private $conf;

    //암호화 서비스
    private $encrypt;

    //상점 아이디
    public $MID;

    //메세지 클래스 message.php
    public $MSG;

    //이니시스 암호화 키
    public $KEY;

    function __construct(){

        //CAKEPHP 세션 시작
        if(session_name() != "CAKEPHP"){
            session_name('CAKEPHP');
            session_start();
        }

        //Configuration 가졍기
        $this->conf = require('../../../config/app.php');
        //DB 세팅
        $dbconf = $this->conf['Datasources']['default'];
        $port = (isset($dbconf['port'])) ? ";port=" . $dbconf['port'] : '';
        $this->pdo = new PDO('mysql:host=' . $dbconf['host'] . $port . ';dbname=' . $dbconf['database'].';charset=utf8',
            $dbconf['username'],
            $dbconf['password']
        );

        //실제 결제 또는 테스트 결제에 따른 키값 저장
        $this->KEY = $this->conf['PAYMENT_TEST'] == 'Y' ? $this->conf['INILITETESTKEY'] : $this->conf['INILITEKEY'];

        //암호화 서비스
        $this->encrypt = new \App\Service\EncryptService();

        //상점 아이디 저장
        $this->MID = $this->conf['PAYMENT_TEST'] == 'Y' ? $this->conf['INITESTMID'] : $this->conf['INIMID'];

        //한국시간 기준 계산
        date_default_timezone_set(TIME_ZONE);

        //메세지 클래스 불러오기
        $this->MSG = new messages();

    }

    /*
     * 이니시스결제 결과에 따른 데이터를 디비에 저장한다.
     * name : receivePaymentResult
     * @param mid : 상점 아이디, orderCode : 주문코드, totalPrice : 총결제금액, resultCode : 이니시스 결제성공여부 코드, tid : 트랜잭션아이디, paymethod : 결제수단
     * return array : ['result' => true or false, 'msg' => string]
     */

    public function receivePaymentResult($mid, $orderCode, $totalPrice, $resultCode, $tid, $paymethod){
        $conf = $this->conf;

        //DB setting
        $pdo = $this->pdo;

        //상점 아이디 가져오기
        $merchantId = $this->MID;

        //이니시스에서 받은 상점 아이디와 FCCTV의 Configuration에 있는 상점 아이디 비교
        if($merchantId != $mid){
            //에러 로그에 기록
            $this->setErrorLog($this->MSG->getUnknownErrorLog($merchantId, $mid));
            //리턴 어레이
            $result = ['result' => false, 'msg' => $this->MSG->One];
            return $result;
        }

        //주문정보 가져오기
        $orderQuery = "SELECT * FROM ch_order WHERE order_code = ?";
        $orderStmt = $pdo->prepare($orderQuery);
        $orderStmt->execute(array($orderCode));
        $orderList = $orderStmt->fetchAll();

        //주문코드에 맞는 주문정보가 없을때
        if(empty($orderList)){
            //에러 로그에 기록
            $this->setErrorLog($this->MSG->getNoOrderInfoLog($orderCode));
            //주문정보에러 메세지 리턴
            $result = ['result' => false, 'msg' => $this->MSG->One];
            return $result;
        }

        //구매정보 가져오기
        $purchaseQuery = "SELECT * FROM ch_purchase WHERE order_code = ?";
        $purchaseStmt = $pdo->prepare($purchaseQuery);
        $purchaseStmt->execute(array($orderCode));
        $purchaseList = $purchaseStmt->fetchAll();

        //주문코드에 따른 구매정보가 없을때
        if(empty($purchaseList)){
            //에러 로그에 기록
            $this->setErrorLog($this->MSG->getNoPurchaseInfoLog($orderCode));
            //구매정보에러 메세지 리턴
            $result = ['result' => false, 'msg' => $this->MSG->One];
            return $result;
        }

        //결제가 테스트인지 확인 후 결제금액 설정
        if($conf['PAYMENT_TEST'] == 'Y'){
            $amount = 1000;
        }
        //리얼 결제일때 결제음액 설정
        else{
            $amount = 0;
            foreach($purchaseList as $purchase){
                $amount += $purchase['amount'];
                $amount += $purchase['shipping_price'];
            }
        }

        //결제금액이 달라도 그대로 디비에 저장
        if($amount != $totalPrice){
            //에러 로그에 기록
            $notMatchMsg = $this->MSG->getNotMatchPaymentLog($orderCode, $totalPrice, $amount);
            $this->setErrorLog($notMatchMsg);
            //관리자에게 이메일 전송
            $this->sendPaymentNotMatchEmail($notMatchMsg);
        }

        //결제테이블 가져오기
        $paymentQuery = "SELECT * FROM ch_payment WHERE order_code = ?";
        $paymentStmt = $pdo->prepare($paymentQuery);
        $paymentStmt->execute(array($orderCode));
        $paymentList = $paymentStmt->fetchAll();

        //주문코드에따른 결제정보가 없을때
        if(empty($paymentList)){
            //에러 로그에 기록
            $this->setErrorLog($this->MSG->getNoPaymentInfoLog($orderCode));
            //payment정보 에러 메세지 리턴
            $result = ['result' => false, 'msg' => $this->MSG->One];
            return $result;
        }

        //상품 상태 체크를 위한 어레이작성
        $productOptionCode = array();
        $productQuantity = array();
        foreach($purchaseList as $purchase){
            $productOptionCode[] = $purchase['product_option_code'];
            $productQuantity[] = $purchase['quantity'];
        }
        //상품 상태 체크
        $productStatus = $this->checkProductStatus($orderCode);

        //게이트웨이명 가져오기
        $gateWay = GATEWAY;
        $type = TYPE;

        /* 결제상태에 따른 status 값 설정 */
        //이니시스 결제 성공시
        if($resultCode == 00){
            //상품이 팔릴수있는 상태가 아닐경우
            if($productStatus['result'] == false){
                $paymentStatus = FAIL;
                $orderStatus = FAIL;
                $purchaseStatus = FAIL;
            }
            //상품이 팔수 있는 상태일 경우
            else{
                $paymentStatus = SUCCESS;
                $orderStatus = PURCHASED;
                $purchaseStatus = PURCHASED;
            }
        }
        //이니시스결제가 실패한 경우
        else{
            $paymentStatus = FAIL;
            $orderStatus = FAIL;
            $purchaseStatus = FAIL;
        }

        //트랜잭션 시작
        $pdo->beginTransaction();

        //TRY CATCH 시작
        try{

            //ch_payment에 업데이트
            $updatePaymentQuery = "
              UPDATE ch_payment
              SET
                transaction_id = ?,
                gateway = ?,
                type = ?,
                method = ?,
                gateway_status = ?,
                status = ?,
                total = ?,
                handling = ?,
                tax = ?,
                fee = ?,
                modified = ?
              WHERE order_code = ?";
            $updatePaymentStmt = $pdo->prepare($updatePaymentQuery);
            //업데이트 성공시
            if($updatePaymentStmt->execute(array($tid, $gateWay, $type, $paymethod, $resultCode, $paymentStatus, $totalPrice, 0, 0, 0, date('Y-m-d H:i:s'), $orderCode))){
                $updatePaymentStmt->fetchAll();
            }
            //업데이트 실패시
            else{
                throw new Exception($this->MSG->NotSavePayment);
            }

            //ch_order에 업데이트
            $updateOrderQuery = "UPDATE ch_order set status = ?, transaction_id = ?, modified = ? where order_code = ?";
            $updateOrderStmt = $pdo->prepare($updateOrderQuery);
            //업데이트 성공시
            if($updateOrderStmt->execute(array($orderStatus, $tid, date('Y-m-d H:i:s'), $orderCode))){
                $updateOrderStmt->fetchAll();
            }
            //업데이트 실패시
            else{
                throw new Exception($this->MSG->NotUpdateOrder);
            }

            //이니시스 결제 성공시 카트삭제 및 상품재고 수정
            if($resultCode == 00){

                //ch_cart정보 가져오기
                $getCartQuery = "SELECT * from ch_cart where users_id = ?";
                $getCartStmt = $pdo->prepare($getCartQuery);
                $getCartStmt->execute(array($orderList[0]['users_id']));
                $getCartList = $getCartStmt->fetchAll();

                //ch_purchase에 업데이트
                foreach($purchaseList as $purchase){
                    $updatePurchaseQuery = "UPDATE ch_purchase set status = ?, modified = ? where order_code = ?";
                    $updatePurchaseStmt = $pdo->prepare($updatePurchaseQuery);

                    //업데이트 성공시
                    if($updatePurchaseStmt->execute(array($purchaseStatus, date('Y-m-d H:i:s'), $orderCode))){
                        $updatePurchaseStmt->fetchAll();
                    }
                    //업데이트 실패시
                    else{
                        throw new Exception($this->MSG->NotUpdatePurchase);
                    }

                    //상품상태가 정상일시 재고 변경 및 카트 삭제
                    if($productStatus['result']) {
                        $productOptionCode = $purchase['product_option_code'];
                        $getProductOptionQuery = "
                        SELECT opt.name AS product_option_name, prd.name AS product_name, opt.stock, prd.product_code
                        FROM
                          ch_product_option opt
                        LEFT OUTER JOIN ch_product prd ON opt.product_code = prd.product_code
                        WHERE product_option_code = ?";

                        $getProductOptionStmt = $pdo->prepare($getProductOptionQuery);
                        $getProductOptionStmt->execute(array($productOptionCode));
                        $getProductOption = $getProductOptionStmt->fetchAll();
                        $pay = new afterPayment();

                        //재고가 5개 이하일경우 관리자에게 이메일 전송
                        if ($getProductOption[0]['stock'] > 5 && (($getProductOption[0]['stock'] - $purchase['quantity']) <= 5)) {
                            $pay->sendUrgentStockEmail(
                                $getProductOption[0]['product_code'],
                                $getProductOption[0]['product_name'],
                                $getProductOption[0]['product_option_name']
                            );
                        }
                        //재고 업데이트
                        $this->updateStock($purchase['product_option_code'], $purchase['quantity']);
                        foreach ($getCartList as $cart) {
                            if ($cart['product_option_code'] == $purchase['product_option_code']) {
                                $productCartArray[] = $purchase['product_option_code'];
                            }
                        }
                        //카트에 해당상품이 존재할시
                        if(isset($productCartArray) and !is_null($productCartArray)){

                            //카트삭제
                            $return = $this->deleteCart($productCartArray, $orderList[0]['users_id']);

                            //카트삭제 실패시 예외처리
                            if ($return['result'] != true or $return['msg'] == 'lack') {
                                //메세지가져오기
                                $notDeleteCartMsg = $this->MSG->getNotDeleteCartLog($return['msg']);
                                //에러로그에 기록하기
                                $this->setErrorLog($notDeleteCartMsg);
                                throw new Exception($notDeleteCartMsg);
                            }
                        }
                    }
                }
            }
            //디비에 커밋
            $pdo->commit();
        }
        //PDO 예외처리
        catch(PDOException $e){
            //롤백
            $pdo->rollBack();
            //에러 로그 기록
            $pdoMsg = $this->MSG->getPDOLog($e->getMessage());
            $this->setErrorLog($pdoMsg);
            return ['result' => false, 'msg' => $this->MSG->One, 'exception' => $pdoMsg,];
        }
        //일반 예외처리
        catch(Exception $e){
            //롤백
            $pdo->rollBack();
            //에러 로그 기록
            $Error500Msg = $this->MSG->get500Log($orderCode, $e->getMessage());
            $this->setErrorLog($Error500Msg);
            return ['result' => false, 'msg' => $this->MSG->One, 'exception' => $Error500Msg,];
        }

        //이니시스 결제 성공시 구매자에게 이메일 전송 및 문자메세지 전송테이블에 저장
        if($resultCode == 00 && $productStatus['result'] == true){
            try{
                //주문정보 이메일 전송
                $emailRtn = $this->sendPurchaseEmail($orderCode, $orderList[0]['users_id']);
            }catch(Exception $e){
                //에러 로그 기록
                $this->setErrorLog($e->getMessage());
            }
            try{
                //문자메세지 디비에 저장
                $this->saveSmsForPurchase($orderCode, $orderList[0]['users_id']);
            }catch (Exception $e){
                //에러 로그 기록
                $this->setErrorLog($this->MSG->getSmsErrorLog($orderCode));
                $this->setErrorLog($e->getMessage());
            }
        }
        //상품상태 이상시 리턴
        if($productStatus['result'] == false){
            $result = [
                'result' => false,
                'msg' => $this->MSG->Three,
                'order_code' => $orderCode
            ];
            return $result;
        }
        //디비저장 성공
        return ['result' => true];


    }

    /*
     * 에러로그를 기록한다.
     * name : setErrorLog
     * @param msg: 메세지, path : debug 또는 error
     */
    public function setErrorLog($msg, $path = null){
        if(is_null($path)){
            $path = 'error';
        }
        $logPath = '../../../logs/'.$path.'.log';
        $date = date('Y-m-d H:i:s');
        $type = ucfirst($path);
        $errorMsg = $msg;
        error_log(PHP_EOL.$date.' '.$type.PHP_EOL, 3, $logPath);
        error_log(print_r($errorMsg, true), 3, $logPath);
        error_log(PHP_EOL, 3, $logPath);
    }

    /*
     * 카트를 ch_cart에서 삭제한다.
     * name : deleteCart
     * @param productOptionCodeList : product_option_code리스트가 담긴 어레이, usersId = 구매자 아이디
     * return array ['result' => true or false, 'msg' => 'success' or 'deleteFail' or 'lack']
     */
    private function deleteCart($productOptionCodeList, $usersId){

        //PDO세팅
        $pdo = $this->pdo;

        //카트 variables 분리
        $values = array_values($productOptionCodeList);
        $fields = "'".implode("', '",$values)."'";

        //카트가져오기
        $getCartWithProductOptionCodeQuery = "SELECT * FROM ch_cart WHERE product_option_code IN (".$fields.") AND users_id = ".$usersId;
        $deleteCartWithProductOptionCodeStmt = $pdo->prepare($getCartWithProductOptionCodeQuery);
        //카트가져오기 성공시
        if($deleteCartWithProductOptionCodeStmt->execute()){
            $deleteCartWithProductOptionCodeList = $deleteCartWithProductOptionCodeStmt->fetchAll();
        }
        //카트가져오기 실패시
        else{
            throw new Exception($this->MSG->GetCartError);
        }

        //카트수
        $resultCnt = 0;

        //카트삭제하기
        foreach($deleteCartWithProductOptionCodeList as $item){
            $deleteCartQuery = "DELETE FROM ch_cart WHERE product_option_code = '".$item['product_option_code']."' AND users_id = ".$usersId;
            $deleteCartStmt = $pdo->prepare($deleteCartQuery);
            //카트삭제 성공시
            if($deleteCartStmt->execute()){
                $deleteCartStmt->fetchAll();
                //카트수증가
                $resultCnt += 1;
            }
            //카트삭제 실패시
            else{
                //카트수 그대로
                $resultCnt += 0;
                //에러로그 기록
                $this->setErrorLog($this->MSG->getCartDeleteFailLog($usersId, $item['product_option_code']), 'debug');
            }
        }

        $result = array();
        //삭제한 카트수와 테이블의 카트수가 같은지 확인
        if(sizeof($deleteCartWithProductOptionCodeList)==$resultCnt){
            $result['result'] = true;
            $result['msg'] = 'success';
        }
        //삭제한 카트수가 0일경우
        else if($resultCnt==0){
            $result['result'] = false;
            $result['msg'] = 'deleteFail';
        }
        //삭제한카트수가 카트테이블의 카트수보다 적을때
        else if($resultCnt<sizeof($deleteCartWithProductOptionCodeList)){
            $result['result'] = true;
            $result['msg'] = 'lack';
        }
        return $result;
    }

    /*
     * CAKEPHP 내부에 있는 이메일 폼을 사용하여 주문정보 이메일 전송
     * name : sendPurchaseEmail
     * @param order_code : 주문코드, usersId : 구매자 아이디
     * return boolean
     */
    private function sendPurchaseEmail($order_code, $usersId){
        //POST데이터 어레이로 만들기
        $post = [
            'order_code' => $order_code,
            'users_id' => $usersId
        ];

        //POST 보낼 url 생성
        $url = 'https://'.$_SERVER['HTTP_HOST'].'/Purchases/sendPurchaseInfo';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //POST로 설정
        curl_setopt($ch, CURLOPT_POST, 1);
        //응답받을지 설정
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //전송할 데이터 설정
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        //전송
        if(curl_exec($ch) === false){
            $this->setErrorLog("CURL error number : ".curl_errno($ch));
            $this->setErrorLog($this->MSG->getEmailErrorLog($usersId));
        }
    }

    /*
     * mc_send_sms 테이블에 정보 저장
     * name : saveSmsForPurchase
     * @param orderCode : 주문코드, usersId : 구매자 아이디
     */

    public function saveSmsForPurchase($orderCode, $usersId){
        //pdo 세팅
        $pdo = $this->pdo;

        //쿼리 작성
        $query = "SELECT
                    ord.users_id,
                    COUNT(puc.purchase_code) cnt,
                    puc.product_code,
                    prd.name,
                    prd.designer_name,
                    usr.last_name,
                    usr.first_name,
                    usr.phone_number
                  FROM ch_order ord
                  JOIN ch_purchase puc
                  ON puc.order_code = ord.order_code
                  JOIN ch_product prd
                  ON prd.product_code = puc.product_code
                  JOIN users usr
                  ON usr.id = ord.users_id
                  WHERE ord.order_code = ?";
        $orderStmt = $pdo->prepare($query);

        //주문정보 가져오기 성공시
        if($orderStmt->execute(array($orderCode))){
            $orderList = $orderStmt->fetchAll(PDO::FETCH_ASSOC)[0];
            //주문정보의 사용자 아이디와 구매자 아이디와 비교
            if($orderList['users_id'] != $usersId){
                throw new Exception($this->MSG->NotMatchUser);
            }
            //판매아이템 수량별 메세지 변경
            $stringQuantity = $orderList['cnt'] > 1?" 외 ".($orderList['cnt'] - 1)."개":"";
            $message = $this->MSG->getSmsSaveMsg(
                $orderList['designer_name'],
                $orderList['last_name'].$orderList['first_name'],
                $orderCode,
                $orderList['name'].$stringQuantity);
            $phone = $this->encrypt->decrypt(trim($orderList['phone_number']));
            $created = date('Y-m-d H:i:s');
            $messageWithPrefix = $this->MSG->UpdateSmsPrefix.$message;

            $smsQuery = "INSERT INTO mc_send_sms (
                            recv_phone_number,
                            message,
                            send_yn,
                            created
                          ) VALUES (
                            '$phone',
                            '$messageWithPrefix',
                            'n',
                            '$created'
                          )";
            $smsStmt = $pdo->prepare($smsQuery);
            if($smsStmt->execute()){
                $smsStmt->fetchAll();
            }else{
                throw new Exception($this->MSG->getNotSaveSmsLog($orderCode));
            }
        }
        //주문정보 가져오기 실패시
        else{
            throw new Exception($this->MSG->NotGetOrder);
        }
    }

    /*
     * 주문취소시 이메일전송실패
     * name : sendCancelFailEmail
     * @param msg : 메세지, orderCode : 주문코드
     * return boolean
     */
    public function sendCancelFailEmail($msg, $orderCode){
        $post = [
            'order_code' => $orderCode,
            'msg' => $msg
        ];

        $url = 'https://'.$_SERVER['HTTP_HOST'].'/Orders/sendCancelFailEmail';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $rtn = curl_exec($ch);
        return $rtn;
    }

    private function recoverStock($pdoObj, $purchaseList){

        foreach($purchaseList as $purchase){

            $getProductOptionQuery = "SELECT * FROM ch_product_option WHERE product_option_code = '".$purchase['product_option_code'];
            $getProductOptionStmt = $pdoObj->prepare($getProductOptionQuery);
            if($getProductOptionStmt->execute()){
                $productOptionList = $getProductOptionStmt->fetchAll();
            }else{
                throw new Exception("Unable to get product option table");
            }

            $updateStock = $productOptionList['stock'] + $purchase['quantity'];
            $updateProductOptionQuery = "UPDATE ch_product_option SET stock = ".$updateStock." WHERE product_option_code = '".$purchase['product_option_code'];
            $updateProductOptionStmt = $pdoObj->prepare($updateProductOptionQuery);
            if($updateProductOptionStmt->execute()){
                $updateProductOptionStmt->fetchAll();
            }else{
                throw new Exception("Unable to update product option table");
            }
        }
    }

    /*
     * 주문코드 체크하기
     * name : checkOrderCode
     * @param orderCode : 주문코드
     * return array() ['status' => boolean, 'msg' => string]
     */
    public function checkOrderCode($orderCode){

        if(!isset($_SESSION['token'])){
            //토큰 미존재 로그 기록
            $this->setErrorLog($this->MSG->getSessionExpiredLog($orderCode));
            $rtn = ['status' => false, 'msg' => $this->MSG->SessionExpired];
            return $rtn;
        }
        $decryptToken = $this->encrypt->decrypt(trim($_SESSION['token']));
        if($decryptToken != $orderCode){
            //토큰불일치 로그 기록
            $this->setErrorLog($this->MSG->getSessionNotMatchLog($orderCode, $decryptToken));
            $rtn = ['status' => false, 'msg' => $this->MSG->NotMatchToken];
            return $rtn;
        }
        return ['status' => true];
    }

    /*
     * 상품 상태 체크
     * name : checkProductStatus
     * @param oid : 주문코드
     * return array() ['result' => boolean, 'msg' => string]
     */
    public function checkProductStatus($oid){
        $pdo = $this->pdo;
        $orderQuery = "
            SELECT pc.order_code, pc.product_code, prdo.stock, pc.quantity, pc.status, prd.status AS prdstatus
            FROM ch_purchase pc
            LEFT JOIN ch_product prd ON prd.product_code = pc.product_code
            LEFT JOIN ch_product_option prdo ON prdo.product_option_code = pc.product_option_code
            WHERE pc.order_code = ? AND pc.status = ?
        ";
        $orderStmt = $pdo->prepare($orderQuery);
        $orderStmt->execute(array($oid, ORDERED));
        $orders = $orderStmt->fetchAll();

        $result = ['result' => false];
        if(empty($orders)){
            $rtn = array_merge($result, ['msg' => $this->MSG->NoOrderInfo]);
            $this->setErrorLog($rtn);
            return $rtn;
        }
        foreach($orders as $order){
            if($order['prdstatus'] != OPEN){
                $rtn = array_merge($result, ['msg' => $this->MSG->NotAvailable]);
                $this->setErrorLog($rtn);
                return $rtn;
            }
            if($order['stock'] < $order['quantity']){
                $rtn = array_merge($result, ['msg' => $this->MSG->OutOfStock]);
                $this->setErrorLog($rtn);
                return $rtn;
            }
        }
        return ['result' => true];
    }

    /*
     * 결제 성공후 재고수량 변경
     * name : updateStock
     * @param productOptionCode : 상품옵션코드, quantity : 구매수량
     */
    public function updateStock($productOptionCode, $quantity){
        $pdo = $this->pdo;
        $productOptionQuery = "SELECT * FROM ch_product_option WHERE product_option_code = '".$productOptionCode."'";
        $productOptionStmt = $pdo->prepare($productOptionQuery);

        if(!$productOptionStmt->execute()){
            throw new Exception($this->MSG->NotGetProductOption);
        }else{
            $productOptionList = $productOptionStmt->fetchAll();
        }

        $updateStock = $productOptionList[0]['stock'] - $quantity;
        if($updateStock < 0){
            $lessMsg = $this->MSG->getLessThanStock($productOptionCode);
            $this->setErrorLog($lessMsg);
            throw new Exception($lessMsg);
        }
        $updateStockQuery = "UPDATE ch_product_option SET stock = ".$updateStock." WHERE product_option_code = '".$productOptionCode."'";
        $updateStockStmt = $pdo->prepare($updateStockQuery);
        if($updateStockStmt->execute()){
            $updateStockStmt->fetchAll();
        }else{
            throw new Exception($this->MSG->NotUpdateProductOption);
        }
    }

    public function cancelOrder($mid, $msg, $tid, $oid){
        $key = ($this->conf['PAYMENT_TEST'] == 'Y')?$this->conf['INILITETESTKEY']:$this->conf['INILITEKEY'];
        $iniCancel = new INILite();
        $iniCancel->m_inipayHome = $this->conf['INICIS_PATH'];
        $iniCancel->m_key = $key;
        $iniCancel->m_type = "cancel";
        $iniCancel->m_mid = $mid;
        $iniCancel->m_cancelMsg = $msg;
        $iniCancel->m_tid = $tid;

        $iniCancel->startAction();
        if($iniCancel->m_resultCode != 00){
            //취소 실패시 관리자에게 이메일 전송
            $this->sendCancelFailEmail($msg, $oid);
        }
    }

    /*
     * 재고수량이 5개이하일시 관리자에게 이메일 전송
     * name : sendUrgentStockEmail
     * @param productCode : 상품코드, productName : 상품명, optionName : 옵션명
     * return boolean
     */
    public function sendUrgentStockEmail($productCode, $productName, $optionName){
        $post = [
            'product_code' => $productCode,
            'product_name' => $productName,
            'product_option_name' => $optionName
        ];

        $url = 'https://'.$_SERVER['HTTP_HOST'].'/Purchases/sendUrgentStockWarning';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $rtn = curl_exec($ch);
        return $rtn;
    }

    /*
     * 토큰체크
     * name : tokenCheck
     * @param inicisOid
     * return array() ['result' => boolean, 'msg' => string]
     */
    public function tokenCheck($inicisOid){
        $fccOid = $this->encrypt->decrypt(trim($_SESSION['token']));
        if($inicisOid != $fccOid) {
            $rtn = [
                'msg' => $this->MSG->NotMatchToken,
                'result' => false
            ];
            return $rtn;
        }
        return ['result' => true];
    }

    public function tokenExist(){
        if(!isset($_SESSION['token'])) {
            $rtn = [
                'msg' => $this->MSG->NoToken,
                'result' => false
            ];
            return $rtn;
        }
        return ['result' => true];
    }

    /*
     * 결제금액이 결제예상금액과 다를경우 관리자에게 이메일 전송
     * name : sendPaymentNotMatchEmail
     * @param message : 이메일 메세지
     * return boolean
     */
    public function sendPaymentNotMatchEmail($message){
        $post = [
            'subject' => $this->MSG->NoMatchPaymentEmailSubject,
            'entry' => $this->MSG->NoMatchPaymentEmailEntry,
            'message' => $message
        ];

        $url = 'https://'.$_SERVER['HTTP_HOST'].'/orders/sendCommonEmail';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $rtn = curl_exec($ch);
        return $rtn;
    }

    /*
     * 결제취소 테이블에 저장
     * name : saveCancelPayment
     * @param tid : 트랜잭션아이디, orderCode : 주문코드, msg : 메세지, errMsg : 에러 메세지, status : 상태값
     */
    public function saveCancelPayment($tid, $orderCode, $msg, $errMsg, $status)
    {
        $pdo = $this->pdo;
        $date = date("Y-m-d H:i:s");
        $cancelQuery =
            "INSERT INTO mc_cancel_payment (
                transaction_id,
                order_code,
                message,
                err_msg,
                status,
                created,
                modified
                ) VALUES (
                '$tid',
                '$orderCode',
                '$msg',
                '$errMsg',
                '$status',
                '$date',
                '$date'
            )";
        $cancelStmt = $pdo->prepare($cancelQuery);
        //var_dump($cancelStmt);
        if($cancelStmt->execute()){
            $cancelStmt->fetchAll();
        }else{
            $saveCancelFailMsg = $this->MSG->getSaveCancelFailLog($tid, $orderCode, $msg, $errMsg, $status);
            $this->setErrorLog($saveCancelFailMsg);
        }
    }
}
?>
