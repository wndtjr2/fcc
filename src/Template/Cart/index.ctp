<!-- ************************************** 페이지별 컨텐츠 (가변영역) **************************************-->
<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip"><?=__("FCCTV 미디어 커머스 : 장바구니 보기")?></h2>
    <div class="contents">

        <div id="sign">
            <div class="sign__wrap">
                <!-- 장바구니 -->
                <div class="modal__mycart">
                    <h3 class="section__title"><span class="inner"><?=__("My Cart")?></span></h3>
                    <?php
                    if(count($cartList) > 0){
                    ?>
                    <!-- 카트 상품이 있을 경우. -->
                    <div class="modal__mycart--lists">
                        <ul>
                            <?php $i = 0;?>
                            <form method="post" id="cartFormId" action="/purchases/orderInfo">
                                <!-- Cart list -->
                                <?php foreach($cartList as $cart){ ?>
                                    <!-- Cart list -->
                                    <li class="item" cartId="<?=$cart['cart_id']?>">
                                        <div class="modal__mycart--detail">
                                            <div class="box">
                                                <input class="checkBoxCart" type="checkbox" name="<?=$i?>" value="<?=$cart['product_option_code']?>" checked style="display:none;" price="<?=$cart['quantityPrice']?>" cart_id="<?=$cart['cart_id']?>">
                                                <button type="button" class="chk is-select"><?=__("체크하기")?></button>
                                                <span class="cover" style="background-image: url('<?=$cart['main_image_url']?>');">
                                                    <?php
                                                    if($cart['leftStock']==0){
                                                        echo '<b class="soldout">'.__("SOLD OUT").'</b>';
                                                    }
                                                    ?>
                                                </span>
                                                <div class="details">
                                                    <span class="category"><?=$cart['sellerName']?></span>
                                                    <a href="/product/detail/<?=$cart['product_code']?>" class="title"><?=$cart['product_name']?></a><?php $opArray=explode(';',$cart['option']);?>
                                                    <div class="infowrap">
                                                        <p class="info color"><?=__('Color')?> : <?=$opArray[1]?></p>
                                                        <p class="info size"><?=__('Size')?> : <?=$opArray[2]?></p>
                                                        <p class="info"><?=__("교환 및 환불 불가")?></p>
                                                    </div>
                                                    <div class="quantity">
                                                        <span class="q"><?=__("Quantity")?> :</span>
                                                        <button type="button" class="down">-</button>
                                                        <input type="number" class="count" name="cart_quantity" cart_id="<?=$cart['cart_id']?>" value="<?=$cart['quantity']?>" readonly onchange="cartQuantityChange(this,'');">
                                                        <button type="button" class="up">+</button>
                                                    </div>
                                                    <div class="price">
                                                        <span cart_id="<?=$cart['cart_id']?>"><b>￦ </b><?=number_format($cart['quantityPrice'])?></span><span class="romoves"><button type="button" class="remove removeCart" onclick="removeCart(['<?=$cart['cart_id']?>']);"><?=__('Remove')?></button></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <?php $i++?>
                                <?php }?>
                            </form>

                        </ul>

                        <div class="modal__mycart--bottom">
                            <div class="left">
                                <button type="button" class="btn" id="selectDelete"><?=__("선택상품 삭제")?></button>
                                <button type="button" class="btn" id="allSelect"><?=__("전체상품 선택")?></button>
                            </div>
                            <p class="total"><?=__('Subtotal')?> : <strong id="cartTotalPrice"><?=$this->FccTv->currencyStr($totalPrice)?></strong></p>
                            <a href="#" class="buttons checkout" onclick="cartSubmit();">
                                <span class="text"><?=__('Checkout')?></span>
                                <span class="txt"><?=__('Checkout')?></span>
                                <span class="bg"></span>
                            </a>
                        </div>
                    </div>
                    <!-- 카트 상품이 있을 경우. -->
                    <?php }else{?>


                    <!-- 카트 비었을 경우 -->
                    <div class="modal__mycart--empty">
                        <span class="icon"></span>
                        <p class="text"><?=__("장바구니에 담긴 물건이 없습니다.")?></p>

                        <div class="link">
                            <a href="/" class="buttons checkout">
                                <span class="text"><?=__("쇼핑 계속하기")?></span>
                                <span class="txt"><?=__("쇼핑 계속하기")?></span>
                                <span class="bg"></span>
                            </a>
                        </div>
                    </div>
                    <!-- e:카트 비었을 경우 -->
                    <?php }?>
                </div>

            </div>
        </div>
    </div> <!-- contents -->
</section>

<!-- ************************************** 페이지별 컨텐츠 (가변영역) **************************************-->
<script>
    $(function(){
        $('.modal__mycart--detail .chk').on('click', function(e){
            var boo = $(this).hasClass('is-select');
            if ( boo ) {
                $(this).removeClass('is-select');
                $(this).prev().prop("checked",false);
                calTotalPrice();
                return;
            }
            $(this).addClass('is-select');
            $(this).prev().prop("checked",true);
            calTotalPrice();
        });
        $('#allSelect').click(function(e){
            allSelectCart();
        });
        $('#selectDelete').click(function(e){
            selectCartDelete();
        });
    });
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

    function quantity(change,el) {
        cartQuantityChange($(el).find('.count'),$(el.context).html());
    }
    function removeCart(cartId){
        $.ajax({
            url: '/cart/remove',
            dataType: 'json',
            type : 'Post',
            data : { cartId : cartId},
            success: function (rtn) {
                if(rtn.result==true){
                    $.each(cartId,function(i,v){
                        $('li.item[cartId='+v+']').remove();
                    });
                    $('.cartCnt').html(rtn.cartCnt);
                    if(rtn.cartCnt < 1){
                        $(".cartCnt").hide();
                        $(".modal__mycart--lists").html('');
                        var emptyCartHtml ='<div class="modal__mycart--empty">' +
                            '<span class="icon"></span>' +
                            '<p class="text"><?=__("장바구니에 담긴 물건이 없습니다.")?></p>' +                           '<div class="link">'+
                            '<a href="/" class="buttons checkout">'+
                            '<span class="text"><?=__("쇼핑 계속하기")?></span>'+
                            '<span class="txt"><?=__("쇼핑 계속하기")?></span>'+
                            '<span class="bg"></span>'+
                            '</a>'+
                            '</div>'+
                            '</div>';
                        $(".modal__mycart--lists").append(emptyCartHtml);
                    }else{
                        $(".cartCnt").show();
                    }
                    calTotalPrice();
                    if(rtn.totalPrice>0){
                        $('.modal__mycart--bottom').show();
                    }else{
                        $('.modal__mycart--bottom').hide();
                    }
                }else{
                    modalalert('System Problem');
                }
            }
        });
    }
    function cartQuantityChange(obj,callBack){
        var changeQuantity = $(obj).val();
        if(changeQuantity<1){
            changeQuantity = 1 ;
            modalalert("<?=__("최소 1개의 수량은 선택하셔야 합니다.")?>");
            $(obj).val(1);
            return false;
        }
        var cartIdVal = $(obj).attr("cart_id");
        $.ajax({
            url: '/cart/quantityChange',
            dataType: 'json',
            type : 'Post',
            async : false,
            data : {
                cartId : cartIdVal,
                quantity : changeQuantity
            },
            success: function (rtn) {
                if (rtn.result == true) {
                    $('.quantityPrice[cart_id=' + cartIdVal + ']').html(currencyStr(rtn.price));
                    $('.checkBoxCart[cart_id='+cartIdVal+']').attr('price',rtn.price);
                    calTotalPrice();
                    //$('#cartTotalPrice').html(currencyStr(rtn.totalPrice));
                } else {
                    if(callBack=='+'){
                        $(obj).val(changeQuantity-1);
                    }
                    if (rtn.msg == "OutOfStock") {
                        modalalert("<?="죄송합니다.<br/>상품이 매진 되었습니다."?>");
                    } else if (rtn.msg == "MoreThenStock") {
                        modalalert("<?=__("현재 재고 보다 많은 수량을 선택하셨습니다.")?>");
                    } else if (rtn.msg == "MaxPurchase") {
                        modalalert("<?=__("최대 구매수량을 초과하였습니다.")?>");
                    } else {
                        modalalert("System Fail");
                    }
                }
            }
        });
    }
    function cartSubmit(){
        var checked = false;
        var form = $('#cartFormId');
        var cartCheckBox = $("#cartFormId :input[type=checkbox]");
        var cartQuantity = $("#cartFormId :input[type=number]");
        var n = 0;
        for (var i = 0;i < cartCheckBox.length; i++) {
            if (cartCheckBox[i].checked) {
                for (var e = 0; e < cartQuantity.length; e++){
                    checked = true;
                    if (i == e) {
                        form.append("<input type='hidden' name='data[" + n + "][quantity]' value='" + cartQuantity[e].value + "'>");
                        form.append("<input type='hidden' name='data[" + n + "][product_option_code]' value='" + cartCheckBox[i].value + "'>");
                        n++;
                    }
                }
            }
        }
        if(checked){
            form.submit();
        }else{
            modalalert("<?=__("구매할 상품을 선택해 주세요.")?>");
        }
    }
    function calTotalPrice(){
        var totalPrice=0;
        $('.checkBoxCart:checked').each(function(index){
            totalPrice += $(this).attr('price')*1;
        });
        $('#cartTotalPrice').html(currencyStr(totalPrice));
    }
    var checkedToggle = false;
    function allSelectCart(){
        if(checkedToggle) {
            $('.modal__mycart--detail .chk').addClass('is-select');
            $('.checkBoxCart').prop("checked", true);
            checkedToggle = false;
        }else{
            $('.modal__mycart--detail .chk').removeClass('is-select');
            $('.checkBoxCart').prop("checked", false);
            checkedToggle = true;
        }

        calTotalPrice();
    }
    function selectCartDelete(){
        var cart_id=[];
        $('.checkBoxCart:checked').each(function(index){
            cart_id.push($(this).attr('cart_id'));
        });
        removeCart(cart_id);
    }
</script>