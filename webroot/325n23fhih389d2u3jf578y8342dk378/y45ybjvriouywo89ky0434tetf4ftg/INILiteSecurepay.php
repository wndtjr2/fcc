<script src="/_res/lib/jquery-1.11.2.min.js"></script>
<form action="/orders/returnOrderError" method="POST" id="errorRedirect" accept-charset="UTF-8">
	<input type="hidden" name="msg" value="">
	<input type="hidden" name="order_code" value="">
</form>
<?php
include_once("../../../src/Lib/Inicis/INILiteLib.php"); //�̴Ͻý� ����Ʈ ���̺귯��
include_once("afterPayment.php"); //���� Ŭ����
include_once("messages.php");//�޼��� Ŭ����

//Configure�� �����´�.
$conf = require('../../../config/app.php');

//afterpaymentŬ���� ȣ��
$paymentProcess = new afterPayment();
$message = new messages();

//���޹��� POST�����͸� �α׿� �����Ѵ�.
$paymentProcess->setErrorLog($_POST, 'debug');

//order code�� ���� �޾Ҵ��� Ȯ���Ѵ�.
if(!isset($_POST['oid'])) {
	errorRedirect($message->One);
}

//������ ��ū ���� Ȯ��
$tokenExist = $paymentProcess->tokenExist();
if($tokenExist['result'] == false){
	errorRedirect($message->Two);
}

//������ ������ ��ū üũ
$check = $paymentProcess->tokenCheck($_POST['oid']);
if($check['result'] == false) {
	errorRedirect($message->One);
}

//��ǰ ���� Ȯ��
$status = $paymentProcess->checkProductStatus($_POST['oid']);
if($status['result'] == false) {
	errorRedirect($message->Three, $_POST['oid']);
}

//�̴Ͻý� ���� ��� �غ�
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

//�̴Ͻý� ������� ����
$inipay->startAction();

//�α� ���
$paymentProcess->setErrorLog($inipay, 'debug');

//�����ڵ尡 ��ġ�ϴ��� Ȯ��
$checkToken = $paymentProcess->checkOrderCode($inipay->m_oid);
if($checkToken['status'] == false){
	errorRedirect($message->One);
}

//������ ��� ����
$result = $paymentProcess->receivePaymentResult(
	$inipay->m_qs['mid'],
	$inipay->m_oid,
	$inipay->m_resultprice,
	$inipay->m_resultCode,
	$inipay->m_tid,
	$inipay->m_qs['paymethod']
);

/*
 * ����� ���� Ŭ���̾�Ʈ �׼� �з�
 */
//�̴Ͻý� ���� ���� �� FCCTV��� ���忡�� ����
if($inipay->m_resultCode == 00 and $result['result'] == true){
	redirect($inipay->m_oid);
}
//�̴Ͻý� ���� ���� ������ FCCTV��� ���忡 ���� �Ǵ� ����
elseif($inipay->m_resultCode == 00 and $result['result'] == false){
	$errMsg = NULL; //�����޼���
	$sts = 'n'; //status
	if(isset($result['exception'])){
		$errMsg = $result['exception'];
	}
	//m_tid : Ʈ����� ���̵�, m_oid : �����ڵ�, �޼���, �����޼���, status
	$paymentProcess->saveCancelPayment($inipay->m_tid, $inipay->m_oid, $result['msg'], $errMsg, $sts);
	if(isset($result['order_code'])){
		errorRedirect($result['msg'], $inipay->m_oid);
	}else{
		//���� �����̷�Ʈ
		errorRedirect($result['msg']);
	}
}
//�̴Ͻý� ���� ����
else{
	//���� �����̷�Ʈ
	errorRedirect($message->One);
}

//��
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
