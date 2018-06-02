<?php if(isset($error)):?>
    <script>
        modalalert('<?=$error;?>');
        window.location.href = '/';
    </script>
<?php endif;?>

<section id="sections">
    <h2 class="is-skip">Checkout</h2>

    <div class="contents">

        <div id="checkout">
            <div class="checkout__wrap no-bottom">

                <p class="userpage__title"><?=__('Checkout')?></p>
                <ul class="checkout__progress is-zoom current-3">
                    <li class="tabs tabs1"><span class="tab"><span></span><?=__('Shipping')?></span></li>
                    <li class="tabs tabs2"><span class="tab"><span></span><?=__('Payment')?></span></li>
                    <li class="tabs tabs3"><span class="tab is-current"><?=__('Review')?></span></li>
                </ul>

                <!-- Orders Detail 컨텐츠 -->
                <ul class="orders__lists is-zoom">
                    <li class="orders">
                        <p class="order__detail--number">

                            <span id="ORDER_DATE"><b><?=__('Order Date')?></b> : <?=$this->FccTv->dateToKorean($date)?></span>
                            <span><b><?=__('Order Number')?></b> : <?= $order->order_code?></span>
                        </p>
                    </li>
                    <li class="orders">
                        <h3 class="date"><span><?=__('Order Status')?></span></h3>
                        <p class="order__detail--status">
                            <?= (empty($order->ch_purchase[0]->status))?'':__($order->ch_purchase[0]->status);?>
                            <?php if($order->ch_purchase[0]->status == 'purchased'):?>
                                <button type="button" class="cancel" id="CANCEL_ORDER"><?=__('Cancel this order')?></button>
                            <?php endif;?>

                        </p>
                    </li>
                    <?php if($isShip):?>
                        <li class="orders">
                            <h3 class="date"><span><?=__('Shipping Address')?></span></h3>
                            <p class="order__detail--address">
                                <strong id="BUYER"></strong>
                                <span class="address" id="ADDRESS"></span>
                                <span class="nation" id="COUNTRY"></span>
                                <span class="phone" id="PHONE"></span>
                            </p>
                        </li>
                    <?php endif;?>
                    <li class="orders">
                        <h3 class="date"><span><?=__('Order Summary')?></span></h3>
                        <p class="order__detail--summary">
                            <span class="total2"><?=__('Total')?> : <b id="TOTAL"></b></span>
                        </p>
                    </li>
                    <li class="orders">
                        <h3 class="date"><span><?=__('{0} items', count($order->ch_purchase))?></span></h3>
                        <ul class="orders__details--list">
                            <!-- TIP1 : 상품 리스트 -->
                            <?php foreach($order->ch_purchase as $purchase):?>
                            <li class="list">
                                <div class="package">
                                    <span class="image" style="background-image: url('<?=$purchase->ch_product->ch_image->ch_image_file[0]->murl?>');"></span>
                                    <div class="detail">
                                        <p class="category"><?=$purchase->ch_product->designer_name?></p>
                                        <p class="title"><?=$purchase->ch_product->name?></p>
                                        <?php $option = explode(';', $purchase->ch_product_option->name);?>
                                        <p class="size"><?=__('Color')?> : <?=ucfirst($option[1])?> / <?=__('Size')?> : <?=ucfirst($option[2])?></p>
                                        <p class="quantity"><?=__('Quantity')?> : <?=$purchase->quantity?></p>
                                        <p class="total"><?=$this->FccTv->currencyStr($purchase->amount)?></p>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach;?>
                            <!-- TIP1 -->
                        </ul>
                    </li>
                </ul>

                <p class="pages__contact"><?=__("Questions?")?> <a href="/contact/"><?=__("Q&amp;A")?></a></p>
            </div>
        </div>

    </div> <!-- contents -->
</section>

<?php
foreach($order->ch_purchase as $purch){
    if(!is_null($purch->ch_shipping)){
        $address = $purch->ch_shipping;
    }
};
?>
<script type="text/javascript">

    $(function(){
        <?php if($isShip):?>
            //shipping address

            first_name = "<?= (is_null($address->deliv_first_name))?'':$address->deliv_first_name;?>";
            last_name = "<?= (is_null($address->deliv_last_name))?'':$address->deliv_last_name;?>";
            $("#BUYER")[0].innerHTML = last_name + " " +first_name;

            address = "<?=$this->FccTv->addressStr($address->zipcode, $address->address, $address->address2);?>";
            $("#ADDRESS")[0].innerHTML = address;

            country = "<?= (is_null($address->code_country->country_name))?'':$address->code_country->country_name;?>";
            $("#COUNTRY")[0].innerHTML = country;

            phone = "<?= (is_null($address->phone_decrypt))?'':$address->phone_decrypt;?>";
            $("#PHONE")[0].innerHTML = "<?=__('Phone Number')?>" + " : " + phone;

            //Order Summary
            //Shiping = "<?= (empty($shipping))?__('무료배송'):$this->FccTv->currencyStr($shipping);?>";
            //$("#SHIPPING")[0].innerHTML = Shiping;
        <?php endif;?>
        //Order Summary
        //Subtotal = "<?= (empty($subtotal))?0:$this->FccTv->currencyStr($subtotal);?>";
        //Shiping = "<?= (empty($shipping))?__('무료배송'):$this->FccTv->currencyStr($shipping);?>";
        Total = "<?= (empty($total))?0:$this->FccTv->currencyStr($total);?>";
        //$("#SUBTOTAL")[0].innerHTML = Subtotal;
        //$("#SHIPPING")[0].innerHTML = Shiping;
        $("#TOTAL")[0].innerHTML = Total;
    });

    function closeModal(){
        $('#CANCEL_ORDER').prop('disabled', false);
        modal.hide();
    }

    function contShopping(){
        window.document.location.href = '/';
    };

    $("#CANCEL_ORDER").on("click", function(){
        $('#CANCEL_ORDER').prop('disabled', true);
        modal_show2('modalCancelOrder');
    });

    function cancelOrder(){
        $.ajax({
            url: '/Orders/cancelOrder',
            dataType: 'text',
            type : 'Post',
            data : {
                order_code: "<?=$order->order_code?>",
                users_id: "<?=$this->request->session()->read('Auth.User.id');?>"
            },
            success: function (rtn) {
                if(rtn == 'success'){
                    modal_show2('modalOrderCancelComplete');
                }else{
                    modalalert('The order cancellation has been failed.');
                }
            },
            error: function(request, status, error){
                modalalert("code" + request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    };

</script>