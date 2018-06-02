<div style='margin: 35px auto; padding: 12px; border: #e5e5e5 1px solid; background: url("<?=IMG_URI?>/_res/email/form_frame.png") 0 0 repeat;'>
    <div style='position: relative; padding: 30px 10px 30px; background: #fff; text-align: center;'>
        <!-- header : The area is fixed. -->
        <div style="padding: 30px 0; border-bottom: #e1e1e1 1px solid;">
            <h1 style="margin: 0;">
                <a href="#" target="_blank" style="display: block; width: 160px; height: 40px; margin: 0 auto;"><img src="<?=IMG_URI?>/_res/email/logo_fcctv.png" alt="FCCTV"></a>
            </h1>
        </div>

        <div style='font-size: 18px; line-height: 48px; margin-bottom: 25px; padding-top: 30px;'>
            <span>고객님께서 주문하신 주문번호</span>
            <div style='padding-top: 0; margin-bottom: 10px;'>
                <a href="javascript:void(0);" style='display: inline-block; height: 46px; padding: 0 30px; border: none; border-radius: 23px; text-align: center; font-size: 12px; font-weight: 400; line-height: 46px; color: #fff; background: #ff4b46; text-decoration: none;'>
                    <?= $data['order_code']?>
                </a>
            </div>
            <span>에 대해 주문 취소 요청이 되었습니다.</span>
        </div>
        <p style='font-size: 22px; line-height: 34px; font-weight: 700;'>
            빠른 시일내로 처리 해드릴수 있도록 하겠습니다. <br>
            감사합니다.
        </p>

    </div>

    <!-- footer : The area is fixed. -->
    <div style="padding: 20px 0; background: #000; text-align: center; color: #fff; font-weight: bold;">
        <?=__("Need help?")?> <a href="mailto:<?=FROMFCCTVMAIL?>" target="_blank" style="color: #ff4b46; text-decoration: underline; font-weight: bold"><?=FROMFCCTVMAIL?></a>
    </div>
</div>