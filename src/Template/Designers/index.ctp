<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip"><?=__("FCCTV 미디어 커머스 : 디자이너, 브랜드 소개")?></h2>

    <div class="contents">

        <!-- 디자이너 브랜드 리스트 -->
        <div class="designer__introduction">
            <!-- 커버스토리 -->
            <?php
            echo $this->element('pageCover20160531',[
                'coverType'=>'designer',
            ]);
            ?>

            <ul class="designer__brandlist is-zoom">
                <?php foreach($brands as $brand){ ?>
                    <li class="list">
                        <a href="/designers/detail?designerId=<?=$brand['id']?>" class="link">
                            <dl class="designer__brand">
                                <dt class="logo">
                                    <img src="<?=$brand['image']?>" alt="<?=$brand['name']?>">
                                </dt>
                                <dd class="detail">
                                    <span class="name"><?=$brand['name']?></span>
                                    <span class="line"></span>
                                    <?php if(sizeof($brand['category'])>0){
                                        foreach($brand['category'] as $cateName){?>
                                        <span class="comment"><?=strtoupper($cateName)?></span>

                                    <?php }
                                        }?>
                                </dd>
                            </dl>
                            <span class="overlap"></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>

            <div class="paging__wrap">
                <div class="pages">
                    <?php
                    if($pagination['prev']!=0){
                        echo "<a href='/designers?page=".$pagination['prev']."' class='prev'>".__("Prev")."</a>";
                    }
                    $nowPage = $pagination['nowPage'];
                    for($i=0;$i<sizeof($pagination['scope']);$i++){
                        $paginNo = $pagination['scope'][$i];
                        $isCurrent = '';
                        if($paginNo==$nowPage){
                            $isCurrent = 'is-current';
                        }
                        echo "<a href='/designers?page=".$paginNo."' class='page ".$isCurrent."'>".$paginNo."</a>";
                    }
                    if($pagination['next']!=0){
                        echo "<a href='/designers?page=".$pagination['next']."' class='next'>".__("Next")."</a>";
                    }
                    ?>
                </div>
            </div>
            <?php if($pagination['totalCount'] > $limit){?>
            <div class="main__products--more is-webonly">
                <button type="button" class="buttons save" id="designerMore">
                    <span class="text"><?=__("More")?></span>
                    <span class="bg"></span>
                </button>
            </div>
            <?php }?>
        </div>
    </div> <!-- contents -->
</section>



<script>
    var pageVal=2;
    var limitVal = <?=$limit?>;
    function makeHtml(obj){

        var categoryName = "";
        for(var i = 0;i<obj.category.length;i++){
            categoryName += '<span class="comment">'+
            obj.category[i].toUpperCase()+
            '</span>';
        }
        var html = '' +
            '<li class="list">' +
            '<a href="/designers/detail?designerId='+obj.id+'" class="link">' +
            '<dl class="designer__brand">' +
            '<dt class="logo">' +
            '<img src="'+obj.image+'" alt="'+obj.name+'">' +
            '</dt>' +
            '<dd class="detail">' +
            '<span class="name">'+obj.name+'</span>' +
            '<span class="line"></span>' +
            categoryName+
            '</dd>' +
            '</dl>' +
            '<span class="overlap"></span>' +
            '</a>' +
            '</li>';
        return html;
    }
    $("#designerMore").on("click",function(){
        $.ajax({
            url: '/designers/getNextPage',
            dataType: 'json',
            type : 'Post',
            data : {page :pageVal,limit:limitVal},
            success: function (rtn) {
                var html ="";
                for(var i=0;i<rtn.length;i++){
                    html += makeHtml(rtn[i]);
                }
                $(".designer__brandlist").append(html);
                pageVal++;

                if(rtn.length < limitVal){
                    $(".main__products--more").hide();
                }
            }
        });
    });
</script>