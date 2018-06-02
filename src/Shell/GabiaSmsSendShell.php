<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Shell;
require_once(ROOT.DS."src".DS."Lib".DS."gabiaSmsApi".DS."api.class.php");

use Aura\Intl\Exception;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Log\Log;
use Psy\Shell as PsyShell;
use App\Service\SmsService;
use gabiaSmsApi;
use Cake\Error\Debugger;
/**
 * Simple console wrapper around Psy\Shell.
 */
class GabiaSmsSendShell extends Shell
{
    private $SmsService;

    public function initialize(){
        $this->SmsService = SmsService::Instance();
    }
    /**
     * Start the shell and interactive console.
     *
     * @return int|void
     */
    public function main()
    {
        $sms = $this->SmsService->smsList();
        $sendId= [];
        $cnt = count($sms);
        $this->log("Send Message count ".$cnt,'debug');
        if($cnt>0){
            $refKey = "FCCTV".time();
            $api = new gabiaSmsApi('brickcommerce','6caf388099c1c8f8a782dfa57534b1dc');

            foreach($sms as $key => $info){
                try{
                    //$sendId[]=$info->id;
                    $r = $api->sms_send($info->recv_phone_number, "070-5015-6383",$info->message,$refKey);

                    if ($r == gabiaSmsApi::$RESULT_OK)
                    {
                        $this->log("Send Message ".$info->id." : number : ".$info->recv_phone_number. " : " . $api->getResultMessage() . "이전 : " . $api->getBefore() . " : 이후 : " . $api->getAfter(),'debug');
                        $this->SmsService->smsSendUpdate($info->id);
                    }

                    //$this->log("Send Message ".$info->id." : number : ".$info->recv_phone_number,'debug');
                }catch (Exception $e){
                    $this->log($e->getMessage(),'error');
                }

            }

            $result = $api->get_status_by_ref($refKey);
            $this->log("CODE : ".$result["CODE"]."::::::MESG : ".$result["MESG"],'debug');

        }
    }
}
