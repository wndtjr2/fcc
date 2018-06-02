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
use PhpParser\Node\Expr\Cast\Object_;
use Symfony\Component\Console\Helper\Table;
use Cake\Datasource\ConnectionManager;
use App\Service\EncryptService;
/**
 * Interface ProductInterface
 * @package App\Service
 */
interface WhatInterface{


}

/**
 * Class ProductService
 * @package App\Service
 */
class WhatService
{
    private $McNewProduct;

    private $ChImageFile;

    private $EncryptService;

    public function __construct(){
        $this->McNewProduct= TableRegistry::get("McNewProduct");
        $this->ChImageFile = TableRegistry::get("ChImageFile");
        $this->EncryptService = EncryptService::Instance();
    }

    public static function Instance(){
        static $inst = null;
        if ($inst === null) {
            $inst = new WhatService();
        }
        return $inst;
    }

    public function getWhatsNewList($page=1){
        $cacheCheck = true;
        $products = false;
        if(!CACHEDUSE) {
            $cacheCheck=false;
        }
        if($cacheCheck == true) {
            $products = Cache::read("mainWhatsNew", "products");
        }
        if($products===false || $products == null || $cacheCheck==false) {
            $conn = ConnectionManager::get('default');
            $query = <<<QUERY
            select a.product_code,b.name,b.designer_name,b.model_name,d.murl,c.stockCnt,b.price from
                mc_new_product a
                left outer join
                    ch_product b
                on a.product_code = b.product_code
                left outer join
                (
                    select product_code,sum(stock) stockCnt from ch_product_option group by product_code
                ) c
                on a.product_code=c.product_code
                left join ch_image_file d
                on b.main_image_id = d.image_id and d.type ='image' and d.del_yn = 'n' and d.seq=1
            where b.status = 'open'
            and b.del_yn = 'n'
QUERY;
            $query .= " order by a.seq asc";
            $products = $conn->execute($query)
                ->fetchAll('assoc');

            if($cacheCheck==true) {
                Cache::write("mainWhatsNew", $products, 'products');
            }
        }

        $pageSize=10;
        $rtn = [];
        $pageRang = 6;
        $cnt = count($products);
        $rtn['page']['count']=$cnt;
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
            if($prevRang>0) {
                $rtn['page']['prevRang']=$prevRang;
            }else{
                $rtn['page']['prevRang']='false';
            }
            $nextRang = $page+$pageRang;

            if($nextRang<$totalPage) {
                $rtn['page']['nextRang']=$nextRang;
            }else{
                $rtn['page']['nextRang']='false';
            }
            /*pageRange*/
            $rtn['product']=[];
            for($i=$min;$i<$max;$i++){

                $products[$i]["product_code"] = base64_encode(EncryptService::Instance()->encrypt($products[$i]["product_code"]));
                $rtn['product'][$i]=$products[$i];
            }
        }

        return $rtn;
    }
}