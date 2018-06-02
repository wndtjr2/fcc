<?php
/**
 * Created by PhpStorm.
 * User: brickandmon
 * Date: 2016. 1. 18.
 * Time: 오후 4:26
 */

namespace App\Service;

use App\Model\Table\ChallengeEntryTable;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Database\Schema\Table;
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
 * Interface OrderInterface
 * @package App\Service
 */
interface OrderInterface{

    /**
     * @param $productOptions
     * @return mixed
     */
    public function getProductWithProductOption($productOptions);

    /**
     * @return mixed
     */
    public function createOrder($data, $quantityArray, $userId);
}

/**
 * Class OrderService
 * @package App\Service
 */
class OrderService implements OrderInterface
{

    /**
     * @var \Cake\ORM\Table
     */
    private $Users;

    /**
     * @var \Cake\ORM\Table
     */
    private $UserAccounts;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChProduct;

    /**
     * @var \Cake\ORM\Table
     */
    private $CodeCountry;

    /**
     * @var ProductService
     */
    private $ProductService;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChOrder;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChProductOption;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChPurchase;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChPayment;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChShipping;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChProductShippingCharge;

    /**
     * @var \Cake\ORM\Table
     */
    private $McBasicShippingCharge;

    /**
     * @var \App\Service\AddressService
     */
    private $AddressService;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChCart;

    /**
     * @var \Cake\ORM\Table
     */
    private $McUserAddrInfo;

    private $McCancelPayment;

    private function __construct()
    {
        $this->Users = TableRegistry::get("Users");
        $this->UserAccounts = TableRegistry::get('UserAccounts');
        $this->ChProduct = TableRegistry::get('ChProduct');
        $this->CodeCountry = TableRegistry::get('CodeCountry');
        $this->ProductService = ProductService::Instance();
        $this->ChOrder = TableRegistry::get('ChOrder');
        $this->ChProductOption = TableRegistry::get('ChProductOption');
        $this->ChPurchase = TableRegistry::get('ChPurchase');
        $this->UserService = UserService::Instance();
        $this->ChPayment = TableRegistry::get('ChPayment');
        $this->ChShipping = TableRegistry::get('ChShipping');
        $this->ChProductShippingCharge = TableRegistry::get('ChProductShippingCharge');
        $this->McBasicShippingCharge = TableRegistry::get('McBasicShippingCharge');
        $this->AddressService = AddressService::Instance();
        $this->ChCart = TableRegistry::get('ChCart');
        $this->McUserAddrInfo = TableRegistry::get('McUserAddrInfo');
        $this->McCancelPayment = TableRegistry::get("McCancelPayment");
    }

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new OrderService();
        }
        return $inst;
    }

    public function getProductWithProductOption($productOptions)
    {
        $this->ChProductOption->primaryKey('product_code');
        $contain = [
            'ChProduct' => [
                'ChImage' => [
                    'ChImageFile' => function($q){
                        return $q->order(['seq' => 'ASC']);
                    }
                ]
            ]
        ];
        $product = $this->ChProductOption->find()->contain($contain)->where(["product_option_code in" => $productOptions]);
        return $product;
    }

    public function createOrder($products, $quantityArray, $userId)
    {
        //$shippingTotal = 0;
        $status = 'ordered';

        //calculation
        $grandTotal = 0;
        $productCode = array();
        foreach ($products as $prd) {
            $eachPrice = $prd->price;
            $total = $eachPrice * $quantityArray[$prd->product_option_code];
            $grandTotal += $total;
            $productCode[] = $prd->product_code;
        }

        //Hold Transaction
        $connection = ConnectionManager::get('default');
        $connection->begin();

        try {
            //get order_code
            $orderCode = $this->ProductService->generateCode('ORDER');

            $orderObj = $this->saveOrder($orderCode, $status, $userId, $grandTotal);

            foreach ($products as $product) {

                $purchaseCode = $this->ProductService->generateCode('PURCHASE');

                $this->savePurchase(
                    $purchaseCode,
                    $orderCode,
                    $product->product_code,
                    $product->product_option_code,
                    $product->ch_product->users_id,
                    $userId,
                    $product->price * $quantityArray[$product->product_option_code],
                    $product->price,
                    $quantityArray[$product->product_option_code],
                    $status
                );
            }

            $this->savePayment($orderCode, $grandTotal, $status);

            $connection->commit();

        } catch (Exception $e) {
            $connection->rollback();
            throw new InternalErrorException($e->getMessage());
        }

        return $orderObj->order_code;
    }

    public function countryList($productCode)
    {
        $shipping = $this->ProductService->getShippingWithCountryCode($productCode);
        foreach ($shipping as $ship) {
            $countryCodes[] = $ship->country_code;
        }

        $country = $this->CodeCountry
            ->find('list', [
                'keyField' => 'id',
                'valueField' => 'country_name'
            ])
            ->where(['country_code in' => $countryCodes])
            ->toArray();

        return $country;
    }

    public function getTotalPriceWithProductOptionCodes($productOption, $quantity)
    {
        $product = $productOption;
        $price = 0;
        if ($product[0]->delivery_yn == 'y') {
            //TODO 배송여부 가능 하면 진행
        }
        $price += $product[0]->price;
        $total = $price * $quantity;
        return $total;
    }

    public function getFgKey($cur, $amt, $orderCode)
    {
        $linkBuf = SECRETKEY . "?mid=" . MID . "&ref=" . $orderCode . "&cur=" . $cur . "&amt=" . $amt;
        $fgkey = hash("sha256", $linkBuf);
        return $fgkey;
    }

    public function createPayment($payment)
    {
        if (!$savedPayment = $this->ChPayment->save($payment)) {
            throw new InternalErrorException($savedPayment->errors());
        }
    }

    public function updateOrder($order, $transactionId, $status)
    {
        $orders = $this->ChOrder->patchEntity($order, [
            'status' => $status,
            'transaction_id' => $transactionId
        ]);
        if (!$this->ChOrder->save($orders)) {
            throw new InternalErrorException($orders->errors());
        }
    }

    public function updatePurchase($purchase, $status)
    {
        $purchases = $this->ChPurchase->patchEntity($purchase, [
            'status' => $status
        ]);
        if (!$this->ChPurchase->save($purchases)) {
            throw new InternalErrorException($purchases->errors());
        }
    }

    public function createShipping($shippingId, $orderCode, $countryCode)
    {
        $orderObj = $this->getOrderByOrderCodeForOrderBefore($orderCode);

        $productCode = array();
        foreach($orderObj->ch_purchase as $purchase){
            $productCode[] = $purchase->ch_product->product_code;
        }

        $shippingArr = $this->getProductShippingChargeArrayWithProductCode($productCode, $countryCode);
        $shippings = array();
        $shippingTotal = 0;
        $getTotalProductPrice = 0;
        foreach($orderObj->ch_purchase as $purc){
            $prdCode = $purc->product_code;
            if(isset($shippingArr[$prdCode])){
                $shippings[$purc->product_option_code] = $shippingArr[$prdCode][$countryCode];
                $shippingTotal += $shippingArr[$prdCode][$countryCode];
            }else{
                $shippings[$purc->product_option_code] = 0;
                $shippingTotal += 0;
            }
            $getTotalProductPrice += $purc->unit_price* $purc->quantity;
        }


        //Hold Transaction
        $connection = ConnectionManager::get('default');
        $connection->begin();
        try{

            $this->saveShippingForOrderBefore($orderObj, $shippingId);

            $this->updateOrderForOrderBefore($orderObj, $shippingTotal, $getTotalProductPrice);

            $this->updatePurchaseForOrderBefore($orderObj, $shippings);

            $this->updatePaymentForOrderBefore($orderObj, $shippingTotal, $getTotalProductPrice);

            $connection->commit();
        }catch (Exception $e) {
            $connection->rollback();
            throw new InternalErrorException($e->getMessage());
        }
    }

    public function getCountryWithCountryId($countryCode)
    {
        $countryName = $this->CodeCountry->find()->where(['country_code' => $countryCode])->first();
        return $countryName;
    }

    public function getProductShippingChargeWithProductCodeAndCountryCode($productCode, $countryCode)
    {

        $shipping = $this->ChProductShippingCharge->find()->where([
            'product_code in' => $productCode,
            'country_code' => $countryCode,
        ]);

        $shippingFee = 0;
        foreach($productCode as $prodCode){
            foreach($shipping as $ship){
                if($prodCode == $ship->product_code){
                    $shippingFee += $ship->shipping_charge;
                }
            }
        }
        return $shippingFee;
    }

    public function getProductShippingChargeArrayWithProductCode($productCodeArray){

        $returnArray = array();
        $shipping = $this->ChProductShippingCharge->find()->where([
            'product_code in' => $productCodeArray,
            'use_yn' => 'y'
        ]);

        if (!is_null($shipping)) {
            foreach ($shipping as $shipPrice) {
                if (in_array($shipPrice->product_code, $productCodeArray)) {
                    $returnArray[$shipPrice->product_code][$shipPrice->country_code] = $shipPrice->shipping_charge;
                }
            }
        }
        return $returnArray;
    }

    public function getShipping($usersId)
    {
        $address = $this->AddressService->selectAllAddress($usersId);
        return $address;
    }

    public function cancelOrder($order, $refundType, $amount, $reason, $transid){

        $post = [
            'ver' => 210,
            'mid' => MID,
            'txntype' => 'REFUND',
            'refundtype' => $refundType,
            'ref' => $order->order_code,
            'cur' => 'USD',
            'amt' => $order->total_price,
            'refundamt' => $amount,
            'transid' => $transid,
            'reason' => $reason,
            'lang' => 'EN',
            'fgkey' => $this->getFgKey('USD', $order->total_price, $order->order_code),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, CANCELURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $return = curl_exec($ch);

        parse_str($return, $rtn);
        return $rtn;

    }

    public function UpdateCancelOrder($orderObject){
        //check database
        $payments = $this->ChPayment->findByOrderCode($orderObject->order_code)->first();
        if(($orderObject->status != 'purchased') or ($payments->type != 'payment') or ($payments->status != 'success')){
            Debugger::log('Unable to cancel the order');
            throw new InternalErrorException('Unable to cancel the order');
        }
        $order = $this->ChOrder->patchEntity($orderObject, [
            'status' => 'cancel'
        ]);
        if(!$this->ChOrder->save($order)){
            Debugger::log('Unable to save the order in ChOrder table');
            throw new InternalErrorException($order->errors(), 'error');
        }
        $purchases = $this->ChPurchase->findByOrderCode($orderObject->order_code);
        foreach($purchases as $purchase){
            $purchaseEntity = $this->ChPurchase->patchEntity($purchase, [
                'status' => 'cancel'
            ]);
            if(!$this->ChPurchase->save($purchaseEntity)){
                Debugger::log('Unable to save the order in ChPurchase table');
                throw new InternalErrorException($purchaseEntity->errors());
            }
        }

        $cancelData = array(
            'transaction_id' => $orderObject->transaction_id,
            'order_code' => $orderObject->order_code,
            'message' => 'User Cancel',
            'status' => 'n',
            'err_msg' => '',
        );
        $cancelEntity = $this->McCancelPayment->newEntity($cancelData);
        if(!$this->McCancelPayment->save($cancelEntity)){
            Debugger::log('Unable to save the order in McCancelPayment table');
            throw new InternalErrorException($cancelEntity->errors());
        }
    }

    public function getOrderWithOrderCode($orderCode){
        $contain = array(
            'ChPurchase' => array(
                'ChProduct' => array(
                    'ChImage' => array(
                        'ChImageFile'
                    )
                ),
                'ChProductOption',
                'ChShipping' => array(
                    'CodeCountry'
                )
            ),
        );
        $this->ChShipping->primaryKey('country_code');
        $order = $this->ChOrder->find()->contain($contain)->where(['ChOrder.order_code' => $orderCode]);
        return $order;
    }

    public function getOrderWithOrderCodeForSimple($orderCode){
        $contain = array(
            'ChPurchase' => array(
                'ChProduct'
            )
        );
        $order = $this->ChOrder->find()->contain($contain)->where(['ChOrder.order_code' => $orderCode]);
        return $order;
    }

    public function recoverStock($productOptionCode, $quantity){
        $productOption = $this->ChProductOption->findByProductOptionCode($productOptionCode)->first();
        $productOptionEntity = $this->ChProductOption->patchEntity($productOption, [
            'stock' => $productOption->stock + $quantity
        ]);
        if(!$this->ChProductOption->save($productOptionEntity)){
            Debugger::log($productOptionEntity->errors());
            throw new InternalErrorException('Unable to recover stock for the product.');
        }
    }

    public function checkToShipByProductCode($productCodeArray){
        $products = $this->ChProduct->find()->where(['product_code in' => $productCodeArray])->toArray();

        //product_code 어레이에 담기
        $productCodeArray = array();
        $isShip = false;
        foreach($products as $prd){
            $productCodeArray[] = $prd->product_code;
            //product type 체크
            if($prd->delivery_yn == 'y'){
                $isShip = true;
            }
        }
        return $isShip;
    }

    public function getCountryCodeByMcUserAddrInfoId($addrId){
        $countryCode = $this->McUserAddrInfo->find()->where(['id' => $addrId])->first()->country_code;
        return $countryCode;
    }

    public function checkToShipByProductOpionCode($productOptionArray){
        $this->ChProductOption->primaryKey('product_code');
        $products = $this->ChProductOption->find()->contain(['ChProduct'])->where(['product_option_code in' => $productOptionArray]);

        $isShip = false;
        foreach($products as $prd){
            //product type 체크
            if($prd->ch_product->delivery_yn == 'y'){
                $isShip = true;
            }
        }
        return $isShip;
    }

    public function makeParam($P_TID, $P_MID){
        return "P_TID=".$P_TID."&P_MID=".$P_MID;
    }

    public function parseData($receiveMsg){
        $returnArr = explode("&", $receiveMsg);
        foreach($returnArr as $value){
            $tmpArr = explode("=",$value);
            $returnArr[] = $tmpArr;
        }
        return $returnArr;
    }

    public function checkMerchatIdAndOrderCodeFromInicis($merchantId, $orderCode){
        //MID 체크
        if($merchantId != MID){
            throw new BadRequestException('The merchant id is not equal to CrowdChallenge');
        }

        //오더코드 체크
        $orderObject = $this->ChOrder->findByOrderCode($orderCode)->first();
        if($orderObject->order_code != $orderCode){
            throw new BadRequestException('There is no order according to the ref code');
        }
        return $orderObject;

    }

    public function checkTotalAmountAndPaymentTable($orderCode, $totalAmount){

        //결제 금액 체크
        $purchases = $this->ChPurchase->findByOrderCode($orderCode)->toArray();
        $amount = 0;
        foreach($purchases as $purchase){
            $amount += $purchase->amount;
            $amount += $purchase->shipping_price;
        }
        //$amount = 10;
        if($amount != $totalAmount){
            Debugger::log($totalAmount . ' : ' . $amount, 'error');
            throw new BadRequestException('Internal Error the order total and the total given doesn\'t match.');
        }

        //Payment 테이블이 이미 있는지 체크
        $paymentCheck = $this->ChPayment->findByOrderCode($orderCode)->first();
        if($paymentCheck){
            Debugger::log($paymentCheck, 'error');
            throw new BadRequestException('The payment info is already made.');
        }
        return $purchases;

    }

    public function setPayment($orderCode, $transactionId, $payType, $paymethod, $resultCode, $total){
        //set received data into Order object
        $payment = $this->ChPayment->newEntity();
        $payment->order_code = $orderCode;
        $payment->transaction_id = $transactionId;
        $payment->gateway = 'eximbay';
        $payment->type = strtolower($payType);
        $payment->method = $paymethod;
        $payment->gateway_status = $resultCode;

        //성공 여부
        if($resultCode == '0000'){
            $payment->status = 'success';
        }else{
            $payment->status = 'fail';
        }
        $payment->total = $total;
        return $payment;
    }

    public function createProductCartArrayAndUpdatePurchase($purchaseObj, $productCartObj, $purchaseStatus){
        foreach($purchaseObj as $purchase){
            $this->updatePurchase($purchase, $purchaseStatus);
            //카트 삭제전 어레이에 product_option_code 담기
            foreach ($productCartObj as $productCart) {
                if ($productCart->product_option_code == $purchase->product_option_code) {
                    $productCartArray[] = $purchase->product_option_code;
                }
            }
        }
    }

    public function sendPurchaseInfo($mail, $order, $subtotal, $shipping, $total, $isShip, $payment, $buyerName){

        $email = new Email();

        try{
            $email->transport('brick')
                ->to($mail)//email
                ->from(FROMFCCTVMAIL)
                ->emailFormat('html')
                ->template('order_success')
                ->subject(__('Order Confirmation'))
                ->viewVars([
                    'subtotal' => $subtotal,
                    'order' => $order,
                    'shipping' => $shipping,
                    'total' => $total,
                    'isShip' => $isShip,
                    'payment' => $payment,
                    'buyerName' => $buyerName
                ]);
            if(!$email->send()){
                Debugger::log($email);
                throw new InternalErrorException("Unable to send purchase email.");
            }
        }catch (Exception $e){
            Debugger::log($e->getMessage());
            return 'fail';
        }
        return 'success';

    }

    public function sendUrgentStockWarning($product_code, $product_name, $product_option_name){

        $email = new Email();

        $emailPrefix = '';
        if(defined('EmailPrefix')){
            $emailPrefix = 'TEST-';
        }
        try{
            $email->transport('brick')
                ->to(FROMFCCTVMAIL)
                ->from(FROMFCCTVMAIL)
                ->emailFormat('html')
                ->template('stock_warning')
                ->subject($emailPrefix.__('Stock Warning'))
                ->viewVars([
                    'product_code' => $product_code,
                    'product_name' => $product_name,
                    'product_option_name' => $product_option_name
                ]);
            if(!$email->send()){
                Debugger::log($email);
                throw new InternalErrorException("Unable to send stock warning email.");
            }
        }catch(Exception $e){
            Debugger::log($e->getMessage());
        }




    }

    public function checkProductStatus($products, $quantityArray){
        //check stock and quantity
        $status = array();
        $isAbleToPurchase = true;
        foreach($products as $product){
            $prd = [
                'image' => $product->ch_product->ch_image->ch_image_file[0]->surl,
                'name' => $product->ch_product->name,
                'designer_name' => $product->ch_product->designer_name,
                'option' => $product->name,
                'order_quantity' => $quantityArray[$product->product_option_code],
                'price' => $product->price * $quantityArray[$product->product_option_code],
                'stock' => $product->stock
            ];
            if ($product->stock == 0) {
                $status[] = [
                    'product' => $prd,
                    'reason' => 'outOfStock'
                ];
                $isAbleToPurchase = false;
            }elseif($product->ch_product->status != 'open'){
                $status[] = [
                    'product' => $prd,
                    'reason' => 'outOfStock'
                ];
                $isAbleToPurchase = false;
            }
            elseif($product->stock < $quantityArray[$product->product_option_code]) {
                $status[] = [
                    'product' => $prd,
                    'reason' => 'less'
                ];
                $isAbleToPurchase = false;
            }
        }

        if($isAbleToPurchase == true){
            $status = false;
        }
        return $status;
    }

    public function updateShipping($shippingId, $purchases){
        $newShipping = $this->McUserAddrInfo->get($shippingId);

        $newShipping = [
            'zipcode' => $newShipping->zipcode,
            'country_code' => $newShipping->country_code,
            'address' => $newShipping->address,
            'address2' => $newShipping->address2,
            'deliv_first_name' => $newShipping->deliv_first_name,
            'deliv_last_name' => $newShipping->deliv_last_name,
            'deliv_phone_num' => $newShipping->deliv_phone_num
        ];
        $shipping = $this->ChShipping->updateAll($newShipping, ['purchase_code in' => $purchases]);
        return $shipping;
    }

    public function getOrderByOrderCodeWithoutImage($orderCode){
        $order = $this->ChOrder->find()
            ->contain(['ChPurchase'])
            ->where(['ChOrder.order_code' => $orderCode, 'status' => 'ordered'])
            ->first();
        return $order;
    }

    public function getShippingByPurchaseCode($purchaseCode){
        $shipping = $this->ChShipping->find()
            ->where(['purchase_code in' => $purchaseCode])->toArray();
        return $shipping;
    }

    private function saveOrder($orderCode, $status, $userId, $grandTotal){
        //set data to Order object
        $order = $this->ChOrder->newEntity();
        $order->order_code = $orderCode;
        $order->status = $status;
        $order->users_id = $userId;
        $order->total_price = $grandTotal;
        $order->creator = $userId;
        $order->modifier = $userId;
        if (!$this->ChOrder->save($order)) {
            Debugger::log($order->errors(), 'error');
            throw new InternalErrorException('Unable to save data to ChOrder table.');
        }
        return $order;
    }

    private function savePurchase($purchaseCode, $orderCode, $productCode, $productOptionCode, $sellerId, $buyerId, $amount, $unitPrice, $quantity, $status){

        $purchase = $this->ChPurchase->newEntity();
        $purchase->purchase_code = $purchaseCode;
        $purchase->order_code = $orderCode;
        $purchase->product_code = $productCode;
        $purchase->product_option_code = $productOptionCode;
        $purchase->seller_id = $sellerId;
        $purchase->buyer_id = $buyerId;
        $purchase->amount = $amount;
        $purchase->unit_price = $unitPrice;
        $purchase->quantity = $quantity;
        $purchase->status = $status;
        $purchase->creator = $buyerId;
        $purchase->modifier = $buyerId;

        if (!$this->ChPurchase->save($purchase)) {
            Debugger::log($purchase->errors(), 'error');
            throw new BadRequestException('Unable to save data to Purchase table.');
        }
        return $purchase;
    }

    private function savePayment($orderCode, $total, $status){

        $payment = $this->ChPayment->newEntity();
        $payment->order_code = $orderCode;
        $payment->total = $total;
        $payment->status = $status;

        if(!$this->ChPayment->save($payment)){
            Debugger::log($payment->errors(), 'error');
            throw new BadRequestException('Unable to save data to Purchase table.');
        }
        return $payment;
    }

    public function getOrderByOrderCodeForOrderBefore($orderCode){
        $contain = array(
            'ChPurchase' => array(
                'ChProduct',
                'ChProductOption',
                'ChShipping'
            ),
            'ChPayment'
        );
        $order = $this->ChOrder->find()->contain($contain)->where(['ChOrder.order_code' => $orderCode]);
        return $order->first();
    }

    private function saveShippingForOrderBefore($orderObj, $shippingId){

        $address = $this->McUserAddrInfo->get($shippingId);

        foreach($orderObj->ch_purchase as $purchase){

            $shipping = $this->ChShipping->newEntity();
            $shipping->zipcode = $address->zipcode;
            $shipping->country_code = $address->country_code;
            $shipping->address = $address->address;
            $shipping->address2 = $address->address2;
            $shipping->creator = $address->users_id;
            $shipping->modifier = $address->users_id;
            $shipping->deliv_first_name = $address->deliv_first_name;
            $shipping->deliv_last_name = $address->deliv_last_name;
            $shipping->deliv_phone_num = $address->deliv_phone_num;
            $shipping->purchase_code = $purchase->purchase_code;
            if(!$this->ChShipping->save($shipping)){
                throw new InternalErrorException("Unable to save shipping information");
            }
        }
    }

    private function updateOrderForOrderBefore($orderObj, $shippingTotal, $totalProductPrice){
        $order = $this->ChOrder->get($orderObj->order_code);
        $orderPatch = [
            'total_price' => ($totalProductPrice + $shippingTotal)
        ];
        $orders = $this->ChOrder->patchEntity($order, $orderPatch);
        if(!$this->ChOrder->save($orders)){
            Debugger::log($orders->errors(), 'error');
            throw new InternalErrorException("Unable to update order total_price");
        }
    }

    private function updatePurchaseForOrderBefore($orderObj, $shippings){

        foreach($orderObj->ch_purchase as $purchase){

            $purchase = $this->ChPurchase->get($purchase->purchase_code);
            $shippingFee = [
                'shipping_price' => !empty($shippings[$purchase->product_option_code])?$shippings[$purchase->product_option_code]:0
            ];

            $purchases = $this->ChPurchase->patchEntity($purchase, $shippingFee);
            if(!$this->ChPurchase->save($purchases)){
                Debugger::log($purchases->errors(), 'error');
                throw new InternalErrorException("Unable to update purchase shipping_price");
            }
        }
    }

    private function updatePaymentForOrderBefore($orderObj, $shippingTotal, $totalProductPrice){

        $payment = $this->ChPayment->get($orderObj->ch_payment->payment_id);
        $patch = [
            'total' => $totalProductPrice + $shippingTotal,
            'shipping' => $shippingTotal
        ];

        $payments = $this->ChPayment->patchEntity($payment, $patch);
        if(!$this->ChPayment->save($payments)){
            Debugger::log($payments->errors(), 'error');
            throw new InternalErrorException("Unable to update payment total and shipping");
        }
    }

    public function sendCancelFailEmail($msg, $order_code){
        $email = new Email();

        try{
            $email->transport('brick')
                ->to(FROMFCCTVMAIL)
                ->from(FROMFCCTVMAIL)
                ->emailFormat('html')
                ->template('cancel_fail')
                ->subject(__('Cancellation has failed.'))
                ->viewVars([
                    'msg' => $msg,
                    'order_code' => $order_code
                ]);
            if(!$email->send()){
                Debugger::log($email);
                throw new InternalErrorException("Unable to send cancel fail email.");
            }
        }catch (Exception $e){
            Debugger::log($e->getMessage());
            return false;
        }
        return true;
    }

    public function sendCommonEmail($subject, $entry, $message, $to = null, $from = null){

        $subjectWithPrefix = EmailPrefix . $subject;

        if(is_null($to)){
            $to = FROMFCCTVMAIL;
        }
        if(is_null($from)){
            $from = FROMFCCTVMAIL;
        }

        $email = new Email();

        try{
            $email->transport('brick')
                ->from($from)
                ->to($to)
                ->emailFormat('html')
                ->template('common_email')
                ->subject($subjectWithPrefix)
                ->viewVars([
                    'entry' => $entry,
                    'message' => $message
                ]);
            if(!$email->send()){
                Debugger::log($email);
                throw new InternalErrorException("Unable to send this email.");
            }
        }catch (\Exception $e){
            Debugger::log($e->getMessage());
            return false;
        }
        return true;
    }

    public function getOrderByOrderCodeForError($order_code){
        $contain = [
            'ChPurchase' => [
                'ChProduct' => [
                    'ChImage' => [
                        'ChImageFile'
                    ]
                ],
                'ChProductOption'
            ]
        ];
        $order = $this->ChOrder->find()->contain($contain)->where(['order_code' => $order_code])->first();
        if(is_null($order) or empty($order)){
            throw new BadRequestException("주문정보가 없습니다.");
        }
        //debug($order);
        $error = array();
        foreach($order->ch_purchase as $k => $purchase){
            if($purchase->ch_product->status != 'open'){
                $error[$k] = ['reason' => 'outofstock'];
            }
            elseif($purchase->ch_product_option->stock == 0){
                $error[$k] = ['reason' => 'outofstock'];
            }
            elseif($purchase->ch_product_option->stock < $purchase->quantity){
                $error[$k] = ['reason' => 'less'];
            }
            $info = [
                'designer' => $purchase->ch_product->designer_name,
                'name' => $purchase->ch_product->name,
                'option' => $purchase->ch_product_option->name,
                'stock' => $purchase->ch_product_option->stock,
                'image' => $purchase->ch_product->ch_image->ch_image_file[0]->surl,
                'order_quantity' => $purchase->quantity,
                'price' => ($purchase->amount + $purchase->shipping_price)
            ];
            if(isset($error[$k]['reason'])){
                $error[$k] = array_merge($error[$k], $info);
            }
        }
        return $error;





    }




















}