<script src="/js/back/backModal.js"></script>

<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip"><?=__("Order Details")?></h2>

    <div class="contents">

        <div id="sign">
            <div class="sign__wrap">
            <!-- TIP1: 화면 분할, 2 컬럼. -->
                <h3 class="section__title"><span class="inner"><?=__("주문 내역")?></span></h3>
                <div class="screen__2column is-zoom">
                    <?=$this->element('usersMenu',[
                        'orders'=>'is-current'
                    ]);?>
                    <div class="screen__2column--right">

                        <!-- 분할 컨텐츠. -->
                        <div class="userpage__wrap account">
                            <p class="userpage__title"><?=__("Order Details")?></p>

                            <!-- Orders Detail 컨텐츠 -->
                            <ul class="orders__details is-zoom">
                                <li class="lists">
                                    <p class="order__detail--number">
                                        <span><b><?=__("Order Date")?></b> : <?=$this->FccTv->dateToKorean($order['orderDate'])?></span>
                                        <span><b><?=__("Order Number")?></b> : <?=$order['orderCode']?></span>
                                    </p>
                                </li>
                                <li class="lists f-left">
                                    <h3><span><?=__("Order Status")?></span></h3>
                                    <p class="order__detail--status">
                                        <?=__($order['status'])?>
                                        <?php if($order['status']=="shipped" || $order['status']=="completed" || $order['status']=="delivered"){ ?>
                                            <?php foreach($order["trackingUrl"] as $trackingNo => $url){ ?>
                                                <span class="tracking"><?=__("Tracking Number")?> : <a href="<?=$url?>"><?=$trackingNo?></a></span>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if($order['status']=="purchased"){ ?>
                                            <button type="button" class="cancel" ordercode="<?=$order['orderCode']?>" transId="<?=$order['transId']?>" id="cancelOrder"><?=__("Cancel this order")?></button>
                                        <?php }?>

                                    </p>
                                </li>
                                <li class="lists f-left2">
                                    <h3><span><?=__("Shipping Address")?></span></h3>
                                    <p class="order__detail--address">
                                        <strong><?=$order['shippingAddr']['dilivName']?></strong>
                                        <span class="address"><?=$order['shippingAddr']['address'].$order['shippingAddr']['address2']?>[<?=$order['shippingAddr']['zipcode']?>]</span>
                                        <span class="nation"><?=$order['shippingAddr']['state']?></span>
                                        <span class="phone"><?=__("Phone Number")?> : <?=$order['shippingAddr']['phone']?></span>
                                    </p>
                                </li>
                                <li class="lists f-right">
                                    <h3><span><?=__("Order Summary")?></span></h3>
                                    <p class="order__detail--summary">
                                        <span class="line"><?=__("Subtotal")?> : <b><?=$this->FccTv->currencyStr($order['subTotal'])?></b></span>
                                        <?php $shipping = $order['shippingPrice'];?>
                                        <span class="line"><?=__("Shipping")?> : <b><?=($shipping == 0)?__('무료배송'):$this->FccTv->currencyStr($shipping)?></b></span>
                                        <!--<span class="line"><?//=__("Tax")?> : <b><?//=$this->FccTv->currencyStr($order['tax'])?></b></span>-->
                                        <span class="total"><?=__("Total")?> : <b><?=$this->FccTv->currencyStr($order['totalAmount'])?></b></span>
                                    </p>
                                </li>
                                <li class="lists f-right">
                                    <h3><span><?=__("{0} items", sizeof($order['items']))?></span></h3>
                                    <ul class="orders__details--list">
                                        <?php foreach($order['items'] as $item){ ?>
                                        <!-- TIP1 : 상품 리스트 -->
                                        <li class="list">
                                            <div class="package">
                                                <span class="image" style="background-image: url('<?=$item['mainImageUrl']?>');"></span>
                                                <div class="detail">
                                                    <p class="category"><?=$item['sellerName']?></p>
                                                    <p class="title"><?=$item['productName']?></p>
                                                    <p class="size">
                                                        <?=__("Color")?> : <?=$item['option'][1]?><br/>
                                                        <?=__("Size")?> : <?=$item['option'][2]?>
                                                    </p>
                                                    <p class="quantity"><?=__("Quantity")?> : <?=$item['quantity']?></p>
                                                    <p class="total"><?=$this->FccTv->currencyStr($item['amount'])?></p>
                                                    <p class="tracking">
                                                        <?php if($item['status']=="shipped" || $item['status']=="completed" || $item['status']=="Deliverd"){ ?>
                                                        <?=__("Tracking")?> <span>No.<?=$item['trackingNum1']?></span><br>
                                                        <?php } ?>

                                                        <?php
                                                            //환불여부 확인
                                                            $isRefund =false;
                                                            if(isset($order["refundItems"][$item['purchaseCode']])) {
                                                                if ($item['amount'] == $order["refundItems"][$item['purchaseCode']]) {
                                                                    $isRefund = true;
                                                                }
                                                            }

                                                            $isRefundRequest = in_array($item['purchaseCode'],$order["claimItems"]);
                                                        ?>

                                                        <?php if($isRefundRequest==true && $isRefund==false){?>
                                                            <span class="btn refund"><?=__("Refund")?></span>
                                                        <?php } else if($isRefundRequest==true && $isRefund==true){ ?>
                                                            <span class="btn refund"><?=__("Refunded")?></span>
                                                        <?php }else{ ?>
                                                            <?php if($item['status']=="delivered"){ ?>
                                                                <button type="button" class="btn return returnBtn" pcode="<?=$item['purchaseCode']?>" amt="<?=$item['amount']?>">Return</button>
                                                            <?php }else{?>
                                                                <?=__($item['status'])?>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                        <?php } ?>
                                        <!-- TIP1 -->
                                    </ul>
                                </li>
                            </ul>
                            <input type="hidden" id="purchase_code" name="purchase_code">
                            <input type="hidden" id="amount" name="amount">
                            <input type="hidden" id="transactionId" name="transactionId" value="<?=$order['transId']?>">
                            <input type="hidden" id="orderCode" name="orderCode" value="<?=$order['orderCode']?>">
                            <p class="pages__contact"><?=__("Questions?")?> <a href="/contact/"><?=__("Contact Us")?></a></p>

                        </div>
                    </div>
                </div>
            <!-- TIP1 -->
            </div>
        </div>

    </div> <!-- contents -->
</section>

<script>
    var isProcessing = false;

    /** 환불 요청 **/
    $(".returnBtn").on("click",function(){
        var purchaseCode = $(this).attr("pcode");
        var amount = $(this).attr("amt");

        $("#purchase_code").val(purchaseCode);
        $("#amount").val(amount);

        backModal.show('modalOrderReturn');
    });

    function fnRefundRequest() {
        if (isProcessing) {
            modalalert("현재 진행중 입니다.");
            return false;
        }
        isProcessing = true;

        if ($("#claim_content").val().trim() == '') {
            modalalert('<?=__("메시지 입력이 필요합니다.")?>');
            return false;
        }
        var purChaseCodeVal = $("#purchase_code").val();
//        var amountVal = $("#amount").val();
        var openTypeVal = $("#open_type").val();
        var contentVal = $("#claim_content").val();
        var transactionIdVal = $("#transactionId").val();
        var orderCodeVal = $("#orderCode").val();

        var objData = {
            order_code : orderCodeVal,
            purchase_code : purChaseCodeVal,
            amount : 0,
            open_type :openTypeVal,
            content : contentVal,
            transaction_id : transactionIdVal
        };

        $.ajax({
            url: '/myorder/refundRequest',
            dataType: 'json',
            type : 'Post',
            data : objData,
            success: function (rtn) {

                if(rtn.result==true) {
//                    location.reload();
                    backModal.show2('modalOrderReturnSuccess');
                }else {
                    if (rtn.msg == "already") {
                        modalalert("<?=__("이미 처리중 입니다.<br/>잠시만 기다려주세요.")?>")
                    }else if(rtn.msg == "is not yours"){
                        modalalert("<?=__("잘못된 접근 입니다.")?>")
                    }else {
                        modalalert('fail');
                    }
                }

            },
            complete: function() {
                isProcessing = false;
            }
        });

    }

    /** 주문 취소 **/
    $("#cancelOrder").on("click",function(){
        backModal.show('modalOrderCancel');
    });

    function fnCancelOrder() {
        if (isProcessing) {
            return false;
        }
        isProcessing = true;

        var orderCodeVal = $('#cancelOrder').attr("ordercode");
        var transIdVal = $('#cancelOrder').attr("transId");
        $.ajax({
            url: '/myorder/cancelRequest',
            dataType: 'json',
            type : 'Post',
            data : {
                order_code : orderCodeVal,
                transid : transIdVal
            },
            success: function (rtn) {
                if(rtn.result==true) {
                    backModal.show2('modalOrderCancelComplete');
                }else{
                    modalalert('fail');
                }
            },
            complete: function() {
                isProcessing = false;
            }
        });
    }
</script>