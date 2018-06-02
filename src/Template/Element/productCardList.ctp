<?php
$jsonLinkUrl = "/product/productList.json?".$_SERVER['QUERY_STRING'];
$pageLinkUrl=$_SERVER['REQUEST_URI'];
$pageSize = isset($_GET['pageSize']) ?$_GET['pageSize'] : 30;
$sortId = isset($_GET['sortId']) ?$_GET['sortId'] : 'new';
$pageSizeArray = [30=>30,60=>60,90=>90];
$sortIdArray = ['new'=>'NEW 신상품','best'=>'BEST','lowprice'=>'낮은 가격 순','highprice'=>'높은 가격 순'];

?>
<script>
    var defaultOption = 'top=300, left=300, toolbar=no, menubar=no, location=no, directories=no, status=no, resizable=no, scrollbars=yes';
    var curDomain = "https://"+document.domain;
    function shareing(targetUrl,prdName,value){
        var pop_url = "";
        var popName = "";
        var options = "";
        if(value=="facebook"){
            pop_url = 'http://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(curDomain+"/"+targetUrl);
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
</script>



<div class="main__products">
    <!-- 상품 리스트, 정렬 기준 선택 -->
    <nav>
        <p class="is-skip"><?=__('상품 정렬 방식을 선택하세요.')?></p>
        <div class="array__wrap is-zoom">
            <p class="is-skip"><?=__('상품 노출 개 수 선택')?></p>
            <div class="left">
<?php
foreach($pageSizeArray as $k => $v){
    $isSelect = $pageSize == $v ? 'is-select' : '' ;
    echo '<a href="'.$this->FccTv->updateQueryStringParameter($pageLinkUrl ,'pageSize', $v).'" class="total '.$isSelect.'">'.$v.'</a>';
}
?>
            </div>
            <div class="right is-zoom">
                <p class="is-skip"><?=__('상품 정렬 기준 선택')?></p>
                <div class="array__select">
                    <button type="button" class="current"><?=$sortIdArray[$sortId]?></button>
                    <ul>
                        <?php
                        foreach($sortIdArray as $k => $v){
                            echo '<li><a href="'.$this->FccTv->updateQueryStringParameter($pageLinkUrl ,'sortId', $k).'">'.$v.'</a></li>';
                        }
                        ?>
                    </ul>
                </div>
                <?php
                if(isset($brandTag)){
                    $brandText = 'DESIGNER';
                    if(isset($_GET['designerId'])){
                        if($_GET['designerId']!=''){
                            $brandText =  $brandTag[$_GET['designerId']];
                        }
                    }
                ?>

                <div class="array__select">
                    <button type="button" class="current"><?=$brandText?></button>
                    <ul>
                        <?php
                        echo '<li><a href="'.$this->FccTv->updateQueryStringParameter($pageLinkUrl ,'designerId', '').'">ALL DESIGNER</a></li>';
                        foreach($brandTag as $k=>$v){
                            echo '<li><a href="'.$this->FccTv->updateQueryStringParameter($pageLinkUrl ,'designerId', $k).'">'.$v.'</a></li>';
                        }
                        ?>
                    </ul>
                </div>
                <?php }?>
            </div>
        </div>

    </nav>
    <div class="cardlist products">
        <div class="cardlist__sizer"></div>
        <div class="cardlist__gutter"></div>
        <?php
            foreach($data['product'] as  $product){
        ?>
        <div class="cardlist__lists">

            <div class="border">
                <?php
                $productUrl = '/product/detail/'.$product['product_code'];
                if($product['stockCnt']<1){
                    echo '<strong class="cardlist__soldout">'.__("SOLD OUT").'</strong> <!-- 매진됨. -->';
                }else if($product['stockCnt']<4){
                    echo '<strong class="cardlist__left">'.__('{0} LEFT', [$product['stockCnt']]).'</strong> <!-- 수량이 얼마 남지 않음. -->';
                }else if($product['new_icon']=='y'){
                    echo '<strong class="cardlist__new">NEW</strong>';
                }
                ?>
                <div class="cardlist__item">
                    <a class="link" href="<?=$productUrl?>">
                        <img class="cardlist__thumail" src="<?=$product['murl']?>" alt="">
                        <span class="overlap"></span>
                    </a>
                    <div class="cardlist__social">
                        <div class="drops">
                            <button type="button" class="onoff" onclick="js.social_cardlist(event, this);"><?=__("공유보기")?></button>
                            <ul class="dropslist">
                                <li class="sns"><button type="button" onclick="shareing('<?=$productUrl?>','<?=$product['name']?>','facebook');" class="share f">Facebook</button></li>
                                <li class="sns"><button type="button" onclick="shareing('<?=$productUrl?>','<?=$product['name']?>','twitter');" class="share t">Twitter</button></li>
                                <li class="sns"><button type="button" onclick="shareing('<?=$productUrl?>','<?=$product['name']?>','weibo');" class="share w">Weibo</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <a href="<?=$productUrl?>" class="cardlist__summery">
                    <div class="inner">
                        <h4 class="name"><span><?=$product['designer_name']?></span></h4>
                        <p class="info"><span><?=$product['name']?></span></p>
                        <p class="price"><span class="won">￦</span> <?=number_format($product['price'])?></p>
                    </div>
                </a>
            </div>
        </div>
        <?php }?>
    </div>
<?php
    if($type=='main') {
        if ($data['page']['next'] != 'false') {
            echo <<<more
            <div class="main__products--more is-webonly">
                <button type="button" class="buttons save" id="cardlist__more">
                    <span class="text">제품 더보기</span>
                    <span class="bg"></span>
                </button>
            </div>
more;

        }
    }else {
        echo '<div class="paging__wrap"><div class="pages">';
        if($data['page']['prevRang']!='false'){
            echo '<a href="'. $this->FccTv->updateQueryStringParameter($pageLinkUrl ,'page', $data['page']['prevRang']).'" class="prev">이전</a>';
            $minPage = $data['page']['prevRang']+1;
        }else {
            $minPage = 1;
        }
        if($data['page']['nextRang']!='false'){
            $maxPage = $data['page']['nextRang'];
        }else{
            $maxPage = $data['page']['total'] + 1;
        }

        for ($i = $minPage; $i < $maxPage; $i++) {
            if ($i == $data['page']['now']) {
                echo "<a href='" .  $this->FccTv->updateQueryStringParameter($pageLinkUrl ,'page', $i) . "' class='page is-current'>" . $i . "</a>";
            } else {
                echo "<a href='" . $this->FccTv->updateQueryStringParameter($pageLinkUrl ,'page', $i) . "' class='page'>" . $i . "</a>";
            }
        }
        if($data['page']['nextRang']!='false'){
            echo '<a href="'. $this->FccTv->updateQueryStringParameter($pageLinkUrl ,'page', $data['page']['nextRang']).'" class="next">다음</a>';
        }
        echo '</div></div>';
        if ($data['page']['next'] != 'false') {
            echo <<<more
            <div class="main__products--more is-webonly">
                <button type="button" class="buttons save" id="cardlist__more">
                    <span class="text">제품 더보기</span>
                    <span class="bg"></span>
                </button>
            </div>
more;
        }

    }
?>
</div>

<script>

    $(function(){
        // -------------------------------------------* 정렬선택 기능에 키보드 FOCUS 세팅.
        $('.array__select .current').focus(function(){
            reset();
            $(this).parent().find('ul').show();
        })
        $('.array__select:last').find('a:last').blur(function(){
            reset();
        })
        $('.array__wrap .total:last').focus(function(){
            reset();
        })
        function reset() {
            $('.array__select').find('ul').hide();
        }
    })

    $(function(){
        $('#cardlist__more').click(function(){
            masonryListAppend();
        });

    });

    var page = 1;
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }
        else {
            return uri + separator + key + "=" + value;
        }
    }
    function paramUpdate(key,value){
        var uri = '<?=$pageLinkUrl?>';
        uri = updateQueryStringParameter(uri,'page','1')
        location.href=updateQueryStringParameter(uri,key,value);
    }
    function masonryListAppend(){

        var p=page+1;
        var urlStr = updateQueryStringParameter('<?=$jsonLinkUrl?>','page',p);
        $.ajax({
            url : urlStr,
            async : false,
            success :function(data) {
                page++;
                $('.cardlist').append(setCardList(data));
            }
        });
    }
    function setCardList(data){
        var page = data.rtn.page;
        if(page.next=='false'){
            $('.main__products--more').hide();
        }
        var productList = data.rtn.product;
        var elems='';
        $.each(productList,function(k,v){
            elems+=setItemElement(v);

        });
        return elems;
    }
    function setItemElement(v){

        var html='<div class="cardlist__lists"><div class="border">';
        var productUrl = '/product/detail/'+v.product_code;
        if(v.stockCnt<1) {
            html += '<strong class="cardlist__soldout"><?=__("SOLD OUT")?></strong>';
            productUrl = 'javascript:void(0);';
        }else if(v.stockCnt<4){
            html += '<strong class="cardlist__left">'+ v.stockCnt+' LEFT</strong>';
        }else if(v.newIcon=='y'){
            html += '<strong class="cardlist__new">NEW</strong>';
        }
        html +=
            '<div class="cardlist__item">'+
                '<a class="link" href="'+productUrl+'">'+
                    '<img class="cardlist__thumail" src="'+v.murl+'" alt="" >'+
                    '<span class="overlap"></span>'+
                '</a>'+
                '<div class="cardlist__social">'+
                    '<div class="drops">'+
                        '<button type="button" class="onoff" onclick="js.social_cardlist(event, this);"><?=__("공유보기")?></button>'+
                        '<ul class="dropslist">'+
                            '<li class="sns"><button type="button" onclick="shareing(\''+productUrl+'\',\''+v.name+'\',\'facebook\');" class="share f">Facebook</button></li>'+
                            '<li class="sns"><button type="button" onclick="shareing(\''+productUrl+'\',\''+v.name+'\',\'twitter\');" class="share t">Twitter</button></li>'+
                            '<li class="sns"><button type="button" onclick="shareing(\''+productUrl+'\',\''+v.name+'\',\'weibo\');" class="share w">Weibo</button></li>'+
                        '</ul>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<a href="'+productUrl+'" class="cardlist__summery">'+
                '<div class="inner">'+
                    '<h4 class="name"><span>'+v.designer_name+'</span></h4>'+
                    '<p class="info"><span>'+v.name+'</span></p>'+
                    '<p class="price"><span class="won">￦</span> '+ numberFormat(v.price)+'</p>'+
                '</div>'+
            '</a>'+
        '</div></div>';
        return html;
    }
    function numberFormat(str) {
        str = String(parseInt(str));
        return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
    }
</script>