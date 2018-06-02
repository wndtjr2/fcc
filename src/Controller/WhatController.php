<?php
/**
 * Created by PhpStorm.
 * User: brickandmon
 * Date: 2016. 1. 15.
 * Time: 오후 5:06
 */

namespace App\Controller;

use App\Service\WhatService;
use App\Service\ProductService;

class WhatController extends AppController
{
    private $ProductService;

    public function isAuthorized(){
        return true;
    }

    public function initialize(){
        parent::initialize();

        $this->ProductService = ProductService::Instance();
        $this->Auth->allow();
    }

    /** HOT ITEM 메인화면  */
    public function index(){
        $this->request->query['type']='hot';
        $productList = $this->ProductService->getCardProductList($this->request->query,$this->RequestHandler->ismobile());
        $brandTag = $this->ProductService->getBrandTag();
        $hotItemSelect = 'is-select';
        $this->set(compact('productList','brandTag','hotItemSelect'));
        $this->set('_serialize',['rtn']);

    }

}