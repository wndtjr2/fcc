<?php
    $defaultOptionCode = "";
    $defaultPrice = 0;
    $defaultColor = "";
    $defaultMaxPurchage = 0;
    $defaultSize=array();
    $defaultStock=0;
    $i=0;

    foreach($product['productOption'] as $color => $size){
        $defaultColor = ($i==0)?$color:$defaultColor;
        $defaultSize = ($i==0)?$size:$defaultSize;
        $defaultOptionCode = ($i==0)?$size[0]['prdOptCode']:$defaultOptionCode;
        $defaultPrice = ($i==0)?$size[0]['price']:$defaultPrice;
        $defaultMaxPurchage = ($i==0)?$size[0]['max']:$defaultMaxPurchage;
        $defaultStock = ($i==0)?$size[0]['stock']:$defaultStock;

        $i++;
    }
?>
<script>

    var defaultOption = 'top=300, left=300, toolbar=no, menubar=no, location=no, directories=no, status=no, resizable=no, scrollbars=yes';
    var curDomain = "https://"+document.domain;

    function removeFirst(obj){
        $(obj).find("option:first").remove();
        $(obj).attr("onclick","");
    }

    function shareing(obj){
        var targetUrl = curDomain+($(obj).attr("url"));
        var prdName = $(obj).attr("prdName");
        var value = $(obj).val();
        var pop_url = "";
        var popName = "";
        var options = "";
        if(value=="facebook"){
            pop_url = 'http://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(targetUrl);
            popName = "fbSharePop";
            options = "width=575, height=250,";
        }
        if(value=="weibo"){
            pop_url = 'http://service.weibo.com/share/share.php?url=' + encodeURIComponent(targetUrl) + '&title=' +prdName;
            popName = "share_weibo";
            options = "width=620, height=350,";
        }
        if(value=="twitter"){
            pop_url = 'http://twitter.com/intent/tweet?url='+encodeURIComponent(targetUrl)+'&text='+prdName;
            popName = "share_twitter";
            options = "width=620, height=350,";
        }
        window.open(pop_url, popName, options+ defaultOption);
    }

    function product_quantity(change, el) {}
</script>
<section id="sections">
<a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
<h2 class="is-skip"><?=__("FCCTV 미디어 커머스 상품 구매")?></h2>

    <div class="contents">
        <div class="">
            <div class="product is-zoom">

            <!-- 제품, 미리보기 & 옵션선택 -->
                <div class="product__view1 is-zoom">

                    <!-- 제품, 미리보기 영역 -->
                    <div class="product__slidewrap is-zoom">
                        <div class="preview">
                            <!-- 빅이미지 -->
                            <?php if($product['totalStock']==0){ ?>
                            <strong class="cardlist__soldout"><?=__("SOLD OUT")?></strong> <!-- 매진됨. -->
                            <?php }else if($product['totalStock']<=3){ ?>
                            <strong class="cardlist__left"><?=__('{0} LEFT', [$product['totalStock']])?></strong> <!-- 수량이 얼마 남지 않음. -->
                            <?php } ?>
<!--                            <strong class="cardlist__new">NEW</strong>-->
                            <img src="<?=$product['mainImageUrl']?>" alt="">
                            <!-- 공유기능 -->
                            <div class="cardlist__social">
                                <div class="drops">
                                    <button type="button" class="onoff" onclick="js.social_cardlist(event, this);"><?=__("공유보기")?></button>
                                    <ul class="dropslist">
                                        <li class="sns"><button type="button" class="share f fbShare">Facebook</button></li>
                                        <li class="sns"><button type="button" class="share w wbShare">Weibo</button></li>
                                        <li class="sns"><button type="button" class="share t twShare">Twitter</button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <ul class="thumnail is-zoom">
                            <li class="item">
                                <a class="thum is-select" onclick="js.thum_to_zoom(event, this)" style="background-image: url('<?=$product['mainImageUrl']?>');'">
                                    <img src="/_res/img/guide/product_rate_width.png" alt="" name="<?=$product['mainImageUrl']?>">
                                    <span class="overlap"></span>
                                </a>
                            </li>
                            <?php
                                foreach($product['subImageUrls'] as $subImg){
                                    echo "<li class=\"item\">";
                                    echo "<a class=\"thum\" onclick=\"js.thum_to_zoom(event, this)\" style=\"background-image: url('".$subImg['surl']."');'\">";
                                    echo "<img src=\"/_res/img/guide/product_rate_width.png\" alt=\"\" name=\"".$subImg['lurl']."\">";
                                    echo "<span class=\"overlap\"></span>";
                                    echo "</a>";
                                    echo "</li>";
                                }
                            ?>
                        </ul>
                    </div>
                    <!-- 제품, 정보 -->
                    <div class="product__options">
                        <div class="position__wrap">
                            <div class="position">
                                <p class="product__location">
                                    <span class="inner">
                                        <a href="/product/?categoryId=<?=$product['category1']?>"><?=$product['categorys']['category1']['name']?> </a> > <a href="/product/?categoryId=<?=$product['category2']?>"> <?=$product['categorys']['category2']['name']?></a>
                                    </span>
                                </p>

                                <h2 class="name"><a href="/designers/detail?designerId=<?=$product['designerId']?>"><?=$product['designerName']?></a></h2>
                                <p class="title"><?=$product['productName']?></p>
                                <p class="price"><span class="won">￦</span> <?=number_format($defaultPrice)?></p>

                                <div class="social is-zoom">
                                    <button type="button" class="sns f fbShare">Facebook</button>
                                    <button type="button" class="sns t twShare">Twitter</button>
                                    <button type="button" class="sns w wbShare">Weibo</button>
                                </div>

                                <div class="options">
                                    <div class="option">
                                        <p class="option__title">Color</p>
                                        <select name="select__color" class="is-select" id="selectColor">
                                            <?php
                                            foreach($product['productOption'] as $color => $size){
                                                $selected = ($color==$defaultColor)?"selected":"";
                                                echo "<option value=\"".$color."\" ".$selected.">".$color."</option>";
                                            }
                                            ?>

                                        </select>
                                        <div class="color is-drops">
                                            <ul class="drops">
                                                <?php
                                                $isCurrent = ($color==$defaultColor)?"class=\"current\"":"";
                                                foreach($product['productOption'] as $color => $size){
                                                    echo "<li class=\"item\" ><button type=\"button\" ".$isCurrent.">".$color."</button></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>


                                    <?php
                                    foreach($product['productOption'] as $color => $size){
                                        $display = ($color==$defaultColor)?"":"style=\"display: none\"";
                                        ?>
                                        <div class="option csize" id="<?=$color?>_size" <?=$display?>>
                                            <p class="option__title">Size</p>
                                            <select name="select__size" class="is-select">
                                                <option value="" stock="0" maxpurchage="0" unitprice="0" selected><?=__("사이즈를 선택해주세요.")?></option>
                                                <?php
                                                $i=0;
                                                foreach($size as $sizeDetail){
                                                    $soldoutMsg = "";
                                                    if($sizeDetail['stock']<1){
                                                        $soldoutMsg = " - 매진된 상품입니다";
                                                    }
                                                    echo "<option value=\"".$sizeDetail['prdOptCode']."\" stock=\"".$sizeDetail['stock']."\" maxpurchage=\"".$sizeDetail['max']."\" unitprice=\"".$sizeDetail['price']."\" >".$sizeDetail['size'].$soldoutMsg."</option>";
                                                    $i++;
                                                }
                                                ?>
                                            </select>
                                            <div class="is-drops">
                                                <ul class="drops">
                                                    <?php
                                                    $i=0;
                                                    foreach($size as $sizeDetail){
                                                        $isCurrent = ($i==0)?"class=\"current\"":"";
                                                        echo "<li class=\"item\" ><button type=\"button\" ".$isCurrent.">".$sizeDetail['size']."</button></li>";
                                                        $i++;
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <div class="option" id="qShow">
                                        <p class="option__title"><?=__("Quantity")?></p>
                                        <select name="select__quantity" id="select__quantity" class="is-select nums">
                                            <option value="1" selected>1</option>
                                        </select>
                                        <div class="is-counter">
                                            <button type="button" class="plus" onclick="js.counter(event, this)"><?=__("추가")?></button>
                                            <button type="button" class="minus" onclick="js.counter(event, this)"><?=__("빼기")?></button>
                                            <input type="number" value="1" id="qnt" class="count" disabled="disabled">
                                        </div>

                                    </div>
                                </div>

                                <p class="total"><?=__("소계")?>: <span id="totalPrice">0</span></p>
                            </div>
                            <div class="buynow">
                                <?php
                                $soldoutview = "";
                                $soldoutbtn = "style='display:none'";
                                if($product['totalStock']<1){
                                    $soldoutview = "style='display:none'";
                                    $soldoutbtn = "";
                                } ?>
                                <p id="addToCartBtn" <?=$soldoutview?>><button type="button" class="tocart addToCart"><?=__("장바구니 담기")?></button></p>
                                <p id="checkOutBtn" <?=$soldoutview?>><button type="button"  class="tobuy checkOutBtn"><?=__("즉시구매 결제")?></button></p>
                                <p id="soldoutBtn" <?=$soldoutbtn?>><span class="toout"><?=__("SOLD OUT")?></span></p>
                            </div>
                            <div class="back">
                                <a href="#" class="goto_back"><?=__("목록보기")?></a>
                            </div>
                        </div>
                    </div>

                </div>

                <h3 class="product__title"><b class="txt"><?=__("Product Detail")?><span class="line"></span></b></h3>

                <div class="product__view2">
                    <?php if($product['videoType']!=false){ ?>
                    <!-- 방송이 존재할 경우 노출 -->
                    <div class="product__details--video">
                        <a href="#">
                            <?php

                                $videoUrl = "";
                                switch($product['videoType']){
                                    case "Youtube" :
                                        $videoUrl = "https://www.youtube.com/embed/".$product['videoId'];
                                        break;
                                    case "Vimeo" :
                                        $videoUrl = "https://player.vimeo.com/video/".$product['videoId'];
                                        break;
                                }
                            ?>
                            <iframe class="video" src="<?=$videoUrl?>" width="100%" height="100%" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>
                            <img src="/_res/img/guide/tv_video.png" class="guide">
                        </a>
                            <script>
                                $(function(){
                                    $('.product__details--video').find('.playbtn').on('click', function(e){
                                        $('.product__details--video').find('.cover').fadeOut(500);
                                    })
                                })
                            </script>
                    </div>
                    <?php } ?>
                    <!-- 제품, 디테일(설명) 요소 -->
                    <div class="product__details">
                        <ul class="product__details--list">
                            <li class="item">
                                <ul class="product__details--image">

                                    <li class="image">
                                       <p>
                                          <?=$product['productContent']?>
                                       </p>
                                   </li>
                                    <?php foreach($product['productDetailInfo'] as $detailImage){ ?>
                                    <li class="image">
                                        <img src="<?=$detailImage['url']?>" alt="">
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php if((isset($product['productSubContent']['size']['image']) || $product['productSubContent']['size']['text'] !="") || (isset($product['productSubContent']['washing']['image']) || $product['productSubContent']['washing']['text'] !="")){ ?>
                            <h4 class="product__title"><b class="txt">Information<span class="line"></span></b></h4>
                            <?php } ?>
                            <?php if(isset($product['productSubContent']['size']['image']) || $product['productSubContent']['size']['text'] !=""){ ?>
                            <li class="item">
                                <div class="inner">
                                    <h5 class="product__title sub"><?=__("사이즈표")?></h5>
                                    <ul class="product__details--image">
                                        <li class="image">
                                            <?php if(isset($product['productSubContent']['size']['image'])){ ?>
                                            <p><img src="<?=$product['productSubContent']['size']['image']['lurl']?>" alt=""></p>
                                            <?php } if($product['productSubContent']['size']['text'] !=""){ ?>
                                            <p class="pre"><?=$product['productSubContent']['size']['text']?></p>
                                            <?php } ?>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <?php } ?>
                            <?php if(isset($product['productSubContent']['washing']['image']) || $product['productSubContent']['washing']['text'] !=""){ ?>
                            <li class="item">
                                <div class="inner">
                                    <h5 class="product__title sub"><?=__("세탁 및 관리법")?></h5>
                                    <ul class="product__details--image">
                                        <li class="image">
                                            <?php if(isset($product['productSubContent']['washing']['image'])){ ?>
                                            <p><img src="<?=$product['productSubContent']['washing']['image']['lurl']?>" alt=""></p>
                                            <?php }
                                            if($product['productSubContent']['washing']['text'] !=""){ ?>
                                            <p class="pre"><?=$product['productSubContent']['washing']['text']?></p>
                                            <?php } ?>
                                        </li>
                                    </ul>
                                </div>

                            </li>
                            <?php } ?>

                        </ul>
                    </div>
                </div>


                <?php
                $order   = array("\r\n", "\n", "\r");
                $replace = '</li><li>';
                ?>
                <div class="product__view3">
                    <!-- 제품, 기타 판매 정보 -->
                    <div class="product__infomation">
                        <ul class="tabs is-zoom">
                            <?php if($product['productSubContent']['delivery']['text']!=""){ ?>
                            <li class="tab is-select">
                                <button type="button" class="btn detail_other_info"><?=__("배송정보")?></button><spam class="arr"></spam>
                                <div class="define">
                                    <ul>
                                        <li><?=trim(str_replace($order,$replace,$product['productSubContent']['delivery']['text']))?></li>
                                    </ul>
                                </div>
                            </li>
                            <?php } ?>
                            <?php if($product['productSubContent']['refund']!=""){ ?>
                            <li class="tab">
                                <button type="button" class="btn detail_other_info" ><?=__("교환 / 환불 / A/S 안내")?></button><spam class="arr"></spam>
                                <div class="define">
                                    <ul>
                                        <li><?=trim(str_replace($order,$replace,$product['productSubContent']['refund']['text']))?></li>
                                    </ul>
                                </div>
                            </li>
                            <?php } ?>
                            <li class="tab is-select">
                                <button type="button" class="btn detail_other_info" ><?=__("상품문의")?></button><spam class="arr"></spam>
                                <div class="define">
                                    <div class="product__infomation--contact">
                                        <div class="row">
                                            <label for="pic_title"><?=__("제목")?></label>
                                            <input type="text" id="pic_title" placeholder="<?=__("제목을 입력해주세요.")?>">
                                        </div>
                                        <div class="row">
                                            <label for="pic_email"><?=__("내용입력")?></label>
                                            <textarea name="contact" id="contact"></textarea>
                                        </div>
                                        <div class="row">
                                            <button id="prdAskBtn" class="submit"><?=__("등록하기")?></button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php if($product['productSubContent']['notice']!=""){ ?>
                            <li class="tab">
                                <button type="button" class="btn detail_other_info"><?=__("상품고시")?></button><spam class="arr"></spam>
                                <div class="define">
                                    <ul>
                                        <li><?=trim(str_replace($order,$replace,$product['productSubContent']['notice']['text']))?></li>
                                    </ul>
                                </div>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- contents -->
</section>


<input type="hidden" name="productOptionCode" id="productOptionCode" value="" unitprice="0" stock="0" maxpurchage="1">
<input type="hidden" name="quantity" id="quantity" value="1">
<?php $auth = $this->request->session()->read('Auth.User')?>
<form action="/Purchases/orderInfo" method="post" id="order"></form>
<script>
    $(document).ready(function(){

        Number.prototype.numformat = function(){
            if(this==0) return 0;

            var reg = /(^[+-]?\d+)(\d{3})/;
            var n = (this + '');

            while (reg.test(n)) n = n.replace(reg, '$1' + ',' + '$2');

            return n;
        };

        $(".detail_other_info").on("click",function(){
            $(this).parent().toggleClass("is-select");
        });

        /** 상품 문의 하기 **/
        $("#contact , #pic_title").on("click",function(){
            <?php if(!isset($auth)){?>
            $("#pic_title").val('');
            $("#contact").val('');
            modal.show('uneedlogin');
            <?php }?>
        });

        $("#prdAskBtn").on("click",function(){
            <?php if(!isset($auth)){?>
            modal.show('uneedlogin');
            <?php }else{?>
                var titleVal = $("#pic_title").val();
                var contentsVal = $("#contact").val();
                var productCode = "<?=$product['productCode']?>";
                if(titleVal==""){
                    modalalert('<?=__("제목을 입력해주세요.")?>');
                    return false;
                }

                if(contentsVal==""){
                    modalalert("<?=__("내용을 입력해주세요.")?>");
                    return false;
                }

                var dataObj = {
                    product_code : productCode,
                    title : titleVal,
                    contents : contentsVal,
                };

                $.ajax({
                    url: '/product/registProductAsk',
                    dataType: 'json',
                    type : 'Post',
                    data : dataObj,
                    success: function (rtn) {
                        if(rtn.result==false){
                            modalalert('<?=__("죄송합니다. 다시 한번 등록해주십시오.")?>');
                        }else {
                            modalalert('<?=__("제품에 대한 문의가 등록되었습니다.<br/>답변은 등록하신 이메일로 전달됩니다.<br/>이점 양해 부탁드립니다.<br/>감사합니다.")?>');
                            $("#pic_title").val('');
                            $("#contact").val('');
                        }
                    }
                });

            <?php } ?>
        });


        /** SNS 공유 **/
        var defaultOption = 'top=300, left=300, toolbar=no, menubar=no, location=no, directories=no, status=no, resizable=no, scrollbars=yes';
        var targetUrl = location.href;
        $(document).on("click",".fbShare",function(){
            var popUrl = 'http://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(targetUrl);
            window.open(popUrl, 'fbSharePop', 'width=575, height=250,'+ defaultOption);
        });

        $(document).on("click",".wbShare",function(){
            var pop_url = 'http://service.weibo.com/share/share.php?url=' + encodeURIComponent(targetUrl) + '&title=' + '<?=$product['productName']?>';
            window.open(pop_url, 'share_weibo', 'width=620, height=350,'+ defaultOption);
        });

        $(document).on("click",".twShare",function(){
            var pop_url = 'http://twitter.com/intent/tweet?url='+encodeURIComponent(targetUrl)+'&text='+'<?=$product['productName']?>';
            window.open(pop_url, 'share_twitter', 'width=620, height=350,'+ defaultOption);
        });

        /** 옵션 변경 **/

        $("select[name=select__size]").on("change",function(){
            if($(this).find("option:selected").val()!=""){
                var unitprice = $(this).find("option:selected").attr("unitprice");
                var maxpurchase = $(this).find("option:selected").attr("maxpurchage");
                var stock = $(this).find("option:selected").attr("stock");
                var prdOpCode = $(this).val();
                $("#productOptionCode").val(prdOpCode);
                $("#productOptionCode").attr("unitprice", unitprice);
                $("#productOptionCode").attr("maxpurchage",maxpurchase);
                $("#productOptionCode").attr("stock",stock);

                $("#select__quantity > option").remove();
                for(var i=1;i<=maxpurchase;i++){
                    $("#select__quantity").append("<option value='"+i+"'>"+i+"</option>");
                }

                if(stock < 1){
                    modalalert('<?=__("The selected item is sold out.")?>');
                    $(this).val('');
                    $("#quantity").val(1);
                    $("#qnt").val(1);
                    $("#totalPrice").text(0);
                    $("#productOptionCode").val('');
                    return false;
                }


                $("#quantity").val(1);
                $("#qnt").val(1);
                calcuTotalPrice();
                $("#qShow").show();
            }else{
                $("#totalPrice").text(0);
                $("#productOptionCode").val('');
            }
        });

        $("#selectColor").on("change",function(){
            var color = $(this).val();
            $(".csize").hide();
            $("#productOptionCode").val('');
            $("#quantity").val(1);
            $("#qnt").val(1);

            var firstElement = $("#" + color + "_size").find("select[name=select__size] > option:first");
            $("#" + color + "_size").find("select[name=select__size] > option").attr("selected",false);
            firstElement.attr("selected",true);
            var firstUnitPrice = eval(firstElement.attr("unitprice"));

            $("#productOptionCode").val(firstElement.val());
            $("#productOptionCode").attr("unitprice",firstElement.attr("unitprice"));
            $("#productOptionCode").attr("maxpurchage",firstElement.attr("maxpurchage"));
            $("#totalPrice").text(firstUnitPrice.numformat());

            if(color!="") {
                $("#" + color + "_size").show();
            }
        });
        /** 옵션 변경 **/

        $("#select__quantity").on("change",function(){
            var quantityVal = $(this).val();
            $("#quantity").val(quantityVal);
            calcuTotalPrice();
        });

        /** 총 금액 설정 **/
        function calcuTotalPrice(){
            var unitPrice = $("#productOptionCode").attr("unitprice");
            var quantity = $("#quantity").val();

            var totalPrice = unitPrice*quantity;
            $("#totalPrice").text(totalPrice.numformat());
        }

        /** 수량 조절 **/
        $(".minus").on("click",function(){
            var quantityVal = eval($("#quantity").val());
            quantityVal--;
            if(quantityVal<1){
                modalalert("<?=__("최소 1개의 수량은 선택하셔야 합니다.")?>");
                quantityVal = 1;
            }
            $("#quantity").val(quantityVal);
            calcuTotalPrice();
        });

        $(".plus").on("click",function(){

            if($("#productOptionCode").val()==""){
                modalalert("<?=__("사이즈를 선택 해주세요.")?>");
                $("#qnt").val(1);
                return false;
            }

            var maxpurchage = $("#productOptionCode").attr('maxpurchage');
            var stock = $("#productOptionCode").attr('stock');
            var quantityVal = eval($("#quantity").val());
            quantityVal++;
            var viewQuantVal = $("#qnt").val();

            if(stock < quantityVal){
                quantityVal--;
                viewQuantVal--;
                modalalert("<?=__("남은 수량이 충분하지 않습니다.")?>");
                $("#qnt").val(viewQuantVal);
                return false;
            }

            if(maxpurchage<quantityVal){
                quantityVal--;
                viewQuantVal--;
                modalalert("<?=__("최대 구매수량을 초과하였습니다.")?>");
                $("#qnt").val(viewQuantVal);
                return false;
            }

            $("#quantity").val(quantityVal);
            calcuTotalPrice();
        });
        /** 수량 조절 end **/

        /** 장바구니 담기 **/
        $(".addToCart").on("click",function(){

            <?php if(isset($auth)){?>
            var optionCodeVal = $("#productOptionCode").val();
            if(optionCodeVal==""){
                modalalert("<?=__("사이즈를 선택 해주세요.")?>");
                return false;
            }
            var quantityVal = $("#quantity").val();

            var sendObj = {
                product_option_code : optionCodeVal,
                quantity : quantityVal
            };
            $.ajax({
                url: '/cart/add',
                dataType: 'json',
                type : 'Post',
                data : sendObj,
                success: function (rtn) {
                    if(rtn.result==false){
                        if(rtn.msg=="soldout"){
                            modalalert("<?=__("The selected item is sold out.")?>");
                        }else {
                            modalalert("fail");
                        }
                    }else {
                        modal.show('modal_cart_save.html');
                        cartCnt();
                    }
                }
            });
            <?php }else{ ?>
            location.href='/auth/login?redirect=<?=$this->request->here?>';
            <?php } ?>
        });

        /** 체크 아웃 **/
        $(".checkOutBtn").on("click",function(){

            <?php if(isset($auth)){?>
            var productCode = $("#productOptionCode").val();
            if(productCode==""){
                modalalert("<?=__("사이즈를 선택해주세요.")?>");
                return false;
            }
            var quantityVal = $("#quantity").val();
            $.ajax({
                url: '/fccTv/getStockCount',
                dataType: 'json',
                type : 'Post',
                data : {prdOptCode:productCode},
                async :false,
                success: function (rtn) {
                    if(rtn.result == true){
                        var valueText = "<input type='hidden' name='data[0][product_option_code]' value='" + productCode + "'>\n";
                        valueText += "<input type='hidden' name='data[0][quantity]' value='" + quantityVal + "'>\n";
                        $("#order").html(valueText);
                        $("#order").submit();
                    }else{
                        modalalert("<?=__("The selected item is sold out.")?>");
                        location.reload();
                    }
                }
            });
            <?php }else{ ?>
            location.href='/auth/login?redirect=<?=$this->request->here?>';
            <?php } ?>

        });
        /*목록가기*/
        $(".goto_back").click(function(){
            history.back();
        })
    });
</script>