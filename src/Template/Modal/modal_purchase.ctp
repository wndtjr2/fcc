<div class="modal__content">

    <div class="modal__mycart">

        <div class="modal__mycart--lists">
            <p class="headTitle"><?=__("구매 불가")?></p>
            <ul>

                <?php foreach($product as $prd):?>
                    <?php $aPrd = $prd->product;?>
                    <!-- Cart list -->
                    <li class="item">
                        <div class="modal__mycart--detail">
                            <div class="box">
                            <span class="cover" style="background-image: url('<?=$aPrd->image?>');">
                                <?php if($prd->reason == 'less'){?>
                                    <span class="error left"><?=__('{0} LEFT', [$aPrd->stock])?></span>
                                <?php }else{?>
                                    <span class="error"><?=__("SOLD OUT")?></span>
                                <?php }?>
                            </span>
                                <div class="details">
                                    <a href="#" class="title"><?=$aPrd->name?></a>
                                    <div class="infowrap">
                                        <?php $options = explode(';', $aPrd->option);?>
                                        <?php //debug($options)?>
                                        <p class="info color"><?=__("Color")?> : <?=$options[1]?></p>
                                        <p class="info size"><?=__("Size")?> : <?=$options[2]?></p>
                                        <p class="info size"><?=__("Quantity")?> : <?=$aPrd->order_quantity;?></p>
                                    </div>
                                    <div class="price">
                                        <span><?=$this->FccTv->currencyStr($aPrd->price)?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach;?>
            </ul>

            <!-- 내용 안내 -->
            <p class="why"><?=__("위 상품들은 <b>주문 가능 수량을 초과</b>하였습니다.")?></p>

            <!-- 장바구니로 돌아가기 -->
            <a href="/cart/" class="gotoCart"><?=__("장바구니로 돌아가기")?></a>

        </div>

    </div>

</div>

<style>
    .modal__content { max-width: 320px; }
    /*************/
    @media only screen and ( min-width: 736px ) {
        .modal__content { max-width: 700px; width: auto; }
    }
    @media only screen and ( min-width: 1024px ) {
        .modal__content { max-width: 800px; width: auto; }
    }
</style>