<div style="border: #e1e1e1 1px solid; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; letter-spacing: 3px; font-weight: 200; text-align: center;  no-repeat 0 0; max-width: 720px;">

    <!-- header : The area is fixed. -->
    <div style="padding: 30px 0; border-bottom: #e1e1e1 1px solid;">
        <h1 style="margin: 0;">
            <a href="#" target="_blank" style="display: block; width: 160px; height: 40px; margin: 0 auto;"><img src="<?=IMG_URI?>/_res/email/logo_fcctv.png" alt="FCCTV"></a>
        </h1>
    </div>

    <!-- headline -->
    <div style="padding: 0 10px 45px;">
        <h3 style="font-family: Arial; font-size: 48px; font-weight: 800; color: #ff4b46; margin: 45px 0 20px;">
            Warning
        </h3>
        <div style="font-size: 14px; letter-spacing: 1.5px; line-height: 1.5;">
            <p>
                상품코드(product_code) : <?=$product_code?><br>
                상품명 : <?=$product_name?><br>
                <?php $option = explode(';', $product_option_name);?>
                상품옵션명 : Color : <?= $option[1]?>, Size : <?=$option[2]?><br>의 재고가 5개 이하 입니다.
                재고관리가 필요합니다.
            </p>
            <br>
        </div>
    </div>
</div>