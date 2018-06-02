<?php if(isset($error)):?>
    <script>
        alert("<?=$error;?>");
        window.location.href = "/";
    </script>
<?php endif;?>
<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip"><?=__("FCCTV 미디어 커머스 : 결재하기")?></h2>

    <div class="contents">

        <div id="checkout">
            <div class="checkout__wrap no-bottom">

                <h3 class="section__title"><span class="inner"><?=__("결재하기")?></span></h3>
                <!-- <p class="userpage__title">Checkout</p> -->
                <ul class="checkout__progress is-zoom current-3">
                    <li class="tabs tabs1"><span class="tab"><span></span><?=__("Shipping")?></span></li>
                    <li class="tabs tabs2"><span class="tab"><span></span><?=__("Payment")?></span></li>
                    <li class="tabs tabs3"><span class="tab is-current"><?=__("Review")?></span></li>
                </ul>

                <!-- Orders Detail 컨텐츠 -->
                <ul class="orders__lists is-zoom">
                    <li class="orders">
                        <h3 class="date"><span><?=__("결제를 완료할 수 없습니다")?></span></h3>
                        <p class="order__detail--address">
                            <br>
                            <br>
                            <strong><?=__("불편을 끼쳐드려 죄송합니다.")?></strong>
                            <span class="address">
                                <?=$msg?>
                                <br>
                                <br>
                                <?=__("계속된 문제 발생시 <a href='/contact/' class='link'>FCC 고객센터</a>로 문의 주시기 바랍니다.")?>
                            </span>
                            <br>
                            <br>
                        </p>
                    </li>

                    <?php if(isset($order)):?>
                    <li class="orders">
                        <h3 class="date"><span><?=__("매진 상품")?></span></h3>
                        <ul class="orders__details--list">
                            <?php foreach($order as $purchase):?>
                                <li class="list">
                                    <div class="package">
                                        <div class="box">
                                            <span class="image" style="background-image: url('<?=$purchase['image']?>');"></span>
                                            <div class="detail">
                                                <p class="category"><?=$purchase['designer']?></p>
                                                <p class="title"><?=$purchase['name']?></p>
                                                <?php $options = explode(';', $purchase['option']);?>
                                                <p class="size"><?=__("Color")?> : <?=$options[1]?> / <?=__('Size')?> : <?=$options[2]?></p>
                                                <p class="quantity"><?=__("Quantity")?> : <?=$purchase['order_quantity']?></p>
                                                <p class="total"><?=$this->FccTv->currencyStr($purchase['price'])?></p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach;?>
                        </ul>
                        <p class="noti"><?=__("위 상품들은 <span>주문 가능 수량을 초과</span>하였습니다.")?></p>
                    </li>
                    <?php endif;?>
                </ul>

                <!-- <p class="pages__contact">Questions? <a href="/contact/">Contact Us</a></p> -->
            </div>
        </div>

    </div> <!-- contents -->
</section>