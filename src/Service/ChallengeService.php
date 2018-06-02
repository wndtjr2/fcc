<?php
/**
 * Created by PhpStorm.
 * User: hcs
 * Date: 15. 8. 5.
 * Time: 오전 11:39
 */

namespace App\Service;
use App\Util\RegistrationNumberUtil;
use Cake\Error\Debugger;
use Cake\Network\Email\Email;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Challenge Service
 */
class ChallengeService {

    /**
     * @var \App\Model\Table\ChallengeSubjectTable
     */
    private $ChallengeSubject;

    /**
     * @var \App\Model\Table\ChallengeEntryTable
     */
    private $ChallengeEntry;

    /**
     * @var \App\Model\Table\ChallengerMembersTable
     */
    private $ChallengerMembers;

    /**
     * @var \App\Model\Table\PostTable
     */
    private $Post;

    /**
     * @var \App\Model\Table\PostImagesTable
     */
    private $PostImages;

    /**
     * @var \App\Model\Table\ProfileModelTable
     */
    private $ProfileModel;

    /**
     * @var \App\Model\Table\ProfileDesignTable
     */
    private $ProfileDesign;

    /**
     * @var \App\Model\Table\UsersTable
     */
    private $Users;

    /**
     * @var \App\Model\Table\PostContentsTable
     */
    private $PostContents;

    /**
     * @var \App\Model\Table\PostContentsTable
     */
    private $ChallengeVoter;

    private function __construct() {
        $this->ChallengeSubject = TableRegistry::get("ChallengeSubject");
        $this->ChallengeEntry = TableRegistry::get("ChallengeEntry");
        $this->ChallengerMembers = TableRegistry::get("ChallengerMembers");
        $this->Post = TableRegistry::get("Post");
        $this->PostImages = TableRegistry::get("PostImages");
        $this->ProfileModel = TableRegistry::get("ProfileModel");
        $this->ProfileDesign = TableRegistry::get("ProfileDesign");
        $this->Users = TableRegistry::get("Users");
        $this->PostContents = TableRegistry::get("PostContents");
        $this->ChallengeVoter = TableRegistry::get("ChallengeVoter");
    }

    public static function Instance() {
        static $instance = null;
        if ($instance == null) {
            $instance = new ChallengeService();
        }
        return $instance;
    }


    public function isEvaluationFinished($subjectId){
        $currentDate = date("Y-m-d H:i:s");
        return $this->ChallengeSubject->find()
            ->where([
                'evaluate_closing_datetime <=' => $currentDate,
                'id' => $subjectId
            ])->first();
    }

    public function isPublished($target){
        $currentDate = date("Y-m-d H:i:s");
        $return = $this->ChallengeSubject->find()
            ->where([
                'publication_datetime <=' => $currentDate,
                'target' => $target
            ])->first();
        return $return;
    }

    public function getNowEvaluationInfo($target = 'model'){
        $nowDate = date("Y-m-d H:i:s");
        $conditions = array(
            'evaluate_start_datetime <=' => $nowDate,
            'evaluate_closing_datetime >=' => $nowDate,
            'confirm' =>'y',
            'target' => $target
        );

        $challengeInfo = $this->ChallengeSubject->find()->where($conditions)->first();
        $data = array();
        if($challengeInfo==null){
            $data['isEvalTime'] = 'n';
        }else{
            $data['isEvalTime'] = 'y';
            $data['challengeId'] = $challengeInfo->id;
            $data['target'] = $challengeInfo->target;
            $data['challengeTitle'] = $challengeInfo->title;
            $data['challengeDesc'] = $challengeInfo->description;
            $data['eval_start_date'] = date_format($challengeInfo->evaluate_start_datetime,'Y-m-d H:i:s');
            $data['eval_end_date'] = date_format($challengeInfo->evaluate_closing_datetime,'Y-m-d H:i:s');
            $data['announcedDate'] = date_format($challengeInfo->publication_datetime,'Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * 로그인 유저의 현재 기준 챌린지 엔트리 조회
     *
     * @param string $usersId
     * @return mixed
     */
    public function selectCurrentChallengeEntryByUserId($usersId,$currSubject = null) {
        if(is_null($currSubject)){
            $currSubject = $this->selectRegistPeriodSubject()['id'];
        }
        if(empty($currSubject) or is_null($currSubject)){
            return false;
        }else{
            return $this->ChallengeEntry->find()
                ->where([
                    'users_id'=> $usersId
                    , 'challenge_subject_id' => $currSubject
                    , 'qualification' => 'grant'
                ])->first();
        }
    }


    /**
     * 챌린지 엔트리 INSERT
     *
     * @param $usersId
     */
    public function insertChallengeEntryPublic($usersId,$subjectId) {
        // 현재 챌린지 확인
        $currChallengeEntry = $this->ChallengeEntry->newEntity([
            'challenge_subject_id' => $subjectId
            ,'users_id' => $usersId
            ,'user_type' => 'public'
            ,'members' => 'single'
            ,'team_name' => ' '
            ,'qualification' => 'grant'
        ]);
        if ( !$this->ChallengeEntry->save($currChallengeEntry)) {
            Debugger::log($currChallengeEntry->errors(), 'error');
            throw new InternalErrorException('========== ChallengeEntry save 에러');
        }
    }

    /**
     * 챌린지 엔트리 INSERT OR UPDATE
     *
     * @param string $usersId
     * @param string $userType public / designer / model
     * @param null $data
     * @return bool
     */
    public function saveChallengeEntry($usersId, $userType, $data = null) {
        // 챌린지 엔트리 조회
        $currChallengeEntry = $this->selectCurrentChallengeEntryByUserId($usersId);
        if (is_null($currChallengeEntry)) {     // INSERT
            $currSubject = $this->selectRegistPeriodSubject();
            $currChallengeEntry = $this->ChallengeEntry->newEntity([
                'challenge_subject_id' => $currSubject['id']
                ,'users_id' => $usersId
                ,'user_type' => $userType
                ,'members' => 'single'
                ,'team_name' => ' '
                ,'qualification' => 'grant'
            ]);
            if ( !$this->ChallengeEntry->save($currChallengeEntry)) {
                Debugger::log($currChallengeEntry->errors(), 'error');
                throw new InternalErrorException('========== ChallengeEntry INSERT 에러');
            }

        } else {    // UPDATE
            $currChallengeEntry['user_type'] = $userType;
            if (isset($data)) {     // design
                $currChallengeEntry['members'] = $data['members'];
                $currChallengeEntry['team_name'] = $data['team_name'];
            }
            if (!$this->ChallengeEntry->save($currChallengeEntry)) {
                Debugger::log($currChallengeEntry->errors(), 'error');
                throw new InternalErrorException('========== ChallengeEntry UPDATE 에러');
            }
        }
    }


    /**
     * usersId 로 Post 조회
     *
     * @param string $usersId
     * @param null|string $subjectId
     * @return mixed
     */
    public function selectPostByUserId($usersId, $subjectId = null) {
        $where = [
            'users_id' => $usersId
            //,'confirm' => 'allow' // 관리자 차단사용자의 경우 사용자 자신은 차단 여부를 알 수 없도록
        ];
        if (isset($subjectId)) {
            $where['challenge_subject_id'] = $subjectId;
        }
        return $this->Post->find()->where($where)->first();
    }

    /**
     * postId 로 Post 조회
     *
     * @param string $postId
     * @param null|string $usersId
     * @return mixed
     */
    public function selectPost($postId, $usersId = null) {
        $where = [
            'id' => $postId
            //,'confirm' => 'allow' // 관리자 차단사용자의 경우 사용자 자신은 차단 여부를 알 수 없도록
        ];
        if(isset($usersId)){
            $where['users_id'] = $usersId;
        }
        return $this->Post->find()->where($where)->first();
    }


    /**
     * Post 테이블 INSERT
     *
     * @param array $data
     * @param string $usersId
     * @return mixed
     */
    public function insertPost($data, $usersId) {
        // 현재 챌린지 확인
        $currSubject = $this->selectRegistPeriodSubject();
        if(empty($currSubject) or is_null($currSubject)){
            $errorMsg = '============= 등록 가능한 챌린지 서브젝트 없음';
            Debugger::log($errorMsg, 'error');
            //throw new BadRequestException($errorMsg);     // 400 에러
        }

        $currPost = $this->Post->newEntity([
            'challenge_subject_id' => $currSubject['id']
            ,'users_id' => $usersId
            ,'confirm' => 'allow'
            ,'type' => 'challenge'
            ,'view_count' => 0
            ,'like_count' => 0
            ,'contents' => ' '  // post_contents 테이블 text_contents 필드로 대체 됨
            ,'video_link' => $data['video_link']
        ]);

        $rtn = $this->Post->save($currPost);
        if (!$rtn) {
            Debugger::log($currPost->errors(), 'error');
            throw new InternalErrorException('========== Post save 에러');
        }
        return $rtn->id;
    }


    /**
     * 모델 정보 입력 중 profile_model 테이블 INSERT
     *
     * @param array $data
     * @param string $postId
     */
    public function insertProfileModel($data, $postId) {
        // 실제 단위를 DB ENUM 2자리값으로 (inch -> in)
        $data = $this->modelUnitToEnum($data);

        $data['post_id'] = $postId;
        $profile_model = $this->ProfileModel->newEntity($data);
        if (!$this->ProfileModel->save($profile_model)) {
            Debugger::log($profile_model->errors(), 'error');
            throw new InternalErrorException('========== ProfileModel save 에러');
        }
    }

    /**
     * 모델 프로필 조회
     *
     * @param string $postId
     * @param null|string $usersId
     * @return mixed
     */
    public function selectProfileModel($postId) {
        $model = $this->ProfileModel->find()->where(['post_id' => $postId])->first();
        // DB ENUM 2자리 값을 실제 단위로 (in -> inch)
        $model = $this->EnumToModelUnit($model);
        return $model;
    }

    /**
     * 모델 정보 수정 중 Post 테이블
     *
     * @param string $video_link
     * @param string $postId
     * @param string $usersId
     */
    public function updatePost($video_link, $postId, $usersId) {
        $post = $this->selectPost($postId, $usersId);
        if(is_null($post)){
            Debugger::log('error: 수정 권한 없음. usersId : '. $usersId, 'error');
            throw new UnauthorizedException();     // 401 에러
        } else {
            $post['video_link'] = $video_link;
            if (!$this->Post->save($post)) {
                Debugger::log($post->errors(), 'error');
                throw new InternalErrorException('========== Post save 에러');
            }
        }
    }

    /**
     * 모델 정보 수정 중 profile_model 테이블
     *
     * @param array $data
     * @param string $postId
     */
    public function updateProfileModel($data, $postId) {
        $model = $this->selectProfileModel($postId);
        // 실제 단위를 DB ENUM 2자리값으로 (inch -> in)
        $data = $this->modelUnitToEnum($data);
        $model = $this->ProfileModel->patchEntity($model, $data);
        if (!$this->ProfileModel->save($model)) {
            Debugger::log($model->errors(), 'error');
            throw new InternalErrorException('========== ProfileModel save 에러');
        }
    }

    /**
     * profile_design 테이블 업데이트
     *
     * @param $data
     * @param $postId
     */
    public function updateProfileDesign($data, $postId) {
        $design = $this->selectProfileDesign($postId);
        $design = $this->ProfileDesign->patchEntity($design, $data);
        if (!$this->ProfileDesign->save($design)) {
            Debugger::log($design->errors(), 'error');
            throw new InternalErrorException('========== ProfileDesign save 에러');
        }
    }

    /**
     * post_contents 테이블 업데이트
     *
     * @param $data
     * @param $postId
     */
    public function updatePostContents($data, $postId) {
        $target = $this->selectPostContents($postId);
        $target['text_contents'] = $data['text_contents'];
        if ( !$this->PostContents->save($target)) {
            Debugger::log($target, 'error');
            throw new InternalErrorException('========== PostContents->save 에러');
        }
    }


    /**
     * 해당 포스트가 현재 등록기간인지 조회 (삭제 가능 여부)
     *
     * @param string $postId
     * @return bool
     */
    public function isRegistPeriodPost($postId) {
        $post = $this->Post->get($postId);
        if ($this->subjectCheck($post['challenge_subject_id']) == 'registration') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 모델 정보 삭제 중 ChallengeEntry를 일반 평가자로 전환
     *
     * @param string $usersId
     * @param string $postId
     * @throws InternalErrorException
     */
    public function updateChallengeEntryToPublic($usersId, $postId) {
        $post = $this->selectPost($postId);

        $challenge_entry = $this->ChallengeEntry->find()->where([
            'challenge_subject_id' => $post['challenge_subject_id']
            ,'users_id' => $usersId
        ])->first();

        $challenge_entry['user_type'] = 'public';
        $challenge_entry['members'] = 'single';
        $challenge_entry['team_name'] = '';

        // 작품 삭제시 엔트리 정보 로그 기록
        Debugger::log($challenge_entry);

        if(!$this->ChallengeEntry->save($challenge_entry)){
            Debugger::log($challenge_entry, 'error');
            throw new InternalErrorException('========== ChallengeEntry save 에러');
        }
    }


    /**
     * 모델 정보 삭제 중 ProfileModel table DELETE
     *
     * @param string $postId
     * @param string $usersId   본인 검증 용도
     * @throws InternalErrorException
     */
    public function deleteModel($postId, $usersId) {
        $target = $this->ProfileModel->find()->contain(['Post'])
            ->where(['Post.id' => $postId, 'Post.users_id' => $usersId])->first();
        if (!$this->ProfileModel->delete($target)) {
            Debugger::log($target->errors(), 'error');
            throw new InternalErrorException('========== ProfileModel delete 에러');
        }
    }


    /**
     * 모델 정보 삭제 중 Post table DELETE
     *
     * @param string $postId
     * @param string $usersId   본인 검증 용도
     * @throws InternalErrorException
     */
    public function deletePost($postId, $usersId) {
        $target = $this->Post->find()->where(['id' => $postId, 'users_id' => $usersId])->first();
        if (!$this->Post->delete($target)) {
            Debugger::log($target->errors(), 'error');
            throw new InternalErrorException('========== Post->delete 에러');
        }
    }

    /**
     * postId에 해당하는 user의 정보 조회
     *
     * @param $postId
     * @return mixed
     */
    public function selectUserByPostId($postId) {
        $post = $this->selectPost($postId);
        $rtn = $this->Users->get($post['users_id']);
        $rtn['video_link'] = $post['video_link'];
        return $rtn;
    }


    /**
     * 실제 단위를 DB ENUM 2자리값으로 (inch -> in)
     *
     * @param $data
     * @return mixed
     */
    public function modelUnitToEnum($data) {
        if ($data['height_unit'] == 'inches') { $data['height_unit'] = 'in'; }
        if ($data['height_unit'] == 'feet') { $data['height_unit'] = 'ft'; }
        if ($data['body_unit'] == 'inches') { $data['body_unit'] = 'in'; }
        if ($data['weight_unit'] == 'lbs') { $data['weight_unit'] = 'lb'; }

        if ($data['shoes_unit'] == 'US') { $data['shoes_unit'] = 'us'; }
        if ($data['shoes_unit'] == 'Europe') { $data['shoes_unit'] = 'eu'; }
        if ($data['shoes_unit'] == 'UK') { $data['shoes_unit'] = 'uk'; }
        if ($data['shoes_unit'] == 'cm') { $data['shoes_unit'] = 'jp'; }
        if ($data['shoes_unit'] == 'mm') { $data['shoes_unit'] = 'ko'; }
        return $data;
    }

    /**
     * DB ENUM 2자리 값을 실제 단위로 (in -> inch)
     *
     * @param $model
     * @return mixed
     */
    public function EnumToModelUnit($model) {
        if ($model['height_unit'] == 'in') { $model['height_unit'] = 'inches'; }
        if ($model['height_unit'] == 'ft') { $model['height_unit'] = 'feet'; }
        if ($model['body_unit'] == 'in') { $model['body_unit'] = 'inches'; }
        if ($model['weight_unit'] == 'lb') { $model['weight_unit'] = 'lbs'; }

        if ($model['shoes_unit'] == 'us') { $model['shoes_unit'] = 'US'; }
        if ($model['shoes_unit'] == 'eu') { $model['shoes_unit'] = 'Europe'; }
        if ($model['shoes_unit'] == 'uk') { $model['shoes_unit'] = 'UK'; }
        if ($model['shoes_unit'] == 'jp') { $model['shoes_unit'] = 'cm'; }
        if ($model['shoes_unit'] == 'ko') { $model['shoes_unit'] = 'mm'; }
        return $model;
    }

    /**
     * 등록 기간인 ChallengeSubject 조회
     *
     * @param null|string $target
     * @return null|ChallengeSubject
     */
    public function selectRegistPeriodSubject($target = null) {
        $currentDate = date("Y-m-d H:i:s");
        $where = [
            '\''.$currentDate.'\' BETWEEN regist_start_datetime AND regist_closing_datetime'
            , 'confirm'=> 'y'
            , 'period_option'=>'y'
        ];
        if (isset($target)) {
            $where['target'] = $target;
        }
        return $this->ChallengeSubject->find()->where($where)->order(['evaluate_start_datetime' => 'DESC'])->first();
    }

    /**
     * 평가 기간인 ChallengeSubject 조회
     *
     * @param null|string $target
     * @param null $subjectId
     * @return ChallengeSubject|null
     */
    public function selectEvaluatePeriodSubject($target, $subjectId = null) {
        $currentDate = date("Y-m-d H:i:s");
        if($subjectId==null) {
            $where = [
                '\'' . $currentDate . '\' BETWEEN evaluate_start_datetime AND evaluate_closing_datetime'
                , 'confirm' => 'y'
                , 'period_option' => 'y'
            ];
        }
        if(isset($subjectId)){
            $where['id'] = $subjectId;
        }
        if(isset($target)) {
            $where['target'] = $target;
        }
        return $this->ChallengeSubject->find()->where($where)->order(['evaluate_start_datetime' => 'DESC'])->first();
    }


    /**
     * 평가 발표기간 조회
     * @param $target model || designer
     * @return object
     */
    public function getLastPublishTime($target){
        $currentDate = date("Y-m-d H:i:s");
        $where = array(
            'publication_datetime <=' =>$currentDate,
            'target' => $target,
            'confirm'=> 'y',
            'period_option'=>'y'
        );
        return $this->ChallengeSubject->find()->where($where)->order(['publication_datetime'=>'DESC'])->first();
    }


    /**
     * 해당 타겟인 가장 최근의 ChallengeSubject를 조회
     *
     * @param string $target
     * @return mixed
     */
    public function selectRecentSubjectByTarget($target) {
        return $this->ChallengeSubject->find()
            ->where(['confirm'=> 'y','period_option'=>'y','target' => $target])->order(['evaluate_start_datetime' => 'DESC'])->first();
    }

    /**
     * 작품 정보 등록 중 challenge_members
     * @param $data
     * @param $usersId
     */
    public function saveMembers($data, $usersId) {
        for ($i = 0; $i < 3 ; $i++) {    // 최대 3명 반복
            if (isset($data['members_id'.$i])) {
                if ( !empty($data['email'.$i])) {     // 원래 있던것, 데이터가 있다면
                    // UPDATE
                    $targetMember = $this->ChallengerMembers->get($data['members_id'.$i]);
                    $targetMember['first_name'] = $data['first_name'.$i];
                    $targetMember['last_name'] = $data['last_name'.$i];
                    $targetMember['email'] = $data['email'.$i];
                    if ( !$this->ChallengerMembers->save($targetMember)) {
                        Debugger::log($targetMember, 'error');
                        throw new InternalErrorException($targetMember->errors());
                    }
                } else {        // 원래 있던것, 데이터가 없다면
                    // DELETE
                    $targetMember = $this->ChallengerMembers->get($data['members_id'.$i]);
                    if ( !$this->ChallengerMembers->delete($targetMember)) {
                        Debugger::log($targetMember, 'error');
                        throw new InternalErrorException($targetMember->errors());
                    }
                }
            } else if ( !empty($data['email'.$i])) {  // 새로 추가된것
                // INSERT
                $member = $this->ChallengerMembers->newEntity([
                    'users_id' => $usersId
                    ,'first_name' => $data['first_name'.$i]
                    ,'last_name' => $data['last_name'.$i]
                    ,'email' => $data['email'.$i]
                ]);
                if ( !$this->ChallengerMembers->save($member)) {
                    Debugger::log($member->errors(), 'error');
                    throw new InternalErrorException($member->errors());
                }
            } else {
                break;
            }
        }
    }

    /**
     * 작품 정보 등록 중 post_contents
     *
     * @param $data
     * @param $postId
     */
    public function insertPostContents($data, $postId) {
        $postContents = $this->PostContents->newEntity([
            'post_id' => $postId
            ,'text_contents' => $data['text_contents']
        ]);
        if ( !$this->PostContents->save($postContents)) {
            Debugger::log($postContents->errors(), 'error');
            throw new InternalErrorException($postContents->errors());
        }
    }

    /**
     * 작품 정보 등록 중 profile_design
     *
     * @param $data
     * @param $postId
     */
    public function insertProfileDesign($data, $postId) {
        $design = $this->ProfileDesign->newEntity([
            'post_id' => $postId
            ,'category' => $data['category']
            ,'title' => $data['title']
            ,'summary' => $data['summary']
            ,'currency' => ' '  // 현재 가격 사용하지 않음
            ,'price' => 0       // 현재 가격 사용하지 않음
            ,'target' => $data['target']
        ]);

        if ( !$this->ProfileDesign->save($design)) {
            Debugger::log($design->errors(), 'error');
            throw new InternalErrorException($design->errors());
        }

    }

    /**
     * 디자인 조회
     *
     * @param $postId
     * @return mixed
     */
    public function selectProfileDesign($postId) {
        $design = $this->ProfileDesign->find()->where(['post_id' => $postId])->first();
        return $design;
    }


    /**
     * 팀 멤버 정보 조회
     *
     * @param $userId
     * @return array
     */
    public function selectMembers($userId) {
        return $this->ChallengerMembers->find()->where(['users_id' =>  $userId])->toArray();
    }

    /**
     * challenge_entry 조회
     *
     * @param $usersId
     * @param null $subjectId
     * @return mixed
     */
    public function selectChallengeEntry($usersId, $subjectId = null) {
        $where = [
            'users_id' => $usersId
            ,'qualification' => 'grant'
        ];
        if (isset($subjectId)) {
            $where['challenge_subject_id'] = $subjectId;
        }
        return $this->ChallengeEntry->find()->where([$where])->first();
    }

    /**
     * post_contents 조회
     *
     * @param $postId
     * @return mixed
     */
    public function selectPostContents($postId) {
        return $this->PostContents->find()->where(['post_id' => $postId])->first();
    }

    /**
     * post_contents 삭제
     *
     * @param $postId
     * @param $usersId
     */
    public function deletePostContents($postId, $usersId) {
        $target = $this->PostContents->find()->contain(['Post'])
            ->where(['Post.id' => $postId, 'Post.users_id' => $usersId])->first();
        if (!$this->PostContents->delete($target)) {
            Debugger::log($target, 'error');
            throw new InternalErrorException('========== PostContents->delete 에러');
        }
    }

    /**
     * profile_design 삭제
     * @param $postId
     * @param $usersId
     */
    public function deleteDesign($postId, $usersId) {
        $target = $this->ProfileDesign->find()->contain(['Post'])
            ->where(['Post.id' => $postId, 'Post.users_id' => $usersId])->first();
        if (!$this->ProfileDesign->delete($target)) {
            Debugger::log($target->errors(), 'error');
            throw new InternalErrorException('========== ProfileDesign delete 에러');
        }
    }

    /**
     * challenger_members 삭제
     *
     * @param $usersId
     */
    public function deleteMembers($usersId) {
        $this->ChallengerMembers->deleteAll(['users_id' => $usersId]);
    }

    /**
     * challenge_subject_검색
     * @param $target
     * @return mixed
     */
    public function getSubject($target){
        $currentDate = date("Y-m-d H:i:s");
        $where = [
            '\''.$currentDate.'\' BETWEEN regist_start_datetime AND publication_datetime',
            'confirm'=> 'y',
            'period_option'=>'y',
            'target' => $target
        ];
        return $this->ChallengeSubject->find()->where($where)->order(['publication_datetime' => 'DESC'])->first();
    }

    /**
     * 가장 최근의 challenge_subject 조회
     * @param $target
     * @return mixed
     */
    public function resentSubject($target){
        return $this->ChallengeSubject->find()->where([
            'target' => $target
        ])->order(['publication_datetime' => 'DESC'])->first();
    }

    /**
     * challenge_subject 기간별 상태 조회
     *
     * @param $subjectId
     * @return string
     */
    public function subjectCheck($subjectId){
        $currentDate = date("Y-m-d H:i:s");
        $subject = $this->ChallengeSubject->get($subjectId);
        $intDate = strtotime($currentDate);
        //등록기간 전인지 확인
        if(strtotime(date_format($subject->regist_start_datetime,'Y-m-d H:i:s')) > $intDate){
            return 'before';
        }

        //등록기간인지 확인
        if((strtotime(date_format($subject->regist_start_datetime,'Y-m-d H:i:s')) < $intDate) and (strtotime(date_format($subject->regist_closing_datetime,'Y-m-d H:i:s')) > $intDate)){
            return 'registration';
        }

        //평가기간인지 확인
        if((strtotime(date_format($subject->evaluate_start_datetime,'Y-m-d H:i:s')) < $intDate) and (strtotime(date_format($subject->evaluate_closing_datetime,'Y-m-d H:i:s')) > $intDate)){
            return 'evaluation';
        }

        if((strtotime(date_format($subject->evaluate_closing_datetime,'Y-m-d H:i:s')) < $intDate) and (strtotime(date_format($subject->publication_datetime,'Y-m-d H:i:s')) > $intDate)){
            return 'calculation';
        }

        //결과발표기간 이후 인지 확인
        if(strtotime(date_format($subject->publication_datetime,'Y-m-d H:i:s')) < $intDate){
            return 'publication';
        }
        return 'error';
    }

    public function isUserParticipated($usersId, $subjectId, $target) {
        return $this->ChallengeEntry->find()
            ->where([
                'users_id'=> $usersId,
                'challenge_subject_id' => $subjectId,
                'user_type' => $target,
                'qualification' => 'grant'
            ])->first();
    }

    /**
     * 평가 기간 개인정보등록시 voter 로 등록
     * @param $usersId
     * @param $subjectId
     * @return bool
     */
    public function saveChallengeVoter($usersId,$subjectId){
        $newVoter = $this->ChallengeVoter->newEntity([
            'challenge_subject_id' => $subjectId,
            'users_id' => $usersId,
            'type' => 'public',
            'count'=> 0
        ]);
        if($this->ChallengeVoter->save($newVoter)){
            return true;
        }else{
            Debugger::log('========== ChallengeVoter->save 에러', 'error');
            Debugger::log('========== challenge_subject_id ['.$subjectId.'] , users_id ['.$usersId.']', 'error');
            return false;

        }
    }

    /**
     * ChallengeVoter 조회
     * @param string $usersId
     * @param string $subjectId
     * @return mixed
     */
    public function selectChallengeVoter($usersId, $subjectId) {
        return $this->ChallengeVoter->find()->where([
            'challenge_subject_id' => $subjectId
            , 'users_id' => $usersId
        ])->first();
    }





    function sendRegNumEmail($regNum, $usersId) {
        // 발송 이메일 조회
        $user = $this->Users->find()->contain('UserAccounts')->where(['Users.id' => $usersId])->first();
        $domain = Router::url('/', true);
        try{
            $email = new Email();
            $email->transport('brick')
                ->to($user['user_account']['emailDecrypt'])//email
                ->from(FCC)
                ->emailFormat('html')
                ->template('reg_num')
                ->subject(__('Thank you for registering,{0} {1}',[$user['first_name'],$user['last_name'].'!']))
                ->viewVars(array(
                    'first_name' => $user['first_name']
                    ,'last_name' => $user['last_name']
                    ,'regNum' => $regNum
                    ,'domain' => $domain
                ));
            $email->send();
        }catch (Exception $e){
            Debugger::log($e->getMessage(), 'error');
            Debugger::log('$email : '.$email, 'error');
            throw new InternalErrorException($e->getMessage());
        }


    }


}