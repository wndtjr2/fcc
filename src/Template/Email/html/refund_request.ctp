<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="author" content="Naheo for HTML">
    <meta name="generator" content="">
    <meta name="application-name" content="Naheo for HTML">
    <title>F C C T V</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
</head>

<body>

<div style='margin: 35px auto; padding: 12px; border: #e5e5e5 1px solid; background: url("<?=IMG_URI?>/_res/email/form_frame.png") 0 0 repeat;'>
    <div style='position: relative; padding: 30px 10px 30px; background: #fff; text-align: center;'>
        <!-- header : The area is fixed. -->
        <div style="padding: 30px 0; border-bottom: #e1e1e1 1px solid;">
            <h1 style="margin: 0; text-align: center;">
                <a href="#" target="_blank"><img src="<?=IMG_URI?>/_res/email/logo_fcctv.png" alt="FCCTV"></a>
            </h1>
        </div>

        <div style='font-size: 18px; line-height: 48px; margin-bottom: 25px; padding-top: 30px;'>
            <span>주문하신</span>
            <span style='display: block; padding: 10px 0; margin: 0 10px; background: #f1f1f1; font-size: 18px; line-height: 1;'>
                <?= $data['productName']?>
            </span>
            <span>에 대해 환불 요청이 접수 되었습니다.</span>
        </div>

        <!-- 상품 정보 -->
        <div style="width: 270px; margin: 0 auto 30px; font-size: 16px; letter-spacing: 1px;">
            <div style="margin: 0 30px 20px;">
                <img src="<?=$data['image']?>" style="width: 100%;" alt="">
            </div>
            <p style="margin: 2px 0;">
                <b style="font-weight: 600;"><?= $data['productName']?></b>
            </p>
            <p style="margin: 2px 0;">
                Color: <?=$data['color']?>
            </p>
            <p style="margin: 2px 0;">
                Size: <?=$data['size']?>
            </p>
            <p style="margin: 2px 0;">
                quantity: <?=$data['quantity']?>
            </p>
            <div style="padding-top: 10px; margin-top: 20px; border-top: 1px solid #d9d9d9;">
                <p style="float: left; width: 30%; height: 30px; text-align: left; margin: 0; paddingL 0;"><b style="font-weight: 600;">&nbsp; 판매가</b></p>
                <p style="float: right; width: 70%; height: 30px; text-align: right; margin: 0; paddingL 0;"><?=$this->FccTv->currencyStr($data['unitAmount'])?> &nbsp;</p>
                <p style="float: left; width: 40%; height: 30px; text-align: left; margin: 0; paddingL 0;"><b style="font-weight: 600;">&nbsp; 수량</b></p>
                <p style="float: right; width: 60%; height: 30px; text-align: right; margin: 0; paddingL 0;"><?=$this->FccTv->currencyStr($data['quantity'])?> &nbsp;</p>
                <p style="float: left; width: 40%; height: 30px; text-align: left; margin: 0; paddingL 0;"><b style="font-weight: 600;">&nbsp; 합계</b></p>
                <p style="float: right; width: 60%; height: 30px; text-align: right; margin: 0; paddingL 0;"><?=$this->FccTv->currencyStr($data['sumPrice'])?> &nbsp;</p>
                <p style="float: left; width: 40%; height: 30px; text-align: left; margin: 0; paddingL 0;"><b style="font-weight: 600;">&nbsp; 배송비</b></p>
                <p style="float: right; width: 60%; height: 30px; text-align: right; margin: 0; paddingL 0;"><?=$this->FccTv->currencyStr($data['shipPrice'])?> &nbsp;</p>
            </div>
            <div style="clear: both;"></div>
        </div>

    </div>

    <!-- footer : The area is fixed. -->
    <div style="padding: 20px 0; background: #000; text-align: center; color: #fff; font-weight: 100;">
        <?=__("Need help?")?> <a href="#" target="_blank" style="color: #ff4b46; text-decoration: underline;">question@fcctvhq.com</a>
    </div>
</div>

</body>
</html>