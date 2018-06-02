<?php
namespace App\Controller;

use App\Service\ChallengeService;
use App\Service\MarketService;
use App\Service\UserService;
use App\Util\ImageUtil;
use App\Util\RegistrationNumberUtil;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Filesystem\File;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use App\Service\EncryptService;
use App\Util\CommonUtil;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * @var \App\Model\Table\ChallengeEntryTable
     */
    private $ChallengeEntry;
    /**
     * @var \App\Service\ChallengeService
     */
    private $ChallengeService;
    /**
     * @var \App\Service\UserService
     */
    private $UserService;

    /**
     * @var MarketService
     */
    private $MarketService;

    /**
     * @var \App\Model\Table\UserAccountsTable
     */
    private $UserAccounts;

    public function initialize(){
        parent::initialize();
        $this->UserService = UserService::Instance();
        $this->UserAccounts = TableRegistry::get('UserAccounts');
        $this->ChallengeEntry = TableRegistry::get("ChallengeEntry");
        $this->ChallengeService = ChallengeService::Instance();

        $this->Auth->allow([
            'verifyEmail',
            'checkVerificationEmail',
            'expiredToken',
            'resendVerificationEmail',
            'noActivate',
            'sendVerificationEmailByAdmin',
            'insertPersonalInfo',
            'profileImageUpload'
        ]);
    }

    public function isAuthorized($user){
        /*
        $action = $this->request->action;

        if(in_array($action, [
            'edit',
            'delete',
            'profile',
            'addBasicInfo',
            'addBasicInfo2',
            'passwordAgain',
            'forgotPassword'
        ])){
            $userAccountId = (int)$this->request->params['pass'][0];
            if($user['user_accounts_id'] === $userAccountId){
                return true;
            }else{
                return false;
            }
        }
        */
        return true;
    }

    /**
     * 사용자 정보 중 profile EDIT
     */
    public function edit()
    {
        $usersId = $this->Auth->user('id');
        $user = $this->UserService->getUserByUsersId($usersId);
//        $codeCountry = $this->UserService->selectCodeCountry();
//        $codeLanguage = $this->UserService->selectCodeLanguage();

//        $this->set(compact('user', 'codeCountry', 'codeLanguage'));
        $this->set('user',$user);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->data;

            $conn = ConnectionManager::get('default');
            $conn->begin();

            try {
                $this->UserService->updateUser($usersId, $data);
                //$this->MarketService->insertChallengeEntry($usersId, SUBJECT_ID);   // entry가 없는 경우 public으로 insert 됨
                $conn->commit();
                $session = $this->request->session();
//                $session->write('Auth.User.first_name', $data['first_name']);
//                $session->write('Auth.User.last_name', $data['last_name']);
                $session->write('Auth.User.nickname',$data['nickname']);
                return $this->redirect('/users/edit');
            } catch (Exception $e){
                $conn->rollback();
                Debugger::log($e->getMessage(),'error');
                throw new InternalErrorException($e->getMessage());
            }
        }

        // html layout 회색
        $this->set('idColorGray', true);

    }


    /**
     * 사용자 정보 중 Account EDIT
     * @return \Cake\Network\Response|void
     */
    public function editAccount(){
        $data = $this->request->data;
        $current = $data['current_password'];
        $new = $data['new_password'];
        $confirm = $data['confirm_password'];

        $data = $this->request->data;
        $user = $this->Auth->user();
        $return = $this->UserService->passwordValidateCheck($current, $new, $confirm);

        $userAccounts = $this->UserService->getUserAccountsById($user['user_accounts_id']);
        $hasher = new DefaultPasswordHasher;
        $isCorrect = $hasher->check($data['current_password'], $userAccounts['password']);
        if($isCorrect){
            if($return){
                $rtn = $this->UserService->savePassword($user['user_accounts_id'], $new);
                if($rtn){
                    return $this->redirect(['controller' => 'Users', 'action' => 'edit?validate=complete']);
                }else{
                    return $this->redirect(['controller' => 'Users', 'action' => 'edit?validate=notSaved']);
                }
            }else{
                return $this->redirect(['controller' => 'Users', 'action' => 'edit?validate=incorrect']);
            }
        }else{
            return $this->redirect(['controller' => 'Users', 'action' => 'edit?account=incorrect']);
        }
    }

    /**
     * 사용자 정보 중 Site 수정
     * @return \Cake\Network\Response|void
     */
    public function editSite(){
        $site = $this->request->data['site'];
        $auth = $this->Auth->user();
        $user = $this->Users->get($auth['id']);
        $users = $this->Users->patchEntity($user, [
            'website_url' => $site
        ]);
        if($this->Users->save($users)){
            return $this->redirect(['controller' => 'Users', 'action' => 'edit?other=success']);
        }else{
            Debugger::log($users->errors(), 'error');
            throw new InternalErrorException();
        }
    }



    /**
     * 계정 삭제 요청
     */
    public function delete()
    {
        $this->request->allowMethod(['post', 'delete']);
        $auth = $this->Auth->user();

        if (is_null($auth)) {
            $this->request->session()->write('alertMsg', __("You have been automatically logged out. Please sign in again."));
            return $this->redirect('/');
        }

        $connection = ConnectionManager::get('default');
        $connection->begin();

        try{
            $targetUserAccount = $this->UserAccounts->get($auth['user_accounts_id']);
            $this->UserAccounts->patchEntity($targetUserAccount, [
                'status' => 'boltrequest'
            ]);
            if ( !$this->UserAccounts->save($targetUserAccount)) {
                Debugger::log($targetUserAccount->errors(), 'error');
                throw new InternalErrorException();
            }
            $targetUser = $this->Users->get($auth['id']);
            $this->Users->patchEntity($targetUser, [
                'status' => 'boltrequest'
            ]);
            if ( !$this->Users->save($targetUser)) {
                Debugger::log($targetUser->errors(), 'error');
                throw new InternalErrorException();
            }
            $this->UserService->sendBoltRequestEmail($targetUserAccount->emailDecrypt, $targetUser->first_name);
            $this->Auth->logout();

            $connection->commit();

        }catch (Exception $e){
            $connection->rollback();
            Debugger::log($e->getMessage(), 'error');
            throw new InternalErrorException();
        }
        // ajax 요청 방식으로 변경
        $this->autoRender = false;
        echo 'ok';
    }

    /**
     * 최초 가입
     * @return \Cake\Network\Response|void
     */
    /*/
    public function firstTimeJoin(){
        $this->set('account', 'account');
        if($this->request->is('POST')){
            $data = $this->request->data;
            $data['token'] = $this->UserService->generateToken();
            $data['status'] = 2; //Activate

            //get language from header
//            $acceptLang = $this->request->acceptLanguage();
//            $httpLang = $this->UserService->getLangFromHeader($acceptLang);
//            $data['code_language_id'] = $httpLang;

            $user = $this->UserService->findUser($data['email'], 'normal');

            //user가 없는 상태
            if(is_null($user)){
                if ($users = $this->UserService->saveUser($data)) {
                    $this->UserService->sendEmail($data['email'], $data['first_name'], $data['last_name']);
                    $this->logUserIn($users->user_accounts_id);
                    return $this->redirect('/');
                }else{
                    Debugger::log($data, 'error');
                    throw new InternalErrorException();
                }


                //$return = $this->UserService->sendConfirmEmail($data);


                if($return == 'success'){
                    $session->write('user.verify', $data);
                    return $this->redirect(['controller' =>'Users', 'action' => 'checkVerificationEmail']);
                }else{
                    $message = 'Internal Error!! We cannot send you an email. please try again';
                    Debugger::log($message, 'error');
                    throw new InternalErrorException($message);
                }

            } else {  //유저가 있는상태
                $message = 'The user information already exist';
                Debugger::log($message, 'error');
                return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
            }
        }
    }
    /*/

    /**
     * 확인 이메일 전송
     * @return \Cake\Network\Response|void
     */
    public function checkVerificationEmail(){
        $this->set('idColorGray', 'idColorGray');
        $session = $this->request->session()->read('user.verify');
        if(is_null($session) or empty($session)){
            $error = [
                'message' => 'Users\'s session is empty.',
                'HTTP REFERER' => $_SERVER['HTTP_REFERER'],
                'HTTP REQUEST URI' => $_SERVER['REQUEST_URI'],
                'SESSION DATA' => $this->request->session()->read()
            ];
            Debugger::log($error, 'error');
            return $this->redirect(['controller' => 'Auth', 'action' => 'join']);
        }
        $name = $session['first_name'] . ' ' . $session['last_name'];
        $email = $session['email'];
        $this->set(compact('name', 'email'));

        //resend verification email
//        if($this->request->is('POST')){
//            Debugger::log(2);
//            $this->resendVerificationEmail();
//            $this->autoRender = true;
//        }
    }

    /**
     * 확인 이메일 재전송
     */
    public function resendVerificationEmail(){
        $session = $this->request->session();
        $verify = $session->read('user.verify');
        //session 이 없을시
        if(is_null($verify)){
            return $this->redirect(['controller' => 'Auth', 'action' => 'join']);
        }
        //세션있을때
        if($this->request->is('POST')) {
            $userAccount = $this->UserService->getUserAccountsByEmail($verify['email']);
            if (isset($userAccount)) {
                $user = $this->UserService->getUserByUserAccountId($userAccount->id);
                $name = $user->first_name . ' ' . $user->last_name;
                $email = $verify['email'];
                $this->set(compact('name', 'email'));

                $return = $this->UserService->sendVerificationEmail($verify);

                //이메일 전송이 성공했을때
                if ($return == 'success') {
                    //$this->redirect(['controller' => 'Users', 'action' => '']);
                } //이미 베리파이했을때
                elseif ($return == 'already') {
                    return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
                } //ERROR
                else {
                    Debugger::log('resendVerificationEmail 에러', 'error');
                    throw new InternalErrorException();
                }
            }

        }
    }

    /**
     * 이메일 주소 인증
     */
    public function verifyEmail(){
        $this->autoRender = false;
        $token = $this->request->params['token'];
        $return = $this->UserService->checkEmailVerification($token);

        //token 확인 오류시
        if($return == 'error'){
            return $this->redirect(['controller' => 'Users', 'action' => 'expiredToken']);
        }
        elseif($return == 'noToken'){
            return $this->redirect(['controller' => 'Users', 'action' => 'expiredToken']);
        }
        //사용자가 없을시
        elseif(is_null($return)){
            return $this->redirect(['controller' => 'Users', 'action' => 'expiredToken']);
        }
        //이미 가입한경우
        elseif($return->status == 'already'){
//            $user = $this->UserService->findSimpleUserById($return->id);
//            $this->Auth->setUser((array)json_decode(json_encode($user), true));
            $this->logUserIn($return->id);
            return $this->redirect('/');
        }
        //사용자 존재시
        else{
            $users = $this->UserService->getUserByUserAccountId($return->id);
            if($users){
                // 첫 가입자 환영 이메일 전송
                $this->UserService->sendEmail($return->emailDecrypt, $users->first_name, $users->last_name);
                $this->logUserIn($return->id);
                return $this->redirect('/');
            }else{
                throw new MethodNotAllowedException('=============== 사용자 이메일 조회결과 없음');
            }
        }
    }

    /**
     * 토큰 만료 페이지
     */
    public function expiredToken(){
        $this->set('idColorGray', 'idColorGray');
        //no action
    }

    /**
     * 이메일 인증 필요함 페이지
     */
    public function noActivate(){
        $this->set('idColorGray', 'idColorGray');
        if($this->request->is('POST')){
            $this->resendVerificationEmail();
            $this->autoRender = true;
        }
    }

    /**
     * 사용자 프로필화면
     */
    public function profile(){
        $this->set('idColorGray', 'idColorGray');
        $user = $this->Auth->user();

        $users = $this->UserService->getUserByUsersId($user['id']);

        if($users->code_language_id == 0){
            $language = '';
        }else{
            $codeLangauge = TableRegistry::get('CodeLanguage');
            $language = $codeLangauge->get($users->code_language_id)['language'];
        }
        $this->set('language', $language);

        $this->set('users', $users);
    }

    public function account(){
        $this->set('idColorGray', 'idColorGray');
        if($this->request->is('POST')){
            $data = $this->request->data;
            $current = $data['current_password'];
            $new = $data['new_password'];
            $confirm = $data['confirm_password'];

            $user = $this->Auth->user();
            $return = $this->UserService->passwordValidateCheck($current, $new, $confirm);

            $userAccounts = $this->UserService->getUserAccountsById($user['user_accounts_id']);
            $hasher = new DefaultPasswordHasher;
            $isCorrect = $hasher->check($data['current_password'], $userAccounts['password']);
            if($isCorrect){
                if($return){
                    $rtn = $this->UserService->savePassword($user['user_accounts_id'], $new);
                    if($rtn){
                        echo "complete";
                        exit();
                    }else{
                        echo "notSaved";
                        exit();
                    }
                }else{
                    echo "validateError";
                    exit();
                }
            }else{
                echo "incorrect";
                exit();
            }
        }
    }

    /**
     * 기본 정보 입력
     * @param $target
     */
    public function addBasicInfo($target){
        if($this->ChallengeService->selectRegistPeriodSubject($target)){

        }else{
            if($this->ChallengeService->selectEvaluatePeriodSubject($target)){

            }else{
                $this->set('NoSeason', 'NoSeason');
            }
        }

        $usersId = $this->Auth->user('id');
        $user = $this->Users->get($usersId);
        $codeLanguage = $this->Users->CodeLanguage->find('list', [
            'limit' => 50,
            'keyField' => 'id',
            'valueField' => 'language'
        ])->toArray();;
        $codeCountry = $this->Users->CodeCountry->find('list', [
            'limit' => 250,
            'keyField' => 'id',
            'valueField' => 'country_name'
        ])->toArray();
        $this->set(compact('user','codeLanguage', 'codeCountry','target'));
    }

    /**
     * 기본 정보 입력 프로세스
     * @param $target
     */
    public function addBasicInfoSave($target){

        //참여기간 아닐경우 서밋버튼클릭시 얼럿으로
        $currSubject = $this->ChallengeService->selectRegistPeriodSubject($target);
        $type ='';
        if($currSubject){
            $type = 'register';
            $this->set('subject', $currSubject->target);
        }else{
            $currSubject = $this->ChallengeService->selectEvaluatePeriodSubject($target);
            if($currSubject){
                $type = 'eval';
                $this->set('subject', $currSubject->target);
            }else{
                $this->set('NoSeason', 'NoSeason');
            }
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->data;
            $usersId = $this->Auth->user('id');
            $user = $this->Users->get($usersId);

            $data['birthday'] = $data['year'].'-'.$data['month'].'-'.$data['day'];

            $codeCountry = $this->Users->CodeCountry->find('list', [
                'limit' => 250,
                'keyField' => 'id',
                'valueField' => 'country_name'
            ])->toArray();
            $data['country'] = $codeCountry[$data['code_country_id']];
            $users = $this->Users->patchEntity($user, $data);

            $conn = ConnectionManager::get('default');
            $conn->begin();
            try{

                if ( !$this->Users->save($users)){  // User 기본 정보 UPDATE
                    Debugger::log($users->errors(), 'error');
                    Debugger::log('========== $this->request->data : ', 'error');
                    Debugger::log($this->request->data, 'error');
                    Debugger::log('========== $data : ', 'error');
                    Debugger::log($data, 'error');
                    Debugger::log('========== $users : ', 'error');
                    Debugger::log($users, 'error');
                    throw new InternalErrorException();
                }

                /**
                 * 평가 가능 기간이라면 voter 추가
                 */
                if($type == 'eval'){
                    $voter = $this->ChallengeService->selectChallengeVoter($usersId, $currSubject->id);
                    if ($voter == null) {
                        $this->ChallengeService->saveChallengeVoter($usersId, $currSubject->id);
                    }
                }

                $currChallengeEntry = $this->ChallengeService->selectChallengeEntry($usersId,$currSubject->id);
                // 챌린지 등록 가능기간이고, 해당 유저의 챌린지 엔트리가 없는 경우
                if ($currSubject != null && $currChallengeEntry == null) {
                    // public으로 entry 등록
                    $this->ChallengeService->insertChallengeEntryPublic($usersId,$currSubject->id);
                    /**
                     * 이번 회차의 designer post가 이미 있다면 (앱에서 메일 링크로 접근해서 개인정보 입력)
                     * => model or design entry save
                     */
                    if($type!="eval") {
                        $post = $this->ChallengeService->selectPostByUserId($usersId, $currSubject['id']);
                        if ($post) {
                            $this->ChallengeService->saveChallengeEntry($usersId, $target);
                            // 등록 번호 생성
                            $regNum = RegistrationNumberUtil::randUniqid($post['id']);
                            return $this->redirect('/fcc2015/complete?regNum=' . $regNum);
                        }
                    }
                }
                $conn->commit();

                $session = $this->request->session();
                $session->write('Auth.User.first_name', $data['first_name']);
                $session->write('Auth.User.last_name', $data['last_name']);

            } catch (Exception $e){
                $conn->rollback();
                Debugger::log($e->getMessage(),'error');
                throw new InternalErrorException();
            }

        }
        if($type=='eval'){
            if($target=='model'){
                return $this->redirect('/mcc2015/eval');
            }else if($target=='designer'){
                return $this->redirect('/fcc2015/eval');
            }
        }
    }

    /**
     * 개인정보 수정 화면 암호 재확인
     */
    public function passwordAgain(){
        $this->set('idColorGray', 'idColorGray');
        $userAccountId = $this->Auth->user('user_accounts_id');
        $userAccount = $this->UserService->getUserAccountsById($userAccountId);
        $this->set(compact('userAccount'));
        if($this->request->is('POST')){
            $data = $this->request->data;
            if($data['signup'] == 'normal'){
                $userAccounts = $this->Auth->identify();
            }else{
                $hasher = new DefaultPasswordHasher;
                $isMatch = $hasher->check($data['password'], $userAccount->password);
                //$isMatch = password_verify($data['password'], $userAccount->password);
                if($isMatch){
                    $userAccounts = [
                        'result' => 'success'
                    ];
                }else{
                    $userAccounts = [
                        'result' => 'fail',
                        'code' => 'password'
                    ];
                }
            }
            if($userAccounts['result'] == 'fail'){
                //비번이 틀렸을시
                if(isset($userAccounts['code']) and ($userAccounts['code'] == 'password')){
                    $this->set('password', 'password');
                }
                else{
                    return $this->redirect('/');
                }
            }
            //가입자 확인 성공시
            elseif($userAccounts['result'] == 'success') {
                $this->logUserIn($userAccountId);
                return $this->redirect(['controller' => 'Users', 'action' => 'edit']);
            }else{
                throw new UnauthorizedException('======== 암호 재설정 중 예외');
            }
        }
    }

    /**
     * 비밀번호 잊었을시
     */
    public function forgotPassword(){
        $this->set('idColorGray', 'idColorGray');
        $userAccountId = $this->Auth->user('user_accounts_id');
        $user = $this->UserService->getUserAccountsById($userAccountId);
        $email = $user->emailDecrypt;
        $this->set(compact('email', 'userAccountId'));
        if($this->request->is('POST')){
            $email = $this->request->data['email'];
            $userAccount = $this->UserService->getUserAccountsByEmailWithActivate($email);
            $encryptedEmail = $userAccount->email;
            $token = $this->UserService->generateToken();

            $return = $this->UserService->sendEmailToResetPassword($email, $token);

            if($return == 'success'){
                $currUserAccounts = $this->UserAccounts->patchEntity($userAccount, [
                    'authentication_code' => $token]);
                if($this->UserAccounts->save($userAccount)){
                    $this->request->session()->write('user.email', $encryptedEmail);
                    return $this->redirect(['controller' => 'Auth', 'action' => 'resendPassword']);
                }else {
                    Debugger::log($currUserAccounts->errors(), 'error');
                    throw new InternalErrorException();
                }
            }
        }
    }

    /**
     * 확인 이메일 재전송 API by Administrator
     */

    public function sendVerificationEmailByAdmin(){
        $this->autoRender = false;
        if($this->request->is('POST')){
            $data = $this->request->data;

            //전달 받은 데이터가 없을때
            if(is_null($data) or empty($data)){
                $status = [
                    'code' => 400,
                    'message' => 'no email was given',
                    'status' => 'error'
                ];
                echo json_encode($status);
                exit;
            }
            $encryptService = EncryptService::Instance();
            $data['email'] = $encryptService->decrypt(trim($data['email']));

            //전달받은 이메일이 정상적이지 않을때
            if(!$data['email']){
                $status = [
                    'code' => 400,
                    'message' => 'email address is invalid',
                    'status' => 'error'
                ];
                echo json_encode($status);
                exit;
            }
            $userAccount = $this->UserService->getUserAccountsByEmail($data['email']);

            //어카운트를 찾을수 없을때
            if(is_null($userAccount)){
                $status = [
                    'code' => 400,
                    'message' => 'no user found',
                    'status' => 'error'
                ];
                echo json_encode($status);
                exit;
            }

            $user = $this->UserService->getUserByUserAccountId($userAccount->id);
            $data['first_name'] = $user->first_name;
            $data['last_name'] = $user->last_name;

            $return = $this->UserService->sendVerificationEmail($data);
            //이메일 전송이 성공했을때
            if($return == 'success'){
                $status = [
                    'code' => 200,
                    'status' => 'success'
                ];
                echo json_encode($status);
                //exit;
            }
            //이미 인증했을때
            elseif($return == 'already'){
                $status = [
                    'code' => 200,
                    'message' => 'the user email has been already verified',
                    'status' => 'already'
                ];
                echo json_encode($status);
                //exit;
            }
            //ERROR
            else{
                $status = [
                    'code' => 500,
                    'message' => 'Internal Server Error',
                    'status' => 'error'
                ];
                echo json_encode($status);
                //exit;
            }

        }
    }
    public function logUserIn($userAccountId = null){
        //set user login
        $user = $this->UserService->findSimpleUserById($userAccountId);
        $this->Auth->setUser((array)json_decode(json_encode($user), true));
        if($this->Auth->user()){

            //로그인 쿠키 세팅
//            $this->Cookie->configKey('cross-login', [
//                'domain' => '.staging.brickand.com'
//            ]);
//            $this->Cookie->write('cross-login', [
//                'user_accounts_id' => EncryptService::Instance()->encrypt($this->Auth->user('user_accounts_id')),
//                'email' => $this->Auth->user('user_account.email')
//            ]);

            $this->UserService->saveLoginLog($userAccountId);
        }else{
            Debugger::log('UserAccountId : ' . $userAccountId . ' has failed to log in', 'error');
        }
    }


    /**
     * Mobile App에서 작품 등록 후 메일 링크 url
     *
     * @param $authCode
     */
    public function insertPersonalInfo($authCode){
        $this->autoRender = false;
        $userAccount = $this->UserService->selectAuthCodeUserAccount($authCode);
        if ($userAccount != null) {
            $this->logUserIn($userAccount['id']);
            return $this->redirect('/users/addBasicInfo/designer');
        } else {
            return $this->redirect('/users/expiredToken');
        }

    }

    public function profileImageUpload(){
        $this->autoRender = false;

        if($this->Auth->user()) {
            $file = $this->request->data('file_data');
            $data['result'] = 'ERR';
            if ($file['error'] == 0) {
                $tempFile = new File($file['tmp_name']);
                if ($tempFile->exists()) {
                    $name = new File($file['name']);
                    $filename = ImageUtil::createTempImage($tempFile, $name);

                    $image = null;
                    $image = ImageUtil::resizeImagesSet($filename, 'profile');
                    $data['image_path'] = $image['original'];
                    $data['image_name'] = $file['name'];
                    $data['image_extension'] = $name->ext();
                    $data['image_uri'] = FILE_URI . $image['original'];

                    $usersId = $this->Auth->user('id');
                    $user = $this->Users->get($usersId);
                    $users = $this->Users->patchEntity($user, $data);
                    if ($this->Users->save($users)) {
                        $session = $this->request->session();
                        $orgImagePath = $this->Auth->user('image_path');
                        $session->write('Auth.User.image_path', $data['image_path']);
                        ImageUtil::deleteImage($orgImagePath);
                        $data['result'] = 'success';
                    }else{
                        $data['result'] = 'saveERR';
                    }
                }
            }
        }else{
            $data['result']='allowERR';
        }
        echo json_encode($data);
        exit;
    }


    /**
     * 프로필 이미지 삭제
     */
    public function profileImageDelete() {
        $this->autoRender = false;
        if($this->Auth->user()) {
            $usersId = $this->Auth->user('id');
            $user = $this->Users->get($usersId);
            $user['image_path'] = '';
            if ($this->Users->save($user)) {
                $session = $this->request->session();
                $orgImagePath = $this->Auth->user('image_path');
                $session->write('Auth.User.image_path', '');
                ImageUtil::deleteImage($orgImagePath);
                $data['result'] = 'success';
            }else{
                $data['result'] = 'saveERR';
            }
        }else{
            $data['result']='allowERR';
        }
        echo json_encode($data);
        exit;
    }

    public function checkIsMobile(){
        $this->autoRender = false;
        $return = CommonUtil::checkIsMobile($_SERVER['HTTP_USER_AGENT']);
        echo $return;
    }


}
