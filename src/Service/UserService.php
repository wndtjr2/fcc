<?php
namespace App\Service;

error_reporting(E_STRICT);
/**
 * 사용자 조회 서비스 인터페이스
 * User: Winner
 * Date: 15. 2. 6.
 * Time: 오후 1:54
 */

use App\Model\Table\ChallengeEntryTable;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Network\Email\Email;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Controller\Component;


/**
 * Interface UserInterface
 * 사용자 인터 페이스
 * @package App\Service
 */
interface UserInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function findSimpleUserById($id);

    /**
     * @param $email
     * @return mixed
     */
    public function getUserAccountsByEmail($email);

    /**
     * 예전 비밀번호와 비교
     * @param $newPassword
     * @param $email
     * @return mixed
     */
    public function checkOldPassword($newPassword, $email);

    /**
     * 개인정보 & 모델정보입력 상태 확인
     * @param $id
     * @return mixed
     */
    public function checkChallengeJoin($userId, $type);

}

/**
 * 사용자 조회 서비스
 * Class UserService
 * @package App\Service
 */
class UserService implements UserInterface {

    /**
     * @var \Cake\ORM\Table
     */
    private $Users;

    /**
     * @var \Cake\ORM\Table
     */
    private $UserAccounts;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChallengeEntry;

    /**
     * @var \Cake\ORM\Table
     */
    private $LogUserSign;

    /**
     * @var \Cake\ORM\Table
     */
    private $CodeLanguage;

    /**
     * @var \Cake\ORM\Table
     */

    private $CodeCountry;


    private function __construct() {
        $this->Users = TableRegistry::get("Users");
        $this->UserAccounts = TableRegistry::get('UserAccounts');
        $this->ChallengeEntry = TableRegistry::get('ChallengeEntry');
        $this->LogUserSign = TableRegistry::get('LogUserSign');
        $this->CodeCountry = TableRegistry::get('CodeCountry');
        $this->CodeLanguage = TableRegistry::get('CodeLanguage');
    }

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new UserService();
        }
        return $inst;
    }

    /**
     * user_accounts 아이디로 사용자 검색 (비밀번호 포함)
     * @param string $user_accounts_id
     * @return mixed
     */
    public function findSimpleUserById($user_accounts_id)
    {
        return $this->Users->find()->contain('UserAccounts')->where([
            'user_accounts_id' => $user_accounts_id
            ,'Users.status' => 'activate'
            ,'UserAccounts.status' => 'activate'
        ])->first();
    }

    public function getUserAccountsByEmail($email){
        $user = $this->UserAccounts->find()->where(['email' => EncryptService::Instance()->encrypt($email)])->first();
        return $user;
    }

    public function getUsersByEmail($email){
        $userAccount = $this->UserAccounts->find()->where(['email' => EncryptService::Instance()->encrypt($email)])->first();
        $user = $this->Users->find()->where(['user_accounts_id' => $userAccount->id])->first();
        return $user;
    }

    public function getUserAccountsById($userAccountsId){
        $user = $this->UserAccounts->find()->where(['id' => $userAccountsId])->first();
        return $user;
    }

    public function getUserByUsersId($usersId){
        return $this->Users->get($usersId);
    }
    public function getUserByUserAccountId($user_accounts_id){
        return $this->Users->find()->where([
            'user_accounts_id' => $user_accounts_id
        ])->first();
    }
    public function getUsersAndUserAccountsByUsersId($usersId){
        $userAccount = $this->Users->find()->contain(['UserAccounts'])->where(['Users.id' => $usersId])->first();
        //$email = $userAccountId->user_account->emailDecrypt;
        return $userAccount;
    }


    /**
     * 확인 이메일 전송
     * @param $data
     * @return string
     */
    public function sendConfirmEmail($data){
        $data['domain'] = Router::url('/', true);
        $email = new Email();
        try{
            $email->transport('brick')
                ->to($data['email'])
                ->from(FROMFCCTVMAIL)
                ->emailFormat('html')
                ->subject(__('Email Verification'))
                ->viewVars(array(
                    'data' => $data
                ))
                ->template('verification');
            if(!$email->send()){
                Debugger::log($email);
                throw new InternalErrorException();
            }
        }catch (Exception $e){
            Debugger::log($e->getMessage());
            return 'fail';
        }
        return 'success';
//        $email->transport('amazon')
//            ->to($data['email'])
//            ->from(CROWDCHALLENGE)
//            ->emailFormat('html')
//            ->template('verification')
//            ->subject('Email Verification')
//            ->viewVars(array(
//                'data' => $data
//            ))
//            ->send();
//        return 'success';
    }
    /**
     * 랜덤 토큰 생성
     * @param int $length
     * @return string
     */
    public function generateToken($length = 20) {
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
    /**
     * 이메일 + 타입 중복 확인
     * @param string $email
     * @param string $signup normal | facebook | linkedin | weibo
     * @return array|null
     */
    public function findUser($email, $signup = null) {
        $emails = EncryptService::Instance()->encrypt($email);
        return $this->UserAccounts->find()->where([
            'email' => EncryptService::Instance()->encrypt($emails)
            ,'signup' => $signup
        ])->first();
    }

    /**
     * 참여자 추가
     * @param $userData
     * @return bool
     */
    public function saveUser($userData)
    {
        $connection = ConnectionManager::get('default');
        $connection->begin();
        try{
            $userAccounts = $this->UserAccounts->newEntity([
                'email' => $userData['email'],
                'password' => $userData['password'],
                'status' => $userData['status'],
                'signup' => $userData['signup'],
                'authentication_code' => $userData['token'],
                'token' => ''
            ]);
            if(!$userAccount = $this->UserAccounts->save($userAccounts)){
                Debugger::log($userAccounts->errors(), 'error');
                throw new InternalErrorException('========== UserAccounts Save 에러');
            }
            $users = $this->Users->newEntity([
                'user_accounts_id' => $userAccount['id'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'status' => $userData['status'],
                'code_language_id' => 38,
                'gender' => '',
                'birthday' => $userData['birthday'],
                'fcctv_terms_yn' => 'Y',
                'phone_number' => $userData['phone_number'],
                'nickname' => $userData['nickname'],
                //'code_language_id' => $userData['code_language_id']
            ]);
            if(!$user = $this->Users->save($users)){
                Debugger::log($users->errors(), 'error');
                throw new InternalErrorException('========== Users Save 에러');
            }
            $connection->commit();
        }catch (Exception $e){
            $connection->rollback();
            Debugger::log($e->getMessage(), 'error');
            throw new InternalErrorException($e->getMessage());
        }
        return $user;
    }

    /**
     * 확인 이메일 검증
     * @param $token
     * @return mixed|string
     */
    public function checkEmailVerification($token)
    {
        $check = $this->UserAccounts->find()->where(['authentication_code' => $token])->first();
        if($check){
            if ($check->status == 'activate') {
                $check->status = 'already';
                return $check;
            }else{
                $userAccounts = $this->UserAccounts
                    ->updateAll([
                        'status' => 'activate'], ['authentication_code' => $token, 'status' => 'noneactivate']);
                if (!$userAccounts) {
                    Debugger::log($userAccounts->errors(), 'error');
                    throw new InternalErrorException('========== UserAccounts updateAll 에러');
                } else {
                    $users = $this->getUserByUserAccountId($check->id);
                    if($users['status'] == 'activate'){
                        return $check;
                    }else{
                        $user = $this->Users->updateAll([
                            'status' => 'activate'], ['user_accounts_id' => $check->id, 'status' => 'noneactivate'
                        ]);
                        if(!$user){
                            Debugger::log($user->errors(), 'error');
                            throw new InternalErrorException('========== Users updateAll 에러');
                        }else{
                            return $check;
                        }
                    }
                }
            }
        } else {
            return 'error';
        }
    }

    /**
     * 참여 환영 이메일 전송
     * @param $email
     * @param $firstName
     * @param $lastName
     * @return mixed
     */
    public function sendEmail($email, $firstName, $lastName){

        $mailTo = $email;
        $email = new Email();

        try{
            $email->transport('brick')
                ->from(FROMFCCTVMAIL)
                ->to($mailTo)
                ->emailFormat('html')
                ->subject(__('Thank you for participating.'))
                ->template('thanks')
                ->viewVars(array(
                    'name' => $lastName.' '.$firstName
                ));
            if(!$email->send()){
                Debugger::log($email);
                throw new InternalErrorException();
            }
        }catch (Exception $e){
            Debugger::log($e->getMessage());
            return 'fail';
        }
        return $rtn = 'success';
//        $email->transport('amazon')
//            ->from($mailFrom)
//            ->to($mailTo)
//            ->emailFormat('html')
//            ->subject('Thank you for participating.')
//            ->template('thanks')
//            ->viewVars(array(
//                'name' => $firstName . ' ' . $lastName
//            ))
//            ->send();
//        return $rtn = 'success';
    }

    /**
     * 인증 이메일 전송
     * @param $verify
     * @return string
     */
    public function sendVerificationEmail($verify){
        if(empty($verify)){
            return 'no_session';
        }
        $UserAccounts = $this->UserAccounts->find()->where(['email' => EncryptService::Instance()->encrypt($verify['email']), 'signup' => 'normal'])->first();
        if($UserAccounts->status == 'activate'){
            return $return = 'already';
        }

        //null 일때
        if(is_null($UserAccounts)){
            return $return = 'error';
        }

        //데이터 부족할때
        elseif(!isset($verify['first_name'])) {
            $user = $this->Users->find()->where(['user_accounts_id' => $UserAccounts->id])->first();
            $verify['first_name'] = $user->first_name;
            $verify['last_name'] = $user->last_name;
        }
        $token = $this->generateToken();
        $UserAccount = $this->UserAccounts->patdtity($UserAccounts, [
            'authentication_code' => $token,
        ]);
        if($this->UserAccounts->save($UserAccount)){
            $verify['token'] = $token;

            $return = $this->sendConfirmEmail($verify);

            if($return == 'success'){
                return $return;
            }else{
                return $return = 'error';
            }
        }else{
            Debugger::log($UserAccount->errors(), 'error');
            throw new InternalErrorException('========== UserAccounts save 에러');
        }
    }

    /**
     * 비밀번호 재설정 메일 발송
     * @param $mail
     * @param $token
     * @return string
     */
    public function sendEmailToResetPassword($mail, $token){
        $data['domain'] = Router::url('/', true);
        $data['token'] = $token;//encryptedEmail
        try{
            $user = $this->getUsersByEmail($mail);
            if($user){
                $data['first_name'] = $user->first_name;
                $data['last_name'] = $user->last_name;
            }else{
                throw new InternalErrorException('No User Table Exist.');
            }
            $email = new Email();
            $email->transport('brick')
                ->to($mail)//email
                ->from(FROMFCCTVMAIL)
                ->emailFormat('html')
                ->subject(__('Password Reset'))
                ->viewVars(array(
                    'data' => $data
                ))
                ->template('password_reset');
            if(!$email->send()){
                Debugger::log($email);
                throw new InternalErrorException();
            }
        }catch (Exception $e){
            Debugger::log($e->getMessage());
            return 'fail';
        }
        return 'success';
//        $email->transport('amazon')
//            ->to($mail)//email
//            ->from(CROWDCHALLENGE)
//            ->emailFormat('html')
//            ->template('password_reset')
//            ->subject('Password Reset')
//            ->viewVars(array(
//                'data' => $data
//            ))
//            ->send();
//        return 'success';
    }

    /**
     * 계정 삭제 이메일 발송
     * @param $mail
     * @param $firstName
     * @return string
     */
    public function sendBoltRequestEmail($mail, $firstName){
        $email = new Email();

        try{
            $email->transport('brick')
                ->to($mail)//email
                ->from(FROMFCCTVMAIL)
                ->emailFormat('html')
                ->template('bolt_request')
                ->subject(__('Account Deletion'))
                ->viewVars(array(
                    'name' => $firstName
                ));
            if(!$email->send()){
                Debugger::log($email);
                throw new InternalErrorException();
            }
        }catch (Exception $e){
            Debugger::log($e->getMessage());
            return 'fail';
        }
        return 'success';
//        $email->transport('amazon')
//            ->to($mail)//email
//            ->from(CROWDCHALLENGE)
//            ->emailFormat('html')
//            ->template('bolt_request')
//            ->subject('Account Deletion')
//            ->viewVars(array(
//                'name' => $firstName
//            ))
//            ->send();
//        return 'success';
    }

    /**
     * 토큰으로 UserAccounts 조회
     * @param $token
     * @return mixed|string
     */
    public function getUserAccountsByToken($token){
        $userAccount = $this->UserAccounts->find()->where(['authentication_code' => $token])->first();
        if(is_null($userAccount)){
            return 'expired';
        }else{
            return $userAccount;
        }
    }

    /**
     * 이메일로 계정 activate 여부 조회
     * @param $email
     * @return mixed
     */
    public function getUserAccountsByEmailWithActivate($email){
        $user = $this->UserAccounts->find()->where(['email' => EncryptService::Instance()->encrypt($email), 'status' => 'activate'])->first();
        return $user;
    }

    /**
     * 암호 재설정 SAVE
     * @param $userAccountId
     * @param $password
     * @return bool
     */
    public function savePassword($userAccountId, $password){
        $userAccounts = $this->getUserAccountsById($userAccountId);
        $saveToken = $this->UserAccounts->patchEntity($userAccounts, [
            'password' => $password
        ]);
        if($this->UserAccounts->save($saveToken)){
            return true;
        }else{
            Debugger::log($saveToken->errors(), 'error');
            throw new InternalErrorException('========== UserAccounts save 에러');
        }
    }


    /**
     * 기존 암호 일치 여부 확인
     * @param $newPassword
     * @param $email
     * @return bool
     */
    public function checkOldPassword($newPassword, $email){
        $userAccounts = $this->getUserAccountsByEmail($email);
        if(!is_string($newPassword)){
            settype($newPassword, 'string');
        }
        $hasher = new DefaultPasswordHasher();
        $return  = $hasher->check($newPassword, $userAccounts->password);
        return $return;
    }

    /**
     * usersId로 챌린지 참가 여부 조회
     * @param $usersId
     * @return string
     */
    public function checkChallengeJoin($usersId, $target){
        $targetSubject = ChallengeService::Instance()->selectRegistPeriodSubject($target);
        if(is_null($targetSubject)){
            $return = ['status' => 'noSeason'];
            return $return;
        }
        $user = $this->Users->find()->where([
            'id' => $usersId,
            'OR' => [
                ['status' => 'noneactivate'],
                ['first_name' => ''],
                ['last_name' => ''],
                ['gender' => ''],
                ['birthday' => ''],
                ['city' => ''],
                ['code_country_id' => ''],
                ['code_language_id' => ''],
            ]])->first();
        //모든기본입력값이 채워져있는상태
        if(is_null($user)){
            //주어진 target으로 등록되어있는지 체크
            $entry = $this->ChallengeEntry->find()->where(['users_id' => $usersId, 'challenge_subject_id' => $targetSubject->id, 'user_type' => $target])->first();

            $return = ['challenge_subject_id' => $targetSubject->id];
            //등록되어있는 상태
            if(!is_null($entry)){
                $return['status'] = 'complete';
                return $return;
            }else{
                $return['status'] = 'basicComplete';
                return $return;
            }
        }
        //기본입력값이 채워지지 않은 상태
        else{
            $return['status'] = 'needBasic';
            return $return;
        }
    }

    /**
     * 계정 삭제 복구 프로세스
     * @param $userAccountId
     */
    public function retreatBoltRequest($userAccountId){

        $connection = ConnectionManager::get('default');
        $connection->begin();

        try{
            $userAccount = $this->UserAccounts->updateAll([
                'status' => 'activate'], ['id' => $userAccountId, 'status' => 'boltrequest'
            ]);
            if($userAccount == false){
                Debugger::log($userAccount->errors(), 'error');
                throw new InternalErrorException('========== UserAccounts updateAll 에러');
            }
            $user = $this->Users->updateAll([
                'status' => 'activate'], ['user_accounts_id' => $userAccountId, 'status' => 'boltrequest'
            ]);
            if($user == false){
                Debugger::log($user->errors(), 'error');
                throw new InternalErrorException('========== Users updateAll 에러');
            }
            $connection->commit();
        }catch(\Exception $e){
            $connection->rollback();
            Debugger::log($e->getMessage(), 'error');
            throw new InternalErrorException($e->getMessage());
        }
    }

    /**
     * 로그인 로그 저장
     * @param null $userAccountId
     */
    public function saveLoginLog($userAccountId = null){
        $users = $this->Users->find()->where(['user_accounts_id' => $userAccountId])->first();

        $conn = ConnectionManager::get('default');
        $conn->begin();

        try{
            $user = $this->Users->patchEntity($users, [
                'signin_modified' => new \DateTime('now')
            ]);
            if(!$this->Users->save($user)){
                Debugger::log($user->errors(), 'error');
                throw new InternalErrorException('========== Users save 에러');
            }
            $log = $this->LogUserSign->newEntity([
                'users_id' => $user->id,
                'device' => 1,
                'sign' => 1
            ]);
            if(!$this->LogUserSign->save($log)){
                Debugger::log($log->errors(), 'error');
                throw new InternalErrorException('========== LogUserSign save 에러');
            }
            $conn->commit();
        }catch (\Exception $e) {
            $conn->rollback();
            Debugger::log($e->getMessage(), 'error');
            throw new InternalErrorException('========== saveLoginLog 중 에러');
        }
    }

    /**
     * 비밀번호 변경시 유효성 확인
     * @param $current
     * @param $new
     * @param $confirm
     * @return bool
     */
    public function passwordValidateCheck($current, $new, $confirm){

        if($current == $new){
            return false;
        }
        if($new != $confirm){
            return false;
        }
        return true;
    }

    /**
     * 사용자 조회
     * @param $id
     * @return \Cake\Datasource\EntityInterface|mixed
     */
    public function inquireUsers($id){
        return $this->Users->get($id);
    }

    public function isJudged($userAccountId, $target, $subjectId = null){
        if(isset($subjectId)){
            $currentSubject = ChallengeService::Instance()->selectEvaluatePeriodSubject($target, $subjectId);
        }else{
            $currentSubject = ChallengeService::Instance()->selectEvaluatePeriodSubject($target);
        }

        //평가시즌인지 확인
        $date = date("Y-m-d");//get date of today
        //$date = '2015-09-11 00:00:00';

        if($currentSubject){
            $userId = $this->getUserByUserAccountId($userAccountId)['id'];
            $voters = TableRegistry::get('ChallengeVoter');
            $voter = $voters->find()->where(['users_id' => $userId, 'challenge_subject_id' => $currentSubject->id])->first();
            //voter에 등록되있는지 확인
            if($voter){
                //voter로 등록 되있을때
                $answers = TableRegistry::get('ChallengeAnswer');
                $answer = $answers->find()->where(['challenge_voter_id' => $voter->id, 'challenge_subject_id' => $currentSubject->id])->first();
                //answer를 등록하였는지 확인
                if($answer){
                    //같은 시즌에 answer를 한번이라도 하였을때
                    $judgeCount = $answers->find()->where([
                        'challenge_voter_id' => $voter->id,
                        'challenge_subject_id' => $currentSubject->id,
                        'DATE(created)' => $date,//today
                        'score >' => 0.00
                    ])->count();

                    $emptyCount = $answers->find()->where([
                        'challenge_voter_id' => $voter->id,
                        'challenge_subject_id' => $currentSubject->id,
                        'DATE(created)' => $date,//today
                        'score' => 0.00
                    ])->count();

                    //평가기간 계산
                    $interval = date_diff($currentSubject->evaluate_start_datetime, $currentSubject->evaluate_closing_datetime);
                    $dateInterval = $interval->format('%a');
                    $hourInterval = $interval->format('%h');
                    if($hourInterval > 0){
                        $dateInterval = $dateInterval + 1;
                    }
                    //voter 타입이 public 인경우
                    if($voter->type == 'public'){
                        //아직 투표를 하지 않은 경우
                        if($voter->count == 0){
                            return $evaluation = 'season';
                        }
                        //챌린지 기간동안 전부 투표한 경우
                        elseif($voter->count == ($dateInterval * 30)){
                            return $evaluation = 'noMore';
                        }
                        //오늘 평가가 끝난상태
                        elseif($judgeCount == 30 and $emptyCount == 0){
                            return $evaluation = 'notToday';
                        }
                        //오늘 평가를 하지 않았거나 남아있는 경우
                        else{
                            return $evaluation = 'more';
                        }
                    }
                    //voter 타입이 모델이나 디자이너인경우
                    else{
                        //챌린지 기간동안 전부 투표한 경우
                        if($voter->count == ($dateInterval * 10)){
                            return $evaluation = 'noMore';
                        }
                        //아직 투표하지 않은 경우
                        elseif($judgeCount == 0){
                            return $evaluation = 'more';
                        }
                        //오늘은 다 투표한 경우
                        else{
                            return $evaluation = 'notToday';
                        }
                    }
                }else{
                    //시즌이나 Answer를 전혀 등록 하지 않았을때
                    return $evaluation = 'season';
                }
            }else{
                //시즌이나 voter로 등록 않되있을때
                return $evaluation = 'season';
            }
        }else{
            //시즌이 아닐때
            return $evaluation = 'notSeason';
        }

    }

    public function thirdStatusCheck($userId, $target){

        $subjectId = ChallengeService::Instance()->isPublished($target)['id'];

        if($subjectId){
            $entry = TableRegistry::get('ChallengeEntry');
            $entries = $entry->find()->where(['challenge_subject_id' => $subjectId, 'users_id' => $userId, 'user_type' => $target])->first();
            if($entries){
                return $third = 'challenger';
            }else{
                return $third = 'public';
            }
        }
        return $third = 'noSeason';

    }



    /**
     * insertPersonalInfo 코드 검증
     *
     * @param $authCode
     * @return bool
     */
    public function selectAuthCodeUserAccount($authCode)
    {
        $userAccount = $this->UserAccounts->find()->where(['authentication_code' => $authCode])->first();
        if($userAccount != null && $userAccount->status == 'activate'){
            $user = $this->getUserByUserAccountId($userAccount['id']);
            if ($user->status == 'activate') {
                return $userAccount;
            }
        }
        return null;
    }

    public function getLangFromHeader($acceptLang){
        $englishId = 7;//default language = English
        if(is_null($acceptLang) or empty($acceptLang[0])){
            $userLangId = $englishId;
        }elseif(strlen($acceptLang[0]) > 2 and strpos($acceptLang[0],'-') == false) {
            $userLangId = $englishId;
        }elseif(substr($acceptLang[0], 0, strpos($acceptLang[0], '-')) > 2){
            $userLangId = $englishId;
        }else{
            $codeLang = TableRegistry::get('CodeLanguage');
            $filterLang1 = $codeLang->find()->where(['iso' => $acceptLang[0]])->first();
            if(is_null($filterLang1)){
                $shortenLang = (substr($acceptLang[0], 0, 2));
                $filterLang2 = $codeLang->find()->where(['iso' => $shortenLang])->first();
                if(is_null($filterLang2)){
                    $userLangId = $englishId;
                }else{
                    $userLangId = $filterLang2->id;
                }
            }else{
                $userLangId = $filterLang1->id;
            }
        }
        return $userLangId;
    }

    public function checkEmailIfExist($email, $signup = null){
        $userAccount = $this->getUserAccountsByEmail($email);
        //가입자 존재시
        if($userAccount) {
            //탈퇴요청한 사용자
            if($userAccount->status == 'boltrequest'){
                return 'bolt';
            }
            //다른가입수단으로 가입시
            if($userAccount->signup == 'normal') $userAccount->signup = 'email';
            return $userAccount->signup;
        }
        //가입자 미 존재시
        else{
            return 'new';
        }
    }

    /**
     * CodeCountry 조회
     *
     * @return array
     */
    public function selectCodeCountry() {
        return $this->CodeCountry->find('list', [
            'limit' => 250,
            'keyField' => 'id',
            'valueField' => 'country_name'
        ])->toArray();
    }

    /**
     * CodeLanguage 조회
     *
     * @return array
     */
    public function selectCodeLanguage() {
        return $this->CodeLanguage->find('list', [
            'limit' => 50,
            'keyField' => 'id',
            'valueField' => 'language'
        ])->toArray();
    }

    /**
     * profile 화면 Users 정보 업데이트
     *
     * @param $usersId
     * @param $data
     */
    public function updateUser($usersId, $data) {
//        if (empty($data['code_country_id'])) {
//            $data['code_country_id'] = 0;
//            $data['country'] = '';
//        } else {
//            $data['code_country_id'] = $data['code_country_id'];
//            $codeCountry = $this->selectCodeCountry();
//            $data['country'] = $codeCountry[$data['code_country_id']];
//        }

//        $data['birthday'] = $data['year'].'-'.$data['month'].'-'.$data['day'];

        $user = $this->Users->patchEntity($this->Users->get($usersId), $data);

        if ( !$users = $this->Users->save($user)){  // User 기본 정보 UPDATE
            Debugger::log($user->errors(), 'error');
            Debugger::log($user, 'error');
            Debugger::log($data, 'error');
            throw new InternalErrorException();
        }
    }

    /** 휴대폰 번호 업데이트  */
    public function updatePhoneNum($userId,$phoneNum){
        $user = $this->Users->patchEntity($this->Users->get($userId), ['phone_number'=>$phoneNum]);
        if ( !$users = $this->Users->save($user)){  // User 기본 정보 UPDATE
            Debugger::log($user);
            Debugger::log($users);
        }
    }

    /**
     * 이용약관 동의 처리
     * @param $userId\
     */
    public function termsUpdate($userId){
        $userEntity = $this->Users->find()->where(["id"=>$userId])->first();
        $newUserEntity = $this->Users->patchEntity($userEntity,['fcctv_terms_yn'=>'Y']);
        $this->Users->save($newUserEntity);
        Debugger::log($newUserEntity);
    }


    /**
     * 휴대폰번호 중복 체크
     * @param $phoneNo
     * @return bool
     */
    public function mobileNumCheck($phoneNo){
        $encPhoneNo = EncryptService::Instance()->encrypt($phoneNo);
        $isExsist  =$this->Users->find()->where(["phone_number"=>$encPhoneNo])->first();
        if($isExsist == null){
            return true;
        }
        return false;
    }


    /**
     * 닉네임 중복 체크
     * @param $phoneNo
     * @return bool
     */
    public function nickNameCheck($nickname){
        $isExsist  =$this->Users->find()->where(["nickname"=>$nickname])->first();
        if($isExsist == null){
            return true;
        }
        return false;
    }

    public function kmcert($data,$randNo){
        $CurTime = date('YmdHis');
        $RandNo = rand(100000, 999999);

        //요청 번호 생성
        $reqNum = $CurTime.$RandNo;

        // KMC 본인인증 범용서비스 샘플소스 STEP02

        //01.입력값 변수로 받기
        $cpId       = "FCCM1001";        // 회원사ID
        $urlCode    = KMSURLCODEAUTHJOIN; //$_REQUEST['urlCode'];     // URL 코드
        $certNum    = $reqNum;     // 요청번호
        $date       = $CurTime;        // 요청일시
        $certMet    = "M";     // 본인인증방법
        $birthDay   = "";	// 생년월일
        $gender     = "";		// 성별
        $name       = $data['userName'];        // 성명
        $phoneNo    = $data['phoneNum'];		// 휴대폰번호
        $phoneCorp 	= "";	// 이동통신사
        $nation     = "";      // 내외국인 구분
        $plusInfo   = $randNo;	// 추가DATA정보
        $extendVar  = "0000000000000000";       // 확장변수

        // [ 입력값 유효성 검증 ]----------------------------------------------------------------------------------
        // 비정상적인 호출, XSS공격, SQL Injection 방지를 위해 입력값 유효성 검증 후 서비스를 호출해야 함

        // 성명 (값이 있는 경우에는 최대 30byte까지만 유효)
        if(strlen($name) > 0 ){
            if(strlen($name) > 30 ){
                Debugger::log("성명 비정상 ($name)");
                return false;
            }
            else{
                if(preg_match('/[<>]/', $name)){  //태그문자 금지
                    Debugger::log("성명 비정상1 ($name)");
                    return false;
                }
            }
        }

        // 휴대폰번호 (값이 있는 경우에는 숫자 10 또는 11자리까지만 유효)
        if(strlen($phoneNo) > 0 ){
            if(preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9]/u', $phoneNo) || strlen($phoneNo) < 10 || strlen($phoneNo) > 11){
                Debugger::log("휴대폰번호 비정상 ($phoneNo)");
                return false;
            }
        }
        //----------------------------------------------------------------------------------------------------------
        // [ certNum 주의사항 ]--------------------------------------------------------------------------------------
        // 1. 본인인증 결과값 복호화를 위한 키로 활용되므로 중요함.
        // 2. 본인인증 요청시 중복되지 않게 생성해야함. (예-시퀀스번호)
        // 3. certNum값 생성 후 쿠키 또는 Session에 저장한 후 본인인증 결과값 수신 후 복호화키로 사용함.
        // 4. 아래 샘플은 쿠키를 사용하지 않았음.
        //----------------------------------------------------------------------------------------------------------
        $name = str_replace(" ", "+", $name) ;  //성명에 space가 들어가는 경우 "+"로 치환하여 암호화 처리
        //03. tr_cert 데이터변수 조합 (서버로 전송할 데이터 "/"로 조합)

        $name = iconv("UTF-8","EUC-KR", $name);
        $tr_cert	= $cpId . "/" . $urlCode . "/" . $certNum . "/" . $date . "/" . $certMet . "/" . $birthDay . "/" . $gender . "/" . $name . "/" . trim($phoneNo) . "/" . $phoneCorp . "/" . $nation . "/" . $plusInfo . "/" . $extendVar;

        //암호화모듈 호출
        if (extension_loaded('ICERTSecu')) {
            //04. 1차암호화
            $enc_tr_cert = ICertSeed(1,0,'',$tr_cert);
            //05. 변조검증값 생성
            $enc_tr_cert_hash = ICertHMac($enc_tr_cert);
            //06. 2차암호화
            $enc_tr_cert = $enc_tr_cert . "/" . $enc_tr_cert_hash . "/" . "0000000000000000";
            $enc_tr_cert = ICertSeed(1,0,'',$enc_tr_cert);

        }else{
            Debugger::log("암호화모듈 호출 실패!!!");
            return false;
        }

        return $enc_tr_cert;
    }

    public function kmcertDecrypt($data,$randKey){
        $rec_cert       = $data['rec_cert'];
        $cookieCertNum  = $data['certNum']; // certNum값을 쿠키 또는 Session을 생성하지 않았을때 certNum 수신처리
        $iv = $cookieCertNum;  // certNum값을 쿠키 또는 Session을 생성하지 않았을때 수신한 certNum을 복호화키에 세팅


        if (extension_loaded('ICERTSecu')) {
            //01.인증결과 1차 복호화
            $rec_cert = ICertSeed(2,0,$iv,$rec_cert);

            //02.복호화 데이터 Split (rec_cert 1차암호화데이터 / 위변조 검증값 / 암복화확장변수)
            $decStr_Split = explode("/", $rec_cert);

            $encPara  = $decStr_Split[0];		//rec_cert 1차 암호화데이터
//            $encMsg   = $decStr_Split[1];		//위변조 검증값

            //03.인증결과 2차 복호화
            $rec_cert = ICertSeed(2,0,$iv,$encPara);

            //04. 복호화 된 결과자료 "/"로 Split 하기
            $decStr_Split = explode("/", $rec_cert);

//            $certNum    = $decStr_Split[0];
            $date       = $decStr_Split[1];
//            $CI         = $decStr_Split[2];
//            $phoneNo    = $decStr_Split[3];
//            $phoneCorp  = $decStr_Split[4];
            $birthDay   = $decStr_Split[5];
//            $gender     = $decStr_Split[6];
//            $nation     = $decStr_Split[7];
//            $name       = $decStr_Split[8];
            $result     = $decStr_Split[9];
//            $certMet    = $decStr_Split[10];
//            $ip         = $decStr_Split[11];
//            $M_name     = $decStr_Split[12];
//            $M_birthDay = $decStr_Split[13];
//            $M_Gender   = $decStr_Split[14];
//            $M_nation   = $decStr_Split[15];
            $plusInfo   = $decStr_Split[16];
//            $DI         = $decStr_Split[17];

            //05. CI,DI 복호화
//            if(strlen($CI) > 0){
//                $CI = ICertSeed(2,0,$iv,$CI);
//            }
//            if(strlen($DI) > 0){
//                $DI = ICertSeed(2,0,$iv,$DI);
//            }

        }else{
            Debugger::log("암호화모듈 호출 실패!!!");
            return false;
        }

        //	현재 서버 시각 구하기
        $end_date = date("YmdHis");
        $start_date = $date;

        //mktime()을 만들기 위해 각 시간 단위로 분할
        $yy = substr($end_date, 0, 4);
        $mm = substr($end_date, 4, 2);
        $dd = substr($end_date, 6, 2);
        $hh = substr($end_date, 8, 2);
        $ii = substr($end_date, 10, 2);
        $ss = substr($end_date, 12, 2);

        //mktime()을 만들기 위해 DB에서 불러온 datetime 값을 시간 단위로 분할
        $yy_start = substr($start_date, 0, 4);
        $mm_start = substr($start_date, 4, 2);
        $dd_start = substr($start_date, 6, 2);
        $hh_start = substr($start_date, 8, 2);
        $ii_start = substr($start_date, 10, 2);
        $ss_start = substr($start_date, 12, 2);

        $toDate = mktime($hh, $ii, $ss, $mm, $dd, $yy);
        $fromDate = mktime($hh_start, $ii_start, $ss_start, $mm_start, $dd_start, $yy_start);
        $timediff = intval(($toDate - $fromDate) / 60);		// 분

        if ( $timediff < -30 || 30 < $timediff  ){
            Debugger::log("비정상적인 접근입니다. (요청시간경과)");
            return false;
        }

        if($result!="Y" || $randKey!=$plusInfo){
            Debugger::log("인증 실패");
            return false;
        }

        return $birthDay;
    }

    public function getUserAllDetail($userAccountId){
        $contain = ['UserAccounts'];
        $userDetail = $this->Users->find()
            ->contain($contain)
            ->where(['UserAccounts.id' => $userAccountId])
            ->first();
        return $userDetail;
    }

    public function findUserEmail($data){
        $result = $this->Users->find()->contain("UserAccounts")->where($data)->first();
        return $result;
    }
}
