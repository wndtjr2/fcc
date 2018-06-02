<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip"><?=__('Orders')?></h2>

    <div class="contents">

        <div id="sign">
            <div class="sign__wrap">
            <!-- TIP1: 화면 분할, 2 컬럼. -->
                <h3 class="section__title"><span class="inner"><?=__('Orders')?></span></h3>
                <div class="screen__2column is-zoom">
                    <?=$this->element('usersMenu',[
                        'orders'=>'is-current'
                    ]);?>
                    <div class="screen__2column--right">

                        <!-- 분할 컨텐츠. -->
                        <div class="userpage__wrap account">
                            <p class="userpage__title"><?=__("Orders")?></p>

                            <!-- Orders 컨텐츠 -->
                            <ul class="orders__lists is-zoom">

                                <?php if(sizeof($orderListByDay)>0){ ?>
                                    <?php foreach($orderListByDay as $orderDay => $orderDayList){ ?>
                                        <!-- TIP1 : 날짜 목록 업데이트. -->
                                        <li class="orders">
                                            <h3 class="date"><span><?=$this->FccTv->dateToKorean($orderDay)?></span></h3>

                                            <?php foreach($orderDayList as $order){ ?>

                                                <!-- TIP2 : 주문 내역 묶음. -->
                                                <div class="orders__list">
                                                    <dl class="detail">
                                                        <dt>
                                                            <span><?=__("Order Number")?>.<?=$order['orderCode']?></span>
                                                        </dt>
                                                        <dd>
                                                            <strong><?=__($order['status'])?></strong>
                                                            <a href="/myorder/detail?ordercode=<?=$order['orderCode']?>" class="link"><?=__("View Detail")?></a>
                                                        </dd>
                                                    </dl>

                                                    <p class="total">
                                                        <span><?=__("{0} items", sizeof($order['items']))?></span>
                                                        <strong><?=__("Total")?> : <b><?=$this->FccTv->currencyStr($order['totalAmount'])?></b></strong>
                                                    </p>
                                                    <ul class="lists">
                                                        <?php foreach($order['items'] as $item){ ?>
                                                            <!-- TIP3 : 상품 묶음. -->
                                                            <li class="list">
                                                                <div class="orders__item">
                                                                    <div class="package">
                                                                        <span class="image" style="background-image: url('<?=$item['mainImageUrl']?>');"></span>
                                                                        <div class="summery">
                                                                            <p class="category"><?=$item['sellerName']?></p>
                                                                            <p class="title"><?=$item['productName']?></p>
                                                                            <p class="size">
                                                                                <?=__("Color")?> : <?=$item['option'][1]?><br/>
                                                                                <?=__("Size")?> : <?=$item['option'][2]?>
                                                                            </p>
                                                                            <p class="quantity"><?=__("Quantity")?> : <?=$item['quantity']?></p>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>

                                                            <!-- TIP3  -->
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                                <!-- TIP2 -->
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                <?php }else{ ?>

                                        <li class="orders">
                                            <h3 class="date"><span><img src="/_res/img/icon/icon_cc.png" alt="" class="icon_cc"> <?=__("주문 내역이 없습니다.")?></span></h3>
                                        </li>


                                <?php } ?>


                            </ul>

                        </div>
                    </div>
                </div>
            <!-- TIP1 -->
            </div>
        </div>

    </div> <!-- contents -->
</section>

