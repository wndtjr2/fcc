<?php
$productCnt=count($productList['product']);
?>
<!-- ************************************** 페이지별 컨텐츠 (가변영역) **************************************-->
<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip">FCCTV 미디어 커머스 : 검색결과보기</h2>
    <div class="contents">
        <!-- 검색 결과 -->
        <div class="search__result">
            <h3 class="section__title"><span class="inner">검색 결과</span></h3>

            <div class="product__graybackground m_white">
                <div class="contents__inner mg20">
                    <?php
                    if($productCnt>0) {
                        ?>
                        <!-- 상품 리스트: 검색결과 존재 -->
                        <div class="main__products">
                            <p class="search__result--noti">검색결과 <span class="red">총 <?=$productList['page']['count']?>개</span> 상품이 검색되었습니다.</p>
                            <?php
                            echo $this->element('productCardList',[
                                'data'=>$productList,
                                'type'=>'search'
                            ]);
                            ?>
                        </div>
                    <?php
                    }else{
                        ?>
                        <!-- 상품 리스트: 검색결과 없음 -->
                        <div class="search__result--empty">
                            <span class="icon"></span>
                            <p>검색 결과가 없습니다.</p>
                        </div>
                        <!-- 상품 리스트: 검색결과 없음 -->
                    <?php }?>

                </div>

            </div>
        </div> <!-- contents -->
    </div>
</section>

<!-- ************************************** 페이지별 컨텐츠 (가변영역) **************************************-->
