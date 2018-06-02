
<?php
foreach($order->ch_purchase as $purch){
    if(!is_null($purch->ch_shipping)){
        $address = $purch->ch_shipping;
    }
};
?>
<!-- mail form : strat -->
<div style="border: #e1e1e1 1px solid; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; letter-spacing: 3px; font-weight: 200; text-align: center; no-repeat 0 0; max-width: 720px;">

    <!-- header : The area is fixed. -->
    <div style="padding: 30px 0; border-bottom: #e1e1e1 1px solid;">
        <h1 style="margin: 0;">
            <a href="#" target="_blank" style="display: block; width: 160px; height: 40px; margin: 0 auto;"><img src="<?=IMG_URI?>/_res/email/logo_fcctv.png" alt="FCCTV"></a>
        </h1>
    </div>

    <!-- headline -->
    <div style="padding-bottom: 45px">
        <h3 style="font-family: Arial; font-size: 48px; font-weight: 800; color: #ff4b46; margin: 45px 0 20px;">
            <?=__('Thank you')?>
        </h3>
        <p style="padding: 0 15px; font-size: 14px; letter-spacing: 1.5px;">
            고객님, 저희 쇼핑몰을 이용해 주셔서 감사합니다. <br>
            <br>
            <b style="font-weight: 600;">
                <?=$buyerName?>
            </b>님께서 주문하신 제품이 주문 접수 되었습니다. <br>
            주문내역 및 배송정보는 내 주문내역에서 확인하실 수 있습니다. <br>
            고객님께 빠르고 정확하게 제품이 전달될 수 있도록 최선을 다하겠습니다.
        </p>
    </div>

    <!-- have a look -->
    <div style="margin: 0 5%; padding: 0 0 50px;">
        <h4 style="height: 10px; background: #000000; margin-bottom: 50px;">
                    <span style="position: relative; display: block; width: 260px; height: 22px; margin: 0 auto 0; background: #ffffff; text-align: center;">
                        <b style="position: absolute; top: -6px; left: 0; display: block; width: 100%; height: 22px; font-size: 20px; font-weight: 800; line-height: 22px;">
                            <?=__('YOUR ORDER INFO')?>
                        </b>
                    </span>
        </h4>

        <div style="padding: 0 0 30px; margin-bottom: 50px; border-bottom: #000 1px solid;">
            <table style="width: 100%; text-align: center; border: none; border-collapse: collapse; border-spacing: 0; font-size: 16px; color: #4d4d4d; letter-spacing: 1.5px;">
                <caption style="display: block; overflow: hidden; font-size: 0px; line-height: 0; text-indent: -9999px">Order</caption>
                <tbody>
                <tr>
                    <td>
                        <p style="font-weight: 600;">
                            주문번호
                            <br>
                            <span style="font-weight: 200; font-size: 14px;">
                                <?= $order->order_code?>
                            </span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-weight: 600;">
                            주문하시는 분
                            <br>
                            <span style="font-weight: 200; font-size: 14px;">
                                <?=$buyerName?>
                            </span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-weight: 600;">
                            전화번호
                            <br>
                            <span style="font-weight: 200; font-size: 14px;">
                                <?= (is_null($address->phone_decrypt))?'':$address->phone_decrypt;?>
                            </span>
                        </p>
                    </td>
                </tr>
<!--                <tr>-->
<!--                    <td>-->
<!--                        <p style="font-weight: 600;">-->
<!--                            핸드폰-->
<!--                            <br>-->
<!--                            <span style="font-weight: 200; font-size: 14px;">{mobileOrder}</span>-->
<!--                        </p>-->
<!--                    </td>-->
<!--                </tr>-->
                <tr>
                    <td>
                        <p style="font-weight: 600;">
                            배송 정보<br>
                            <span style="font-weight: 200; font-size: 14px;">
                                <?= (is_null($address->deliv_last_name))?'':$address->deliv_last_name;?>
                                &nbsp;
                                <?= (is_null($address->deliv_first_name))?'':$address->deliv_first_name;?>
                                <br>
                                <?=$this->FccTv->addressStr($address->zipcode, $address->address, $address->address2)?>
                                <br>
<!--                                <span style="color: black; font-weight: 700;">{memo}</span>-->
                            </span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-weight: 600;">
                            결제방법<br>
                            <span style="font-weight: 200; font-size: 14px;">
                                <?=$payment->method?>
                            </span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-weight: 600;">
                            결제금액<br>
                            <span style="font-weight: 200; font-size: 14px; color: #ff4b46; font-weight: 700;">
                                <?=$this->FccTv->currencyStr($payment->total)?>
                            </span>
                        </p>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>

        <!-- 상품 정보 -->

        <?php foreach($order->ch_purchase as $purchase):?>
            <div style="width: 270px; margin: 0 auto 30px; font-size: 16px; letter-spacing: 1px;">
                <div style="margin: 0 30px 20px;">
                    <img src="<?=$purchase->ch_product->ch_image->ch_image_file[0]->murl?>" style="width: 100%;" alt="">
                </div>
                <p style="margin: 2px 0;">
                    <b style="font-weight: 600;"><?=$purchase->ch_product->name?></b>
                </p>
                <?php $option = explode(';', $purchase->ch_product_option->name);?>
                <p style="margin: 2px 0;">
                    <?=__('Color')?>: <?=ucfirst($option[1])?>
                </p>
                <p style="margin: 2px 0;">
                    <?=__('Size')?>: <?=strtoupper($option[2])?>
                </p>
                <div style="padding-top: 10px; margin-top: 20px; border-top: 1px solid #d9d9d9;">
                    <p style="float: left; width: 30%; height: 30px; text-align: left; margin: 0; paddingL 0;"><b style="font-weight: 600;">&nbsp; 판매가</b></p>
                    <p style="float: right; width: 70%; height: 30px; text-align: right; margin: 0; paddingL 0;"><?=$this->FccTv->currencyStr($purchase->unit_price)?> &nbsp;</p>
                    <p style="float: left; width: 40%; height: 30px; text-align: left; margin: 0; paddingL 0;"><b style="font-weight: 600;">&nbsp; 수량</b></p>
                    <p style="float: right; width: 60%; height: 30px; text-align: right; margin: 0; paddingL 0;"><?=$purchase->quantity?> &nbsp;</p>
                    <p style="float: left; width: 40%; height: 30px; text-align: left; margin: 0; paddingL 0;"><b style="font-weight: 600;">&nbsp; 합계</b></p>
                    <p style="float: right; width: 60%; height: 30px; text-align: right; margin: 0; paddingL 0;"><?=$this->FccTv->currencyStr($purchase->amount + $purchase->shipping_price)?> &nbsp;</p>
                    <p style="float: left; width: 40%; height: 30px; text-align: left; margin: 0; paddingL 0;"><b style="font-weight: 600;">&nbsp; 배송비</b></p>
                    <p style="float: right; width: 60%; height: 30px; text-align: right; margin: 0; paddingL 0;">
                        <?= ($purchase->shipping_price == 0)?__("무료배송"):$this->FccTv->currencyStr($purchase->shipping_price);?>
                        &nbsp;
                    </p>
                </div>
                <div style="clear: both;"></div>
            </div>
        <?php endforeach;?>

    </div>

    <!-- footer : The area is fixed. -->
    <div style="padding: 20px 0; background: #000; text-align: center; color: #fff; font-weight: bold;">
        <?=__("Need help?")?> <a href="mailto:<?=FROMFCCTVMAIL?>" target="_blank" style="color: #ff4b46; text-decoration: underline; font-weight: bold"><?=FROMFCCTVMAIL?></a>
    </div>

</div>
<!-- mail form : end -->