<script src="/_res/lib/jquery-1.11.2.min.js"></script>
<form action="/orders/returnOrderError" method="POST" id="errorRedirect" accept-charset="UTF-8">
	<input type="hidden" name="msg" value="">
	<input type="hidden" name="order_code" value="">
</form>
<?php
include_once("../../../src/Lib/Inicis/INILiteLib.php"); //이니시스 라이트 라이브러리
include_once("afterPayment.php"); //공통 클래스
include_once("messages.php");//메세지 클래스

//Configure를 가져온다.
$conf = require('../../../config/app.php');

//afterpayment클래스 호출
$paymentProcess = new afterPayment();
$message = new messages();

//전달받은 POST데이터를 로그에 저장한다.
$paymentProcess->setErrorLog($_POST, 'debug');

//order code를 전달 받았는지 확인한다.
if(!isset($_POST['oid'])) {
	errorRedirect($message->One);
}

//결제전 토큰 존재 확인
$tokenExist = $paymentProcess->tokenExist();
if($tokenExist['result'] == false){
	errorRedirect($message->Two);
}

//결제전 생성되 토큰 체크
$check = $paymentProcess->tokenCheck($_POST['oid']);
if($check['result'] == false) {
	errorRedirect($message->One);
}

//상품 상태 확인
$status = $paymentProcess->checkProductStatus($_POST['oid']);
if($status['result'] == false) {
	errorRedirect($message->Three, $_POST['oid']);
}

//이니시스 소켓 통신 준비
$inipay = new INILite();

$inipay->m_inipayHome = $conf['INICIS_PATH'];
$inipay->m_key = $paymentProcess->KEY;
$inipay->m_ssl = "true";
$inipay->m_type = WEBPAYTYPE;
$inipay->m_pgId = "INlite".$pgid;
$inipay->m_log = "true";
$inipay->m_debug = "true";
$inipay->m_mid = $mid;
$inipay->m_uid = $uid;
$inipay->m_uip = getenv("REMOTE_ADDR");
$eucgoodname = mb_convert_encoding($goodname, 'euc-kr', 'utf-8');
$inipay->m_goodName = $eucgoodname;
$inipay->m_currency = $currency;
$inipay->m_price = $price;
$eucbuyername = mb_convert_encoding($buyername, 'euc-kr', 'utf-8');
$inipay->m_buyerName = $eucbuyername;
$inipay->m_buyerTel = $buyertel;
$inipay->m_buyerEmail = $buyeremail;
$inipay->m_payMethod = $paymethod;
$inipay->m_encrypted = $encrypted;
$inipay->m_sessionKey = $sessionkey;
$inipay->m_url = $_SERVER['HTTP_HOST'];
$inipay->m_cardcode = $cardcode;
$inipay->m_oid = $_POST['oid'];

//이니시스 소켓통신 시작
$inipay->startAction();

//로그 기록
$paymentProcess->setErrorLog($inipay, 'debug');

//오더코드가 일치하는지 확인
$checkToken = $paymentProcess->checkOrderCode($inipay->m_oid);
if($checkToken['status'] == false){
	errorRedirect($message->One);
}

//데이터 디비에 저장
$result = $paymentProcess->receivePaymentResult(
	$inipay->m_qs['mid'],
	$inipay->m_oid,
	$inipay->m_resultprice,
	$inipay->m_resultCode,
	$inipay->m_tid,
	$inipay->m_qs['paymethod']
);

/*
 * 결과에 따른 클라이언트 액션 분류
 */
//이니시스 결제 성공 및 FCCTV디비 저장에도 성공
if($inipay->m_resultCode == 00 and $result['result'] == true){
	redirect($inipay->m_oid);
}
//이니시스 결제 성공 하지만 FCCTV디비 저장에 실패 또는 오류
elseif($inipay->m_resultCode == 00 and $result['result'] == false){
	$errMsg = NULL; //에러메세지
	$sts = 'n'; //status
	if(isset($result['exception'])){
		$errMsg = $result['exception'];
	}
	//m_tid : 트랜잭션 아이디, m_oid : 오더코드, 메세지, 에러메세지, status
	$paymentProcess->saveCancelPayment($inipay->m_tid, $inipay->m_oid, $result['msg'], $errMsg, $sts);
	if(isset($result['order_code'])){
		errorRedirect($result['msg'], $inipay->m_oid);
	}else{
		//얼럿후 리다이렉트
		errorRedirect($result['msg']);
	}
}
//이니시스 결제 실패
else{
	//얼럿후 리다이렉트
	errorRedirect($message->One);
}

//얼럿
function errorRedirect($msg, $orderCode = null){
	$pay = new afterPayment();
	$pay->setErrorLog($msg, 'debug');
	?>
	<script>
		var msgNum = "<?=isset($msg)?$msg:""?>";
		var orderCode = "<?=isset($orderCode)?$orderCode:""?>";
		$("input[name='msg']").val(msgNum);
		$("input[name='order_code']").val(orderCode);
		$("#errorRedirect").submit();
	</script>
<?php
	exit;
}

function redirect($oid){?>
	<script>
		window.location.href = "/Orders/returnOrderInfo/"+"<?=$oid?>";
	</script>
<?php }?>
