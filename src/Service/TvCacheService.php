<?php
namespace App\Service;

error_reporting(E_STRICT);
/**
 * Tv 서비스 인터페이스
 * User: Makun
 * Date: 16. 2. 1.
 * Time: 오후 1:54
 */

use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\Controller\Component;


/**
 * Interface AddressInterface
 * Tv 인터 페이스
 * @package App\Service
 */
interface TvCacheInterface
{

}

/**
 *  조회 서비스
 * Class UserService
 * @package App\Service
 */
class TvCacheService implements TvCacheInterface {

    private $McMainHistory;
    private $McVideoInfo;
    private $ChImageFile;

    private $EncryptService;

    private function __construct(){
        $this->McMainHistory = TableRegistry::get("McMainHistory");
        $this->McVideoInfo = TableRegistry::get("McVideoInfo");
        $this->ChImageFile = TableRegistry::get("ChImageFile");
        $this->EncryptService = EncryptService::Instance();
    }

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new TvCacheService();
        }
        return $inst;
    }

    private function getMainData($preview,$historyId){
        $cacheCheck = true;
        $mainCache = false;
        if(!CACHEDUSE){
            $cacheCheck = false;
        }else {
            $mainCache = Cache::read("hotItem", "products");
        }
        if (($mainCache=== false) ||$mainCache==null || $preview!=null || $cacheCheck== false) {
            $historyWhere = array();
            if ($preview == null) {
                $historyWhere['view_yn'] = 'y';
            }else{
                $historyWhere['id'] = $historyId;
            }
            $mainData = $this->McMainHistory->find()->where($historyWhere)->order(["id"=>"desc"])->first();
            $data = json_decode($mainData->json);
            if($preview==null && $cacheCheck==true) {
                Cache::write("hotItem", $data,"products");
            }
        }else{
            $data = $mainCache;
        }
        return $data;
    }

    public function getMainContents($preview,$historyid){
        $data = $this->getMainData($preview,$historyid);
        return $data;
    }


    public function getSchedule($page=1,$limit= 6){

        $schedule = $this->McVideoInfo->find()->where(['view_yn'=>'y'])->page($page,$limit)->order(['id'=>'desc']);

        $imageIds = array();
        $videoInfos = array();
        foreach($schedule as $video){
            $tempArray = array(
                'id' => base64_encode($this->EncryptService->encrypt($video->id)),
                'category' => $video->code,
                'title' =>$video->title,
                'info' =>$video->video_info,
                'video_id' =>$video->video_id,
                'image_id' => $video->main_image_id
            );
            $videoInfos[] = $tempArray;
            $imageIds[] = $video->main_image_id;
        }

        $mainImages = $this->ChImageFile->find()->where(['image_id in'=>$imageIds,"type"=>'image']);

        $imagesUrlArray = array();
        foreach($mainImages as $image){
            $imagesUrlArray[$image->image_id] = $image->murl;
        }

        $result = array();
        foreach($videoInfos as $video){
            $tempArray = $video;
            $tempArray['video_image_url'] = $imagesUrlArray[$video['image_id']];
            $result['videoInfos'][] = $tempArray;
        }

        $scale = 10;

        $totalCount = $this->McVideoInfo->find()->where(['view_yn'=>'y'])->order(['id'=>'desc'])->count();
        $totalPageNo = ceil($totalCount/$limit);
        $start_page = ((ceil($page/$scale)-1)*$scale)+1;
        $end_page = $start_page+$scale-1;
        $prev_page = ($start_page >1)?$start_page-1:0;
        $next_page = ($totalPageNo>$end_page)?($end_page+1):0;
        $end_page = ($end_page >=$totalPageNo)?$totalPageNo:$end_page;

        $scopeArr = array();
        for($i=$start_page;$i<=$end_page;$i++){
            $scopeArr[]= $i;
        }


        $pageArray= array(
            'prev' =>$prev_page,
            'next' =>$next_page,
            'scope' =>$scopeArr,
            'nowPage' => $page,
            'totalCount' => $totalCount
        );

        $result['pagination'] = $pageArray;

        return $result;
    }
}
