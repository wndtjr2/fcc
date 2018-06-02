<!-- mail form : strat -->
<div style="border: #e1e1e1 1px solid; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; letter-spacing: 3px; font-weight: 200; text-align: center; max-width: 680px;">

    <!-- header : The area is fixed. -->
    <div style="padding: 30px 0; border-bottom: #e1e1e1 1px solid;">
        <h1 style="margin: 0;">
            <a href="#" target="_blank" style="display: block; width: 160px; height: 40px; margin: 0 auto; text-indent: -9999px; background: url('<?=IMG_URI?>/_res/email/logo_fcctv.png') no-repeat 0 0;"></a>
        </h1>
    </div>

    <!-- headline -->
    <div style="padding-bottom: 45px">
        <h3 style="font-family: Arial; font-size: 42px; font-weight: 800; color: #ff4b46; margin: 45px 0 20px;">
            <?=__("FORGOT<br>YOUR<br>PASSWORD?")?>
        </h3>
        <br>
        <br>
        <span style="display: block; padding: 10px 0; margin: 0 10px; background: #f1f1f1; font-size: 18px; line-height: 1;">
            <a href="<?php echo $data['domain'].'resetPassword?token='.$data['token'] ?>" style="font-size: 12px; font-family: Arial; color: #000; text-decoration: none;">
                <?php echo $data['domain'].'resetPassword?token='.$data['token'] ?>
            </a>
            <br>
            <br>
            <a href="<?php echo $data['domain'].'resetPassword?token='.$data['token'] ?>" style="background-color: #ff4b46; color: #fff; display:inline-block; padding: 5px 10px; text-decoration: none; font-size: 12px; border-radius: 3px;">클릭하세요</a>
        </span>

        <p style="font-size: 14px;">
            <b style="font-weight: 600;"><?= $data['last_name'].' '.$data['first_name']?></b>님 안녕하세요. <a href="http://www.fcctv.co.kr" style="text-decoration: none;"><b style="font-weight: 600; color: #ff4b46;">FCCTV</b></a> 입니다. <br>
            요청하신 비밀번호 찾기 비밀번호 초기화 링크 안내 드립니다. <br>
<!--            아래번호를 인증번호 입력란에 입력해 주세요.-->
        </p>
    </div>

    <!-- footer : The area is fixed. -->
    <div style="padding: 20px 0; background: #000; text-align: center; color: #fff; font-weight: 100;">
        <?=__("Need help?")?> <a href="mailto:<?=FROMFCCTVMAIL?>" target="_blank" style="color: #ff4b46; text-decoration: underline;"><?=FROMFCCTVMAIL?></a>
    </div>

</div>
<!-- mail form : end -->