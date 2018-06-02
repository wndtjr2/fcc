<?php
if(isset($payment_test) and $payment_test == 'Y'){
    $price=1000;
}else{
    $price = $grandTotal + $shippingTotal;
}
$mobileDetect = isset($isMobile) ? true : false;
?>
<script language=javascript src="https://plugin.inicis.com/pay61_unissl_cross.js"></script>
<script language=javascript>StartSmartUpdate();</script>
<script type="text/javascript" src="/_res/lib/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="/_res/js/address.js"></script>
<?php if(isset($alert)){?>
    <?php \Cake\Error\Debugger::log($alert)?>
    <script type="text/javascript">
        modalalert("<?=$alert?>");
        location.href = '/';
    </script>
<?php }else{?>
    <section id="sections">
        <h2 class="is-skip">Checkout</h2>

        <div class="contents">

            <div id="checkout">
                <div class="checkout__wrap">

                    <p class="userpage__title"><?=__('Checkout')?></p>
                    <ul class="checkout__progress is-zoom current-1">
                        <li class="tabs tabs1"><span class="tab is-current"><?=__('Shipping')?></span></li>
                        <li class="tabs tabs2"><span class="tab"><?=__('Payment')?></span></li>
                        <li class="tabs tabs3"><span class="tab"><?=__('Review')?></span></li>
                    </ul>

                    <!-- 개인정보 제3자 제공고지 -->
                    <div class="orders__terms">
                        <table>
                            <caption class="">개인정보 제3자 제공고지</caption>
                            <tbody>
                            <tr>
                                <th>제공 받는자</th>
                                <td>국내 택배사</td>
                            </tr>
                            <tr>
                                <th>목적</th>
                                <td>판매자와 구매자의 거래의 원활한 진행, 본인의사의 확인, 고객상담 및 불만처리, 상품과 경품 배송을 위한 배송지 확인 등</td>
                            </tr>
                            <tr>
                                <th>항목</th>
                                <td>이름, ID, 휴대폰번호, 이메일 주소, 전화번호, 상품 구매 정보, 상품 수취인 정보 (성명, 주소, 전화번호)</td>
                            </tr>
                            <tr>
                                <th>보유기간</th>
                                <td>배송완료 후 1달</td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="chekboxs infor__8th">
                            <div class="checks">
                                <input type="checkbox" id="lb_termsok">&nbsp;
                                <label class="text" for="lb_termsok">위 약관에 동의합니다.</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Orders Detail 컨텐츠 -->
                    <ul class="orders__lists is-zoom">

                        <li class="orders">
                            <h3 class="date"><span><?=__('Shipping Address')?></span></h3>
                            <?php if(empty($address) or is_null($address)){?>
                                <!-- TIP: 주소 추가 버튼 -->
                                <p class="add__address">
                                    <button type="button" class="link" id="AddAddress"><?=__('Add New Address')?></button>
                                </p>
                            <?php }else{?>
                                <!-- TIP: 주소가 존재할 경우. -->
                                <p class="order__detail--address">
                                    <strong id="NAME"><?=$address[0]['deliv_last_name'] . ' ' . $address[0]['deliv_first_name']?></strong>
                                <span class="address" id="ADDRESS">
                                    <?=$this->FccTv->addressStr($address[0]['zipcode'],$address[0]['address'],$address[0]['address2']);?>
                                </span>
                                    <span class="nation" id="COUNTRY"><?=$address[0]->code_country->country_name?></span>
                                    <span class="phone" id="PHONE"><?=__('Phone Number')?> : <?=$address[0]->phone_decrypt?></span>
                                    <a href="javascript:void(0);" class="add__address--change" id="ModalAddress"><?=__('Change')?></a><br/>
                                </p>
                            <?php }?>
                        </li>
                        <li class="orders">
                            <h3 class="date"><span><?=__('{0} items', sizeof($products->toArray()))?></span></h3>
                            <ul class="orders__details--list">
                                <!-- TIP1 : 상품 리스트 -->
                                <?php foreach($products as $key => $product):?>
                                    <li class="list">
                                        <div class="package">
                                            <span class="image" style="background-image: url('<?=$product->ch_product->ch_image->ch_image_file[0]->surl?>');"></span>
                                            <div class="detail">
                                                <p class="category"><?= $product->ch_product->designer_name?></p>
                                                <p class="title"><?= $product->ch_product->name?></p>
                                                <?php $option = explode(';', $product->name)?>
                                                <p class="size"><?=__('Color')?> : <?=ucfirst($option[1])?> / <?=__('Size')?> : <?=ucfirst($option[2])?></p>
                                                <p class="quantity"><?=__('Quantity')?> : <?= $orderQuantity[$key]?></p>
                                                <p class="total"><?= $this->FccTv->currencyStr($product->price * $orderQuantity[$key])?></p>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach;?>
                                <!-- TIP1 -->
                            </ul>
                        </li>
                        <li class="orders">
                            <h3 class="date"><span><?=__('Order Summary')?></span></h3>
                            <p class="order__detail--summary">
                                <span class="line"><?=__('Subtotal')?> : <b id="SubTotalFee"><?= $this->FccTv->currencyStr($grandTotal)?></b></span>
                                <?php if(is_null($address) or empty($address)){?>
                                    <span class="line"><?=__('Shipping')?> : <b id="ShippingFee"><?=$this->FccTv->currencyStr(0);?></b></span>
                                    <span class="total"><?=__('Total')?> : <b id="TotalFee"><?=$this->FccTv->currencyStr($grandTotal)?></b></span>
                                <?php }else{?>
                                    <span class="line"><?=__('Shipping')?> : <b id="ShippingFee"><?= ($shippingTotal == 0)?__('무료배송') : $this->FccTv->currencyStr($shippingTotal);?></b></span>
                                    <span class="total"><?=__('Total')?> : <b id="TotalFee"><?=$this->FccTv->currencyStr($grandTotal + $shippingTotal)?></b></span>
                                <?php }?>
                                <!--                            <span class="line">Tax : <b>$0.00</b></span>-->
                            </p>
                        </li>
                    </ul>

                    <a href="javascript:void(0);" class="checkout__continue" id="checkoutContinue"><?=__('Continue')?></a>

                </div>
            </div>

        </div> <!-- contents -->
    </section>

    <form method="post" id="saveOrder" action="/Purchases/orderBefore">
        <?php for($i = 0; $i < sizeof($data); $i++):?>
            <input type="hidden" name="data[<?=$i?>][product_option_code]" value="<?=$data[$i]['product_option_code']?>">
            <input type="hidden" name="data[<?=$i?>][quantity]" value="<?=$data[$i]['quantity']?>">
            <!--        <input type="hidden" name="payType" value="E">-->
            <input type="hidden" name="payType" value="I">
        <?php endfor;?>
        <input type="hidden" name="shipping_id"
               value="<?php if($address){echo $address[0]->id;}else{echo '';} ?>"
               id="ShippingId">
        <input type="hidden" name="orderCode" value="<?=isset($orderCode)?$orderCode:'';?>">
    </form>
<?php $productArray = $products->toArray();
    $phone = '';
    if(is_null($address) or empty($address)){
        $phone = '';
    }else{
        $phone = $address[0]->phone_decrypt;
    }
    ?>
    <!-- 모바일 웹 결제 폼 시작 -->
    <?php if($mobileDetect){
    $orderStr=$productArray[0]->ch_product->name;
    $orderStr.=sizeof($productArray) > 1 ? "  ".(sizeof($productArray)-1):" ";
    ?>
        <form id="InicisForm" name="ini" method="post" action="https://mobile.inicis.com/smart/wcard/" target = "BTPG_WALLET"  accept-charset="euc-kr">
            <?php //convert from utf-8 to euc-kr
            //$eucGood = convert($orderStr);
            $utfUserName = $userInfo['last_name'] . " " . $userInfo['first_name'];
            //$eucUserName = convert($utfUserName);
            ?>
            <input type="hidden" name="P_OID" id="oid" value="<?=isset($orderCode)?$orderCode:'';?>"/>
            <input type="hidden" name="P_GOODS" value="<?=$orderStr?>" />
            <input type="hidden" name="P_AMT" id="pamt" value="<?=$price?>"/>
            <input type="hidden" name="P_UNAME" value="<?=$utfUserName?>"/>
            <input type=hidden name="P_MOBILE" size=20 value="<?=$phone?>">
            <input type="hidden" name="P_EMAIL" value="<?=$userInfo['user_account']['emailDecrypt']?>"/>
            <input type="hidden" name="P_MID" value="<?=INIMID?>">
            <input type=hidden name="P_NEXT_URL" value="https://<?=$_SERVER['HTTP_HOST']?>/325n23fhih389d2u3jf578y8342dk378/y45ybjvriouywo89ky0434tetf4ftg/mx_rnext.php">
            <input type="hidden" name="P_QUOTABASE" value="01:02:03:04:05">
            <input type="hidden" name="P_RESERVED" value="twotrs_isp=Y&block_isp=Y&twotrs_isp_noti=N&apprun_check=Y"/>
            <input type="hidden" name="P_NOTI" id="noti" value="<?=isset($orderCode)?$orderCode:'';?>"/>
        </form>
        <!-- 모바일 웹 결제 폼 끝 -->
    <?php }else{?>
        <!-- 웹 결제 폼 시작 -->
        <form name=ini method=post action="/325n23fhih389d2u3jf578y8342dk378/y45ybjvriouywo89ky0434tetf4ftg/INILiteSecurepay.php" id="InicisForm" onsubmit="return pay(this)" style="display: none">
            <?php //convert from utf-8 to euc-kr
            $utfGoodName = $productArray[0]->ch_product->name?><?= (sizeof($productArray) > 1)? " + ".(sizeof($productArray)-1):"";
            //$eucGoodName = convert($utfGoodName);
            $utfBuyer = $userInfo['last_name']." ".$userInfo['first_name'];
            //$eucBuyer = convert($utfBuyer);
            ?>
            <!-- 결제방법을 신용카드로 한정 -->
            <input type=hidden name=gopaymethod value="Card">
            <input type=hidden name=goodname size=20 value="<?=$utfGoodName?>">
            <!-- TODO 결제금액 변경 필요 -->
            <input type=hidden name=price size=20 id="pamt" value="<?=$price?>">
            <input type=hidden name=buyername size=20 value="<?=$utfBuyer?>">
            <input type=hidden name=buyeremail size=20 value="<?=$userInfo['user_account']['emailDecrypt']?>">
            <input type=hidden name=buyertel size=20 value="<?=$phone?>">
            <input type=hidden name=mid value="<?=INIMID?>">
            <input type=hidden name=currency value="<?=currency?>">
            <input type=hidden name=acceptmethod value="SKIN(ORIGINAL):HPP(2):OCB">
            <input type=hidden name=oid size=40 id="oid" value="<?=isset($orderCode)?$orderCode:'';?>"/>
            <input type=hidden name=quotainterest value="">
            <input type=hidden name=paymethod value="Card">
            <input type=hidden name=cardcode value="">
            <input type=hidden name=cardquota value="">
            <input type=hidden name=rbankcode value="">
            <input type=hidden name=reqsign value="DONE">
            <input type=hidden name=encrypted value="">
            <input type=hidden name=sessionkey value="">
            <input type=hidden name=uid value="">
            <input type=hidden name=sid value="">
            <input type=hidden name=version value=4000>
            <input type=hidden name=clickcontrol value="">
            <input type=hidden name=nointerest value="no">
            <input type=hidden name=quotabase value="lumpsum:00:02:03:04:05">
        </form>
        <!-- 웹 결제 폼 끝 -->
    <?php }?>
<?php }?>

<!-- 결제 오류시 리다이렉트 -->
<form action="/orders/returnOrderError" method="POST" id="errorRedirect" accept-charset="UTF-8">
    <input id="openerMsg" type="hidden" name="msg" value="">
    <input id="openerOrderCode" type="hidden" name="order_code" value="">
</form>

<!-- 결제 밸리데이션 체크 후 플래시 불러오기 시작 -->

<?php if(isset($productStatus)):?>
<script type="text/javascript">
    var prd = <?= json_encode($productStatus);?>;
    var msg = '';
    for(x in prd){
        msg += '* ';
        msg += prd[x].product.name;
        var options = prd[x].product.option;
        var option = options.split(";");
        msg += '('+ option[1] + ', ' + option[2] +')'
        msg += '상품이 ';
        if(prd[x].reason == 'less'){
            msg += '구매수량보다 재고가 적습니다.'
        }else{
            msg += '매진되었습니다.'
        }
        msg += '\n';
    }
    alert(msg);
    location.href = document.referrer;
</script>
<?php endif;?>

<script language="JavaScript" type="text/JavaScript">

    /** checkout 버튼 클릭시 **/
    var shippingId = $("#ShippingId").val();
    $(document).on("click", "#checkoutContinue", function () {

        //버튼 비활성화
        $(this).prop("disabled", true);
        if ($("#lb_termsok").is(":checked") == false) {
            modalalert("약관에 동의해 주세요.");
            $(this).prop("disabled", false);
        } else if (shippingId == "") {
            modalalert("배송지 정보를 입력해 주세요.");
            $(this).prop("disabled", false);
        }else{
            saveOrder();
        }
    });
<?php if($mobileDetect){?>
        /** orderBefore에 데이터 저장후 이니시스에 폼 서밋 **/
        function saveOrder(){

            var total = <?=$grandTotal?> + <?=$shippingTotal?>;
            var frmTotal = $("#pamt").val();
            if(total != frmTotal){
                modalalert("알수 없는 오류가 발생 하였습니다.");
                $("#checkoutContinue").prop("disabled", false);
            }else{
                $("input[name='clickcontrol']").val("enable");

                var frm = $("#saveOrder").append(oid).append(noti);

                if($("#saveOrder input[name=P_OID]").val() === undefined){
                    var oid = $("#oid").clone(true);
                    var withoutId = oid.removeAttr("id");
                    frm.append(withoutId);
                }
                if($("#saveOrder input[name=P_NOTI]").val() === undefined){
                    var noti = $("#noti").clone(true);
                    var withoutNoti = noti.removeAttr("id");
                    frm.append(withoutNoti);
                }

                var completeFrm = frm.serializeArray();
                var rtn;
                $.ajax({
                    url: '/Purchases/orderBefore',
                    type : 'POST',
                    data : completeFrm,
                    dataType : 'json',
                    async : false,
                    success : function(data){
                        rtn = data;
                    },
                    error : function(request, status, error){
                        if(request.status == 403){
                            window.location.reload();
                        }
                        else{
                            var obj = JSON.parse(request.responseText);
                            modalalert(obj.message);
                        }
                    },
                    complete : function(){
                        $("#checkoutContinue").prop("disabled", false);
                    }
                });
                if(rtn[0].product){
                    modal_purchase_alert(rtn);
                }else{
                    var wallet = window.open("", "BTPG_WALLET");
                    //$("#oid").val(rtn);
                    document.charset='euc-kr';
                    $("#InicisForm").submit();
                }
            }
        };
<?php }else{ ?>
    /** orderBefore에 데이터 저장후 이니시스에 폼 서밋 **/
    function saveOrder(){

        var total = <?=$grandTotal?> + <?=$shippingTotal?>;
        var frmTotal = $("#pamt").val();
        if(total != frmTotal) {
            modalalert("알수 없는 오류가 발생 하였습니다.");
            $("#checkoutContinue").prop("disabled", false);
        }else{
            $("input[name='clickcontrol']").val("enable");
            if(validationCheckForInicisForm()) {
                var frm = $("#saveOrder");
                if($("#saveOrder input[name=oid]").val() === undefined){
                    var oid = $("#oid").clone(true);
                    var withoutId = oid.removeAttr("id");
                    frm.append(withoutId);
                }else{
                    $("#saveOrder input[name=oid]").val($("#oid").val());
                }
                var completeFrm = frm.serializeArray();
                $.ajax({
                    url: '/Purchases/orderBefore',
                    type: 'POST',
                    data: completeFrm,
                    dataType: 'json',
                    success: function (rtn) {
                        //상품 상태 얼럿
                        if(rtn[0].product){
                            modal_purchase_alert(rtn);
                        }else {
                            //정상
                            $("#InicisForm").submit();
                        }
                    },
                    error: function (xhr, status, error) {
                        var obj = JSON.parse(xhr.responseText);
                        modalalert(obj.message);
                        $("#checkoutContinue").prop("disabled", false);
                    },
                    complete : function(){
                        $("#checkoutContinue").prop("disabled", false);
                    }
                });
            }
        }
    };
<?php }?>
    /** 배송여부 확인후 배송비 책정 **/
    function isTrueToShip(addrId){
        var products = new Array();
        var int = 0;
        <?php foreach($products as $product):?>
        products[int] = "<?=$product->product_option_code?>";
        int++;
        <?php endforeach;?>
        $.ajax({
            url : '/Orders/isTrueToShip',
            dataType : 'json',
            data : {
                'products' : products,
                'addrId' : addrId
            },
            type : 'post',
            success : function(data){
                data = data + '';
                if(data == 0){
                    $("#ShippingFee")[0].innerHTML = "<?=__('무료배송')?>";
                }else{
                    data = data + '';
                    $("#ShippingFee")[0].innerHTML = currencyStr(data.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                }
                var SubTotalFee = "<?=$grandTotal?>";
                var TotalFee = +SubTotalFee + +data + '';
                $("#TotalFee")[0].innerHTML = currencyStr(TotalFee.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));

            }
        });
    };

    function getOrderCode(order_code){
        if(order_code){
            var input = "<input name='order_code' type='hidden' value='" + order_code + "'>";
            $("#saveOrder").append(input);
        }
    }

    function number_format(numval) {
        return numval.toString().replace(/(\d)(?=(\d{3})+$)/g, "$1,");
    }

    var currency = '<?=currency?>';
    function currencyStr(price){
        switch(currency){
            case 'WON':
                price = '￦ '+number_format(price);
                break;
            case 'USD':
                price = '$'+price;
                break;

        }
        return price;
    }

    function validationCheckForInicisForm(){

        if($("input[name='clickcontrol']").val() == "enable")
        {
            if($("input[name='goodname']").val() == "")  // 필수항목 체크 (상품명, 상품가격, 구매자명, 구매자 이메일주소, 구매자 전화번호)
            {
                modalalert("상품명이 빠졌습니다. 필수항목입니다.");
                $("#checkoutContinue").prop("disabled", false);
                return false;
            }
            else if($("input[name='price']").val() == "")
            {
                modalalert("상품가격이 빠졌습니다. 필수항목입니다.");
                $("#checkoutContinue").prop("disabled", false);
                return false;
            }
            else if($("input[name='buyername']").val() == "")
            {
                modalalert("구매자명이 빠졌습니다. 필수항목입니다.");
                $("#checkoutContinue").prop("disabled", false);
                return false;
            }
            else if($("input[name='buyeremail']").val() == "")
            {
                modalalert("구매자 이메일주소가 빠졌습니다. 필수항목입니다.");
                $("#checkoutContinue").prop("disabled", false);
                return false;
            }
            else if($("input[name='buyertel']").val() == "")
            {
                modalalert("구매자 전화번호가 빠졌습니다. 필수항목입니다.");
                $("#checkoutContinue").prop("disabled", false);
                return false;
            }
            else if($("input[name='gopaymethod']").val() != "Card")
            {
                modalalert("결제 방법은 카드로만 가능합니다.");
                $("#checkoutContinue").prop("disabled", false);
                return false;
            }
            else if($("input[name='currency']").val() != "<?=\Cake\Core\Configure::read('currency')?>")
            {
                modalalert("결제는 한화로만 가능합니다.");
                $("#checkoutContinue").prop("disabled", false);
                return false;
            }
            else if(ini_IsInstalledPlugin() == false)  // 플러그인 설치유무 체크
            {
                modalalert("\n이니페이 플러그인 128이 설치되지 않았습니다. \n\n안전한 결제를 위하여 이니페이 플러그인 128의 설치가 필요합니다. \n\n다시 설치하시려면 Ctrl + F5키를 누르시거나 메뉴의 [보기/새로고침]을 선택하여 주십시오.");
                $("#checkoutContinue").prop("disabled", false);
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            $("#checkoutContinue").prop("disabled", false);
            return false;
        }
    }

    var openwin;

    function pay(frm)
    {
        // MakePayMessage()를 호출함으로써 플러그인이 화면에 나타나며, Hidden Field
        // 에 값들이 채워지게 됩니다. 일반적인 경우, 플러그인은 결제처리를 직접하는 것이
        // 아니라, 중요한 정보를 암호화 하여 Hidden Field의 값들을 채우고 종료하며,
        // 다음 페이지인 INILiteSecurepay.php로 데이터가 포스트 되어 결제 처리됨을 유의하시기 바랍니다.

        //console.log($("input[name='clickcontrol']").val());

        if($("input[name='clickcontrol']").val() == "enable")
        {
                /******
                 * 플러그인이 참조하는 각종 결제옵션을 이곳에서 수행할 수 있습니다.
                 * (자바스크립트를 이용한 동적 옵션처리)
                 */

                if (MakePayMessage(frm))
                {
                    disable_click();
                    //openwin = window.open("childwin","childwin","width=299,height=149");
                    return true;
                }
                else
                {
                    //버튼 활성화
                    $("#checkoutContinue").prop("disabled", false);
                    if( IsPluginModule() )     //plugin타입 체크
                    {
                        modalalert("결제를 취소하셨습니다.");
                        return false;
                    }
                }
            $("#checkoutContinue").prop("disabled", false);
            return false;
        }
        else
        {
            $("#checkoutContinue").prop("disabled", false);
            return false;
        }
    }


    function disable_click()
    {
        $("input[name='clickcontrol']").val("disable");
    }

    function MM_reloadPage(init) {  //reloads the window if Nav4 resized
        if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
            document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
        else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
    }
    MM_reloadPage(true);

    function MM_jumpMenu(targ,selObj,restore){ //v3.0
        eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
        if (restore) selObj.selectedIndex=0;
    }

</script>

<!-- 결제 밸리데이션 체크 후 플래시 불러오기 끝 -->