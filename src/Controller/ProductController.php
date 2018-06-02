<?php
/**
 * Created by PhpStorm.
 * User: brickandmon
 * Date: 2016. 1. 15.
 * Time: 오후 5:06
 */

namespace App\Controller;

use App\Service\DesignerService;
use App\Service\EncryptService;
use App\Service\ProductService;
use Cake\Cache\Cache;
use Cake\Routing\Router;

class ProductController extends AppController
{
    private $ProductService;

    public function isAuthorized(){
        return true;
    }

    public function initialize(){
        parent::initialize();

        $this->ProductService = ProductService::Instance();

        $this->Auth->allow();
        $this->set('productSelect','is-select');
    }

    /**
     * 상품별 문의 등록
     * $data = array(
     *  'product_code' => 상품코드,
     *  'title' => '제목',
     *  'contents' = '내용'
     * )
     */
    public function registProductAsk(){
        $this->autoRender = false;

        $data = $this->request->data();

        $userId = $this->Auth->user('id');

        $result = $this->ProductService->registProductAsk($data,$userId);

        echo json_encode(['result'=>$result]);
    }
    
    /**
     * 상품 상세
     * $id = 암호화된 제품 코드
     * $preview = 미리보기 여부
     */
    public function detail($productCode,$preview=null){
        if($preview!="preview"){
            $preview= null;
        }

        $isMobile =false;

        $request = $this->request;

        if($request->is("mobile")){
            $isMobile = true;
        }

        $productCode = base64_decode($productCode);
        $productCode=EncryptService::Instance()->decrypt($productCode);

        $productDetail = $this->ProductService->getProductDetail($productCode,$preview,$isMobile);
        if($productDetail==false){
            $this->view = "error";
            $this->set("msg","잘못된 접근 입니다.");
        }else {
            $url = Router::url( $this->here, true );
            $snsShare = $this->ProductService->getDetailSnsShare($productCode,$isMobile);
            $snsShare['url'] = $url;

            $this->set('snsShare',$snsShare);
            $this->set('product', $productDetail);
        }
    }

    /*
     * $type :  main 메인페이지용
     *          designer 메인 페이지
     *          category1 카테고리에 속한 상품리스트
     *          category2 카테고리에 속한 상품리스
     * $searchId : designer_id,category1,category2
     * $page
     */
    /** 사용중지 */
    public function productList(){
        $rtn = $this->ProductService->getCardProductList($this->request->query,$this->RequestHandler->ismobile());
        $this->set('rtn',$rtn);
        $this->set('_serialize',['rtn']);
    }

    /** 제품 목록  */
    public function index(){

        $cacheCheck = true;
        $category = false;
        if(!CACHEDUSE) {
            $cacheCheck=false;
        }
        if($cacheCheck==true) {
            $category = Cache::read('category', 'category');
        }
        if($category===false || $category == null || $cacheCheck== false){
            $category = $this->ProductService->getCategory();
        }
        $categoryId=$this->request->query('categoryId');
        $productList = $this->ProductService->getCardProductList($this->request->query,$this->RequestHandler->ismobile());
        $brandTag = $this->ProductService->getBrandTag($categoryId);
        $categoryName = "";


        /** pageCover처리를 위한 코드값 저장 */

        $clothArr = array(
            'VG9JQWJZRmRQSGszTUxQcjhiZTJDUT09',
            'OFVQektRbzY0YnZhaXhLN0xCMUZvUT09',
            'akhKeTFXZ3hMZW51aVRDSTNhdTZVZz09',
            'TkltcFJqSWN6RFkyVWwzQ0l1ZnlpZz09'
        );

        $accArr = array(
            'aXpwbmpuaVoyc1VCRTIwNFRqQm1pUT09',
            'ajBub0JOZnpNMzZPUkRqRDExSzdxZz09',
            'Y3N2Z3dVdVFpVjdpcktKTTcxSzljUT09',
            'aGEwTThpSGdSclVWMW8rSkZSZjBXdz09'
        );

        $lifeArr = array(
            'eUhWZEhCa3lJeStIVlRrbUU5SS9JUT09',
            'bUhBdGNSUnFUT0dqNW4vOVlta3NGZz09',
            'UmduVEZHOURxZmo2bEEvMko4Z1ZGdz09',
            'cERRaWtxNTN2QStXdDhsMkZTU2hPdz09',
            'R3RQRE9xdUlBNTI1cWhjeGdaY3JkQT09'
        );
        if(in_array($categoryId,$clothArr)){
            $categoryName = "CLOTHES";
        }else if(in_array($categoryId,$accArr)){
            $categoryName = "BAG&ACC";
        }else if(in_array($categoryId,$lifeArr)){
            $categoryName = "LIFESTYLE";
        }
        $this->set(compact('productList','category','categoryId','brandTag','categoryName'));
    }

    //TODO 카테고리별 상품 리스트
    public function getProductByCategory($categoryId){
        $this->autoRender = false;
        if($this->request->is('GET')){
            $imageSize = 'surl';
            $products = $this->ProductService->getProductByCategory($categoryId, $imageSize);
            echo json_encode($products);
            exit;
        }
    }

}