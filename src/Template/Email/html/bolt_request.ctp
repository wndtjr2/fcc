<div style="border: #e1e1e1 1px solid; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; letter-spacing: 3px; font-weight: 200; text-align: center; no-repeat 0 0; max-width: 720px;">

    <!-- header : The area is fixed. -->
    <div style="padding: 30px 0; border-bottom: #e1e1e1 1px solid;">
        <h1 style="margin: 0;">
            <a href="#" target="_blank" style="display: block; width: 160px; height: 40px; margin: 0 auto;"><img src="<?=IMG_URI?>/_res/email/logo_fcctv.png" alt="FCCTV"></a>
        </h1>
    </div>

    <!-- headline -->
    <div style="padding: 0 10px 45px;">
        <h3 style="font-family: Arial; font-size: 48px; font-weight: 800; color: #ff4b46; margin: 45px 0 20px;">
            <?=__('Goodbye !')?>
        </h3>
        <div style="font-size: 14px; letter-spacing: 1.5px; line-height: 1.5;">
            <p>
                <b style="font-weight: 600;"><?=$name?></b> 고객님, 그동안 <a href="http://www.fcctv.co.kr" style="text-decoration: none;"><b style="font-weight: 600; color: #ff4b46;">FCCTV</b></a>을 이용해 주셔서 대단히 감사합니다.
            </p>
            <br>
            <p>
                요청하신대로 FCCTV 회원 탈퇴가 이루어졌습니다. <br>
                <?=$name?> 고객님께 만족스런 쇼핑을 못드려 죄송합니다. <br>
                다음 기회에 FCCTV와 더 좋은 만남으로 이루어지길 바랍니다. <br>
                기타 문의사항이 있으시면, <a href="#" target="_blank" style="color: #ff4b46; text-decoration: underline;"><?=FROMFCCTVMAIL?></a>로 연락주시기 바랍니다. <br>
                <br>
                FCCTV을 이용해 주셔서 감사합니다.
            </p>
        </div>
    </div>

    <!-- footer : The area is fixed. -->
    <div style="padding: 20px 0; background: #000; text-align: center; color: #fff; font-weight: bold;">
        <?=__("Need help?")?> <a href="mailto:<?=FROMFCCTVMAIL?>" target="_blank" style="color: #ff4b46; text-decoration: underline; font-weight: bold"><?=FROMFCCTVMAIL?></a>
    </div>

</div>