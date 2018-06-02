<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 7/3/15
 * Time: 1:25 PM
 */

namespace App\Controller;


use App\Service\EncryptService;
use Cake\Error\Debugger;
use Cake\Network\Email\Email;
use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Contacts Controller
 *
 */
class ContactsController extends AppController{
    /**
     * @var \App\Model\Table\CsQuestionsTable
     */
    private $CsQuestions;

    /**
     * @var \App\Model\Table\CsQuestionsTable
     */
    private $CsTeaserQuestions;

    //이메일 리스트
    private $English = 'help@fcctvhq.com';
    private $Korean = 'help@fcctvhq.com';
    private $Help = "help@fcctvhq.com";


    public function initialize() {
        parent::initialize();

        $this->CsQuestions = TableRegistry::get('CsQuestions');

        // 비로그인 접근 가능
        $this->Auth->allow(['add', 'complete','teaser','teaserComplete']);
    }

    public function isAuthorized() {
        return true;
    }

    /**
     * Contact us 문의 작성
     */
    public function add() {
        $this->set('contact', 'contact');
        $data = $this->request->data();
        if ($this->request->is('post')) {
//            switch ($data['language']){
//                case 'E':
//                    $language = $this->English;
//                    break;
//                case 'ko':
                    $language = $this->Korean;
//                    break;
//                case 'C':
//                    $language = $this->Chinese;
//                    break;
//                case 'J':
//                    $language = $this->Japanese;
//                    break;
//                case 'F':
//                    $language = $this->French;
//                    break;
//                case 'R':
//                    $language = $this->Russian;
//                    break;
//                case 'S':
//                    $language = $this->Spanish;
//                    break;
//            }

            //디비에 넣기
            $this->__contactUs($data, $language);

            // 완료 화면
            $this->request->session()->write('contactEmail',$data['email']);
            $this->redirect('/contacts/complete');
        }
    }
    /**
     * 작성 완료
     */
    public function complete(){
        $session = $this->request->session();
        $email = $session->read()['contactEmail'];
        $this->set('email', $email);
    }

    /**
     * CsQuestions INSERT
     *
     * @param $data
     * @param $rep
     * @return bool
     * @throws InternalErrorException
     */
    private function __contactUs($data, $rep){

        $question = $this->CsQuestions->newEntity([
            'prefix_domain' => 'fcctv'
            ,'from_email' => $data['email']
            ,'email' => $rep
            ,'subject' => $data['subject']
            ,'message' => $data['message']
            ,'users_id' => 0
            ,'is_replied' => 'n'
            ,'trash' => 'n'
        ]);

        if ($this->CsQuestions->save($question)){
            Debugger::log($data);
            $this->sendConfirmEmail($data);
            return true;
        } else {
            Debugger::log($question);
            throw new InternalErrorException();
        }
    }


    /** 확인 이메일 전송 */
    private function sendConfirmEmail($data){
        $data['domain'] = Router::url('/', true);
        $email = new Email();
        try{
            $email->transport('brick')
                ->to($this->Help)
                ->from($data['email'])
                ->emailFormat('html')
                ->subject($data['subject'])
                ->viewVars(array(
                    'data' => $data
                ))
                ->template('contactus');
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

}