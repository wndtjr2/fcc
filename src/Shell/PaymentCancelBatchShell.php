<?php
namespace App\Shell;

use App\Service\OrderService;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Error\Debugger;
use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;
require_once(ROOT.DS."src".DS."Lib".DS."Inicis".DS."INILiteLib2.php");
/**
 * Created by PhpStorm.
 * User: Makun
 * Date: 15. 10. 11.
 * Time: 오후 1:11
 */

class PaymentCancelBatchShell extends Shell
{
    private $paymentTest;

    private $inilitetestkey;

    private $inilitekey;

    private $inicisPath;

    private $inimid;

    private $initestmid;

    private $inilite;

    private $McCancelPayment;

    private $ChOrder;

    private $ChPayment;

    private $ChPurchase;

    public function initialize()
    {
        parent::initialize();
        $this->paymentTest = Configure::read('PAYMENT_TEST');
        $this->inilitetestkey = Configure::read('INILITETESTKEY');
        $this->inilitekey = Configure::read('INILITEKEY');
        $this->inicisPath = Configure::read('INICIS_PATH');
        $this->inimid = Configure::read("INIMID");
        $this->initestmid = Configure::read("INITESTMID");
        $this->McCancelPayment = TableRegistry::get("McCancelPayment");
        $this->ChOrder = TableRegistry::get("ChOrder");
        $this->ChPayment = TableRegistry::get("ChPayment");
        $this->ChPurchase = TableRegistry::get("ChPurchase");

        $key = ($this->paymentTest == 'Y')?$this->inilitetestkey:$this->inilitekey;
        $mid = ($this->paymentTest == 'Y')?$this->initestmid:$this->inimid;

        $this->inilite = new \INILite();
        $this->inilite->m_inipayHome = $this->inicisPath;
        $this->inilite->m_key = $key;
        $this->inilite->m_type = "cancel";
        $this->inilite->m_mid = $mid;

    }

    public function main() {
        $McCancelPaymentList = $this->McCancelPayment->find()->where(['status'=>'n'])->toArray();
        $CancelCount =$this->McCancelPayment->find()->where(['status'=>'n'])->count();
        $this->log("주문취소 수 : ".$CancelCount,'debug');

        $this->McCancelPayment->updateall(['status'=>'p'],['status'=>'n']);

        foreach ($McCancelPaymentList as $cancelPayment) {

              try {
                $this->inilite->m_cancelMsg = $cancelPayment['message'];
                $this->inilite->m_tid = $cancelPayment['transaction_id'];
                $this->inilite->startAction();
                if ($this->inilite->m_resultCode != 00) {
                    $this->log("Inicis ErrorCode = ".$this->inilite->m_resultCode." order_code = ".$cancelPayment['order_code']. " ");
                    $this->sendCancelFailEmail("Inicis ErrorCode = ".$this->inilite->m_resultCode." order_code = ".$cancelPayment['order_code']. " ",$cancelPayment['order_code']);
                    throw new InternalErrorException("Inicis ErrorCode = " . $this->inilite->m_resultCode);
                } else {
                    $this->updatePaymentCancel($cancelPayment, 'y');
                    $this->updateOrderStatus($cancelPayment['order_code']);
                }
            } catch (Exception $e) {
                $this->log($e->getMessage());
                $msg = $e->getMessage();
                $this->updatePaymentCancel($cancelPayment, 'e',$msg);
                $this->log($e->getMessage(),'error');
            }
        }

    }

    private function updatePaymentCancel($data,$status,$e = null){
        $patchData = array(
            'status' => $status
        );

        if($e != null){
            $patchData['err_msg'] = $e;
        }

        $update = $this->McCancelPayment->patchEntity($data,$patchData);
        $updateResult = $this->McCancelPayment->save($update);
        if(!$updateResult){
            $this->log($update->errors(), 'error');
        }
    }

    private function updateOrderStatus($orderCode){
        
        $getOrder = $this->ChOrder->find()->where(['order_code'=>$orderCode,"status !="=>'fail'])->first();
        if($getOrder != null) {
            $orderEntity = $this->ChOrder->patchEntity($getOrder, ['status' => 'cancelled']);
            if (!$this->ChOrder->save($orderEntity)) {
                $this->log("Chorder update Fail",'error');
            }
        }

        $getPayment = $this->ChPayment->find()->where(['order_code'=>$orderCode,"status !="=>'fail'])->first();
        if($getPayment!=null) {
            $paymentEntity = $this->ChPayment->patchEntity($getPayment, ['status' => 'cancelled']);
            if (!$this->ChPayment->save($paymentEntity)) {
                $this->log("Chpayment update Fail",'error');
            }
        }

        $getPurchase = $this->ChPurchase->find()->where(['order_code'=>$orderCode,"status !="=>'fail']);
        if($getPurchase!=null) {
            foreach($getPurchase as $pch) {
                $purChaseEntity = $this->ChPurchase->patchEntity($pch, ['status' => 'cancelled']);
                if (!$this->ChPurchase->save($purChaseEntity)) {
                    $this->log("Chpurchase update Fail", 'error');
                }
            }
        }
    }

    private function sendCancelFailEmail($msg, $orderCode){

        $orderService = OrderService::Instance();
        $orderService->sendCancelFailEmail($msg, $orderCode);
    }

}