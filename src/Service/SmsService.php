<?php
namespace App\Service;

/**
 * Sms Service
 */
use Cake\Error\Debugger;
use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;

class SmsService {
    /**
     * @var \Cake\ORM\Table
     */
    private $McSendSms;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChCodeGen;

    private $Message = [
        'buy' => '{shopName} {userName}님의 주문번호 : {orderCode}, 상품명 : {productName}의 입금이 확인되었습니다',
        'soldOut' => '{orderCode}의 남은 수량이 {stockCount}남았습니다.',
        'cancel' => '주문번호 {orderCode} 에 대해 취소완료 되었습니다.',
        'refund' => '{productName} 상품에 대해 환불이 되었습니다.',
        'delivery' => '{shopName} {userName}님 주문은 {shippingCompany}/{trackingNumber}로 발송되었습니다'
    ];

    private $Prefix = '[FCC TV]';

    private function __construct() {
        $this->McSendSms = TableRegistry::get("McSendSms");
        $this->ChCodeGen = TableRegistry::get("ChCodeGen");
    }

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new SmsService();
        }
        return $inst;
    }
    /*
     * 발송할 sms 를 발송목록에 추가한다.
     */
    public function insertSmsMessage($type,$recvPhoneNumber,$data){
        $message = '';
        $message = $this->Message[$type];
        foreach($data as $k => $v){
            $message = str_replace($k,$v,$message);
        }

        $entityMcSendSms = $this->McSendSms->newEntity([
            'recv_phone_number' => $recvPhoneNumber,
            'message' => $this->Prefix.$message,
            'send_yn' => 'n'
        ]);
        $rtn = $this->McSendSms->save($entityMcSendSms);
        return $rtn;

    }
    /*
     * 마지막 발송 번호 이후의 sms 메세지를 조회한다.
     */
    public function smsList(){
        $query = $this->ChCodeGen->find()->where(['cdg_kind' => 'SMS'])->first();
        $lastId = $query->last_num;
        $sms = $this->McSendSms->find()->where(['id > ' => $lastId,'send_yn'=>'n'])->order(['id'=>'asc'])->toArray();
        if(count($sms)>0) {
            $query->last_num = $sms[count($sms) - 1]->id;
            $this->ChCodeGen->save($query);
        }
        return $sms;
    }
    /*
     * 발송 완료된 sms 의 상태를 변경한다
     */
    public function smsSendUpdate($sendId){

        //return $this->McSendSms->save($patch);
        return $this->McSendSms->updateAll(['send_yn'=>'y'],['id in '=>$sendId]);
    }
}