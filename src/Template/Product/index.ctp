 <section id="sections">
     <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
     <h2 class="is-skip"><?=__("FCCTV 미디어 커머스 : 상품 리스트")?></h2>

    <div class="contents">
        <div class="">
            <!-- 카테고리, 광고영역 -->
            <div class="product__covers is-zoom">
                <div class="advertisement">
                    <div class="position">
                        <div class="js_h_middle">
                            <div class="box <?php echo (in_array($categoryName,['LIFESTYLE','CLOTHES','BAG&ACC']))?"sub":""?>">
                                <?php if($categoryName=="LIFESTYLE"){ ?>
                                <h3 class="title"><span class="line1"><?=__("LIFESTYLE")?></span></h3>
                                <?php }else if($categoryName=="CLOTHES"){ ?>
                                <h3 class="title"><span class="line1"><?=__("CLOTHES")?></span></h3>
                                <?php }else if($categoryName=="BAG&ACC"){ ?>
                                <h3 class="title"><?=__("<span class='line1'>LEATHER GOODS</span><span class='line1'>&amp;ACC</span>")?></h3>
                                <?php }else{ ?>
                                <h3 class="title"><?=__("<span class='line1'>MEET YOUR</span><span class='line2'>PRODUCT</span>")?></h3>
                                <p class="headeline">
                                <span class="line"></span>
                                <?=__("FCC TV 디자이너의 스토리가 담긴 아이템을 소개합니다. <br class='only__web'>당신이 몰랐던 디자이너의 이야기는 어땠나요, 그들의 제품이 <br class='only__web'>궁금하지 않으세요? 이제 디자이너의 스토리가 담긴 제품을 <br class='only__web'>직접 확인하세요.")?>
                                </p>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                    <?php if($categoryName=="LIFESTYLE"){ ?>
                    <img src="/_res/img/product_cover/product_cover_life_mobile.jpg" alt="" class="cover mobile">
                    <img src="/_res/img/product_cover/product_cover_life_web.jpg" alt="" class="cover web">
                    <?php }else if($categoryName=="CLOTHES"){ ?>
                    <img src="/_res/img/product_cover/product_cover_clothes_mobile.jpg" alt="" class="cover mobile">
                    <img src="/_res/img/product_cover/product_cover_clothes_web.jpg" alt="" class="cover web">
                    <?php }else if($categoryName=="BAG&ACC"){ ?>
                    <img src="/_res/img/product_cover/product_cover_acc_mobile.jpg" alt="" class="cover mobile">
                    <img src="/_res/img/product_cover/product_cover_acc_web.jpg" alt="" class="cover web">
                    <?php }else{ ?>
                    <img src="/_res/img/product_cover/product_cover_default_mobile.jpg" alt="" class="cover mobile">
                    <img src="/_res/img/product_cover/product_cover_default_web.jpg" alt="" class="cover web">
                    <?php }?>
                </div>
                <div class="category">
                    <ul class="category__menu">

                        <li class="item">
                            <a href="/product/" class="link<?= is_null($categoryId)?" is-select":"";?>" id="all"><span><?=__("ALL PRODUCT")?></span></a>
                        </li>

                        <?php
                        $i = 0;
                        $isSelect = '';
                        $selectName = 'PRODUCT';

                        foreach($category as $ctg){
                            $isSelect = '';
                            if($ctg['id']==$categoryId){
                                $isSelect = 'is-select';
                                $selectName = $ctg['name'];
                            }else{
                                foreach($ctg['sub'] as $sub){
                                    if($sub['id']==$categoryId){
                                        $isSelect = 'is-select';
                                    }
                                }

                            }


                            ?>
                        <li class="item">
                            <a href="/product/?categoryId=<?=$ctg['id'];?>" class="link <?=$isSelect?>"><span><?=strtoupper($ctg['name'])?></span></a>
                            <ul class="category__submenu">
                            <?php foreach($ctg['sub'] as $sub){
                                if($sub['id']==$categoryId){
                                    $isSelect = 'is-select';
                                    $selectName = $sub['name'];
                                }else{
                                    $isSelect = '';
                                }
                                if($sub['name']!='Men' && $sub['name']!='Interior'){//임시로 men,Interior 카테고리 노출안
                                ?>
                                <li class="subs">
                                    <a href="/product/?categoryId=<?=$sub['id'];?>" class="sub <?=$isSelect?>"><?=$sub['name'];?><span class="dash"></span></a>
                                </li>
                            <?php }
                            }?>
                            </ul>
                            <?php $i++?>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <div class="product__graybackground m_white">
                <div class="contents__inner">
                    <!-- 상품 리스트 -->
                    <div class="main__products">
                        <div class="product__category">
                            <select name="select__category" class="selectbox" id="lb_category" onchange="select_on_option(this);">
                                <option value=""><?=__("카테고리 선택")?></option>
<?php
$labelBox="ALL PRODUCT";
foreach($category as $ctg){
    if($ctg['id']==$categoryId){
        $selected = 'selected';
        $labelBox = strtoupper($ctg['name']);
    }else{
        $selected = '';
    }
    echo '<option value="categoryId='.$ctg['id'].'" '.$selected.'>'.strtoupper($ctg['name']).'</option>';
    foreach($ctg['sub'] as $sub){
        if($sub['id']==$categoryId){
            $selected = 'selected';
            $labelBox = strtoupper($ctg['name']).' > '.strtoupper($sub['name']);
        }else{
            $selected = '';
        }
        echo '<option value="categoryId='.$sub['id'].'" '.$selected.'>'.strtoupper($ctg['name']).' > '.strtoupper($sub['name']).'</option>';
    }
}
?>
                            </select>
                            <label for="lb_category" class="labelbox"><?=__($labelBox)?></label>
                        </div>
                        <?php
                        echo $this->element('productCardList',[
                            'data'=>$productList,
                            'type'=>'product',
                        ]);
                        ?>
                    </div> <!-- main__products -->
                </div>
            </div>





        </div>
    </div> <!-- contents -->
</section>

<script>

    function select_on_option(obj) {
        location.href='/product/?'+$(obj).val();
    }

    function showDiv(category) {
        var selector = "#" + category;
        var ct1 = $(selector).prop('style').display;
        if(ct1 == 'none'){
            $(selector).prop('style').display = 'block';
        }else{
            $(selector).prop('style').display = 'none';
        }
    }
</script>