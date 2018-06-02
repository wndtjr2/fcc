<?php
namespace App\Service;

error_reporting(E_STRICT);
/**
 * 배송지 조회 서비스 인터페이스
 * User: Makun
 * Date: 16. 2. 1.
 * Time: 오후 1:54
 */

use App\Model\Table\ChallengeEntryTable;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Network\Email\Email;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Controller\Component;


/**
 * Interface CartInterface
 * 장바구니  인터 페이스
 * @package App\Service
 */
interface CartInterface
{
    /**
     * 장바구니 정보 리스트 호출
     * @param $userId 사용자 아이디
     * @return mixed array
     */
    public function selectCartList($userId);

    /**
     * 장바구니 상품 추가
     * @param $data 상품 정보
     * @param $userId 사용자 아이디
     * @return array
     */
    public function saveCart($data,$userId);

    /**
     * 장바구니 상품 제거
     * @param $cartIds 장바구니 아이디
     * @param $userId 사용자 아이디
     * @return array
     */
    public function removeCart($cartIds,$userId);

    /**
     * 장바구니 상품 제거
     * @param $productOptionCode 상품 옵션 코드
     * @param $usersId 사용자 아이디
     * @return array
     */
    public function removeCartOfProductionCode($productOptionCode,$usersId);
    /**
     * 유저의 카트에 담긴 개수
     * @param $usersId 사용자 아이디
     */
    public function userCartCnt($userId);
}

/**
 * 장바구니  서비스
 * Class CartService
 * @package App\Service
 */
class CartService implements CartInterface {

    private $ChCart;
    private $ChProductOption;
    private $ChProduct;

    private function __construct() {
        $this->ChCart = TableRegistry::get("ChCart");
        $this->ChProductOption = TableRegistry::get("ChProductOption");
        $this->ChProduct = TableRegistry::get("ChProduct");
    }

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new CartService();
        }
        return $inst;
    }
    /*
     * 유저Id에 해당되는 카트 개수를 리턴한다
     */
    public function userCartCnt($userId){
        $cnt = $this->ChCart->find()->where(['users_id'=>$userId])->count();
        return $cnt;
    }
    /** 장바구니 불러오기 */
    public function selectCartList($userId){
        $contain = array(
            "ChProductOption" => array(
                "ChProduct" => array(
                    "ChImage" => array(
                        "ChImageFile"
                    ),
                )
            )
        );
        $cartListObj = $this->ChCart->find()->contain($contain)->where(["ChCart.users_id"=>$userId])->order(["ChCart.cart_id"=>"ASC"]);

        $cartList = array();
        $totalPrice = 0;
        foreach($cartListObj as $cartObj){
            $cartList[] = array(
                'cart_id' => $cartObj->cart_id,
                'main_image_url' => $cartObj->ch_product_option->ch_product->ch_image->ch_image_file[0]->murl,
                'sellerName' => $cartObj->ch_product_option->ch_product->designer_name,
                'product_name' => $cartObj->ch_product_option->ch_product->name,
                'product_code' => base64_encode(EncryptService::Instance()->encrypt($cartObj->ch_product_option->product_code)),
                'product_option_code' => $cartObj->product_option_code,
                'finalPurChase' => ($cartObj->ch_product_option->stock<$cartObj->ch_product_option->max_purchase)?$cartObj->ch_product_option->stock:$cartObj->ch_product_option->max_purchase,
                'option' => $cartObj->ch_product_option->name,
                'quantity' => $cartObj->quantity,
                'price' => $cartObj->ch_product_option->price,
                'quantityPrice' => $cartObj->quantity * $cartObj->ch_product_option->price,
                'max_purchace' => $cartObj->ch_product_option->max_purchase,
                'leftStock' => $cartObj->ch_product_option->stock
            );

            $totalPrice+= $cartObj->quantity * $cartObj->ch_product_option->price;
        }

        return array(
            'cartList' => $cartList,
            'totalPrice' => $totalPrice
        );
    }


    /** 장바구니에 담기 */
    public function saveCart($data,$userid){

        $result = array();
        $productOptionCode = $data['product_option_code'];

        $data['users_id'] = $userid;

        $productStatus = $this->ChProductOption->find()->where(['product_option_code'=>$productOptionCode])->first();
        if($productStatus->stock < 1){
            $result['result'] = false;
            $result['msg'] = 'soldout';
        }else if($productStatus->stock < $data['quantity']) {
            $result['result'] = false;
            $result['msg'] = 'out of stock';
        }else if($data['quantity'] > $productStatus->max_purchase){
            $result['result'] = false;
            $result['msg'] = 'max quantity over';
        }else{

            $alreadyInCart = $this->ChCart->find()->where(['product_option_code'=>$productOptionCode,'users_id'=>$userid])->first();
            if (empty($alreadyInCart)) {
                $data['creator'] = $userid;
                $data['modifier'] = $userid;
                $chChartEntity = $this->ChCart->newEntity($data);
            }else{
                $data['modifier'] = $userid;
                $chChartEntity = $this->ChCart->patchEntity($alreadyInCart,$data);
            }

            $cartResult = $this->ChCart->save($chChartEntity);
            if ($cartResult) {
                $result['result'] = true;
                $result['msg'] = 'success';
            } else {
                $result['result'] = false;
                $result['msg'] = 'fail';
            }
        }
        return $result;
    }

    /** 장바구니 삭제 */
    public function removeCart($cartIds,$userId){
        $cartItems = $this->ChCart->find()->where(['cart_id in'=>$cartIds,'users_id'=>$userId]);

        $resultCnt = 0;
        foreach($cartItems as $item){
            $resultCnt +=($this->ChCart->delete($item))?1:0;
        }

        $result = array();

        if(sizeof($cartIds)==$resultCnt){
            $result['result'] = true;
            $result['msg'] = 'success';
        }else if($resultCnt==0){
            $result['result'] = false;
            $result['msg'] = 'deleteFail';
        }else if($resultCnt<sizeof($cartIds)){
            $result['result'] = true;
            $result['msg'] = 'lack';
        }


        if($result['result']){
            $cartListObj = $this->selectCartList($userId);
            $totalPrice = 0;
            $cnt=0;
            foreach($cartListObj['cartList'] as $cartObj){
                $cnt++;
                $totalPrice+= $cartObj['quantity'] * $cartObj['price'];
            }
            $result['cartCnt']=$cnt;
            $result['totalPrice']=$totalPrice;
        }

        return $result;
    }

    /** 장바구니 삭제 옵션 코드 기준 */
    public function removeCartOfProductionCode($productOptionCode,$usersId){
        $cartItems =$this->ChCart->find()->where(['product_option_code in'=>$productOptionCode,'users_id'=>$usersId]);
        $resultCnt = 0;
        foreach($cartItems as $item){
            $resultCnt +=($this->ChCart->delete($item))?1:0;
        }
        $result = array();
        if(sizeof($productOptionCode)==$resultCnt){
            $result['result'] = true;
            $result['msg'] = 'success';
        }else if($resultCnt==0){
            $result['result'] = false;
            $result['msg'] = 'deleteFail';
        }else if($resultCnt<sizeof($productOptionCode)){
            $result['result'] = true;
            $result['msg'] = 'lack';
        }

        return $result;
    }

    /** 장바구니 수량 변경 */
    public function changeQuantity($cartId,$quantity,$userId){
        $cartEntity = $this->ChCart->find()->where(['cart_id'=>$cartId,'users_id'=>$userId])->first();

        $prdOption = $this->ChProductOption->find()->where(['product_option_code'=>$cartEntity['product_option_code']])->first();

        $result = array();
//        Debugger::log($cartEntity['product_option_code']."::::".$prdOption->stock."::::".$quantity);
        if($prdOption->stock == 0){
            $result['result'] = false;
            $result['msg'] = 'OutOfStock';
            return $result;
        }else if($prdOption->stock < $quantity){
            $result['result'] = false;
            $result['msg'] = 'MoreThenStock';
            return $result;
        }else if($prdOption->max_purchase < $quantity){
            $result['result'] = false;
            $result['msg'] = 'MaxPurchase';
            return $result;

        }

        $newCartEntity = $this->ChCart->patchEntity($cartEntity,['quantity'=>$quantity]);
        $rtn = $this->ChCart->save($newCartEntity);

        if($rtn){
            $result['result'] = true;
            $result['msg'] = 'success';

            $cartListObj = $this->selectCartList($userId);
            $totalPrice = 0;
            $price = 0;
            foreach($cartListObj['cartList'] as $cartObj){
                if($cartId==$cartObj['cart_id']){
                    $price=$cartObj['quantity'] * $cartObj['price'];
                }
                $totalPrice+= $cartObj['quantity'] * $cartObj['price'];
            }
            $result['price']=$price;
            $result['totalPrice']=$totalPrice;

            return $result;
        }

        $result['result'] = false;
        $result['msg'] = 'fail';

        return $result;
    }
}
