<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip"><?=__("FCCTV 미디어 커머스 방송 컨텐츠")?></h2>

    <div class="contents">
        <!-- 커버스토리 -->
        <?php
        echo $this->element('pageCover20160531',[
            'coverType'=>'tv',
        ]);
        ?>
        <!-- 방송컨텐츠 리스트 -->
        <div class="fcctv">
            <ul class="fcctv__videos is-zoom">
                <?php foreach($videoInfos['videoInfos'] as $video){ ?>
                <li class="item">
                    <div class="video__thumnail">
                        <a href="/tv/detail/<?=$video['id']?>" class="cover" style="background-image: url('<?=$video['video_image_url']?>');">
                            <img src="/_res/img/guide/tv_video.png" class="thumnail" alt="">
                            <span class="playbtn"></span>
                        </a>
                        <a href="/tv/detail/<?=$video['id']?>" class="summery">
                            <p class="title">
                                <span class="category"><?=$video['category']?></span>
                                <span class="name"><?=$video['title']?></span>
                            </p>
                            <p class="description">
                                <?=nl2br($video['info'])?>
                            </p>
                        </a>
                    </div>
                </li>
                <?php } ?>

            </ul>

            <div class="paging__wrap">
                <div class="pages">
                    <?php
                    if($videoInfos['pagination']['prev']!=0){
                        echo "<a href='/tv?page=".$pagination['prev']."' class='prev'>이전</a>";
                    }
                    $nowPage = $videoInfos['pagination']['nowPage'];
                    for($i=0;$i<sizeof($videoInfos['pagination']['scope']);$i++){
                        $paginNo = $videoInfos['pagination']['scope'][$i];
                        $isCurrent = '';
                        if($paginNo==$nowPage){
                            $isCurrent = 'is-current';
                        }
                        echo "<a href='/tv?page=".$paginNo."' class='page ".$isCurrent."'>".$paginNo."</a>";
                    }
                    if($videoInfos['pagination']['next']!=0){
                        echo "<a href='/tv?page=".$videoInfos['pagination']['next']."' class='next'>다음</a>";
                    }
                    ?>
                </div>
            </div>
            <?php if($videoInfos['pagination']['totalCount'] > $limit){?>
            <div class="main__products--more is-webonly">
                <button type="button" class="buttons save" id="tvMore">
                    <span class="text"><?=__("More")?></span>
                    <span class="bg"></span>
                </button>
            </div>
            <?php } ?>
        </div>

    </div> <!-- contents -->
</section>

<script>
    var pageVal=2;
    var limitVal = <?=$limit?>;
    function makeHtml(obj){
        var html = "";
        for(var i=0;i<obj.length;i++){
            html += '<li class="item">';
            html += '<div class="video__thumnail">';
            html += '<a href="/tv/detail/'+obj[i].id+'" class="cover" style="background-image: url('+obj[i].video_image_url+');">';
            html += '<img src="/_res/img/guide/tv_video.png" class="thumnail" alt="">';
            html += '<span class="playbtn"></span>';
            html += '</a>';
            html += '<a href="/tv/detail/'+obj[i].id+'" class="summery">';
            html += '<p class="title">';
            html += '<span class="category">'+obj[i].category+'</span>';
            html += '<span class="name">'+obj[i].title+'</span>';
            html += '</p>';
            html += '<p class="description">';
            html += obj[i].info;
            html += '</p>';
            html += '</a>';
            html += '</div>';
            html += '</li>';
        }

        return html;
    }

    $("#tvMore").on("click",function(){
        $.ajax({
            url: '/tv/getNextPage',
            dataType: 'json',
            type : 'Post',
            data : {page :pageVal,limit:limitVal},
            success: function (rtn) {
                if(typeof(rtn.videoInfos)!="undefined"){
                   var html = makeHtml(rtn.videoInfos);
                   $(".fcctv__videos").append(html);
                   pageVal++;
                    if(rtn.videoInfos.length < limitVal){
                        $(".main__products--more").hide();
                    }
                }else{
                    $(".main__products--more").hide();
                }

            }
        });
    });
</script>