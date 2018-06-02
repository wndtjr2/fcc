<script src="/_res/lib/jquery-1.11.2.min.js"></script>
<?php
include("../../../src/Lib/Inicis/INImx.php"); //이니시스 모바일 라이브러리
include("afterPayment.php"); //공통 클래스
include_once("messages.php");//메세지 클래스

//Configure를 가져온다.
$conf = require('../../../config/app.php');

//afterpayment클래스 호출
$paymentProcess = new afterPayment();
$message = new messages();

//전달받은 POST데이터를 로그에 저장한다.
if(empty($_POST)){
	errorRedirect($message->One);
}
$paymentProcess->setErrorLog($_POST, 'debug');

//order code를 전달 받았는지 확인한다.
if(!isset($_POST['P_NOTI'])) {
	errorRedirect($message->One);
}

//결제전 토큰 존재 확인
$tokenExist = $paymentProcess->tokenExist();
if($tokenExist['result'] == false){
	errorRedirect($message->Two);
}

//결제전 생성되 토큰 체크
$check = $paymentProcess->tokenCheck($_POST['P_NOTI']);
if($check['result'] == false) {
	errorRedirect($message->One);
}

//상품 상태 확인
$status = $paymentProcess->checkProductStatus($_POST['P_NOTI']);
if($status['result'] == false) {
	errorRedirect($message->Three, $_POST['P_NOTI']);
}

//이니시스 소켓통신 준비
$inimx = new INImx;

$inimx->reqtype 		= MOBPAYTYPE;  //결제 타입
$inimx->inipayhome 	= $conf['INICIS_PATH']; //로그 경로
$inimx->status			= $P_STATUS;
$inimx->rmesg1			= $P_RMESG1;
$inimx->tid		= $P_TID;
$inimx->req_url		= $P_REQ_URL;
$inimx->noti		= $P_NOTI;
$inimx->id_merchant = $paymentProcess->MID; //상점 아이디

// 소켓통신전 결제 상태 확인
if($inimx->status =="00") //결제 가능 상태
{
	//이니시스 소켓통신 시작
	$inimx->startAction();

	//결과 파싱
	$inimx->getResult();

	//로그 기록
	$paymentProcess->setErrorLog($inimx, 'debug');

	//오더코드가 일치하는지 확인
	$checkToken = $paymentProcess->checkOrderCode($inimx->m_moid);
	if($checkToken['status'] == false) {
		errorRedirect($message->One);
	}

	//데이터 디비에 저장
	$result = $paymentProcess->receivePaymentResult(
		$inimx->id_merchant,
		$inimx->m_moid,
		$inimx->m_resultprice,
		$inimx->m_resultCode,
		$inimx->m_tid,
		$inimx->m_payMethod
	);

	/*
	 * 결과에 따른 클라이언트 액션 분류
	 */

	//이니시스 결제 성공 및 FCCTV디비 저장에도 성공
	if($result['result'] == true and $inimx->m_resultCode == 00){?>
		<!-- 결제 성공후 주문정보로 리다이렉트 -->
		<script>
			window.opener.location = '/Orders/returnOrderInfo/<?=$inimx->m_moid?>';
			window.close();
		</script>
	<?php
	}
	//이니시스 결제 성공 하지만 FCCTV디비 저장에 실패 또는 오류
	elseif($result['result'] == false and $inimx->m_resultCode == 00){
		$errMsg = NULL; //에러메세지
		$sts = 'n'; //status
		if(isset($result['exception'])){
			$errMsg = $result['exception'];
		}
		//m_tid : 트랜잭션 아이디, m_moid : 오더코드, 메세지, 에러메세지, status
		$paymentProcess->saveCancelPayment($inimx->m_tid, $inimx->m_moid, $result['msg'], $errMsg, $sts);

		if(isset($result['order_code'])){
			errorRedirect($result['msg'], $result['order_code']);
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
}
//소켓통신 전 결제가능 불가 상태
else{
	errorRedirect($message->One);
}
//얼럿
function errorRedirect($msg, $orderCode = null){
	$pay = new afterPayment();
	$pay->setErrorLog($msg, 'debug');
	?>
	<script>
		var msgNum = "<?=isset($msg)?$msg:''?>";
		var orderCode = "<?=isset($orderCode)?$orderCode:''?>";
		window.opener.$("#openerMsg").val(msgNum);
		window.opener.$("#openerOrderCode").val(orderCode);
		window.opener.$("#errorRedirect").submit();
		window.close();
	</script>
<?php exit;}?>