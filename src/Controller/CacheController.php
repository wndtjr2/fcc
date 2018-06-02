<?php
/**
 * Created by PhpStorm.
 * User: brickandmon
 * Date: 2016. 1. 15.
 * Time: 오후 5:06
 */

namespace App\Controller;

use Cake\Cache\Cache;
use Cake\Error\Debugger;

class CacheController extends AppController
{
    public function isAuthorized(){
        return true;
    }

    public function initialize(){
        parent::initialize();

        $this->Auth->allow();
    }

    /** 전체 캐쉬 Remove */
    public function allRemove(){
        Cache::clear(false,"products");
        Cache::clear(false,"designer");
        Cache::clear(false,"category");
        Cache::clear(false,"sns");
        Debugger::log("전체 캐쉬 삭제 ");
    }

    /**
     * 캐쉬 삭제 추후 추가를 쉽게 할수 있도록 컨트롤러에서 처리
     * @param null $productCode
     */
    public function removeProduct($productCode=null){
        $this->autoRender = false;
        if($productCode==null){
            Cache::clear(false,"products");
            Debugger::log("전체 products Cache 제거.");
            Debugger::log("main Cache 제거");
            Debugger::log("Whats New 제거");
        }else {
            if ($productCode == "main") {
                Cache::delete("hotItem", "products");
                Cache::delete("main", "products");
                Debugger::log("main Cache 제거");
            } else if ($productCode == "new") {
                Cache::delete("mainWhatsNew", "products");
                Debugger::log("WhatsNew Cache 제거");
            }else {
                Cache::delete("PrdDetail" . $productCode, "products"); //제품 상세
                Cache::delete("PrdSNS" . $productCode, "products"); //제품 상세 sns Share info
            }
        }
    }

    /**
     * 디자이너 캐쉬 제거
     */
    public function removeDesigner(){
        $this->autoRender = false;
        Cache::clear(false,"designer");
        Debugger::log("전체 Designer Cache 제거.");
    }


    /**
     * 카테고리 캐쉬 제거
     */
    public function removeCategory(){
        $this->autoRender = false;
        Cache::clear(false,"category");
        Debugger::log("전체 Category Cache 제거.");
    }
}