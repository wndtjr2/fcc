<?php
namespace App\Service;

/**
 * 배송지 조회 서비스 인터페이스
 * User: Makun
 * Date: 16. 2. 1.
 * Time: 오후 1:54
 */

use App\Model\Table\ChallengeEntryTable;
use Cake\Core\Exception\Exception;
use Cake\Error\Debugger;
use Cake\ORM\TableRegistry;
use Cake\Controller\Component;


/**
 * Interface AddressInterface
 * 배송지 조회 인터 페이스
 * @package App\Service
 */
interface AddressInterface
{
    /**
     * 사용자 주소지 정보 호출
     * @param $userId
     * @return array
     */
    public function selectAllAddress($userId);

    /**
     * 전체 컨트리 코드 가져오기
     * @return object
     */
    public function selectCountryCode();

    /**
     * 사용자 주소 상세 호출
     * @param $addrId 주소 아이디
     * @param $userId 사용자 아이디
     * @return array
     */
    public function getAddressDetail($addrId,$userId);

    /**
     * 사용자 주소 신규 저장
     * @param $data 주소 데이터
     * @param $userId 사용자 아이디
     * @return array
     */
    public function saveAddress($data,$userId);

    /**
     * 사용자 주소 삭제
     * @param $addrId 주소 아이디
     * @param $userId 사용자 아이디
     * @return mixed
     */
    public function deleteAddress($addrId,$userId);

    /**
     * 기본 주소 설정
     * @param $addrId 주소 아이디
     * @param $userId 사용자 아이디
     * @return mixed
     */
    public function makeDefault($addrId , $userId);
}

/**
 * 사용자 조회 서비스
 * Class UserService
 * @package App\Service
 */
class AddressService implements AddressInterface {

    /**
     * @var \Cake\ORM\Table
     */
    private $McUserAddrInfo;

    /**
     * @var \Cake\ORM\Table
     */
    private $CountryCode;

    private function __construct() {
        $this->McUserAddrInfo = TableRegistry::get("McUserAddrInfo");
        $this->CountryCode = TableRegistry::get("CodeCountry");
    }

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new AddressService();
        }
        return $inst;
    }

    /** 전체 컨트리 코드 가져오기 */
    public function selectCountryCode(){
        return $this->CountryCode->find()->toArray();
    }

    /** 전체 배송 목록 */
    public function selectAllAddress($userId){
        $where = array(
            'users_id' => $userId
        );
        $this->McUserAddrInfo->primaryKey("country_code");
        return $this->McUserAddrInfo->find()->contain("CodeCountry")->where($where)->order(["default_addr" => 'desc']);
    }

    /**  배송지 상세 */
    public function getAddressDetail($addrId,$userId){
        $this->McUserAddrInfo->primaryKey("country_code");
        $detail = $this->McUserAddrInfo->find()->contain("CodeCountry")->where(['McUserAddrInfo.id'=>$addrId,'McUserAddrInfo.users_id'=>$userId])->first();
        $detailArr = $detail->toArray();
        $detailArr['phone_decrypt'] = $detail->phone_decrypt;

        return $detailArr;
    }

    /** 배송지 저장 */
    public function saveAddress($data,$userId){

        if(isset($data['default_addr'])){
            $data['default_addr']='y';
            /*기본주소일 경우 다른 주소는 기본주소에서 제외한다*/
            $this->McUserAddrInfo->updateAll(['default_addr'=>'n'],['users_id'=>$userId]);
        }else{
            $data['default_addr']='n';
        }
        $type = $data['type'];
        $addrId = $data['id'];

        unset($data['type']);

        $mcUserAddrEntity = "";
        if($type=="regist"){
            unset($data['id']);
            $data['users_id'] = $userId;
            $mcUserAddrEntity = $this->McUserAddrInfo->newEntity($data);
        }else if($type=="update"){
            $oldEntity = $this->McUserAddrInfo->get($addrId);
            unset($oldEntity['users_id']);
            $mcUserAddrEntity = $this->McUserAddrInfo->patchEntity($oldEntity,$data);
        }
        try{
            $mcUserAddr = $this->McUserAddrInfo->save($mcUserAddrEntity);
        }catch (Exception $e){
            Debugger::log($e->getMessage());
        }

        if(!$mcUserAddr){
            //Debugger::log($mcUserAddrEntity);
            return false;
        }

        return true;
    }

    /** 배송지 삭제 */
    public function deleteAddress($addrId,$userId){
        $oldEntity = $this->McUserAddrInfo->get($addrId);
        if($userId==$oldEntity['users_id']){
            $rtn = $this->McUserAddrInfo->delete($oldEntity);
            return $rtn;
        }else{
            return 'err';
        }
    }

    /** 기본 배송주소 만들기 */
    public function makeDefault($addrId , $userId){
        $oldEntity = $this->McUserAddrInfo->find()->where(['users_id'=>$userId , 'default_addr'=>'y'])->first();
        if(!is_null($oldEntity)){
            $mcUserAddrEntity = $this->McUserAddrInfo->patchEntity($oldEntity,['default_addr'=>'n']);
            $mcUserAddr = $this->McUserAddrInfo->save($mcUserAddrEntity);
            if($mcUserAddr){
                $newEntity = $this->McUserAddrInfo->find()->where(['id'=>$addrId])->first();
                $mcUserAddrNewEntity = $this->McUserAddrInfo->patchEntity($newEntity,['default_addr'=>'y']);
                $newResult = $this->McUserAddrInfo->save($mcUserAddrNewEntity);
                if($newResult){
                    return true;
                }
            }
        }else{
            $newEntity = $this->McUserAddrInfo->find()->where(['id'=>$addrId])->first();
            $mcUserAddrNewEntity = $this->McUserAddrInfo->patchEntity($newEntity,['default_addr'=>'y']);
            $newResult = $this->McUserAddrInfo->save($mcUserAddrNewEntity);
            if($newResult){
                return true;
            }
        }
        return false;
    }
    /*
    * 도로명 검색
    */
    public function searchAddress($data){
        $ch = curl_init();
        $url = 'http://openapi.epost.go.kr/postal/retrieveNewAdressAreaCdSearchAllService/retrieveNewAdressAreaCdSearchAllService/getNewAddressListAreaCdSearchAll'; /*URL*/
        $queryParams = '?' . urlencode('ServiceKey') . '=9WF%2FsPion6i88SH66VAvi9Spfv4EEkz30%2BoYTC2LvagyvmyuUqSo2EzmOQhh3ndrHmF0MPyC04rKO%2Bj8zgdwlg%3D%3D'; /*Service Key*/
        //$queryParams .= '&' . urlencode('searchSe') . '=' . urlencode('road'); /*검색구분(읍/면/동, 도로명, 우편번호)*/
        $queryParams .= '&' . urlencode('srchwrd') . '=' . urlencode($data['sch']); /*검색어*/
        $queryParams .= '&' . urlencode('countPerPage') . '=' . '50'; /*검색건수*/
        $queryParams .= '&' . urlencode('currentPage') . '=' . $data['currentPage']; /*페이지 번호*/
        curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
