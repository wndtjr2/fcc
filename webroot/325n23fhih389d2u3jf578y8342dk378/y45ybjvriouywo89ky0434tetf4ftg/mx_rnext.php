<script src="/_res/lib/jquery-1.11.2.min.js"></script>
<?php
include("../../../src/Lib/Inicis/INImx.php"); //�̴Ͻý� ����� ���̺귯��
include("afterPayment.php"); //���� Ŭ����
include_once("messages.php");//�޼��� Ŭ����

//Configure�� �����´�.
$conf = require('../../../config/app.php');

//afterpaymentŬ���� ȣ��
$paymentProcess = new afterPayment();
$message = new messages();

//���޹��� POST�����͸� �α׿� �����Ѵ�.
if(empty($_POST)){
	errorRedirect($message->One);
}
$paymentProcess->setErrorLog($_POST, 'debug');

//order code�� ���� �޾Ҵ��� Ȯ���Ѵ�.
if(!isset($_POST['P_NOTI'])) {
	errorRedirect($message->One);
}

//������ ��ū ���� Ȯ��
$tokenExist = $paymentProcess->tokenExist();
if($tokenExist['result'] == false){
	errorRedirect($message->Two);
}

//������ ������ ��ū üũ
$check = $paymentProcess->tokenCheck($_POST['P_NOTI']);
if($check['result'] == false) {
	errorRedirect($message->One);
}

//��ǰ ���� Ȯ��
$status = $paymentProcess->checkProductStatus($_POST['P_NOTI']);
if($status['result'] == false) {
	errorRedirect($message->Three, $_POST['P_NOTI']);
}

//�̴Ͻý� ������� �غ�
$inimx = new INImx;

$inimx->reqtype 		= MOBPAYTYPE;  //���� Ÿ��
$inimx->inipayhome 	= $conf['INICIS_PATH']; //�α� ���
$inimx->status			= $P_STATUS;
$inimx->rmesg1			= $P_RMESG1;
$inimx->tid		= $P_TID;
$inimx->req_url		= $P_REQ_URL;
$inimx->noti		= $P_NOTI;
$inimx->id_merchant = $paymentProcess->MID; //���� ���̵�

// ��������� ���� ���� Ȯ��
if($inimx->status =="00") //���� ���� ����
{
	//�̴Ͻý� ������� ����
	$inimx->startAction();

	//��� �Ľ�
	$inimx->getResult();

	//�α� ���
	$paymentProcess->setErrorLog($inimx, 'debug');

	//�����ڵ尡 ��ġ�ϴ��� Ȯ��
	$checkToken = $paymentProcess->checkOrderCode($inimx->m_moid);
	if($checkToken['status'] == false) {
		errorRedirect($message->One);
	}

	//������ ��� ����
	$result = $paymentProcess->receivePaymentResult(
		$inimx->id_merchant,
		$inimx->m_moid,
		$inimx->m_resultprice,
		$inimx->m_resultCode,
		$inimx->m_tid,
		$inimx->m_payMethod
	);

	/*
	 * ����� ���� Ŭ���̾�Ʈ �׼� �з�
	 */

	//�̴Ͻý� ���� ���� �� FCCTV��� ���忡�� ����
	if($result['result'] == true and $inimx->m_resultCode == 00){?>
		<!-- ���� ������ �ֹ������� �����̷�Ʈ -->
		<script>
			window.opener.location = '/Orders/returnOrderInfo/<?=$inimx->m_moid?>';
			window.close();
		</script>
	<?php
	}
	//�̴Ͻý� ���� ���� ������ FCCTV��� ���忡 ���� �Ǵ� ����
	elseif($result['result'] == false and $inimx->m_resultCode == 00){
		$errMsg = NULL; //�����޼���
		$sts = 'n'; //status
		if(isset($result['exception'])){
			$errMsg = $result['exception'];
		}
		//m_tid : Ʈ����� ���̵�, m_moid : �����ڵ�, �޼���, �����޼���, status
		$paymentProcess->saveCancelPayment($inimx->m_tid, $inimx->m_moid, $result['msg'], $errMsg, $sts);

		if(isset($result['order_code'])){
			errorRedirect($result['msg'], $result['order_code']);
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
}
//������� �� �������� �Ұ� ����
else{
	errorRedirect($message->One);
}
//��
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