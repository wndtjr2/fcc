<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2/10/15
 * Time: 9:43 AM
 */

namespace App\Controller;

use App\Service\EncryptService;
use App\Service\FacebookService;
use App\Service\LinkedInService;
use App\Service\UserService;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class AuthController extends AppController{

    /**
     * @var \App\Service\UserService
     */
    private $UserService;

    /**
     * @var \App\Service\FacebookService
     */
    private $FacebookService;

    /**
     * @var \App\Model\Table\UserAccountsTable
     */
    private $UserAccounts;

    /**
     * @var \App\Model\Table\UsersTable
     */
    private $Users;

    public function initialize() {
        parent::initialize();

        $this->UserService = UserService::Instance();
        $this->FacebookService = FacebookService::Instance();

        $this->Users = TableRegistry::get("Users");
        $this->UserAccounts = TableRegistry::get('UserAccounts');

        // 비로그인 접근 가능
        $this->Auth->allow();
    }

    public function isAuthorized() {
        return true;
    }

    /**
     * 로그인
     */
    public function login()
    {
        $this->set('idColorGray', 'idColorGray');

        //이미 로그인 상태인 경우
        if($this->Auth->user()){
            // 메일 링크 등으로 redirect url이 있는 경우
            if (isset($this->request->query) != null && isset($this->request->query['redirect'])) {
                // 해당 주소로 리다이렉트
                return $this->redirect($this->request->query['redirect']);
            }
            return $this->redirect('/');
        }
        if(!is_null($this->Cookie->read('remember'))){
            $this->set('email', EncryptService::Instance()->decrypt($this->Cookie->read('remember')));
        }

        if($this->request->is('post')){

            $query = $this->request->query;
            $data = $this->request->data();
            $redirect = $data['lastPage'];

            //잘못된 url로 리다이렉트되는것 방지
            $checkRedirect = strpos($redirect, 'passwordResetComplete');
            if($checkRedirect){
                $redirect = '/';
            }

            if(isset($data['remember'])){
                $this->Cookie->configKey('remember', [
                    'expires' => '+30 days',
                ]);
                $this->Cookie->write('remember', EncryptService::Instance()->encrypt($data['email']));
            }else{
                $this->Cookie->delete('remember');
            }
            if(isset($query['types']) && $query['types'] == 'page'){
                $userAccounts = $this->Auth->identify();

                //가입자 확인 실패시
                if($userAccounts['result'] == 'fail'){
                    //다른 가입경로 가입자

                    if(isset($userAccounts['type'])){
                        $user = $this->UserService->getUserAccountsByEmail($data['email']);

                        //탈퇴신청된 계정 확인
                        if($user->status == 'boltrequest'){
                            $this->set('status', 'bolt');
                        }else{
                            return $this->redirect(DEFURL."/auth/login?signes=".$userAccounts['type']);
                        }
                    }
                    //비번이 틀렸을시
                    elseif(isset($userAccounts['code']) and ($userAccounts['code'] == 'password')){
                        return $this->redirect(DEFURL."/auth/login?password=incorrect");
                    }
                    //가입자가 존재하지 않을시
                    else{
                        return $this->redirect(DEFURL."/auth/login?signes=noUser");
                    }
                }
                //가입자 확인 성공시
                elseif($userAccounts['result'] == 'success') {
                    //가입자가 이메일 인증 않했을시
                    /*/
                    if ($userAccounts['status'] == 'noneactivate') {
                        $session = $this->request->session();
                        $user = $this->UserService->getUserByUserAccountId($userAccounts['id']);
                        $session->write('user.verify', [
                            'email' => $userAccounts['emailDecrypt'],
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name
                        ]);
                        return $this->redirect(['controller' => 'Users', 'action' => 'noActivate']);
                    }
                    /*/
                    // 탈퇴 유저가 로그인시 (탈퇴 취소)
                    if ($userAccounts['status'] == 'boltrequest' || $userAccounts['status'] == 'bolt') {
                        // 계정 복구
                        //$this->UserService->retreatBoltRequest($userAccounts['id']);
                        // 로그인
                        //$this->logUserIn($userAccounts['id']);
                        //return $this->redirect($redirect);
                        $this->set('status', $userAccounts['status']);
                    }else{
                        //정상적인 로그인시
                        $this->logUserIn($userAccounts['id']);
                        return $this->redirect(DEFURL.$redirect);
                    }
                    // authenticate return error
                }else{
                    //에러
                    $message = 'authenticate 리턴값 예외 ';
                    Debugger::log($message, 'error');
                    return $this->redirect(DEFURL.'/');
                }
            }
            //query없을시
            else{
                //에러
                Debugger::log('========== query 없는 auth/login 요청', 'error');
                $this->request->session()->write('alertMsg', 'No Query exists');
                return $this->redirect(DEFURL.'/');
            }
        }
    }



    /**
     * @return \Cake\Network\Response|void
     */
    public function logout() {
        $this->Auth->logout();
        if(isset($_SERVER['HTTP_REFERER'])){
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        return $this->redirect('/');
    }

    /**
     * login with facebook account
     *
     */
    public function loginFacebook(){
        //check if where the user come from and save them to session
        $query = $this->request->query;

        $session = $this->request->session();
        if(!empty($query)){
            $session->write($query);
        }
        $loginUrl = FacebookService::Instance()->getLoginUrl();
        //Debugger::log('============== Facebook State code generated is : ' . $_SESSION['FBRLH_state'], 'error');
        return $this->redirect($loginUrl);
        //$this->set(compact('loginUrl'));
    }

    /**
     * loginCallBackFacebook
     *
     */
    public function loginCallBackFacebook(){
        //리다이렉트 되었을시
        if($this->Auth->user()){
            return $this->redirect('/');
        }
        $query= $this->request->query;
        if(isset($query['error']) and $query['error'] == 'access_denied'){
            echo "<script>window.close();</script>";
        }
        //Debugger::log('============== Facebook State code from facebook is : ' . $query['state'], 'error');
        $fbUser = FacebookService::Instance()->getProfile();
        $fbUser = json_decode($fbUser);
        $fbArray = (array)$fbUser;

        $sns = 'facebook';

        if(isset($fbArray['email'])){
            $this->joinAndLoginSns($sns, $fbArray['email'], $fbArray['first_name'], $fbArray['last_name']);
        }else{
            $this->set('noEmail', 'noEmail');
        }

    }

    /**
     * login with linkedin account
     *
     *
     */
    public function loginLinkedin(){

        //state 생성 및 세션에 저장
        $state = LinkedInService::Instance()->getState();

        $this->request->session()->write('generated_state', $state);
        //Debugger::log('============== LinkedIn State code generated is : ' . $state, 'error');

        //check if where the user come from and save them to session
        $query = $this->request->query;
        $session = $this->request->session();
        if(!empty($query)){
            $session->write($query);
        }

        $redirect = LinkedInService::Instance()->login($state);
        return $this->redirect($redirect);
    }

    /**
     * Linkedin
     * 토큰 받아오기
     */
    public function loginCallBackLinkedin(){
        $type = 'login';
        $response = $this->request->query;

        //query 파라미터 확인후 있을시
        if(!empty($response) && !empty($response['code'])){
            $code = $response['code'];
            //Debugger::log('============== LinkedIn State code from facebook is : ' . $code, 'error');
            //state코드가 보낸것과 받은것이 일치하는지 확인
            if($response['state'] != $this->request->session()->read('generated_state')){
                $message = __("LinkedIn State Code does not match.");
                Debugger::log($message . ' a server state code is ' . $this->request->session()->read('generated_state') . ' and a given state code is ' . $response['state'], 'error');
                $this->request->session()->write('alertMsg', $message);
                return $this->redirect('/');
                //throw new BadRequestException($message);
            }

            //token 받는 API 호출
            $token = LinkedInService::Instance()->getToken($code, $type);

            if($token){
                //pass token
                return $this->redirect(['controller' => 'Auth', 'action' => 'getProfile', $token, $type]);
            }else{
                $message = __("No LinkedIn token was given.");
                Debugger::log($message, 'error');
                $this->request->session()->write('alertMsg', $message);
                return $this->redirect('/');
                //throw new BadRequestException($message);
            }
        }else{
            $this->autoRender = false;
            echo "<script>window.close();</script>";
        }
    }
    /**
     * Linkedin
     * 토큰받은후 로그인 및 가입
     * @param $token
     */
    public function getProfile($token){
        //리다이렉트 되었을시
        if($this->Auth->user()){
            return $this->redirect('/');
        }
        $data = LinkedInService::Instance()->getProfile($token);
        $data['resultName'] = $data['first_name'] . ' ' . $data['last_name'];
        $data['email_verified'] = 'Y';

        $sns = 'linkedin';
        $this->joinAndLoginSns($sns, $data['email'], $data['first_name'], $data['last_name']);

    }

    /**
     * Email join
     *
     * @return \Cake\Network\Response|void
     */
    public function join(){
        $this->set('idColorGray', 'idColorGray');

        //이미 로그인 상태인경우
        if($this->Auth->user()){
            return $this->redirect('/');
        }
        $session = $this->request->session();
        if($this->request->is('POST')){
            $data = $this->request->data;
            $data['status'] = 2; //Activate
            $data['token'] = ' ';
            //Debugger::log($data);

            $userAccount = $this->UserService->getUserAccountsByEmail($data['email']);
            //가입자 존재시
            if($userAccount) {
                //탈퇴요청한 사용자
                if($userAccount->status == 'boltrequest'){
                    return $this->redirect(['controller' => 'Auth', 'action' => 'join?status=bolt']);
                }
                //다른가입수단으로 가입시
                if($userAccount->signup == 'normal') $userAccount->signup = 'email';
                return $this->redirect(['controller' => 'Auth', 'action' => 'join?signup='.$userAccount->signup]);
            }
            //가입자 미 존재시
            else{
                //$data['token'] = $this->UserService->generateToken();

                //get language from header
                /*/
                $acceptLang = $this->request->acceptLanguage();
                $httpLang = $this->UserService->getLangFromHeader($acceptLang);
                $data['code_language_id'] = $httpLang;
                /*/

                $randKey = $session->read("randKey");
                if($data['plusInfo'] != $randKey){
                    $this->autoRender = false;
                    echo "<html>";
                    echo "<script>
                            alert('인증값이 잘못되었습니다.');
                            location.href('/');
                    </script>";
                    echo "</html>";
                }else {
                    if ($users = $this->UserService->saveUser($data)) {
                        $this->UserService->sendEmail($data['email'], $data['first_name'], $data['last_name']);
                        $this->logUserIn($users->user_accounts_id);
//                    $this->redirect(DEFURL.'/auth/joinComplete');
                        $this->redirect("/");
                    } else {
                        Debugger::log('Couldn\'t save the user : ' . $data, 'error');
                        throw new InternalErrorException();
                    }
                }
            }
        }else{
            $randNo = mt_rand();
            $session->write("randKey",$randNo);
        }
    }

    public function joinComplete(){}

    //
    /**
     * if no password has been stored, set password
     *
     * @return \Cake\Network\Response|void
     */
    public function setPassword(){
        $this->set('idColorGray', 'idColorGray');
        //만약 비번이 존재할시 로그인시킴

        /*/
        $userAccountSession = $this->request->session()->read('user.verify');
        //세션 없을시 로그인 페이지로
        if(is_null($userAccountSession) or empty($userAccountSession)){
            return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        }

        //나중에 비밀번호 입력하고 다시 들어왔을시
        $userAccountId = $userAccountSession->id;

        //비밀번호가 있는지 확인
        $hasher = new DefaultPasswordHasher();
        $result = $hasher->check(12345, $userAccountSession->password);

        if(!$result){
            if($userAccountSession->signup == 'facebook'){
                if (session_status() == PHP_SESSION_ACTIVE) {
                    session_destroy();
                }
            }
            $this->logUserIn($userAccountId);
        }
        /*/

        //if the request method is post
        if($this->request->is('POST')){

            /*/
            $connection = ConnectionManager::get('default');
            $connection->begin();
            try{
                $foundUserAccount = UserService::Instance()->getUserAccountsById($userAccountId);
                if(isset($data['email'])){
                    $patchUserAccount = $this->UserAccounts->patchEntity($foundUserAccount,[
                        'password' => $this->request->data['password']
                    ]);
                }else{
                    $patchUserAccount = $this->UserAccounts->patchEntity($foundUserAccount,[
                        'password' => $this->request->data['password']
                    ]);
                }

                $savedUserAccount = $this->UserAccounts->save($patchUserAccount);
                if(!$savedUserAccount){
                    Debugger::log($savedUserAccount->errors(), 'error');
                    throw new InternalErrorException();
                }
                $users = $this->UserService->findSimpleUserById($userAccountId);
                $patchUser = $this->Users->patchEntity($users, [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name']
                ]);
                $savedUser = $this->Users->save($patchUser);
                if(!$savedUser){
                    Debugger::log($savedUserAccount->errors(), 'error');
                    throw new InternalErrorException();
                }
            }catch (Exception $e){
                $connection->rollback();
                Debugger::log($e->getMessage());
                throw new InternalErrorException();
            }
            /*/

            $data = $this->request->data();

            $connection = ConnectionManager::get('default');
            $connection->begin();
            try {

                // ===== UserAccounts->save
                $email = EncryptService::Instance()->decrypt(trim($data['email']));
                $entityUserAccounts = $this->UserAccounts->newEntity([
                    'email' => $email,
                    'status' => 'activate',
                    'signup' => $data['sns'],
                    'password' => $data['password'],
                ]);
                $saveUserAccounts = $this->UserAccounts->save($entityUserAccounts);
                //Debugger::log($saveUserAccounts);
                if (!$saveUserAccounts) {
                    Debugger::log($saveUserAccounts->errors(), 'error');
                    throw new InternalErrorException('========== UserAccounts->save 에러');
                }

                //get http language

                //$httpLang = $this->UserService->getLangFromHeader($this->request->acceptLanguage());

                // ===== Users->save
                $entityUsers = $this->Users->newEntity([
                    'user_accounts_id' => $saveUserAccounts->id,
                    'status' => 'activate',
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'gender' => '',
                    'birthday' => $data['birthday'],
                    'nickname' => $data['nickname'],
                    'phone_number' => $data['phone_number'],
                    'fcctv_terms_yn' => 'Y',
                    //'code_language_id' => $httpLang
                ]);
                $saveUsers = $this->Users->save($entityUsers);
                if (!$saveUsers) {
                    Debugger::log($saveUsers->errors(), 'error');
                    throw new InternalErrorException('========== Users->save 에러');
                }
                $connection->commit();

            } catch (Exception $e){
                $connection->rollback();
                Debugger::log($e->getMessage());
                throw new InternalErrorException();
            }


            $this->logUserIn($saveUserAccounts->id);
            return $this->redirect('/');
        }
    }

    /**
     * joinAndLoginSns
     *
     * @param null $sns
     * @param $email
     * @param $firstName
     * @param $lastName
     * @return \Cake\Network\Response|void
     */
    public function joinAndLoginSns($sns = null, $email, $firstName, $lastName){

        //get from which the user come from
        $session = $this->request->session();
        $type = $session->read('type');

        //if the session is empty, show error page.
        $from = $session->read('from');
        $errorMessage = __("The Session has expired. Please try again.");
        if (empty($from) or empty($type)) {
            Debugger::log($errorMessage, 'error');
            $this->request->session()->write('alertMsg', $errorMessage);
            return $this->redirect('/');
        }
        $encEmail = EncryptService::Instance()->encrypt($email);

        $this->set(compact('firstName', 'lastName', 'sns', 'encEmail'));

        $userAccounts = $this->UserService->getUserAccountsByEmail($email);
        //popup 인 경우 = login
        if ($from == 'popup') {
            //계정이 있는경우
            /*/
            if ($userAccounts) {
                //동일한 가입경로인지 확인
                if ($userAccounts->signup == $sns) {

                    //비밀번호가 있는지 확인
                    $hasher = new DefaultPasswordHasher();
                    $result = $hasher->check(' ', $userAccounts->password);
                    if(!$result){

                        //탈퇴요청시 상태 변경
                        if($userAccounts['status'] == 'boltrequest'){
                            $this->UserService->retreatBoltRequest($userAccounts['id']);
                            if($sns == 'facebook'){
                                if (session_status() == PHP_SESSION_ACTIVE) {
                                    session_destroy();
                                }
                            }
                            $this->logUserIn($userAccounts['id']);
                            return $this->redirect('/');
                        }else{
                            //set user login
                            if($sns == 'facebook'){
                                if (session_status() == PHP_SESSION_ACTIVE) {
                                    session_destroy();
                                }
                            }
                            $this->logUserIn($userAccounts['id']);
                            $this->view = 'join_and_login_sns';
                            $this->set('login', 'success');

                        }
                    }
                    //비밀번호가 없는경우
                    else{
                        $this->request->session()->write('user.verify', $userAccounts);
                        $this->view = 'join_and_login_sns';
                        $this->set('setPassword', 'setPassword');
                        //return $this->redirect(['controller' => 'Auth', 'action' => 'setPassword']);
                    }
                }
                //동일한 가입경로가 아닐경우
                else {
                    //다른 가입경로로 들어온경우
                    if ($userAccounts->signup == 'normal') $userAccounts->signup = 'email';
                    $this->view = 'join_and_login_sns';
                    $this->set('sign', $userAccounts->signup);
                }
            }
            // 로그인시 해당 이메일이 없는 경우
            else {
                $this->view = 'join_and_login_sns';
                $this->set('noMail', 'noMail');
            }
            /*/
        }
        //페이지에서 들어온 경우
        elseif ($from == 'page') {
            //로그인인 경우
            if ($type == 'login') {
                //계정이 있는 경우
                if ($userAccounts) {
                    //동일한 가입경로인지 확인
                    if ($userAccounts->signup == $sns) {
                        //탈퇴요청자일경우
                        if($userAccounts['status'] == 'boltrequest'){

                            //로그인 허용
                            //$this->UserService->retreatBoltRequest($userAccounts['id']);
                            /*/
                            if($sns == 'facebook'){
                                if (session_status() == PHP_SESSION_ACTIVE) {
                                    session_destroy();
                                }
                            }/*/
                            //$this->logUserIn($userAccounts['id']);
                            //$this->view = 'join_and_login_sns';
                            //$this->set('login', 'success');

                            //로그인 방지
                            $this->view = 'join_and_login_sns';
                            $this->set('boltrequest', 'boltrequest');
                        }else{
                            /*/
                            if($sns == 'facebook'){
                                if (session_status() == PHP_SESSION_ACTIVE) {
                                    session_destroy();
                                }
                            }/*/
                            $this->logUserIn($userAccounts['id']);
                            $this->view = 'join_and_login_sns';
                            $this->set('login', 'success');
                        }
                    }
                    //동일한 가입경로가 아닐경우
                    else {
                        if ($userAccounts->signup == 'normal') $userAccounts->signup = 'email';
                        $this->view = 'join_and_login_sns';
                        $this->set('signes', $userAccounts->signup);
                    }
                }
                //계정이 없는경우
                else {
                    $this->view = 'join_and_login_sns';
                    $this->set('noMails', 'noMails');
                }
            }
            //가입일 경우
            elseif ($type == 'join') {
                //계정이 있는 경우
                if ($userAccounts) {
                    //탈퇴요청일경우
                    if($userAccounts->status == 'boltrequest'){
                        $this->view = 'join_and_login_sns';
                        $this->set('boltrequest', 'boltrequest');
                    }

                    //동일한 가입경로인지 확인
                    elseif ($userAccounts->signup == $sns) {
                        /*/
                        if($sns == 'facebook'){
                            if (session_status() == PHP_SESSION_ACTIVE) {
                                session_destroy();
                            }
                        }
                        /*/
                        $this->logUserIn($userAccounts['id']);
                        $this->view = 'join_and_login_sns';
                        $this->set('join', 'success');
                    }
                    //동일한 가입경로가 아닐경우
                    else {
                        if ($userAccounts->signup == 'normal') $userAccounts->signup = 'email';
                        $this->view = 'join_and_login_sns';
                        $this->set('joinSign', $userAccounts->signup);
                    }
                }

                //해당 이메일이 가입이 되어있지 않을경우(새 가입자)
                else {
                    /*/
                    $connection = ConnectionManager::get('default');
                    $connection->begin();
                    try {

                        // ===== UserAccounts->save
                        $entityUserAccounts = $this->UserAccounts->newEntity([
                            'email' => $email,
                            'status' => 'activate',
                            'signup' => $sns,
                            'password' => 12345
                        ]);
                        $saveUserAccounts = $this->UserAccounts->save($entityUserAccounts);
                        if (!$saveUserAccounts) {
                            Debugger::log($saveUserAccounts->errors(), 'error');
                            throw new InternalErrorException('========== UserAccounts->save 에러');
                        }

                        //get http language

                        //$httpLang = $this->UserService->getLangFromHeader($this->request->acceptLanguage());

                        // ===== Users->save
                        $entityUsers = $this->Users->newEntity([
                            'user_accounts_id' => $saveUserAccounts->id,
                            'status' => 'activate',
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            //'code_language_id' => $httpLang
                        ]);
                        $saveUsers = $this->Users->save($entityUsers);
                        if (!$saveUsers) {
                            Debugger::log($saveUsers->errors(), 'error');
                            throw new InternalErrorException('========== Users->save 에러');
                        }
                        $connection->commit();

                    } catch (Exception $e){
                        $connection->rollback();
                        Debugger::log($e->getMessage());
                        throw new InternalErrorException();
                    }
                    $this->request->session()->write('user.verify', $saveUserAccounts);
                    /*/
                    $this->view = 'join_and_login_sns';
                    $this->set('setPassword', 'setPassword');
                }
            }
            //type 세션 오류
            else{
                $message = __("Session Error. The information you entered does not exist.");
                Debugger::log($message, 'error');
                $this->request->session()->write('alertMsg', $message);
                return $this->redirect('/');
            }
        }
        //from 세션 오류
        else{
            $message = __("Session Error. The information you entered does not exist.");
            Debugger::log($message, 'error');
            $this->request->session()->write('alertMsg', $message);
            return $this->redirect('/');
        }
    }


    /**
     * 암호 재설정 메일 발송
     *
     * @return \Cake\Network\Response|void
     */
    public function password_forgot(){
        $this->set('idColorGray', 'idColorGray');

        //로그인 시 리다이렉트
        if($this->Auth->user()){
            return $this->redirect('/');
        }

        if($this->request->is('POST')){
            $email = $this->request->data['email'];
            $userAccount = $this->UserService->getUserAccountsByEmail($email);
            if(is_null($userAccount)){
                $this->set('noUser', 'noUser');

            }else{
                $encryptedEmail = $userAccount->email;
                $token = $this->UserService->generateToken();

                $return = $this->UserService->sendEmailToResetPassword($email, $token);

                if($return == 'success'){
                    $saveToken = $this->UserAccounts->patchEntity($userAccount, [
                        'authentication_code' => $token
                    ]);
                    if($this->UserAccounts->save($saveToken)){
                        $this->request->session()->write('user.email', $encryptedEmail);
                        return $this->redirect(['controller' => 'Auth', 'action' => 'resendPassword']);
                    }else{
                        Debugger::log($saveToken->errors(), 'error');
                        throw new InternalErrorException();
                    }
                }else{
                    Debugger::log('========== 암호 재설정 메일 발송 실패', 'error');
                    throw new InternalErrorException();
                }
            }
        }
    }

    /**
     * 재설정 암호 저장
     *
     * @return \Cake\Network\Response|void
     */
    public function resetPassword(){
        $this->set('idColorGray', 'idColorGray');

        $token = $this->request->query['token'];
        $return = $this->UserService->getUserAccountsByToken($token);
        if($return == 'expired'){
            return $this->redirect(['controller' => 'Users', 'action' => 'expiredToken']);
        }else{
            $user = $this->UserService->getUserByUserAccountId($return->id);
            $this->set('email', $return->emailDecrypt);
            $this->set(compact('user'));
        }
        if($this->request->is('POST')){
            $data = $this->request->data;
            //비밀번호 서로 일치하는지 확인
            if($data['new_password'] != $data['confirm_password']){
                $this->set('NoMatch', 'NoMatch');
            }else{
                $this->UserService->savePassword($return->id, $data['new_password']);
                return $this->redirect(['controller' => 'Auth', 'action' => 'passwordResetComplete']);
            }
        }
    }

    /**
     * 암호 재설정 메일 재발송
     * @return \Cake\Network\Response|void
     */
    public function resendPassword(){

        //로그인 시 리다이렉트
        if($this->Auth->user()){
            return $this->redirect('/');
        }

        $this->set('idColorGray', 'idColorGray');
        $encryptedEmail = $this->request->session()->read('user.email');
        if(is_null($encryptedEmail) or empty($encryptedEmail)){
            $message = 'The Session Is Empty';
            return $this->redirect('/');
        }

        if($this->request->is('POST')){
            $email = EncryptService::Instance()->decrypt(trim($encryptedEmail));
            $userAccount = $this->UserService->getUserAccountsByEmail($email);
            if(is_null($userAccount)){
                $this->request->session()->write('alertMsg', __("No email found."));
                return $this->redirect('/');
                //throw new BadRequestException(__("No email found."));
            }else{
                $token = $this->UserService->generateToken();
                $decryptedEmail = $userAccount->emailDecrypt;
                $return = $this->UserService->sendEmailToResetPassword($decryptedEmail, $token);
                if($return == 'success'){
                    $saveToken = $this->UserAccounts->patchEntity($userAccount, [
                        'authentication_code' => $token
                    ]);

                    if($this->UserAccounts->save($saveToken)) {
                        $this->request->session()->write('user.email', $encryptedEmail);
                    }else{
                        Debugger::log($saveToken->errors(), 'error');
                        throw new InternalErrorException();
                    }
                }else{
                    Debugger::log('========== 암호 재설정 메일 발송 실패', 'error');
                    throw new InternalErrorException();
                }
            }
        }
    }

    /**
     * 암호 재설정 메일 재발송 완료 화면
     */
    public function passwordResetComplete(){
        $this->set('idColorGray', 'idColorGray');
        //로그인 시 리다이렉트
        if($this->Auth->user()){
            return $this->redirect('/');
        }
    }

    /**
     * 로그인 로그 테이블 업데이트 후 로그인
     * @param null $userAccountId
     */
    private function logUserIn($userAccountId = null){
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
            Debugger::log('UserAccountId : ' . $userAccountId . ' has failed to log in');
        }
    }

    public function noEmail(){
        $this->set('idColorGray', 'idColorGray');
        //no action
    }

    //email validation
    public function checkValidation(){
        $this->autoRender = false;
        if($this->request->is('POST')){
            $email = $this->request->data['email'];
            $userAccounts = $this->UserService->getUserAccountsByEmail($email);
            if(is_null($userAccounts) or empty($userAccounts)){
                $status = ['status' => 'ok'];
                echo json_encode($status);
            }else{
                if($userAccounts->status == 'boltrequest'){
                    $status = ['status' => 'boltrequest'];
                    echo json_encode($status);
                }
                elseif ($userAccounts->signup != 'normal') {
                    $status = ['status' => 'another', 'signup' => $userAccounts->signup];
                    echo json_encode($status);
                }
                //동일한 가입경로가 아닐경우
                else {
                    $status = ['status' => 'success'];
                    echo json_encode($status);
                }
            }
        }
    }


    public function androidSaveUser(){
        $type = 'signUp';
        $response = $this->request->query;


        $linkedinService = LinkedInService::Instance();
        //query 파라미터 확인
        if(!empty($response)){
            $code = $response['code'];

            //state코드가 보낸것과 받은것이 일치하는지 확인
            if($response['state'] != $this->request->session()->read('generated_state')){
                throw new UnauthorizedException('Warning!!The state code does not match.');
            }


            //token 받는 API 호출
            $token = $linkedinService->getToken($code, $type);

            if($token){
                //사용자 정보 받는 API 호출
                $data = $linkedinService->getProfile($token);

                //가입확인
                $linkedinService->checkProfile($data, $type);

                $participants = TableRegistry::get('Participants');

                $data = $participants->newEntity($data);
                if($participants->save($data)){
                    $data['result'] = 'success';
                    $this->set(compact('data'));
                }else{
                    throw new BadRequestException('Cannot save the participant');
                }
            }else{
                throw new BadRequestException('No token was given.');
            }
        }else{
            throw new BadRequestException(__("There is no response from LinkedIn. Please check the internet connection."));
        }
    }

    public function checkEmailIfExist(){
        $this->autoRender = false;
        $data = $this->request->data;
        if($this->request->is('POST')){
            if(isset($data['email'])){
                $response = $this->UserService->Instance()->checkEmailIfExist($data['email']);
                if($response == 'bolt') {
                    echo json_encode('bolt');
                    exit;
                }elseif($response == 'new'){
                    echo json_encode('new');
                    exit;
                }else{
                    echo json_encode($response);
                    exit;
                }
            }
        }
    }

    /**
     * 휴대폰 번호 중복 체크
     */
    public function mobileNumCheck(){
        $data = $this->request->data();
        $phoneno = $data['phone_number'];
        $this->autoRender = false;
        $result = array();
        if($this->UserService->mobileNumCheck($phoneno)){
            $result['result'] = true;
            $result['msg'] = 'success';
        }else{
            $result['result'] = false;
            $result['msg'] = 'areadyExsist';
        }

        echo json_encode($result);
    }

    /**
     * 닉네임 중복 체크 
     */
    public function nickNameCheck(){
        $data = $this->request->data();
        $nickname = $data['nickname'];
        $this->autoRender = false;
        $result = array();
        if($this->UserService->nickNameCheck($nickname)){
            $result['result'] = true;
            $result['msg'] = 'success';
        }else{
            $result['result'] = false;
            $result['msg'] = 'areadyExsist';
        }

        echo json_encode($result);
    }

    /**
     * 본인 인증 창 호출
     */
    public function userConfirmPop(){
        $this->autoRender= false;
        $data = $this->request->data();

        $session = $this->request->session();

        $randNo = $session->read("randKey");

        $enc_tr_cert = $this->UserService->kmcert($data,$randNo);
        $domain = Router::url('/', true);

        $tr_url     = $domain."/auth/getConfirmResult?call=".$data['callPage'];      // 본인인증 결과수신 POPUP URL

        echo "<html>";
        if($enc_tr_cert==false){
            echo "
            <script>alert('인증값이 잘못되었습니다.');
            window.close();
            </script>";
            echo "</html>";
        }

        echo '
        <body>
        <form name="reqKMCISForm" method="post" action="#">
            <input type="hidden" name="tr_cert"     value = "'.$enc_tr_cert.'">
            <input type="hidden" name="tr_url"      value = "'.$tr_url.'">
            <input type="hidden" name="plusInfo"   maxlength="320" value="'.$randNo.'">
        </form>
        </body>
        ';
        echo "
        <script language=javascript>
            <!--
              window.name = \"kmcis_web_sample\";
              var KMCIS_window;
              function openKMCISWindow(){
                var UserAgent = navigator.userAgent;
                /* 모바일 접근 체크*/
                // 모바일일 경우 (변동사항 있을경우 추가 필요)
                if (UserAgent.match(/iPhone|iPod|Android|Windows CE|BlackBerry|Symbian|Windows Phone|webOS|Opera Mini|Opera Mobi|POLARIS|IEMobile|lgtelecom|nokia|SonyEricsson/i) != null || UserAgent.match(/LG|SAMSUNG|Samsung/) != null) {
                  document.reqKMCISForm.target = '';
                } else {                  // 모바일이 아닐 경우
//                    KMCIS_window = window.open('', 'KMCISWindow', 'width=425, height=550, resizable=0, scrollbars=no, status=0, titlebar=0, toolbar=0, left=435, top=250' );


//                    document.reqKMCISForm.target = 'KMCISWindow';
                    }
                    document.reqKMCISForm.action = 'https://www.kmcert.com/kmcis/web/kmcisReq.jsp';
                    document.reqKMCISForm.submit();
               }
               openKMCISWindow();
            //-->
        </script>
        ";
        echo "</html>";
    }

    /**
     * 본인 인증 완료 후 리다이렉트 페이지
     */
    public function getConfirmResult(){
        $this->autoRender= false;

        $rec_cert = $this->request->query('rec_cert');
        $certNum = $this->request->query('certNum');
        $callType = $this->request->query('call');

        echo "<html>";
        echo '<script type="text/javascript">';
	    echo 'var move_page_url = "/auth/confirmFinalResult";';
	    echo 'function end() {';
        echo 'document.kmcis_form.action = move_page_url;';
        echo 'var UserAgent = navigator.userAgent;';
        echo 'if (UserAgent.match(/iPhone|iPod|Android|Windows CE|BlackBerry|Symbian|Windows Phone|webOS|Opera Mini|Opera Mobi|POLARIS|IEMobile|lgtelecom|nokia|SonyEricsson/i) != null || UserAgent.match(/LG|SAMSUNG|Samsung/) != null) {';
        echo 'document.kmcis_form.submit();';
        echo '}else{';
//	    echo 'document.kmcis_form.target = opener.window.name;';
	  	echo 'document.kmcis_form.submit();';
//	  	echo 'self.close();';
        echo '}';
	    echo '}';
        echo '</script>';
        echo '<body onload="javascript:end()">';
        echo '<form id="kmcis_form" name="kmcis_form" method="post">';
	    echo '<input type="hidden"	name="rec_cert"		id="rec_cert"	value="'.$rec_cert.'"/>';
	    echo '<input type="hidden"	name="certNum"		id="certNum"	value="'.$certNum.'"/>';
        echo '<input type="hidden"	name="callType"		id="callType"	value="'.$callType.'"/>';
        echo '</form>';
        echo '</body>';
        echo '</html>';
    }

    /**
     * 인증 완료 후 화면
     * callType 에 따라 리턴이 달라진다.
     */
    public function confirmFinalResult(){
        $this->autoRender=false;
        $data = $this->request->data();

        $session = $this->request->session();

        $randKey = $session->read("randKey");

        $birthday = $this->UserService->kmcertDecrypt($data,$randKey);


        if($birthday==false){
            echo 'alert("인증에 실패하였습니다.\n이름과 전화번호를 확인해주세요.");';
        }else{
            if($data['callType']=="join") {
                echo "<html>";
                echo '<script type="text/javascript">';
                echo "var now      = new Date();\n";
                echo "var year     = now.getFullYear();\n";
                echo "var month    = now.getMonth()+1;\n";
                echo "var day      = now.getDate();\n";
                echo "if(month < 10){month = '0'+month;}\n";
                echo "if(day < 10){day = '0'+day;}\n";
                echo "var today = year+''+month+''+day;\n";
                echo "var result    = today - parseInt(" . $birthday . ") - 140000;\n";
                echo "if(result < 0){ \n";
                echo "alert('만 14세미만 회원은 가입하실수 없습니다.');\n";
//            echo "opener.location.href='/';";
                echo "}else{ \n";
                echo "opener.document.firstForm.first_name.readyonly= true;\n";
                echo "opener.document.firstForm.last_name.readyonly= true;\n";
                echo "opener.document.firstForm.confirmOk.value = 'Y';\n";
                echo "opener.document.firstForm.plusInfo.value = '" . $randKey . "';\n";
                echo "opener.document.firstForm.phone_number.readonly = true;\n";
                echo "opener.document.firstForm.birthday.value = '" . $birthday . "';\n";
                echo "window.opener.confirmComplete('Y');\n";
                echo "}\n";
                echo "window.close()\n";
                echo '</script>';
                echo "</html>";
            }else if($data['callType']=='searchId'){
                echo "<html>";
                echo '<script type="text/javascript">';
                echo "opener.document.emailSearch.first_name.readyonly= true;";
                echo "opener.document.emailSearch.last_name.readyonly= true;";
                echo "opener.document.emailSearch.confirmOk.value = 'Y';";
                echo "opener.document.emailSearch.plusInfo.value = '" . $randKey . "';";
                echo "opener.document.emailSearch.phone_number.readonly = true;";
                echo "window.opener.confirmComplete('Y');\n";
                echo "window.close()";
                echo '</script>';
                echo "</html>";
            }else if($data['callType']=='resetPassword'){
                echo "<html>";
                echo '<script type="text/javascript">';
                echo "opener.document.resetPwd.email.readyonly= true;";
                echo "opener.document.resetPwd.first_name.readyonly= true;";
                echo "opener.document.resetPwd.last_name.readyonly= true;";
                echo "opener.document.resetPwd.confirmOk.value = 'Y';";
                echo "opener.document.resetPwd.plusInfo.value = '" . $randKey . "';";
                echo "opener.document.resetPwd.phone_number.readonly = true;";
                echo "window.opener.confirmComplete('Y');\n";
                echo "window.close()";
                echo '</script>';
                echo "</html>";
            }
        }

    }

    /**
     * 비밀번호 찾기 화면
     * 로그인 되어있을 경우 접근 불가
     * @return \Cake\Network\Response|void
     */
    public function email_forgot(){
        $this->set('idColorGray', 'idColorGray');

        //로그인 시 리다이렉트
        if($this->Auth->user()){
            return $this->redirect('/');
        }


    }

    public function searchEmail(){
        if($this->request->is('POST')){
            $lastName = $this->request->data['last_name'];
            $firstName = $this->request->data['first_name'];
            $phoneNo = $this->request->data['phone_number'];

            $encrypt = EncryptService::Instance();

            $data = array(
                'first_name' => $firstName,
                'last_name' =>$lastName,
                'phone_number' =>$encrypt->encrypt($phoneNo),
            );

            $userAccount = $this->UserService->findUserEmail($data);

            if(is_null($userAccount)){
                $this->set('noUser', 'noUser');
            }else{
                if($userAccount->user_account->signup == "normal") {
                    $encryptedEmail = $userAccount->user_account->email;

                    $decrpytedEmail = $encrypt->decrypt($encryptedEmail);

                    $this->set("email", $decrpytedEmail);
                }else if($userAccount->user_account->signup == "facebook") {
                    $this->set("socialJoin",true);
                    $this->set("socialType","FaceBook");
                }else if($userAccount->user_account->signup == "linkedin") {
                    $this->set("socialJoin",true);
                    $this->set("socialType","Linkedin");
                }
            }
        }
    }

    /**
     * 암호 재설정 메일 발송
     *
     * @return \Cake\Network\Response|void
     */
    public function password_reset(){
        $this->set('idColorGray', 'idColorGray');

        //로그인 시 리다이렉트
        if($this->Auth->user()){
            return $this->redirect('/');
        }

        if($this->request->is('POST')){
            $email = $this->request->data['email'];
            $userAccount = $this->UserService->getUserAccountsByEmail($email);
            if(is_null($userAccount)){
                $this->set('noUser', 'noUser');
                $this->view("password_forgot");
            }else{
                $token = $this->UserService->generateToken();
                $saveToken = $this->UserAccounts->patchEntity($userAccount, [
                    'authentication_code' => $token
                ]);

                if($this->UserAccounts->save($saveToken)){
                    return $this->redirect("/resetPassword?token=".$token);
                }else{
                    Debugger::log($saveToken->errors(), 'error');
                    throw new InternalErrorException();
                }
            }
        }


    }
}