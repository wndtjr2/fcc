<?php
/**
 * Created by PhpStorm.
 * User: brickandmon
 * Date: 2016. 1. 15.
 * Time: 오후 5:06
 */

namespace App\Controller;


use App\Service\ProductService;
use App\Service\TvCacheService;
use App\Service\TvService;
use Cake\Routing\Router;

class FccTvController extends AppController
{

    private $fccTvService;

    private $videoCategory;

    private $ProductService;

    private $url;

    private $TvCacheService;

    public function isAuthorized(){
        return true;
    }

    public function initialize(){
        parent::initialize();

        $this->fccTvService = TvService::Instance();

        $this->videoCategory = $this->fccTvService->getVideoCategory();

        $this->ProductService = ProductService::Instance();

        $this->TvCacheService = TvCacheService::Instance();

        $this->Auth->allow();

        $this->set("isTvMenu",true);

        $this->url = Router::url( $this->here, true );
    }

    /** 사용중지 */
    public function index(){

        $getDate = $this->request->query("day");

        if(!$getDate){
            $getDate= date("Y-m-d");
        }

        $onAir = $this->fccTvService->getSchedule($getDate);

        $calendar = $this->fccTvService->getCalendarInfo($getDate);

        $dayAfterTomorrowTimeStamp = $calendar['calendar']['dayAfterTomorrowTimeStamp'];

        if(strtotime($getDate) > $dayAfterTomorrowTimeStamp){
            $this->view = "error";
            $this->set("msg","잘못된 접근 입니다.");
        }else {

            $isNow = $calendar["isNow"];
            $selDate = $calendar["selDate"];
            $today = $calendar["today"];
            $dateArr = $calendar["dateArr"];
            $dayOfTheWeek = $calendar['dayOfTheWeek'];
            $monthString = $calendar['monthString'];


            $snsShare = $this->fccTvService->getIndexShareInfo($this->url, $onAir['timeLine'], $onAir['mainImages']);

            $prevMonthLastDay = date("Y-m-t", strtotime("-1 month", $calendar['selDateTimeStamp']));

            $haveSchedule = $this->fccTvService->haveSchedule($getDate);

            $prevMonthLastDayHaveSchedule = $this->fccTvService->dayhaveSchedule($prevMonthLastDay);


            $this->set("colorBlack", true);
            $this->set("prevMonthLastDayHaveSchedule", $prevMonthLastDayHaveSchedule);
            $this->set("thisMonthSchedule", $haveSchedule);
            $this->set("prevMonthLastDay", $prevMonthLastDay);
            $this->set('snsShare', $snsShare);
            $this->set("isNow", $isNow);
            $this->set("selDate", $selDate);
            $this->set("today", $today);
            $this->set("dateArr", $dateArr);
            $this->set("calendar", $calendar['calendar']);
            $this->set("dayOfTheWeek", $dayOfTheWeek);
            $this->set("monthString", $monthString);

            $this->set('videoCategory', $this->videoCategory);
            $this->set('nowTimestamp', strtotime('now'));

            $this->set("data", $onAir);
        }
    }

    /** 사용중지  */
    public function detail(){

        $videoId = $this->request->params['videoId'];

        $videoProducts = $this->fccTvService->getProductList($videoId);
        if($videoProducts==null){
            $this->view = "error";
            $this->set("msg","잘못된 접근 입니다.");
        }else {
            $userInfo = $this->Auth->user();
            $title = $videoProducts['video']->title;
            $description = $videoProducts['video']->video_info;
            $imagePath = str_replace("original", "1024", $videoProducts['videoMainImage']->path);

            $snsShare = $this->fccTvService->getDetailShareInfo($videoId, $this->url, $title, $description, $imagePath);

            $this->set("mobileHead", true);
            $this->set("snsShare", $snsShare);
            $this->set("colorBlack", true);
            $this->set('videoCategory', $this->videoCategory);
            $this->set('userInfo', $userInfo);
            $this->set("videoWithProduct", $videoProducts);
        }
    }

    /** 제품 재고 체크  */
    public function getStockCount(){
        $this->autoRender = false;
        $productOptionCode = $this->request->data('prdOptCode');
        $result = $this->fccTvService->getStockCount($productOptionCode);
        echo json_encode($result);
    }

    /** 사용중지  */
    public function getProductDetail(){
        $this->autoRender = false;
        $productCode = $this->request->data('productCode');
        $result = $this->fccTvService->getProductDetail($productCode);
        echo json_encode($result);
    }

    /** 코멘트 가져오기 페이징시.. */
    public function getMoreComment(){
        $this->autoRender = false;
        $userId = $this->Auth->user('id');
        $page = $this->request->data("page");
        $videoId = $this->request->data("videoId");
        $comments = $this->fccTvService->getComment($videoId,$page,10,$userId);

        echo json_encode($comments);
    }

    /** 코멘트 추가  */
    public function commentAdd(){
        $this->autoRender = false;
        $data = $this->request->data();
        $userId = $this->Auth->user("id");
        $data['users_id'] = $userId;
        $rtn = $this->fccTvService->commentSave($data);

        echo json_encode($rtn);
    }

    /** 코멘트 카운트 */
    public function getCommentCount(){
        $this->autoRender = false;
        $videoId= $this->request->data("videoId");
        $count = $this->fccTvService->getCommentCount($videoId);
        echo json_encode(['count'=>$count]);
    }

    /** 코멘트 삭제  */
    public function commentDelete(){
        $this->autoRender = false;
        $data = $this->request->data();
        $commentId = $data['commentId'];
        $userId = $this->Auth->user('id');

        $rtn = $this->fccTvService->commentDelete($commentId,$userId);

        echo json_encode($rtn);
    }

    /** 사용안함 */
    public function search(){
        $keyword = $this->request->query("search");

        $searchList = $this->fccTvService->search($keyword);

        $calendar = $this->fccTvService->getCalendarInfo(date("Y-m-d"));

        $isNow = $calendar["isNow"];
        $selDate = $calendar["selDate"];
        $today = $calendar["today"];
        $dateArr = $calendar["dateArr"];
        $dayOfTheWeek = $calendar['dayOfTheWeek'];
        $monthString = $calendar['monthString'];

        $this->set("colorBlack",true);
        $this->set("isNow",$isNow);
        $this->set("selDate",$selDate);
        $this->set("today",$today);
        $this->set("dateArr",$dateArr);
        $this->set("calendar",$calendar['calendar']);
        $this->set("dayOfTheWeek",$dayOfTheWeek);
        $this->set("monthString",$monthString);

        $prevMonthLastDay = date("Y-m-t",strtotime("-1 month",$calendar['selDateTimeStamp']));


        $haveSchedule = $this->fccTvService->haveSchedule(date("Y-m-d"));

        $prevMonthLastDayHaveSchedule = $this->fccTvService->dayhaveSchedule($prevMonthLastDay);

        $this->set("prevMonthLastDayHaveSchedule",$prevMonthLastDayHaveSchedule);
        $this->set("thisMonthSchedule",$haveSchedule);

        $this->set("prevMonthLastDay",$prevMonthLastDay);
        $this->set('videoCategory',$this->videoCategory);
        $this->set('nowTimestamp',strtotime('now'));

        $this->set("keyword",$keyword);
        $this->set("search",$searchList);
    }

    /**
     * 지정한 날짜 이전달 / 다음달 가져오기
     * 사용중지
     */
    public function anotherMonthCal(){
        $this->autoRender = false;

        $status = $this->request->data['prevNext'];
        $getMonthDate = $this->request->data['date'];

        $calendar = $this->fccTvService->getNextPrevCalendar($getMonthDate,$status);

        $haveSchedule = $this->fccTvService->haveSchedule($calendar['reqDate']);
        $result = array(
            "calendar" => $calendar,
            'haveSchdule' => $haveSchedule
        );
        echo json_encode($result);
    }

    /** 메인화면  */
    public function main($preview=null){
        $historyId = 0;
        if($preview!="preview"){
            $preview = null;
        }else{
            $historyId = $this->request->query("id");
        }
        $data = $this->TvCacheService->getMainContents($preview,$historyId);
        $this->set("preview",$preview);
        $this->set("data",$data);
        $this->set("isMain",true);
//        $this->request->query['type'] = 'main';
//        $productList = $this->ProductService->getCardProductList($this->request->query,$this->RequestHandler->ismobile());
//        $this->set('productList',$productList);
    }
    /*
     * 검색 기능
     */
    public function searchTvAndProduct(){
        $productList = $this->ProductService->getCardProductList($this->request->query,$this->RequestHandler->ismobile());
        $this->set(compact('productList'));
    }
}