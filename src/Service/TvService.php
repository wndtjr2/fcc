<?php
namespace App\Service;

error_reporting(E_STRICT);
/**
 * Tv 서비스 인터페이스
 * User: Makun
 * Date: 16. 2. 1.
 * Time: 오후 1:54
 */

use Cake\Error\Debugger;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\Controller\Component;


/**
 * Interface AddressInterface
 * Tv 인터 페이스
 * @package App\Service
 */
interface TvInterface
{
    /**
     * 비디오 카테고리
     * @return mixed
     */
    public function getVideoCategory();

    /**
     * 날짜별 스케쥴
     * @param $date Y-m-d
     * @return mixed
     */
    public function getSchedule($date);

    /**
     * 달력 정보 가져오기
     * @param $date Y-m-d
     * @return mixed
     */
    public function getCalendarInfo($date);

    /**
     * 이전달 다음달 정보 가져오기
     * @param $getDate Y-m-d
     * @param $status next,prev
     * @return mixed
     */
    public function getNextPrevCalendar($getDate,$status);

    /**
     * 현재 방송 정보
     * @return mixed
     */
    public function nowOnAir();

    /**
     * search
     * @param $keyword 검색 키워드
     * @return mixed
     */
    public function search($keyword);

    /**
     * SNS 공유 정보
     * @param $url      공유 url
     * @param $timeLine 스케줄 정보
     * @param $imageArray   이미지 정보
     * @return mixed
     */
    public function getIndexShareInfo($url,$timeLine,$imageArray);

    /**
     * 상세 페이지 SNS 공유 정보
     * @param $videoId  비디오 아이디
     * @param $url      상세 url
     * @param $title    제목
     * @param $description  설명
     * @param $imgPath  이미지 경로
     * @return mixed
     */
    public function getDetailShareInfo($videoId,$url,$title,$description,$imgPath);

    /**
     * 상품정보
     * @param $videoId 비디오 아이디
     * @return mixed
     */
    public function getProductList($videoId);

    /**
     * 상품 상세
     * @param $productCode 상품 코드
     * @return mixed
     */
    public function getProductDetail($productCode);

    /**
     * 댓글 정보 가져오기
     * @param $videoId 비디오 아이디
     * @param $page 현재 페이지
     * @param int $limit  최대 표시 갯수 기본 10
     * @param null $userId 사용자 아이디
     * @return mixed
     */
    public function getComment($videoId,$page,$limit=10,$userId = null);

    /**
     * 댓글 저장
     * @param $data 댓글 내용 및 정보
     * @return mixed
     */
    public function commentSave($data);

    /**
     * 해당월의 스케줄이 존재 하는지 여부
     * 2016-03-10 사용 중지
     * 2016-03-17 사용 재개
     * @param $date Y-m-d
     * @return array
     */
    public function haveSchedule($date);

    /**
     * 해당일의 스케쥴이 있는지 확인
     * @param $date Y-m-d
     * @return mixed
     */
    public function dayhaveSchedule($date);
}

/**
 *  조회 서비스
 * Class UserService
 * @package App\Service
 */
class TvService implements TvInterface {

    private $McVideoProductInfo;

    private $McVideoInfo;

    private $ProductService;

    private $ChImageFile;

    private $McVideoComment;

    private $McTimVideoViwInfo;

    private $ChCode;

    private $monthString;

    private $ChProductOption;

    private function __construct(){
        $this->McVideoProductInfo = TableRegistry::get("McVideoProductInfo");
        $this->McVideoInfo = TableRegistry::get("McVideoInfo");
        $this->ChImageFile = TableRegistry::get("ChImageFile");
        $this->McVideoComment = TableRegistry::get("McVideoComment");
        $this->McTimVideoViwInfo = TableRegistry::get("McTimVideoViwInfo");
        $this->ChCode = TableRegistry::get("ChCode");
        $this->ProductService = ProductService::Instance();
        $this->ChProductOption = TableRegistry::get("ChProductOption");
        $this->monthString = array(
            '01'=>__('January'),
            '02'=>__('February'),
            '03'=>__('March'),
            '04'=>__('April'),
            '05'=>__('May'),
            '06'=>__('June'),
            '07'=>__('July'),
            '08'=>__('August'),
            '09'=>__('September'),
            '10'=>__('October'),
            '11'=>__('November'),
            '12'=>__('December')
        );

    }

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new TvService();
        }
        return $inst;
    }

    /** 비디오 카테고리 코드 */
    public function getVideoCategory(){
        $videoCode= $this->ChCode->find()->where(['cds_kind'=>'MC_VIDEO_CODE','use_flag'=>'Y','del_flag'=>'N'])->order(['seq'=>'ASC']);
        $result = array();
        foreach($videoCode as $code){
            $result[$code->code] = $code->name;
        }
        return $result;
    }

    /**
     * 날짜 타입 변경 Y-m-d -> unixtimestamp
     * @param $stringDate Y-m-d
     * @return unixtimestamp
     */
    private function stringToTimeStamp($stringDate){
        $ymdHis = explode(" ",$stringDate);
        $ymd = explode("-",$ymdHis[0]);
        $his = explode(":",$ymdHis[1]);
        $timeStamp = mktime($his[0],$his[1],$his[2],$ymd[1],$ymd[2],$ymd[0]);
        return $timeStamp;
    }

    /**
     * 해당월의 스케줄이 존재 하는지 여부
     */
    public function haveSchedule($date){

        $dateArr = explode("-",$date);

        $year = $dateArr[0];
        $month = $dateArr[1];
        $lastDay = date("t", mktime(0,0,0,$month,1,$year));

        $condition = array(
            "st_dtm >=" => $year."-".$month."-01"." 00:00:00",
            "ed_dtm <=" => $year."-".$month."-".$lastDay." 23:59:59"
        );

//        $pastYearMonth = date("Y-m", mktime(0,0,0,date("m")-1,1,date("Y")));
//        $nextYearMonth = date("Y-m", mktime(0,0,0,date("m")+1,1,date("Y")));
//        $nextMonthLastDay = date("t", mktime(0,0,0,date("m")+1,1,date("Y")));

//        $condition = array(
//            "st_dtm >=" => $pastYearMonth."-01"." 00:00:00",
//            "ed_dtm <=" => $nextYearMonth."-".$nextMonthLastDay." 23:59:59"
//        );

        $allSchedule = $this->McTimVideoViwInfo->find()->where($condition)->order(['st_dtm'=>'ASC']);
        $totalSchedule = array();

        foreach($allSchedule as $past){
            $totalSchedule[]=date_format($past->st_dtm,'Y-m-d');
        }
        $result = array_unique($totalSchedule);
        return $result;
    }

    /** 해당 날짜 스케쥴이 있는지 확인 */
    public function dayhaveSchedule($date){
        $condition = array(
            "st_dtm >=" => $date." 00:00:00",
            "ed_dtm <=" => $date." 23:59:59"
        );
        $dayScheduleCnt = $this->McTimVideoViwInfo->find()->where($condition)->count();
        if($dayScheduleCnt>0){
            return true;
        }
        return false;
    }

    /** 요청 날짜의 방송 스케쥴 호출 */
    public function getSchedule($date){

        $condition = array(
            "st_dtm >=" => $date." 00:00:00",
            "ed_dtm <=" => $date." 23:59:59",
        );

        $schedule = $this->McTimVideoViwInfo->find()->contain(['McVideoInfo'])->where($condition)->order(['st_dtm'=>'ASC']);

        $imageIds = array();

        $timeLine = array();
        $nowHour = date("Y-m-d H:i:s");
        $nowTimeStamp = $this->stringToTimeStamp($nowHour);

        foreach($schedule as $videoInfo){

            $stHour = $this->stringToTimeStamp(date_format($videoInfo->st_dtm,'Y-m-d H:i:s'));//.":00");
            $edHour = $this->stringToTimeStamp(date_format($videoInfo->ed_dtm,'Y-m-d H:i:s'));//.":59");

            $onAir = '';

            if($nowTimeStamp >= $stHour && $nowTimeStamp <= $edHour){
                $onAir = 'onAir';
            }else if($nowTimeStamp < $stHour){
                $onAir = 'standby';
            }else if($nowTimeStamp > $edHour){
                $onAir = 'passed';
            }

            $timeKey=  substr(date_format($videoInfo->st_dtm,'Y-m-d H:i:s'),11,5);

            $timeLine[$timeKey] = array(
                'videoInfo' => $videoInfo,
                'onAir' => $onAir,
            );
            $imageIds[] = $videoInfo->mc_video_info->main_image_id;
        }

        $mainImages = $this->ChImageFile->find()->where(['image_id in'=>$imageIds,"type"=>'image']);

        $imagesArray = array();


        foreach($mainImages as $image){
            $imagesArray[$image->image_id] = FILE_PRD_URI.str_replace("original","512",$image->path);
        }

        $onAirVideoInfo = $this->nowOnAir();
        $mainStartTimeStamp = array();

        $onAirImage=  "";
        if($onAirVideoInfo){
            $mainStartTimeStamp = strtotime(date_format($onAirVideoInfo->st_dtm,'Y-m-d H:i:s'));
            $mainImgObj = $this->ChImageFile->find()->where(['image_id'=>$onAirVideoInfo->mc_video_info->main_image_id,"type"=>'image'])->first();
            $onAirImage = FILE_PRD_URI.str_replace("original","1024",$mainImgObj->path);
        }

        return array(
            'mainVideo' => $onAirVideoInfo,
            'mainStartTimeStamp' => $mainStartTimeStamp,
            'mainImages' => $imagesArray,
            'onAirImage' =>$onAirImage,
            'timeLine' => $timeLine,
        );
    }

    /**  달력 정보 가져오기 */
    public function getCalendarInfo($date){
        $today = date("Y-m-d");
        if(!$date){
            $date = $today;
        }

        $selDateArr = explode("-",$date);
        $selYear = $selDateArr[0];
        $selMonth = $selDateArr[1];

        $dayOfTheWeek = ['SUN','MON','TUE','WED','THU','FRI','SAT'];

        $selDateTimeStamp = strtotime($date);

        // 1. 총일수 구하기
        $last_day = date("t", $selDateTimeStamp);
        // 2. 시작요일 구하기
        $start_week = date("w", strtotime($selYear."-".$selMonth."-01"));
        $calendar = array(
            'lastDay' =>$last_day,                                  //선택일 기준
            'startWeek' =>$start_week,                              //선택일 기준
            'dayAfterTomorrowTimeStamp' =>strtotime("+1 day")       //오늘 기준
        );

        $todayArr = explode("-",$today);
        $isNow = "now";
        if(strtotime($todayArr[0]."-".$todayArr[1]."-01")>strtotime($selYear."-".$selMonth."-01")){
            $isNow = "prev";
        }else if(strtotime($todayArr[0]."-".$todayArr[1]."-01")<strtotime($selYear."-".$selMonth."-01")){
            $isNow = "next";
        }
        return array(
            "isNow" =>$isNow,
            "selDate" =>$date,
            "today" =>$today,
            "dateArr" =>$selDateArr,
            "selDateTimeStamp" => $selDateTimeStamp,
            "calendar" =>$calendar,
            "dayOfTheWeek" =>$dayOfTheWeek,
            "monthString" =>  $this->monthString
        );
    }

    public function getNextPrevCalendar($getDate,$status){
        $getDateTimeStamp = strtotime($getDate);
        $date = date("Y-m-d");

        switch($status){
            case "prev":
                $date = date("Y-m",strtotime("-1 month",$getDateTimeStamp));
                break;
            case "next":
                $date = date("Y-m",strtotime("+1 month",$getDateTimeStamp));
                break;
        }
        $dateArr = explode("-",$date);
        $year = $dateArr[0];
        $month = $dateArr[1];
        $last_day = date("t", mktime(0, 0, 0, $month, 1, $year));
        $start_week = date("w", strtotime($year."-".$month."-01"));

        return array(
            'reqDate' => $date,
            'lastDay' =>$last_day,
            'startWeek' =>$start_week,
            'getMonthString' => $this->monthString[$month],
            'month' => $month,
            'year' => $year,
            'today' => date("Y-m-d"),
            'dayAfterTomorrowTimeStamp' =>strtotime("+1 day")       //오늘 기준
        );
    }

    /** 현재 방송중인 컨텐츠 */
    public function nowOnAir(){
        $date = date("Y-m-d H:i:s");
        $condition = array(
            "st_dtm <=" => $date,
            "ed_dtm >=" => $date
        );
        $onAir = $this->McTimVideoViwInfo->find()->contain(['McVideoInfo'])->where($condition)->order(['st_dtm'=>'ASC'])->first();

        return $onAir;
    }

    /** 검색 */
    public function search($keyword){

        $onAirVideoInfo = $this->nowOnAir();

        $imageIds = array();
        $mainStartTimeStamp = array();
        if($onAirVideoInfo!=null) {
            $mainStartTimeStamp = strtotime(date_format($onAirVideoInfo->st_dtm, 'Y-m-d H:i:s'));
            $imageIds[] = $onAirVideoInfo->mc_video_info->main_image_id;
        }

        $date = date("Y-m-d H:i:s");


        $tomorrowAfterTimeStamp  = strtotime("+1 day");
        $tomorrowAfterDate = date("Y-m-d",$tomorrowAfterTimeStamp)." 23:59:59";

        $searchList = $this->McVideoInfo->find()->contain("McTimVideoViwInfo")->where(["McVideoInfo.title Like"=>'%'.$keyword.'%',"McVideoInfo.view_yn"=>"y","McTimVideoViwInfo.st_dtm <="=>$tomorrowAfterDate])->order(["McTimVideoViwInfo.st_dtm"=>'ASC']);

        $processingSearchList = array();

        $now = strtotime($date);

        foreach($searchList as $search){
            $timeKey = date_format($search->mc_tim_video_viw_info->st_dtm,'H:i');
            $dateKey = date_format($search->mc_tim_video_viw_info->st_dtm,'Y-m-d');
            $stTime = strtotime(date_format($search->mc_tim_video_viw_info->st_dtm,'Y-m-d H:i:s'));
            $isDisabled = ($now < $stTime)?true:false;
            $isOnAir = ($onAirVideoInfo!=null && $onAirVideoInfo->id == $search->mc_tim_video_viw_info->id)?true:false;
            $processingSearchList[$dateKey][$timeKey]['data'] =$search;
            $processingSearchList[$dateKey][$timeKey]['timId'] =$search->mc_tim_video_viw_info->id;
            $processingSearchList[$dateKey][$timeKey]['isDiabled'] = $isDisabled;
            $processingSearchList[$dateKey][$timeKey]['isOnAir'] = $isOnAir;
            $imageIds[] = $search->main_image_id;
        }
        $mainImages = $this->ChImageFile->find()->where(['image_id in'=>$imageIds,"type"=>'image']);

        $imagesArray = array();

        $onAirImage = "";
        foreach($mainImages as $image){
            $onAirImage = FILE_PRD_URI.$image->path;
            $imagesArray[$image->image_id] = FILE_PRD_URI.str_replace("original","1024",$image->path);
        }

        $result = array(
            'mainVideo' => $onAirVideoInfo,
            'mainStartTimeStamp' => $mainStartTimeStamp,
            'searchList' => $processingSearchList,
            'images' => $imagesArray,
            'onAirImage' => $onAirImage,
        );

        return $result;
    }

    /**
     * index sns share info
     * 공유 시점에서 가장 가까운 시작 시간을 공유
     */
    public function getIndexShareInfo($url,$timeLine,$imageArray){
        $nowTimeStamp = strtotime(date("Y-m-d H:i:s"));
        $minTime= 0;
        $count = 0;
        $nearStartVideo = array();
        foreach($timeLine as $key => $val){
            $stDtmTimeStamp = strtotime(date_format($val['videoInfo']->st_dtm,"Y-m-d H:i:s"));
            $calTime = 0;
            if($nowTimeStamp > $stDtmTimeStamp){
                $calTime = $nowTimeStamp-$stDtmTimeStamp;
            }else if($nowTimeStamp < $stDtmTimeStamp){
                $calTime = $stDtmTimeStamp-$nowTimeStamp;
            }
            if($count==0){
                $minTime = $calTime;
                $nearStartVideo = $val['videoInfo'];
            }
            if($minTime > $calTime){
                $minTime = $calTime;
                $nearStartVideo =  $val['videoInfo'];
            }
            $count++;
        }

        $shareTitle = "";
        $shareDesc = "미디어커머스 FCC TV";
        $shareImage = "";
        if(sizeof($nearStartVideo) > 0) {
            $startDate = date_format($nearStartVideo->st_dtm, "Y-m-d H:i");
            $endDate = date_format($nearStartVideo->ed_dtm, "H:i");
            $shareDesc = "방송일시 : " . $startDate . "~" . $endDate . "\n";
            $shareDesc .= (strlen($nearStartVideo->mc_video_info->video_info) > 116) ? substr($nearStartVideo->mc_video_info->video_info, 0, 116) : $nearStartVideo->mc_video_info->video_info;
            $shareDesc .= "\n.... 미디어커머스 FCC TV";
            $shareTitle = $nearStartVideo->mc_video_info->title;
            $shareImage = $imageArray[$nearStartVideo->mc_video_info->main_image_id];
        }
        return  array(
            'url' => $url,
            'title' => $shareTitle,
            'image' => $shareImage,
            'desc' => $shareDesc,
        );
    }

    /** * tv 상세 sns share info */
    public function getDetailShareInfo($videoId,$url,$title,$description,$imgPath){
//        $videoSchedule = $this->getVideoSchedule($videoId);
        $shareDesc = (strlen($description)>116)?substr($description,0,116):$description;
        $shareDesc .="\n.... 미디어커머스 FCC TV";

        $shareImg = FILE_PRD_URI.str_replace("original","512",$imgPath);

        $snsShare = array(
            'url' => $url,
            'title' => $title,
            'image' => $shareImg,
            'desc' => $shareDesc,
            'video' => true,
        );

        return $snsShare;
    }

    /** 비디오 스케쥴 */
    public function getVideoSchedule($videoId , $mc_tim_video_id = null){
        if($mc_tim_video_id == null) {
            $videoSchedule = $this->McTimVideoViwInfo->find()->where(['mc_video_info_id' => $videoId]);
            $nowTimeStamp = strtotime(date("Y-m-d H:i:s"));
            $minTime = 0;
            $count = 0;

            $nearSchedule = array();
            foreach ($videoSchedule as $schedule) {
                $stDtmTimeStamp = strtotime(date_format($schedule->st_dtm, "Y-m-d H:i:s"));
                $calTime = 0;
                if ($nowTimeStamp > $stDtmTimeStamp) {
                    $calTime = $nowTimeStamp - $stDtmTimeStamp;
                } else if ($nowTimeStamp < $stDtmTimeStamp) {
                    $calTime = $stDtmTimeStamp - $nowTimeStamp;
                }
                if ($count == 0) {
                    $minTime = $calTime;
                    $nearSchedule = $schedule;
                }
                if ($minTime > $calTime) {
                    $minTime = $calTime;
                    $nearSchedule = $schedule;
                }
                $count++;
            }

            return $nearSchedule;
        }else{
            return $this->McTimVideoViwInfo->find()->where(['mc_video_info_id' => $videoId,"id"=>$mc_tim_video_id])->first();
        }
    }

    /** 상품 리스트 */
    public function getProductList($videoId){

        $videoInfo = $this->McVideoInfo->find()->where(["McVideoInfo.id"=>$videoId])->first();

        if($videoInfo==null){
            return false;
        }

        $videoFile = $this->ChImageFile->find()->where(["type" => 'video', 'image_id' => $videoInfo->video_id])->first();
        $videoMainImage = $this->ChImageFile->find()->where(["type" => 'image', 'image_id' => $videoInfo->main_image_id])->order(["seq" => 'asc'])->first();
        $videoUrl = ($videoFile != null) ? FILE_PRD_URI . $videoFile->path : "";

        $videoProductInfos = $this->McVideoProductInfo->find()->where(['mc_video_info_id'=>$videoId]);

        $productCodes = array();
        foreach($videoProductInfos as $videos){
            $productCodes[] = $videos->product_code;
        }

        $products = $this->ProductService->getProductList($productCodes,'desc');

        $comments = $this->McVideoComment->find()->contain(['Users'])->where(["mc_video_info_id"=>$videoId,"del_yn"=>"N"]);

        $totalCommentCount = $comments->count();

        $comment = $comments->page(1,10)->order(['McVideoComment.id'=>'desc']);

        $productSimpleInfo = array();

        $designerInfo =array();


        foreach($products as $product) {
            $productSimpleInfo[$product->product_code]['stock'] =0;

            $prdOptionInfo = array();



            foreach ($product->ch_product_option as $prdOption) {

                $optionArr = explode(";", $prdOption->name);
                $colorName = $optionArr[1];
                $size = array(
                    'size' => $optionArr[2],
                    'price' => $prdOption->price,
                    'stock' => $prdOption->stock,
                    'max' => $prdOption->max_purchase,
                    'prdOptCode' => $prdOption->product_option_code
                );
                $prdOptionInfo[$colorName][] = $size;
                $productSimpleInfo[$prdOption->product_code]['stock'] += $prdOption->stock;
            }
            $productSimpleInfo[$prdOption->product_code]['price'] = $product->price;
            $productSimpleInfo[$prdOption->product_code]['name'] = $product->name;
            $productSimpleInfo[$prdOption->product_code]['mainImage'] = FILE_PRD_URI.str_replace("original","512",$product->ch_image->ch_image_file[0]->path);
            $productSimpleInfo[$prdOption->product_code]['userName'] = $product->designer_name;
            $productSimpleInfo[$prdOption->product_code]['encId'] = base64_encode(EncryptService::Instance()->encrypt($product->product_code));
            $productSimpleInfo[$prdOption->product_code]['detail'] = $prdOptionInfo;
            $did = base64_encode(EncryptService::Instance()->encrypt($product->designer_id));
            $designerInfo[$did] = trim($product->designer_name);
        }

        $designerInfo = array_unique($designerInfo);

        return array(
          'video' => $videoInfo,
          'videoMainImage' =>$videoMainImage,
          'comments' => $comment,
          'commentCount' =>$totalCommentCount,
          'videoUrl' => $videoUrl,
          'productPriceInfo' => $productSimpleInfo,
          'designerInfo' =>$designerInfo
        );
    }

    /** 상품 상세 */
    public function getProductDetail($productCode){
        $product = $this->ProductService->getProduct($productCode);

        $images = array();
        if($product->ch_image->ch_image_file==null){
            Debugger::log("ch_image is null");
            Debugger::log("productObj : ".$product);
            Debugger::log("ProductCode :".$productCode);
        }else {
            foreach ($product->ch_image->ch_image_file as $imageFile) {
                $images[] = FILE_PRD_URI . str_replace("original", "512", $imageFile->path);
            }
        }

        if(isset($product->sub_image_id)) {
            $subImages = $this->ChImageFile->find()->where(['image_id' => $product->sub_image_id]);
            foreach($subImages as $imageFile){
                $images[] = FILE_PRD_URI.str_replace("original","512",$imageFile->path);
            }
        }

        $optionColor = array();
        $optionSize = array();

        if($product->ch_product_option == null){
            Debugger::log("ch_product_option is null");
            Debugger::log("productObj : ".$product);
            Debugger::log("ProductCode :".$productCode);
        }else {
            foreach ($product->ch_product_option as $option) {
                $optionArr = explode(";", $option->name);
                $sizeTempArr = array(
                    'name' => $optionArr[2],
                    'color' => $optionArr[1],
                    'price' => $option->price,
                    'stock' => $option->stock,
                    'prdOptCode' => $option->product_option_code
                );
                $optionColor[] = $optionArr[1];
                $optionSize[] = $sizeTempArr;
            }
        }
        $optionColor = array_unique($optionColor);
        $reOptionColor = array();
        foreach($optionColor as $color){
            $reOptionColor[] = $color;
        }
        $result = array(
            'productCode' =>$productCode,
            'userName' => $product->designer_name,
            'images' => $images,
            'title' => $product->name,
            'price' => $product->ch_product_option[0]->price,
            'optionColor' => $reOptionColor,
            'optionSize' => $optionSize,
            'description' => nl2br($product->content)
        );

        return $result;
    }

    public function getCommentCount($videoId){
        return $this->McVideoComment->find()->contain("Users")->where(["mc_video_info_id"=>$videoId,"del_yn"=>"N"])->count();
    }

    /** 코멘트 가져오기 */
    public function getComment($videoId,$page,$limit=10,$userId = null){
        $comments = $this->McVideoComment->find()->contain(['Users'])->where(["mc_video_info_id"=>$videoId,"del_yn"=>"N"])->page(1,$page*$limit)->order(['McVideoComment.id'=>'desc']);
        $result = array();
        foreach($comments  as $comment){
            $userProfileImage = ($comment->user->image_path!="")?FILE_URI.$comment->user->image_path:"/_res/img/navigation/rnb_user_default.png";

            $temp = array(
                'commentId' => $comment->id,
                'comment' => nl2br($comment->comment),
                'commentOrg' => $comment->comment,
                'usersId' => $comment->users_id,
                'userName' => $comment->user->nickname,
                'userProfile' => $userProfileImage,
                'isMine' => ($comment->users_id==$userId)?true:false
            );
            $result[] =$temp;
        }
        return $result;
    }

    /** 댓글 저장 */
    public function commentSave($data){
        $data['comment'] = strip_tags($data['comment']);
        $data['del_yn'] = "N";
        if($data['mode']=="regist"){
            $commentEntitiy = $this->McVideoComment->newEntity($data);
        }else{
            $oldCommentEntitiy = $this->McVideoComment->get($data['id']);
            $commentEntitiy = $this->McVideoComment->patchEntity($oldCommentEntitiy,$data);
        }

        $result = array(
            'result' => false,
            'msg' => 'fail'
        );
        if($this->McVideoComment->save($commentEntitiy)){
            $result = array(
                'result' => true,
                'msg' => 'success'
            );
        }
        return $result;
    }

    public function getStockCount($productOptionCode){
        $prdOption = $this->ChProductOption->find()->where(['product_option_code'=>$productOptionCode])->first();
        if($prdOption->stock > 0){
            return array('result'=>true);
        }
        return array('result'=>false);
    }

    /** 댓글 삭제 */
    public function commentDelete($commentId,$userId){
        $comment = $this->McVideoComment->get($commentId);

        if($comment->users_id!=$userId){
            return array(
                'result' => false,
                'msg' => 'this is not your comment'
            );
        }
        $pEntity = $this->McVideoComment->patchEntity($comment,["del_yn"=>"Y"]);

        if($this->McVideoComment->save($pEntity)){
            return array(
              'result' => true,
              'msg' => 'success'
            );
        }

        return array(
            'result' => false,
            'msg' => 'fail'
        );
    }


}
