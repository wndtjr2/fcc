<?php
/**
 * Created by PhpStorm.
 * User: brickandmon
 * Date: 2016. 1. 15.
 * Time: 오후 5:06
 */

namespace App\Controller;


use App\Service\CartService;

class CartController extends AppController
{
    private $CartService;

    public function isAuthorized(){
        return true;
    }

    public function initialize(){
        parent::initialize();

        $this->CartService = CartService::Instance();

    }

    /**
     * 장바구니 리스트
     */
    public function index(){

        $userId = $this->Auth->user('id');

        $cart = $this->CartService->selectCartList($userId);

        $this->set("cartList",$cart['cartList']);
        $this->set("totalPrice",$cart['totalPrice']);
    }
    /**
     * 장바구니 ajax 리스트
     */
    public function getCartList(){
        $this->autoRender = false;
        $userId = $this->Auth->user('id');

        $cart = $this->CartService->selectCartList($userId);

        echo json_encode(['cartList'=>$cart['cartList'],'totalPrice'=>$cart['totalPrice']]);
    }
    /**
     * 장바구니 추가
     */
    public function add(){
        $this->autoRender = false;

        $userId = $this->Auth->user('id');
        $data = $this->request->data();

        $rtn = $this->CartService->saveCart($data,$userId);

        echo json_encode(array('result'=>$rtn));
    }

    /**
     * 장바구니 제거
     */
    public function remove(){
        $this->autoRender = false;
        $userId = $this->Auth->user('id');
        $data = $this->request->data();
        $cartIds = $data['cartId'];

        $rtn = $this->CartService->removeCart($cartIds,$userId);

        echo json_encode($rtn);
    }
    /*
     * 카트에 담은 상품 수
     */
    public function cartCnt()
    {
        $this->autoRender = false;
        $userId = $this->Auth->user('id');
        $rtn['cartCnt'] = $this->CartService->userCartCnt($userId);

        echo json_encode($rtn);
    }

    /**
     * 장바구니 수량 변경
     */
    public function quantityChange(){
        $this->autoRender = false;
        $data = $this->request->data();
        $cartId = $data['cartId'];
        $quantity = $data['quantity'];
        $userId = $this->Auth->user('id');

        $rtn = $this->CartService->changeQuantity($cartId,$quantity,$userId);

        echo json_encode($rtn);

    }
}