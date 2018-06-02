<?php
/**
 * Created by PhpStorm.
 * User: brickandmon
 * Date: 2016. 1. 15.
 * Time: 오후 5:06
 */

namespace App\Controller;


use App\Service\MyOrderService;

class MyorderController extends AppController
{

    private $myOrderService;

    public function isAuthorized(){
        return true;
    }

    public function initialize(){
        parent::initialize();
        $this->myOrderService = MyOrderService::Instance();
    }

    /** 구매이력  */
    public function index(){
        $userId = $this->Auth->user('id');
        $orderListByDay = $this->myOrderService->getMyOrders($userId);

        $this->set("orderListByDay",$orderListByDay);
    }

    /** 구매한 내용 주문상세  */
    public function detail(){
        $ordercode = $this->request->query('ordercode');

        $orderDetailObj = $this->myOrderService->orderDetail($ordercode,$this->Auth->user());
        if($orderDetailObj==false){
            $this->redirect("/");
        }
        $this->set("order",$orderDetailObj);
    }

    /** 주문 취소 요청  */
    public function cancelRequest(){

        $this->autoRender = false;
        $data = $this->request->data();

        $orderCode = $data['order_code'];

        $userInfo = $this->Auth->user();
        $userId = $userInfo['id'];
        $userEmail = $userInfo['user_account']['email'];

        $result = $this->myOrderService->orderCancelTotal($orderCode,$userEmail,$userId);

        echo json_encode($result);
    }

    /** 주문 환불 요청  */
    public function refundRequest(){
        $this->autoRender = false;

        $userInfo = $this->Auth->user();
        $userId = $userInfo['id'];
        $userEmail = $userInfo['user_account']['email'];

        $data = $this->request->data();
        $result = $this->myOrderService->refundReason($data,$userId,$userEmail);

        echo json_encode($result);
    }


}