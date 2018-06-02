<?php
/**
 * Created by PhpStorm.
 * User: brickandmon
 * Date: 2016. 1. 15.
 * Time: 오후 5:06
 */

namespace App\Controller;

use App\Service\EncryptService;
use App\Service\TvCacheService;
use App\Service\TvService;
use App\Service\WhatService;
use Cake\Cache\Cache;

class TvController extends AppController
{
    private $TvService;

    private $fccTvService;
    public function isAuthorized(){
        return true;
    }

    public function initialize(){
        parent::initialize();

        $this->TvService = TvCacheService::Instance();

        $this->fccTvService = TvService::Instance();

        $this->Auth->allow();
        $this->set('tvSelect','is-select');
    }

    /** TV 목록  */
    public function index(){
        $page = 1;
        if(isset($this->request->query['page'])) {
            $page = ($this->request->query['page'] != '') ? $this->request->query['page'] : 1;
        }
        $limit = 30;
        $videoInfos = $this->TvService->getSchedule($page,$limit);

        $this->set("videoInfos",$videoInfos);
        $this->set("limit",$limit);
    }

    /** TV 상세  */
    public function detail($videoId){

        $videoId = base64_decode($videoId);
        $videoId = EncryptService::Instance()->decrypt($videoId);

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

    /** 모바일전용
     * TV 리스트 페이징 처리
     */
    public function getNextPage(){
        $this->autoRender = false;

        $page = $this->request->data('page');
        $limit = $this->request->data('limit');

        $tv = $this->TvService->getSchedule($page,$limit);

        echo json_encode($tv);
    }
}