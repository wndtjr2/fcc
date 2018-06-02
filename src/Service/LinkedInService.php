<?php
namespace App\Service;

error_reporting(E_STRICT);
/**
 * LinkedIn 서비스 인터페이스
 * User: Eric
 * Date: 15. 6. 24.
 * Time: 오후 1:54
 */

use Cake\Auth;
use Cake\Error\Debugger;
use Cake\Network\Email\Email;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Session;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Interface LinkedInInterface
 * 링크드인 인터 페이스
 * @package App\Service
 */
interface LinkedInInterface {

    /**
     * 로그인
     * @return mixed
     */
    public function login($state);
    /**
     * 사용자 검색
     * @param $id
     * @return mixed
     */
    public function getState();

    /**
     * 토큰 받기
     * @return mixed
     */
    public function getToken($code);

    /**
     * 사용자 정보 받기
     * @param $token
     * @return mixed
     */
    public function getProfile($token);


    public function checkProfile($data);

    public function sendConfirmEmail($data);
    //public function shareLinkedIn();
}

/**
 * 사용자 조회 서비스
 * Class UserService
 * @package App\Service
 */
class LinkedInService implements LinkedInInterface {

    /**
     * @var string
     */
    private $loginApi = LINKEDIN_REDIRECT;

    /**
     * @var \Cake\ORM\Table
     */
    private $Users;

    /**
     * @var \Cake\ORM\Table
     */
    private $UserAccounts;

    private function __construct() {
        /**
         * 테이블 객체 로드
         **/
        $this->Users = TableRegistry::get('Users');
        $this->UserAccounts = TableRegistry::get('UserAccounts');
    }
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new LinkedInService();
        }
        return $inst;
    }

    /**
     * 로그인
     * @return mixed
     */
    public function login($state){
        //state 생성 & 세션에 저장
        $clientId = CLIENT_ID;
        $redirect = $this->loginApi;
        $linkedInApi = 'https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id=' . $clientId . '&redirect_uri=' . $redirect . '&state=' . $state . '&scope=r_basicprofile r_emailaddress w_share';

        return $linkedInApi;
    }

    /**
     * state 생성
     * @return int|mixed
     */
    public function getState(){
        $rand = rand(1000000,9999999);
        $state = hash('ripemd160', (string)$rand);
        return $state;
    }

    /**
     * access_token 가져오기
     * @param $code
     * @return mixed
     */
    public function getToken($code){
        $clientId = CLIENT_ID;
        $clientSecret = CLIENT_SECRET;

        $redirect = $this->loginApi;

        //curl 시작
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.linkedin.com/uas/oauth2/accessToken');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            'grant_type=authorization_code'.
            '&code=' . $code .
            '&redirect_uri=' . $redirect .
            '&client_id=' . $clientId .
            '&client_secret=' . $clientSecret
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        //curl 끝

        $data = json_decode($data);
        if(is_null($data->access_token)){
            throw new BadRequestException('There is no Access Token given from LinkedIn');
        }
        return $data->access_token;
    }

    /**
     * 프로필 가져오기
     * @param $token
     * @return array|mixed
     */
    public function getProfile($token){

        //curl 시작
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,
            'https://api.linkedin.com/v1/people/~:(id,first-name,last-name,email-address,picture-urls::(original),Positions)?oauth2_access_token='.$token.'&format=json'
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        $data = json_decode($data);
        curl_close($ch);
        //curl 끝

        //사용자 정보를 다 받아왔는지 확인
        if(!is_null($data->emailAddress and !is_null($data->firstName) and !is_null($data->lastName))){
            $data = [
                'email' => $data->emailAddress,
                'first_name' => $data->firstName,
                'last_name' => $data->lastName,
                'type' => 'Linkedin'
            ];
            return $data;
        }else{
            throw new BadRequestException('There is no email nor name on LinkedIn account.');
        }
    }

    /**
     * 가입되어있는지 확인
     * @param $data
     */
    public function checkProfile($data){

        $check = $this->UserAccounts->find()
            ->where(['email' => $data['email'], 'signup' => $data['type']])
            ->toArray();
        if(!empty($check)) {
            return 'signed';
        }else{
            return 'notSigned';
        }
    }

    public function saveUser($data){

        if($data['type'] == 'N'){
            $data['email_token'] = $this->generateToken();
        }
        $query = $this->Users->find()->where(['email' => $data['email'], 'type' => $data['type']])->first();
        if(!$query){
            $user = $this->Users->newEntity($data);
            $this->Users->save($user);
            return 'success';
        }else{
            return 'exist';
        }
    }

    //send confirmation email first
    public function sendConfirmEmail($data){
        $data['domain'] = Router::url('/', true);
        $email = new Email();
        $email->transport('brick')
            ->to($data['email'])
            ->from(FCC)
            ->emailFormat('html')
            ->template('verification')
            ->subject('Email Verification')
            ->viewVars(array(
                'data' => $data
            ))
            ->send();
        return 'success';
    }

    //resend confirmation email
    public function resendConfirmEmail($data){

        //get user from table and patch email_token
        $user = $this->Users->find()->where([
            'email' => $data['email'],
            'email_verified' => 'N',
            'type' => $data['type']
        ])->first();
        if($user){
            $token = $this->generateToken();
            $entity = $this->Users->patchEntity($user, [
                'email_token' => $token
            ]);
            $this->Users->save($entity);

            //resend email
            $data['domain'] = Router::url('/', true);
            $data['email_token'] = $token;
            $email = new Email();
            $email->transport('brick')
                ->to($data['email'])
                ->from(FCC)
                ->emailFormat('html')
                ->template('verification')
                ->subject('Email Verification')
                ->viewVars(array(
                    'data' => $data
                ))
                ->send();

            return 'success';
        }


    }

    /**
     * 랜덤 토큰 생성
     * @param int $length
     * @return string
     */
    protected function generateToken($length = 20) {
        $possible = '0123456789abcdefghijklmnopqrstuvwxyz';
        $token = "";
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            if (!stristr($token, $char)) {
                $token .= $char;
                $i++;
            }
        }
        return $token;
    }

//    public function shareLinkedIn(){
//        return $url = 'https://www.linkedin.com/shareArticle?mini=true&url=http://www.example.com/users/join';//TODO-url설정
    //권한 세팅
//        $ch = curl_init();
//        curl_setopt_array($ch, [
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_URL => 'https://api.linkedin.com/v1/people/~',
//            CURLOPT_HTTPHEADER => [
//                'Connection: Keep-Alive',
//                'Authorization: Bearer ' . $token
//                ]
//        ]);
//        curl_exec($ch);
    //권한 세팅 끝
    //linkedIn 바로 코멘트 달기
//        $data_array = [
//            'content' => [
//                'title' => 'test',//TODO-title
//                'description' => 'test',//TODO-description
//                'submitted-url' => "http://example.com/users/join",//TODO-submitted-url
//                'submitted-image-url' => 'https://example.com/logo.png'//TODO-submitted-image-url
//            ],
//            'comment' => 'hi there.',//TODO-comment
//            'visibility' => [
//                'code' => 'connections-only'
//            ]
//        ];
//        $data_string = json_encode($data_array);
//        $api = 'https://api.linkedin.com/v1/people/~/shares?format=json';
//        //curl시작
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $api);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, [
//            "Content-Type: application/json",
//            'x-li-format: json',
//            'Authorization: Bearer ' . $token
//        ]);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $data = curl_exec($ch);
//        $data = json_decode($data);
//        curl_close($ch);
//        //curl 끝
//        Debugger::log("http://$_SERVER[HTTP_HOST]/users/join");
//        Debugger::log($data);
//    }
}
