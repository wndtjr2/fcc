<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip"><?=__("FCCTV 미디어 커머스 : 디자이너 소개")?></h2>
    <div class="contents">
        <div class="">
            <!-- 디자이너 커버 영역 -->
            <div class="designer__covers is-zoom">
                <div class="covers">
                    <img src="<?=$designer['main_image']?>" alt="" class="cover mobile">
                    <img src="<?=$designer['main_image']?>" alt="" class="cover web">
                </div>
                <div class="story">
                    <h2 class="title"><?=$designer['name']?> <span class="line"></span></h2>
                    <p class="descriptoon">
                        <?=$designer['contents']?>
                    </p>
                    <div class="gallery" id="Gallery">
                        <div class="designer__gallery">
                            <h3 class="label"><button type="button" class="btn"><?=__("GALLERY")?> <span class="arr"></span></button></h3>
                            <ul class="list">
                                <li class="item" id="Collection">
                                    <h4><span class="t"><?=__("COLLECTION")?></span></h4>
                                    <?php if(!empty($collection)):?>
                                        <?php foreach($collection['type2'] as $k => $v):?>
                                            <a class="l" href="javascript:void(0);" onclick="modal_show3('designerGallery', <?=$v['id']?>)"><?=$v['name']?></a>
                                        <?php endforeach;?>
                                    <?php endif;?>
                                </li>
                                <li class="item pro" id="Project">
                                    <?php if(!empty($project)):?>
                                        <h4>
                                            <a class="t" class="" href="javascript:void(0)" onclick="modal_show3('designerGallery', <?=$project['type2'][0]['id']?>)"><?=__("PROJECT")?></a>
                                        </h4>
                                        <span class="line"></span>
                                    <?php endif;?>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

            <div class="product__graybackground m_white">

                <div class="contents__inner">
                    <?php
                    echo $this->element('productCardList',[
                        'data'=>$productList,
                        'type'=>'designer',
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div> <!-- contents -->
</section>

<script>

    $(document).ready(function(){

        $(function(e){
            $('.designer__gallery').find('.label').on('click', function(e){
                var boo = $(this).parent().hasClass('is-select');
                if ( boo ) {
                    $(this).parent().removeClass('is-select');
                    return;
                }
                $(this).parent().addClass('is-select');
            })
        })

        var html = '';
        var collectionArr = JSON.parse('<?=json_encode($collection)?>');
        var projectArr = JSON.parse('<?=json_encode($project)?>');

        if(collectionArr == '' && projectArr == ''){
            //컬렉션과 프로젝트 둘다 없을시
            $("#Gallery").css("display", 'none');

        }else if(collectionArr != '' && projectArr == ''){
            //콜렉션만 있을시
            $("#Project").css("display", 'none');
        }else if(projectArr != '' && collectionArr == ''){
            //프로젝트만 있을시
            $("#Collection").css("display", 'none');
        }
    });

</script>