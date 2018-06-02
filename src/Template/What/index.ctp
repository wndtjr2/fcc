<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
<h2 class="is-skip">FCCTV 미디어 커머스 : HOT 상품 리스트</h2>

    <div class="contents">
        <div class="">
            <div class="product__graybackground m_white">

                <?php
                echo $this->element('pageCover20160531',[
                    'coverType'=>'hotitem',
                ]);
                ?>

                <div class="contents__inner">
                <!-- 상품 리스트 -->

                    <?php
                    echo $this->element('productCardList',[
                        'data'=>$productList,
                        'type'=>'hot',
                    ]);
                    ?>


                </div>
            </div>

        </div>
    </div> <!-- contents -->
</section>

