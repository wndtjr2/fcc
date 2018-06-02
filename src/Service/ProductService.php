<?php
/**
 * Created by PhpStorm.
 * User: brickandmon
 * Date: 2016. 1. 18.
 * Time: 오후 4:49
 */

namespace App\Service;

use Cake\Cache\Cache;
use Cake\Error\Debugger;
use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

/**
 * Interface ProductInterface
 * @package App\Service
 */
interface ProductInterface{

    /**
     * @param $productCode
     * @return mixed
     */
    public function getProduct($productCode);
}

/**
 * Class ProductService
 * @package App\Service
 */
class ProductService
{
    /**
     * @var \Cake\ORM\Table
     */
    private $ChProduct;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChImage;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChImageFile;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChProductComment;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChProductReview;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChProductShippingCharge;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChProductOption;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChCodeGen;

    /**
     * @var \Cake\ORM\Table
     */
    private $CodeCountry;


    private $McProductAsk;

    private $Users;

    private $ChCategory;

    public function __construct(){
        $this->ChProduct = TableRegistry::get('ChProduct');
        $this->ChImage = TableRegistry::get('ChImage');
        $this->ChImageFile = TableRegistry::get('ChImageFile');
        $this->ChProductComment = TableRegistry::get('ChProductComment');
        $this->ChProductReview = TableRegistry::get('ChProductReview');
        $this->ChProductShippingCharge = TableRegistry::get('ChProductShippingCharge');
        $this->ChProductOption = TableRegistry::get('ChProductOption');
        $this->ChCodeGen = TableRegistry::get('ChCodeGen');
        $this->CodeCountry = TableRegistry::get('CodeCountry');
        $this->McProductAsk = TableRegistry::get("McProductAsk");
        $this->Users = TableRegistry::get("Users");
        $this->ChCategory = TableRegistry::get("ChCategory");
        $this->Designer = TableRegistry::get("McDesigner");
    }

    public static function Instance(){
        static $inst = null;
        if ($inst === null) {
            $inst = new ProductService();
        }
        return $inst;
    }

    /**
     * Get Product by Product Code
     * @param $productCode
     * @return mixed
     */
    public function getProduct($productCode){
        $query = $this->ChProduct->find();
        $product = $query
            ->contain([
                'ChProductOption',
                'ChImage' => array(
                    'ChImageFile'
                )
            ])
            ->where([
                'ChProduct.product_code' => $productCode,
                'ChProduct.del_yn' => 'N',
                'ChProduct.delivery_yn' => 'y'
            ])
            ->first();
        return $product;
    }

    public function getProductList($productCodes,$order = null){
        $query = $this->ChProduct->find();
        $product = $query
            ->contain([
                'ChProductOption',
                'ChImage' => array(
                    'ChImageFile'
                )
            ])
            ->where([
                'ChProduct.product_code in' => $productCodes,
                'ChProduct.del_yn' => 'n',
                'ChProduct.delivery_yn' => 'y'
            ]);

        if($order != null){
            $product->order(['product_code'=>$order]);
        }

        return $product;


    }

    public function getProductOption($productOptionCode){
        return $this->ChProductOption->find()->where(['product_option_code in' => $productOptionCode]);
    }

    public function getAllImages($productCode){
        $product = $this->ChProduct->find()->where(['product_code' => $productCode])->first();
        $images = null;
        if(!is_null($product->main_image_id)){
            $images['main_image'] = $this->getSingleImage($product->main_image_id);
        }
        if(!is_null($product->sub_image_id)){
            $images['sub_image'] = $this->getSingleImage($product->sub_image_id);
        }
        if(!is_null($product->video_id)){
            $images['video'] = $this->getSingleImage($product->video_id);
        }
        return $images;
    }

    public function getSingleImage($imageId){
        $image = $this->ChImage->find()
            ->matching('ChImageFile', function($q) use ($imageId){
                return $q->where([
                    'ChImage.image_id in' => $imageId,
                    'ChImageFile.del_yn' => 'n'
                ]);
            })->first();
        return $image;
    }

    public function getComments($productCode){
        return $this->ChProduct->find()
            ->contain('ChProductComment')
            ->where(['ChProduct.product_code' => $productCode])
            ->toArray();
    }

    public function getReview($productCode){
        $reviews = $this->ChProductReview->find()
//            ->matching('ChProductReview', function ($q) {
//                return $q->where(['ChProductReview.del_yn' => 'n']);
//            })
//            ->contain(['ChProductReview', 'Users'])
//            ->where(['ChProduct.product_code' => $productCode])
//            ->first();
            ->contain(['ChProduct', 'Users'])
            ->join([
                'product' => [
                    'table' => 'ch_product',
                    'type' => 'LEFT',
                    'conditions' => 'product.product_code = ChProductReview.product_code'
                ],
                'user' => [
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'ChProductReview.users_id = user.id'
                ]
            ])
            ->where([
                'ChProductReview.product_code' => $productCode,
                'ChProductReview.del_yn' => 'n'
            ])
            ->toArray();
        return $reviews;
    }

    public function getShipping($productCode){
        $shipping = $this->ChProductShippingCharge->find()
//            ->contain(['CodeCountry'])
            ->join([
                'country' => [
                    'table' => 'code_country',
                    'type' => 'LEFT',
                    'conditions' => 'country.country_code = ChProductShippingCharge.country_code'
                ]
            ])
            ->where(['ChProductShippingCharge.product_code' => $productCode]);

//        var_dump($shipping->toArray());

//            ->toArray();
//        exit;
        return $shipping;
    }

    public function getShippingWithCountryCode($productCode){
        $shipping = $this->ChProductShippingCharge->find()
            ->select(['country_code'])
            ->where([
                'ChProductShippingCharge.product_code' => $productCode
            ])
            ->toArray();
        return $shipping;
    }

    public function updateCode($type){
        //update last code
        $lastCode = $this->ChCodeGen->get($type);
        $lastCode->last_num = $lastCode->last_num + 1;
        if(!$this->ChCodeGen->save($lastCode)){
            throw new InternalErrorException('Cannot save last generated code');
        }
        return $lastCode;
    }

    public function generateCode($type){
        $getCodeInfo = $this->updateCode($type);
        $code = $getCodeInfo->prefix.str_pad($getCodeInfo->last_num, 10, 0, STR_PAD_LEFT);
        return $code;
    }

    public function getProductCodeWithProductOptionCode($productOptionCode){
        $product = $this->ChProductOption->find()->where(['product_option_code' => $productOptionCode])->first();
        return $product;
    }

    public function registProductAsk($data,$userId){

        /** Default Set Value */
        $data['users_id'] =$userId;
        $data['reply_title'] = ' ';
        $data['reply_id'] = 0;
        $data['reply'] = " ";
        $data['reply_yn'] = 'n';
        $data['creator'] =$userId;
        $data['modifier'] = 0;
        /** Default Set Value */

        $newEntity = $this->McProductAsk->newEntity($data);
        $result = $this->McProductAsk->save($newEntity);
        if($result){
            return true;
        }
        Debugger::log($newEntity);
        return false;
    }

    public function getDetailSnsShare($productCode,$isMobile){

        $cacheCheck = true;
        $snsCache = false;
        if(!CACHEDUSE) {
            $cacheCheck=false;
        }else {
            $snsCache = Cache::read("PrdSNS" . $productCode, "products");
        }
        if (($snsCache=== false)||$snsCache==null || $cacheCheck==false) {
            $productCache = Cache::read("PrdDetail".$productCode,"products");
            if (($productCache=== false)||$productCache==null) {
                $productCache = $this->getProductDetail($productCode,null,$isMobile);
            }
            $title = $productCache['productName'];
            $shareDesc = str_replace("&nbsp;","",strip_tags($productCache['productContent']));
            $shareDesc = trim($shareDesc);
            $shareImg = $productCache['mainImageUrl'];

            $snsShare = array(
                'title' => $title,
                'image' => $shareImg,
                'desc' => $shareDesc,
            );
            if($cacheCheck==true) {
                Cache::write("PrdSNS" . $productCode, $snsShare, "products");
            }
        }else{
            $snsShare = $snsCache;
        }

        return $snsShare;
    }

    public function getProductDetail($productCode,$preview,$isMobile){
        $cacheCheck = true;
        if(!CACHEDUSE) {
            $cacheCheck=false;
        }

        $mobile= ($isMobile==true)?"mobile":'';

        $productCache = Cache::read("PrdDetail".$productCode.$mobile,"products");

        if (($productCache=== false)||$productCache==null || $preview!=null || $cacheCheck== false) {

            $category = array();
            if($cacheCheck == true ) {
                if (Cache::read('category')) {
                    $category = Cache::read('category', 'category');
                } else {
                    $category = $this->getCategory();
                    Cache::write('category', $category, 'category');
                }
            }else{
                $category = $this->getCategory();
            }

            $productWhere = array();
            $productWhere['product_code'] = $productCode;
            if($preview==null) {
                $productWhere['del_yn'] = "n";
            }
            $product = $this->ChProduct->find()->where($productWhere)->first();
            if($product==null){
                return false;
            }
            $productName = $product->name;
            $productContent = $product->content;
            $modelName = $product->model_name;
            $designerName = $product->designer_name;
            $category1 = base64_encode(EncryptService::Instance()->encrypt($product->category1));
            $category2 = base64_encode(EncryptService::Instance()->encrypt($product->category2));
            $desingerId= base64_encode(EncryptService::Instance()->encrypt($product->designer_id));

            $categorys = array();

            foreach($category as $cate){

                if($cate['id'] == $category1){
                    $categorys['category1'] = $cate;

                    foreach($cate['sub'] as $subCate){
                        if($subCate['id']==$category2){
                            $categorys['category2'] = $subCate;
                        }
                    }
                }
            }

            $videoType= false;
            $videoId = "";

            if(isset($product->youtube_id) && $product->youtube_id!="" ){
                $videoType = "Youtube";
                $videoId = $product->youtube_id;
            }else if(isset($product->vimeo_id) && $product->vimeo_id!=""){
                $videoType = "Vimeo";
                $videoId = $product->vimeo_id;
            }

            $mainImgObj = $this->ChImageFile->find()->where(['image_id'=>$product->main_image_id,'del_yn'=>'n'])->first();

            $mainImageUrl = (isset($mainImgObj->lurl))?$mainImgObj->lurl:"";
//            $mainImageUrl = ($isMobile)?$mainImgObj->murl:$mainImageUrl;

            $subImgObj = $this->ChImageFile->find()->where(['image_id'=> $product->sub_image_id,'del_yn'=>'n']);
            $subImageArray = array();

//            if($isMobile) {
//                foreach ($subImgObj as $imgObj) {
//                    $subImageArray[] = array(
//                        'lurl' => $imgObj->murl,
//                        'murl' => $imgObj->murl,
//                        'surl' => $imgObj->surl,
//                    );
//                }
//            }else{
                foreach ($subImgObj as $imgObj) {
                    $subImageArray[] = array(
                        'lurl' => $imgObj->lurl,
                        'murl' => $imgObj->murl,
                        'surl' => $imgObj->surl,
                    );
                }
//            }

            $subContentImageIds = array(
                $product->washing_info_image_id,
                $product->size_info_image_id,
                $product->delivery_info_image_id,
                $product->refund_info_image_id,
                $product->notice_info_image_id,
            );

            $subContentImageIds = array_unique($subContentImageIds);
            $subContentsImageObject = $this->ChImageFile->find()->where(['image_id in'=> $subContentImageIds,'del_yn'=>'n']);

            $subContentInfo = array();
            foreach($subContentsImageObject as $subConImgObj){
                $tempImageArray = array(
                    'lurl' => $subConImgObj->lurl,
                    'murl' => $subConImgObj->murl,
                );
                if($product->washing_info_image_id==$subConImgObj->image_id){
                    $subContentInfo['washing']['image'] = $tempImageArray;
                }else if($product->size_info_image_id==$subConImgObj->image_id){
                    $subContentInfo['size']['image'] = $tempImageArray;
                }else if($product->delivery_info_image_id==$subConImgObj->image_id) {
                    $subContentInfo['delivery']['image'] = $tempImageArray;
                }else if($product->refund_info_image_id==$subConImgObj->image_id) {
                    $subContentInfo['refund']['image'] = $tempImageArray;
                }else if($product->notice_info_image_id==$subConImgObj->image_id) {
                    $subContentInfo['notice']['image'] = $tempImageArray;
                }
            }

            $subContentInfo['washing']['text'] = (isset($product->washing_info))?$product->washing_info:"";
            $subContentInfo['size']['text'] = (isset($product->size_info))?$product->size_info:"";
            $subContentInfo['delivery']['text'] = (isset($product->delivery_info))?$product->delivery_info:"";
            $subContentInfo['refund']['text'] = (isset($product->refund_info))?$product->refund_info:"";
            $subContentInfo['notice']['text'] = (isset($product->notice_info))?$product->notice_info:"";

            $productDetailInfo = array();
            $productDetailImageObject = $this->ChImageFile->find()->where(['image_id'=> $product->detail_image_id]);
            foreach($productDetailImageObject as $prdDeImgObj){
                $tempImageArray = array(
                    'url' => $prdDeImgObj->url,
                    'lurl' => $prdDeImgObj->lurl,
                    'murl' => $prdDeImgObj->murl,
                );
                $productDetailInfo[] =$tempImageArray;
            }

            if($preview==null) {
                $productTotalInfo = array(
                    'productName' => $productName,
                    'productContent' => $productContent,
                    'modelName' => $modelName,
                    'designerName' => $designerName,
                    'mainImageUrl' => $mainImageUrl,
                    'subImageUrls' => $subImageArray,
                    'productDetailInfo' => $productDetailInfo,
                    'subContentInfo' => $subContentInfo,
                    'videoType' => $videoType,
                    'videoId' => $videoId,
                    'category1' =>$category1,
                    'category2' =>$category2,
                    'categorys' =>$categorys,
                    'designerId' =>$desingerId
                );
                if($cacheCheck==true) {
                    Cache::write("PrdDetail" . $productCode, $productTotalInfo, "products");
                }
            }

        }else{
            $productName = $productCache['productName'];
            $productContent = $productCache['productContent'];
            $mainImageUrl = $productCache['mainImageUrl'];
            $subImageArray = $productCache['subImageUrls'];
            $productDetailInfo = $productCache['productDetailInfo'];
            $subContentInfo = $productCache['subContentInfo'];
            $modelName = $productCache['modelName'];
            $designerName = $productCache['designerName'];
            $videoId = $productCache['videoId'];
            $videoType = $productCache['videoType'];
            $category1 = $productCache['category1'];
            $category2 = $productCache['category2'];
            $categorys = $productCache['categorys'];
            $desingerId = $productCache['designerId'];
        }


        /** 제품 옵션 */
        $productOptionCode = $this->ChProductOption->find()->where(["product_code"=>$productCode,"use_yn"=>'y','del_yn'=>'n']);

        $totalStock = 0;

        $productOptions = array();
        foreach($productOptionCode as $option){

            $optionArr = explode(";", $option->name);
            $colorName = $optionArr[1];
            $size = array(
                'size' => $optionArr[2],
                'price' => $option->price,
                'stock' => $option->stock,
                'max' => $option->max_purchase,
                'prdOptCode' => $option->product_option_code
            );
            $productOptions[$colorName][] = $size;

            $totalStock += $option->stock;
        }

        return array(
            'productCode' => $productCode,
            'productName' => $productName,
            'modelName' => $modelName,
            'designerName' => $designerName,
            'productContent' => $productContent,
            'mainImageUrl' => $mainImageUrl,
            'subImageUrls' => $subImageArray,
            'productDetailInfo' => $productDetailInfo,
            'productSubContent' => $subContentInfo,
            'productOption' => $productOptions,
            'totalStock' => $totalStock,
            'videoType' => $videoType,
            'videoId' => $videoId,
            'category1' =>$category1,
            'category2' =>$category2,
            'categorys' =>$categorys,
            'designerId' =>$desingerId
        );
    }


    /*
* $prams['type'] :  현재 페이지 속성 main,product,designer
     *  ['designerId'] : 디자이너 id
     *  ['categoryId'] : 카테고리에 속한 상품리스트
     *  ['sortId'] : 정렬기준 new,best,lowprice,highprice
     *  ['keyword'] :  검색어
     *  ['pageSize'] : 1페이지 수 30,60,90 모바일은 10개 고정
     *  ['page'] :  페이지
* $isMobile : 모바일 유무
*/
    public function getCardProductList($params=[],$isMobile){
        $join = '';
        $limit = '';
        $where = ' where a.status="open" and a.del_yn="n" ';
        //판매량 기준 정렬 -- 기본
        $order = ' order by a.created desc';
        extract($params);
        $type       = isset($type)      ?$type      :'';

        $designerId = isset($designerId)?$designerId:'';
        $categoryId = isset($categoryId)?$categoryId:'';
        $keyword    = isset($keyword)   ?$keyword   :'';
        $sortId     = isset($sortId)    ?$sortId    :'';
        $page       = isset($page)      ?$page      :'1';
        //모바일 페이지 사이즈 고정
        if ($isMobile) {
            $pageSize   = 10;
        }else{
            $pageSize   = isset($pageSize)  ?$pageSize  :'30';
        }

        if($type=='hot'){
            $join=' , mc_new_product f ';
            $where.=' and a.product_code=f.product_code';
        }
        $cacheKey = $type;
        if($categoryId!='') {
            $encryptedId = EncryptService::Instance()->decrypt(base64_decode($categoryId));
            $where .= ' and ( a.category1 = '.$encryptedId.' or a.category2 = '.$encryptedId.')';
            $cacheKey .= 'c:'.$encryptedId;
        }
        if($designerId!='') {
            $encryptedId = EncryptService::Instance()->decrypt(base64_decode($designerId));
            $where .= ' and a.designer_id = '.$encryptedId;
            $cacheKey .= 'd:'.$encryptedId;
        }
        if($keyword!='') {
            $cacheKey .= 'k:'.$keyword;
            $where .= " and (a.name like '%$keyword%' or a.content like '%$keyword%' or a.model_name like '%$keyword%' or e.name like '%$keyword%')";
        }
        $cacheKey .= 's:'.$sortId;
        //상품 등록일 기준 내림 차순 -- main 페이지 출력일때는 정렬기준을 판매량으로 한다
        if($sortId=='best' || $type=='main'){
            //판매량 기준 정렬 -- 기본
            $order = " order by b.cnt desc";
        }
        //가격 오름 차순
        if($sortId=='lowprice'){
            $order = ' order by a.price';
        }
        //가격 내림차순
        if($sortId=='highprice'){
            $order = ' order by a.price desc';
        }

        if (CACHEDUSE) {
            $products = Cache::read($cacheKey, 'products');
        }else{
            $min = ($page-1)*$pageSize;
            $limit = " limit $min,$pageSize";
        }

        //캐시가 없거나 $type 이 search 일때는 db를 조회한다
        if(CACHEDUSE == false || $products == false){
            $conn = ConnectionManager::get('default');
            $query = <<<QUERY
            select a.product_code,a.name,a.model_name,a.new_icon,a.price,b.cnt,c.stockCnt,d.murl,e.name as designer_name from
                ch_product a
                left outer join
                    mc_designer e
                on a.designer_id=e.id
                left outer join
                (
                    select product_code,count(*) cnt FROM ch_purchase where status='purchased' group by product_code
                ) b
                on  a.product_code=b.product_code
                left join
                (
                    select product_code,sum(stock) stockCnt from ch_product_option group by product_code
                )c
                on  a.product_code=c.product_code
                left join ch_image_file d
                on a.main_image_id = d.image_id and d.type = 'image' and d.del_yn = 'n' and d.seq=1
QUERY;

            $query .= $join.$where.$order.$limit;
            $products = $conn->execute($query)
                ->fetchAll('assoc');
            if(CACHEDUSE) {
                $cnt = count($products);
                Cache::write($cacheKey, $products, 'products');
            }else{
                $query =<<<QUERY
                select count(*) cnt from ch_product a
                    left outer join
                        mc_designer e
                    on a.designer_id=e.id
                    left outer join
                    (
                        select product_code,count(*) cnt FROM ch_purchase where status='purchased' group by product_code
                    ) b
                    on  a.product_code=b.product_code
QUERY;
                $query .= $join.$where.$order;
                $cntStmt = $conn->execute($query)
                    ->fetch('assoc');
                $cnt = $cntStmt['cnt'];
            }
        }
        $rtn = [];
        $pageRang = 4;

        $rtn['page']['count']=$cnt;
        $rtn['page']['pageSize']=$pageSize;
        if($cnt==0){
            $rtn['page']['total']=0;
            $rtn['page']['now']=1;
            $rtn['product']=[];
            $rtn['page']['next']='false';
            $rtn['page']['prev']='false';
            $rtn['page']['prevRang']='false';
            $rtn['page']['nextRang']='false';
        }else{
            $min = ($page-1)*$pageSize;
            $max = $page*$pageSize;
            $totalPage=floor($cnt/$pageSize)+1;
            $rtn['page']['total']=$totalPage;
            $rtn['page']['now']=$page;
            /*다음페이지 계산*/
            if($max>$cnt){
                $max = $cnt;
                $rtn['page']['next']='false';
            }else{
                $rtn['page']['next']=$page+1;
            }
            /*이전페이지 계산*/
            if($page-1>0){
                $rtn['page']['prev']=$page-1;
            }else{
                $rtn['page']['prev']='false';
            }
            /*pageRange*/
            $prevRang = $page-$pageRang;
            if($prevRang>1) {
                $rtn['page']['prevRang']=$prevRang;
            }else{
                $rtn['page']['prevRang']='false';
            }
            $nextRang = $page+$pageRang;

            if($nextRang<=$totalPage) {
                $rtn['page']['nextRang']=$nextRang;
            }else{
                $rtn['page']['nextRang']='false';
            }
            /*pageRange*/
            $rtn['product']=[];
            if(CACHEDUSE){
                for($i=$min;$i<$max;$i++){

                    $products[$i]["product_code"] = base64_encode(EncryptService::Instance()->encrypt($products[$i]["product_code"]));
                    $rtn['product'][$i]=$products[$i];
                }
            }else{
                foreach($products as $key=>$val){
                    $val["product_code"] = base64_encode(EncryptService::Instance()->encrypt($val["product_code"]));
                    $rtn['product'][$key]=$val;
                }
            }

        }
        //마지막카운트보다 높으면 카운트로 변경함
        // 다음페이지 있는지 없는지 확인


        return $rtn;
    }


    public function getProductsByNameAndSortByPurchase($keyword, $imageSize){

        $this->ChProduct->hasMany('ChPurchase')->foreignKey('product_code');
        $this->ChProduct->belongsTo('ChImage', ['foreignKey' => 'main_image_id']);

        $contains = [
            'ChPurchase' => function($q){
                return $q->select([
                    'product_code',
                    'quantity'
                ]);
            },
            'ChImage' => [
                'ChImageFile' => function($q) use ($imageSize){
                    return $q->select([
                        $imageSize,
                        'image_id'
                    ]);
                }
            ]
        ];
        $selects = [
            'product_code',
            'name',
            'designer_name',
            'price',
            'ChImage.image_id'
        ];

        $products = $this->ChProduct->find()
            ->contain($contains)
            ->select($selects);
        if($keyword != ''){
            $products->where(['name Like' => '%'.$keyword.'%']);
        }


        foreach ($products as $product){
            $quantity = 0;
            foreach($product->ch_purchase as $purchase){
                $quantity += $purchase->quantity;
            }
            $product->purchased_quantity = $quantity;
        }

        return $products;

    }

    public function arrangeProductList($productObjects, $imageSize){

        $result = array();

        foreach ($productObjects as $product) {

            $result[] = [
                'product_code' => $product->product_code,
                'product_name' => $product->name,
                'designer_name' => $product->designer_name,
                'price' => $product->price,
                'image' => $product->ch_image->ch_image_file[0]->$imageSize,
                'quantity' => $product->purchased_quantity
            ];
        }

        usort($result, function($a, $b){
            if($a['quantity'] == $b['quantity']){
                return substr($b['product_code'], 2) - substr($a['product_code'], 2);
            }else {
                return $b['quantity'] - $a['quantity'];
            }
        });

        return $result;

    }
    /*브랜드 리스트 캐시 저장*/
    public function getBrandTag($categoryId=''){
        if(CACHEDUSE) {
            $designerInfo = Cache::read("brands".$categoryId, "products");
        }
        if(CACHEDUSE == false || $designerInfo == false){
            $designers = $this->Designer->find()
                ->select(['McDesigner.id','McDesigner.name'])
                ->order(['McDesigner.name'=>'asc']);
            if($categoryId!=''){
                $categoryId = EncryptService::Instance()->decrypt(base64_decode($categoryId));
                $designers->matching('McDesignerCategory',function($q) use($categoryId){
                   return $q->where([
                        'OR' => [
                            ['category1'=>$categoryId],
                            ['category2'=>$categoryId]
                        ]
                   ]);
                })->distinct(['McDesigner.id','McDesigner.name']);
            }

            $designerInfo =[];
            foreach ($designers as $designer) {
                    $designerInfo[base64_encode($designer->encrypt_id)]=$designer->name;
            }
        }
        if(CACHEDUSE) {
            Cache::write("brands".$categoryId , $designerInfo, "products");
        }
        return $designerInfo;
    }
    /*카테고리 리스트 캐시 저장*/
    public function getCategory(){
        if(CACHEDUSE) {
            $categoryInfo = Cache::read("category", "products");
        }
        if(CACHEDUSE == false || $categoryInfo == false){
            $category = $this->ChCategory->find()->where(['launched_yn' => 'y'])->order(['depth' => 'ASC', 'seq' => 'ASC'])->toArray();

            $encrypt = EncryptService::Instance();

            for($i = 0; $i < count($category); $i++){
                if($category[$i]->parent_id == 0){
                    $categoryInfo[$i]['name'] = $category[$i]->name;
                    $categoryInfo[$i]['id'] = base64_encode($encrypt->encrypt($category[$i]->id));
                }
                for($v = 0;$v < count($category); $v++) {
                    if($category[$i]->id == $category[$v]->parent_id){
                        $categoryInfo[$i]['sub'][] = ['name' => $category[$v]->name, 'id' => base64_encode($encrypt->encrypt($category[$v]->id))];
                    }
                }
            }
        }
        if(CACHEDUSE) {
            Cache::write("category" , $categoryInfo, "products");
        }
        return $categoryInfo;
    }

    public function getProductByCategory($categoryId, $imageSize){

        $contain = [
            'ChImage' => [
                'ChImageFile' => function($q) use ($imageSize) {
                    return $q->select([
                        'ChImageFile.image_id',
                        'ChImageFile.'.$imageSize,
                    ]);
                }
            ]
        ];
        $select = [
            'designer_name',
            'name',
            'price',
            'main_image_id',
            'ChImage.image_id'
        ];

        $productList = $this->ChProduct->find()
            ->contain($contain)
            ->select($select)
            ->where(['category2' => $categoryId])
            ->toArray();

        $result = array();
        foreach($productList as $product){
            $result[] = [
                'product_name' => $product->name,
                'designer_name' => $product->designer_name,
                'image' => $product->ch_image->ch_image_file[0]->$imageSize,
                'price' => $product->price
            ];
        }
        return $result;
    }




}