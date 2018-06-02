<?php
$video = $videoWithProduct['video'];
$videoUrl = $videoWithProduct['videoUrl'];
$videoMainImage = $videoWithProduct['videoMainImage'];
$comments = $videoWithProduct['comments'];
$commentCount = $videoWithProduct['commentCount'];
$productPriceInfo = $videoWithProduct['productPriceInfo'];
$designerInfo = $videoWithProduct['designerInfo'];

?>
<script src="/_res/lib/owl.carousel.min.js"></script>
<link rel="stylesheet" type="text/css" href="/_res/lib/owl.carousel.css">
<div class="mobile__navigation is-zoom">
    <a href="/tv/" class="mobile__navigation--leftbtn"><?=__("Back")?></a>
    <div class="cart">
        <?php $auth = $this->request->session()->read('Auth.User')?>
        <button type="button" class="mobile__navigation--cart" name="rnb" onclick="location.href='/cart/'">카트 <?php if(isset($auth)){?><span class="noti cartCnt"><?=$cartCnt?></span><?php } ?></button>
    </div>
</div>


<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip"><?=__("FCCTV 미디어 커머스 방송 컨텐츠 상세보기")?></h2>
    <div class="contents details">
        <div id="details" class="is-zoom show-onair">
            <div class="details__review is-zoom">
                <!-- TIP : Review 에서 플레이 버튼 클릭시 노출. -->
                <div class="detail__video">
                    <?php if($video->youtube_id){ ?>
                        <iframe id="fcctv__iframe" src="https://www.youtube.com/embed/<?=$video->youtube_id?>?autoplay=1"  width="100%" height="100%" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>
                    <?php }else if($video->vimeo_id){ ?>
                        <iframe id="fcctv__iframe" src="https://player.vimeo.com/video/<?=$video->vimeo_id?>?autoplay=1" width="100%" height="100%" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>
                    <?php }else{ ?>
                        <video id="fcctv__player" src="<?=$videoUrl?>" type="video/mp4"  controls="controls" autoplay loop=""></video>
                    <?php } ?>
                    <img src="/_res/img/fcctv_detail/viwer_guide_video.png" alt="" class="guide">
                    <img src="/_res/img/fcctv_detail/viwer_guide_w.png" alt="" class="guide web">
                </div>
                <!-- TIP -->

                <div class="review__title">
                    <!-- <p class="onair"><strong>ON AIR</strong> <span>01:56:04</span></p> -->
                    <!-- 웹화면만 노출. -->
                    <p class="title"><strong><?=$videoCategory[$video->code]?></strong> <span></span><?=$video->title?></p>
                    <!-- 웹화면만 노출. -->

                    <!-- 모바일 화면만 노출 -->
                    <button type="button" class="view" id="mobileInfo" videoId="<?=$video->id?>" ><?=__("비디오 상세보기")?></button>
                    <div class="social__modal--video">
                        <button type="button" class="open" onclick="js.social(event);">SNS</button>
                        <ul class="social">
                            <li class="sns"><a href="javascript:void(0);"  class="link f fbShare">Facebook</a></li>
                            <li class="sns"><a href="javascript:void(0);"  class="link t twShare">Twitter</a></li>
                            <li class="sns"><a href="javascript:void(0);"  class="link w wbShare">Weibo</a></li>
                        </ul>
                    </div>
                </div>

                <div class="video__about is-zoom">
                    <div class="title">
                        <?=__('About Video')?>
                        <div class="social">
                            <a href="javascript:void(0);"  class="link f fbShare">Facebook</a>
                            <a href="javascript:void(0);"  class="link t twShare">Twitter</a>
                            <a href="javascript:void(0);"  class="link w wbShare">Weibo</a>
                        </div>
                    </div>
                    <p class="description">
                        <?=nl2br($video->video_info)?>
                        <span class="designer">
                            <span class="top"><?=__('Designer')?></span>
                            <?php foreach($designerInfo as $key => $value){ ?>
                                <a href="/designers/detail?designerId=<?=$key?>"><?=$value?></a>&nbsp;
                            <?php }?>
                        </span>
                    </p>
                </div>
            </div>
            <div class="details__products show-detail is-zoom"> <!-- show-comment -->


                <!-- 상품 -->
                <button type="button" class="product_tab" id="itemBtn"><?=__('{0} items', sizeof($productPriceInfo))?></button>
                <ul class="product_list">
                    <?php foreach($productPriceInfo as $prdCode => $product){ ?>

                        <li class="list pr<?=$prdCode?>">
                            <div class="product__thumnail">
                                <span class="cover" style="background-image: url('<?=$product['mainImage']?>');"></span>
                                <div class="summery">
                                    <p class="category"><?=$product['userName']?></p>
                                    <span class="title"><?=$product['name']?></span>
                                    <p class="cost"><?=$this->FccTv->currencyStr($product['price']);?>
                                        <?php

                                        if($product['stock']<4 && $product['stock'] >0) {
                                            echo "<strong> * ".__('{0} LEFT', [$product['stock']])."</strong>";
                                        }else if($product['stock']<1){
                                            echo "<b> * ".__("SOLD OUT")."</b>";
                                        }
                                        ?>
                                    </p>
                                    <button type="button" class="buy"><?=__("옵션 선택")?></button>
                                    <a href="/product/detail/<?=$product['encId']?>" class="buy shop"><?=__("SHOP")?></a>
                                </div>
                            </div>
                            <div class="product__detail">
                                <?php
                                $defaultOptionCode = "";
                                $defaultPrice = 0;
                                $defaultColor = "";
                                $defaultMaxPurchage = 0;
                                $defaultSize=array();
                                $defaultStock=0;
                                $i=0;

                                foreach($product['detail'] as $color => $size){
                                    $defaultColor = ($i==0)?$color:$defaultColor;
                                    $defaultSize = ($i==0)?$size:$defaultSize;
                                    $defaultOptionCode = ($i==0)?$size[0]['prdOptCode']:$defaultOptionCode;
                                    $defaultPrice = ($i==0)?$size[0]['price']:$defaultPrice;
                                    $defaultMaxPurchage = ($i==0)?$size[0]['max']:$defaultMaxPurchage;
                                    $defaultStock = ($i==0)?$size[0]['stock']:$defaultStock;

                                    $i++;
                                }
                                ?>
                                <!-- 제품, 정보 -->
                                <div class="product__options">
                                    <div class="position__wrap">
                                        <div class="position">
                                            <div class="options">
                                                <div class="option">
                                                    <p class="option__title"><?=__("Color")?></p>
                                                    <select name="select__color" prdCode="<?=$prdCode?>" class="is-select">
                                                        <?php
                                                        foreach($product['detail'] as $color => $size){
                                                            $selected = ($color==$defaultColor)?"selected":"";
                                                            echo "<option value=\"".$color."\" ".$selected.">".$color."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="color is-drops">
                                                        <ul class="drops">
                                                            <?php
                                                            $isCurrent = ($color==$defaultColor)?"class=\"current\"":"";
                                                            foreach($product['detail'] as $color => $size){
                                                                echo "<li class=\"item\" ><button type=\"button\" ".$isCurrent.">".$color."</button></li>";
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <?php
                                                foreach($product['detail'] as $color => $size){
                                                    $display = ($color==$defaultColor)?"":"style=\"display: none\"";
                                                    ?>
                                                    <div class="option csize_<?=$prdCode?> csize_<?=$prdCode?>_<?=str_replace(" ","_",$color)?>" <?=$display?>>
                                                        <p class="option__title"><?=__('Size')?></p>
                                                        <select name="select__size" class="is-select" prdCode="<?=$prdCode?>">
                                                            <option value="" stock="0" maxpurchage="0" unitprice="0" selected><?=__("사이즈를 선택해주세요.")?></option>
                                                            <?php
                                                            $i=0;
                                                            foreach($size as $sizeDetail){
                                                                $soldoutMsg = "";
                                                                if($sizeDetail['stock']<1){
                                                                    $soldoutMsg = " - ".__("매진된 상품입니다");
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
                                                <div class="option">
                                                    <p class="option__title"><?=__("Quantity")?></p>
                                                    <select name="select__quantity" id="select__quantity_<?=$prdCode?>" class="is-select nums qty_<?=$prdCode?>">
                                                        <option value="1" selected>1</option>
                                                    </select>
                                                    <div class="is-counter">
                                                        <button type="button" class="plus" prdCode="<?=$prdCode?>" onclick="js.counter(event, this)"><?=__("추가")?></button>
                                                        <button type="button" class="minus" prdCode="<?=$prdCode?>" onclick="js.counter(event, this)"><?=__("빼기")?></button>
                                                        <input type="number" value="1" class="count qty_show_<?=$prdCode?>" id="qnt_<?=$prdCode?>" disabled="disabled">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="total"><?=__("소계")?>: <span id="totalPrice_<?=$prdCode?>">0</span></p>

                                        <input type="hidden" name="productOptionCode_<?=$prdCode?>" id="productOptionCode_<?=$prdCode?>" value="" unitprice="0" stock="0" maxpurchage="1">
                                        <input type="hidden" name="quantity_<?=$prdCode?>" id="quantity_<?=$prdCode?>" value="1">
                                        <div class="buynow">
                                            <?php if($product['stock']<1){ ?>
                                                <p><span class="toout"><?=__("SOLD OUT")?></span></p>
                                            <?php }else{?>
                                            <p><button type="button" class="tocart addToCart" prdCode="<?=$prdCode?>"><?=__("Add to Cart")?></button></p>
                                            <p><button type="button" class="tobuy checkOutBtn" prdCode="<?=$prdCode?>"><?=__("Checkout")?></button></p>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="close"><?=__("Close")?></button>
                            </div>
                        </li>

                    <?php } ?>
                </ul>

                <!-- 코멘트 -->
                <button type="button" class="product_tab coments" id="commentBtn"><?=__('{0} Comments', $commentCount)?></button>

                <div class="products__comments">
                    <p class="count"><?=__("Comments")?> <span id="mobileCmtCnt"><?=$commentCount?></span></p>
                    <!-- 로그인, 로그 아웃 상태 구분 없음. 로그아웃 상태에서 선택될 경우 Sign in 안내 문구 노출. -->
                    <?php if($userInfo!=null){ ?>
                        <div class="comments__white">
                            <div class="white">
                                <?=__("Write a Comment")?> <span class="limit"> <span id="textCnt">0</span>/140</span>
                                <textarea name="comment" id="comment" maxlength="140"></textarea>
                            </div>
                            <a href="javascript:void(0);" class="add" id="commentSaveBtn"><?=__("Add Comment")?></a>
                        </div>
                    <?php }else{ ?>
                        <div class="comments__white si-disabled" onclick="modal.show('uneedlogin');">
                            <div class="white">
                                <?=__("Write a Comment")?> <span class="limit">0/140</span>
                                <textarea name="comment" disabled=""></textarea>
                            </div>
                            <span class="add"><?=__("Add Comment")?></span>
                        </div>
                    <?php } ?>

                    <ul class="comments__list">
                        <?php if($commentCount>0) {
                            foreach($comments as $comment){?>
                                <li class="list">
                                    <div class="comment__view">
                                        <div class="head">
                                            <!-- 코멘트용 유저 사진 -->
                                            <a href="#" class="photo"><span style="background-image: url('<?=($comment->user->image_path)?FILE_URI.$comment->user->image_path:"/_res/img/navigation/rnb_user_default.png"?>')"></span></a>
                                            <!-- e:코멘트용 유저 사진 -->
                                            <?=$comment->user->nickname?>
                                            <?php if($userInfo!=null && $userInfo['id']==$comment->user->id){ ?>
                                                <span class="modi csave_area_<?=$comment->id?>" style="display: none;">
                                                <button type="button" class="save commentEditSave" commentId="<?=$comment->id?>"><?=__('Save')?></button>
                                                <span class="line"></span>
                                                <button type="button" class="cancel commentEditCancel" commentId="<?=$comment->id?>"><?=__('Cancel')?></button>
                                            </span>
                                                <span class="modi cbtn_area_<?=$comment->id?>">
                                                <button type="button" class="commentEdit" commentId="<?=$comment->id?>"><?=__('Edit')?></button>
                                                <span class="line"></span>
                                                <button type="button" class="commentDelete" commentId="<?=$comment->id?>"><?=__('Delete')?></button>
                                            </span>
                                            <?php }?>
                                        </div>
                                        <?php if($userInfo!=null && $userInfo['id']==$comment->user->id){ ?>
                                            <textarea class="description tarea_<?=$comment->id?>" name="comment modify" style="display: none;"><?=$comment->comment?></textarea>
                                        <?php } ?>
                                        <p class="description carea_<?=$comment->id?>"><?=nl2br($comment->comment)?></p>
                                    </div>
                                </li>
                            <?php }
                        }else{?>
                            <li class="list">
                                <div class="comment__view" style="text-align: center"><?=__("등록된 코멘트가 없습니다.")?></div>
                            </li>
                        <?php } ?>
                        <li>
                            <a href="javascript:void(0);" class="more" id="viewMoreComment" style="<?=($commentCount<=10)?"display:none":""?>"><?=__('View More Comments')?></a>
                        </li>
                    </ul>
                </div>
            <!-- 상세팝업, 웹 전용 -->
            </div>
            <a href="/tv/" class="gotolist"><span class="mobile__navigation--leftbtn">뒤로</span> TV 목록보기</a>
        </div>
    </div> <!-- contents -->
</section>

<input type="hidden" name="videoId" id="videoId" value="<?=$video->id?>">
<form action="/Purchases/orderInfo" method="post" id="order"></form>
<script src="/_res/js/onair.js"></script>

<!-- ******************************************** 페이지별 모달 레이어 (가변영역), z-index : 1000 -->
<aside id="modal">
    <div class="modal__board"><div class="modal__contents"></div></div>
    <div class="modal__bg"></div>
</aside>

<script>

    Number.prototype.numformat = function(){
        if(this==0) return 0;

        var reg = /(^[+-]?\d+)(\d{3})/;
        var n = (this + '');

        while (reg.test(n)) n = n.replace(reg, '$1' + ',' + '$2');

        return n;
    };


    $("select[name=select__color]").on("change",function(){
        var color = $(this).val();
        var productCode = $(this).attr("prdCode");
        $(".csize_"+productCode).hide();

        $(".qty_"+productCode).val(1);
        $(".qty_show_"+productCode).val(1);

        color = color.replace(" ","_");

        var firstElement = $(".csize_"+productCode+"_"+color).find("select[name=select__size] > option:first");
        $(".csize_"+productCode+"_"+color).find("select[name=select__size] > option").attr("selected",false);
        firstElement.attr("selected",true);

        $("#productOptionCode_"+productCode).val('');
        $("#productOptionCode_"+productCode).val(firstElement.val());
        $("#productOptionCode_"+productCode).attr("unitprice",firstElement.attr("unitprice"));
        $("#productOptionCode_"+productCode).attr("maxpurchage",firstElement.attr("maxpurchage"));
        $("#totalPrice_"+productCode).text("0");

        if(color!="") {
            $(".csize_"+productCode+"_"+color).show();
        }

    });

    /** 총 금액 설정 **/
    function calcuTotalPrice(productCode){
        var unitPrice = $("#productOptionCode_"+productCode).attr("unitprice");
        var quantity = $("#quantity_"+productCode).val();

        var totalPrice = unitPrice*quantity;

        $("#totalPrice_"+productCode).text(totalPrice.numformat());
    }

    $("select[name=select__size]").on("change",function(){
        var productCode = $(this).attr("prdCode");
        if($(this).find("option:selected").val()!=""){
            var unitprice = $(this).find("option:selected").attr("unitprice");
            var maxpurchase = $(this).find("option:selected").attr("maxpurchage");
            var stock = $(this).find("option:selected").attr("stock");
            var prdOpCode = $(this).val();
            $("#productOptionCode_"+productCode).val(prdOpCode);
            $("#productOptionCode_"+productCode).attr("unitprice", unitprice);
            $("#productOptionCode_"+productCode).attr("maxpurchage",maxpurchase);
            $("#productOptionCode_"+productCode).attr("stock",stock);

            $("#select__quantity_"+productCode+" > option").remove();
            for(var i=1;i<=maxpurchase;i++){
                $("#select__quantity_"+productCode).append("<option value='"+i+"'>"+i+"</option>");
            }

            if(stock < 1){
                modalalert('매진된 상품입니다');
                $(this).val('');
                $("#quantity_"+productCode).val(1);
                $("#qnt_"+productCode).val(1);
                $("#totalPrice_"+productCode).text(0);
                $("#productOptionCode_"+productCode).val('');
                return false;
            }

            $("#quantity_"+productCode).val(1);
            $("#qnt_"+productCode).val(1);
            calcuTotalPrice(productCode);
//            $("#qShow").show();
        }else{
            $("#totalPrice_"+productCode).text(0);
            $("#productOptionCode_"+productCode).val('');
        }
    });

    /** 수량 조절 **/
    $(".minus").on("click",function(){
        var productCode = $(this).attr("prdCode");
        var quantityVal = eval($("#quantity_"+productCode).val());
        quantityVal--;
        if(quantityVal<1){
            modalalert("<?=__("최소 1개의 수량은 선택하셔야 합니다.")?>");
            quantityVal = 1;
        }
        $("#quantity_"+productCode).val(quantityVal);
        calcuTotalPrice(productCode);
    });

    $(".plus").on("click",function(){
        var productCode = $(this).attr("prdCode");
        if($("#productOptionCode_"+productCode).val()==""){
            modalalert("<?=__("사이즈를 선택 해주세요.")?>");
            $("#qnt_"+productCode).val(1);
            return false;
        }

        var maxpurchage = $("#productOptionCode_"+productCode).attr('maxpurchage');
        var stock = $("#productOptionCode_"+productCode).attr('stock');
        var quantityVal = eval($("#quantity_"+productCode).val());
        quantityVal++;
        var viewQuantVal = $("#qnt_"+productCode).val();

        if(stock < quantityVal){
            quantityVal--;
            viewQuantVal--;
            modalalert("<?=__("남은 수량이 충분하지 않습니다.")?>");
            $("#qnt_"+productCode).val(viewQuantVal);
            return false;
        }

        if(maxpurchage<quantityVal){
            quantityVal--;
            viewQuantVal--;
            modalalert("<?=__("최대 구매수량을 초과하였습니다.")?>");
            $("#qnt_"+productCode).val(viewQuantVal);
            return false;
        }

        $("#quantity_"+productCode).val(quantityVal);
        calcuTotalPrice(productCode);
    });
    /** 수량 조절 end **/


    $(function(){
        // -------------------------------------------* 즉시구매 클릭시 옵션 선택 활성.
        $('.product__thumnail').find('button.buy').on('click', function(e){
            if ( $(this).hasClass('is-disabled') ) {
                $(this).removeClass('is-disabled');
                $(this).parent().parent().parent().find('.product__detail').removeClass('is-show');
                return;
            }
            $(this).addClass('is-disabled');
            $(this).parent().parent().parent().find('.product__detail').addClass('is-show');
        });
        $('.product__detail').find('button.close').on('click', function(e){
            $(this).parent().parent().find('button.buy').removeClass('is-disabled');
            $(this).parent().removeClass('is-show');
        });
    })
</script>
<script>
    function product_quantity(change, el) {
//        // -------------------------------------------* 수량 변경 인자 넘김 함수.
//        console.log('Quantity : '+change);
//        console.log(el);
    }
</script>

<?php if(sizeof($productPriceInfo)==0){?>
    <script>
        $('.details__products').addClass('show-comment');
        $('.details__products').removeClass('show-detail');
        $("#itemBtn").prop("disabled",true);
    </script>
<?php } ?>

<script>
    $("#mobileInfo").on("click",function(){
        var videoId = $(this).attr("videoId");
        modal_show2('videoInfo/'+videoId);
    });

    /** SNS 공유 **/
    var defaultOption = 'top=300, left=300, toolbar=no, menubar=no, location=no, directories=no, status=no, resizable=no, scrollbars=yes';
    var targetUrl = location.href;
    $(document).on("click",".fbShare",function(){
        var popUrl = 'http://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(targetUrl);
        window.open(popUrl, 'fbSharePop', 'width=575, height=250,'+ defaultOption);
    });

    $(document).on("click",".wbShare",function(){
        var pop_url = 'http://service.weibo.com/share/share.php?url=' + encodeURIComponent(targetUrl) + '&title=' + '<?=addslashes($video->title)?>';
        window.open(pop_url, 'share_weibo', 'width=620, height=350,'+ defaultOption);
    });
    
    $(document).on("click",".twShare",function(){
        var pop_url = 'http://twitter.com/intent/tweet?url='+encodeURIComponent(targetUrl)+'&text='+'<?=addslashes($video->title)?>';
        window.open(pop_url, 'share_twitter', 'width=620, height=350,'+ defaultOption);
    });


    var loadSize;       //상품 사이즈 array 처리를 위한 배열 선언
    var owlCselLoop;

    function cartReCnt(){
        $.getJSON('/cart/cartCnt',function(data){
            if(data.cartCnt > 0){
                $('.cartCnt').html(data.cartCnt);
                $(".cartCnt").show();
            }else{
                $(".cartCnt").hide();
            }
        });
    }


    var nextPage= 2;
    $("#viewMoreComment").on("click",function(){
        getComment();
        nextPage++;
    });


    function byteCut(text,len) {
        var l = 0;
        for (var i=0; i<text.length; i++) {
            l += (text.charCodeAt(i) > 128) ? 2 : 1;
            if (l > len) return text.substring(0,i);

        }
        return text;
    }


    $("#comment").on("keydown",function(){
        var text = $(this).val();
        var byteCount = getByteLength(text);
        if(byteCount>=140){
            var cutText = byteCut(text,140);
            $(this).val(cutText);
        }else {
            $("#textCnt").text(byteCount);
        }
    });
    /*** 코멘트 입력 길이 체크 ***/
    function getByteLength(str){
        var l = 0;
        for (var i=0; i<str.length; i++) l += (str.charCodeAt(i) > 128) ? 2 : 1;
        return l;
    }

    function getComment(){
        var videoIdVal = $("#videoId").val();
        $.ajax({
            url: '/fccTv/getMoreComment',
            dataType: 'json',
            type : 'Post',
            data : {
                videoId : videoIdVal,
                page : nextPage
            },
            success: function (rtn) {
                var htmlVal = "";
                for(var i=0;i<rtn.length;i++){
                    htmlVal += makeCommentHtml(rtn[i]);
                }

                $(".comments__list > .list").each(function(){
                    $(this).remove();
                });

                $(".comments__list > li:last").before(htmlVal);
                if(rtn.length < (nextPage*5)){
                    $("#viewMoreComment").hide();
                }
            }
        });
    }

    function makeCommentHtml(obj){
        var userProfileImage  = (obj.userProfile!="")?obj.userProfile:"/_res/img/navigation/rnb_user_default.png";
        var html = "" +
            '<li class="list">' +
            '<div class="comment__view">' +
            '<div class="head">' +
            '<a href="#" class="photo">' +
            '<span style="background-image: url(\''+userProfileImage+'\')">' +
            '</span></a>' +
            obj.userName;
            if(obj.isMine==true){
                html += '<span class="modi csave_area_'+obj.commentId+'" style="display: none;">' +
                '<button type="button" class="save commentEditSave" commentId="'+obj.commentId+'"><?=__("Save")?></button>' +
                '<span class="line"></span>' +
                '<button type="button" class="cancel commentEditCancel" commentId="'+obj.commentId+'"><?=__("Cancel")?></button>' +
                '</span>' +
                '<span class="modi cbtn_area_'+obj.commentId+'">' +
                '<button type="button" class="commentEdit" commentId="'+obj.commentId+'"><?=__("Edit")?></button>' +
                '<span class="line"></span>' +
                '<button type="button" class="commentDelete" commentId="'+obj.commentId+'"><?=__("Delete")?></button>' +
                '</span>';
            }
            html += '</div>';
            if(obj.isMine==true){
                html += '<textarea class="description tarea_'+obj.commentId+'" name="comment modify" style="display: none;" maxlength="140">'+obj.commentOrg+'</textarea>';
            }
            html += '<p class="description carea_'+obj.commentId+'">'+obj.comment+'</p>' +
            '</div>' +
            '</li>';

        return html;
    }

    function commentCount(){
        var videoIdVal = $("#videoId").val();
        $.ajax({
            url: '/fccTv/getCommentCount',
            dataType: 'json',
            type : 'Post',
            data : {videoId:videoIdVal},
            success: function (rtn) {
                $("#mobileCmtCnt").text(rtn.count);
                $("#commentBtn").text("댓글 "+(rtn.count)+"개");
            }
        });
    }

    $("#commentSaveBtn").on("click",function(){
        var commentVal = $("#comment").val();
        var videoIdVal = $("#videoId").val();
        var commentId = $("#commentId").val();
        saveComment(commentVal,videoIdVal,'regist',commentId);
        $("#comment").val('');
        commentCount();
    });

    $(document).on("click",".commentEditSave",function(){
        var commentId = $(this).attr("commentId");
        var commentVal = $(".tarea_"+commentId).val();
        var videoIdVal = $("#videoId").val();
        saveComment(commentVal,videoIdVal,'update',commentId);
    });

    $(document).on("click",".commentEditCancel",function(){
        var cmtId = $(this).attr("commentId");
        $(".carea_"+cmtId).show();
        $(".tarea_"+cmtId).hide();
        $(".cbtn_area_"+cmtId).show();
        $(".csave_area_"+cmtId).hide();
    });

    $(document).on("click",".commentEdit",function(){
        var cmtId = $(this).attr("commentId");
        $(".carea_"+cmtId).hide();
        $(".tarea_"+cmtId).show();
        $(".cbtn_area_"+cmtId).hide();
        $(".csave_area_"+cmtId).show();
    });

    $(document).on("click",".commentDelete",function(){
        var commentIdVal = $(this).attr("commentId");
        $.ajax({
            url: '/fccTv/commentDelete',
            dataType: 'json',
            type : 'Post',
            async : false,
            data : {commentId:commentIdVal},
            success: function (rtn) {
                if(rtn.result==true){
                    getComment();
                }
            }
        });
        commentCount();
    });

    function saveComment(commentVal,videoIdVal,modeVal,commentId){
        var dataObj = {
            comment : commentVal,
            mc_video_info_id : videoIdVal,
            mode : modeVal
        };

        if(modeVal=='update'){
            dataObj = {
                comment : commentVal,
                mc_video_info_id : videoIdVal,
                mode : modeVal,
                id : commentId
            };
        }
        $.ajax({
            url: '/fccTv/commentAdd',
            dataType: 'json',
            type : 'Post',
            data : dataObj,
            async :false,
            success: function (rtn) {
                if(rtn.result==true){
                    getComment();
                    $("#textCnt").text(0);
                }
            }
        });
        commentCount();
    }

    $(".checkOutBtn").on("click",function(){

        var productCode = $(this).attr("prdCode");
        var productOptionCode = $("#productOptionCode_"+productCode).val();
        var quantityVal = $("#quantity_"+productCode).val();

        if(productOptionCode==""){
            modalalert("<?=__("상품 옵션을 선택해주세요")?>");
            return false;
        }

        $.ajax({
            url: '/fccTv/getStockCount',
            dataType: 'json',
            type : 'Post',
            data : {prdOptCode:productOptionCode},
            async :false,
            success: function (rtn) {
                if(rtn.result == true){
                    var valueText = "<input type='hidden' name='data[0][product_option_code]' value='" + productOptionCode + "'>\n";
                    valueText += "<input type='hidden' name='data[0][quantity]' value='" + quantityVal + "'>\n";
                    $("#order").html(valueText);
                    $("#order").submit();
                }else{
                    modalalert("<?=__("죄송합니다.<br/>상품이 매진 되었습니다.")?>");
                    location.reload();
                }
            }
        });
    });

    $(".addToCart").on("click",function(){
        var productCode = $(this).attr("prdCode");
        var productOptionCode = $("#productOptionCode_"+productCode).val();
        var quantityVal = $("#quantity_"+productCode).val();

        var sendObj = {
            product_option_code : productOptionCode,
            quantity : quantityVal
        };
        $.ajax({
            url: '/cart/add',
            dataType: 'json',
            type : 'Post',
            data : sendObj,
            success: function (rtn) {
                if(!rtn.result.result){
                    if(rtn.result.msg=="soldout"){
                        modalalert("<?=__("죄송합니다.<br/>상품이 매진 되었습니다.")?>");
                    }else {
                        modalalert("fail");
                    }
                }else {
                    modal.show('modal_cart_save.html');
                    cartCnt();
                }
            }
        });
    });


</script>