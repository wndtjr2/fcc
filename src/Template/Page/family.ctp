<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip"><?=__("FCCTV 미디어 커머스 : 패밀리사이트")?></h2>

    <div class="contents">
        <!-- 페밀리 사이트. -->
        <div class="familysite">
            <!-- 커버스토리 -->
            <div class="family__cover">
                <div class="position">
                    <div class="js_h_middle">
                        <h3 class="title"><?=__("<span class='line1'>FASHION</span><span class='line1'>CROWD</span><span class='line1'>CHALLENGE</span>")?></h3>
                    </div>
                </div>
                <img src="/_res/img/family/family_cover_mobile.jpg" alt="" class="cover">
                <img src="/_res/img/family/family_cover_web.jpg" alt="" class="cover web">
            </div>
            <div class="family__headeline">
                <p class="first">
                    <?=__("Fashion Crowd Challenge는 인터넷을 통해 대중이 직접 디자이너를 평가하는 신개념 글로벌 패션 공모전입니다. 성장할 수 있는 기회를 얻지 못하고, <br>글로벌 시장으로 진출할 수 있는 능력이 없던 로컬 디자이너와 브랜드들이 직접 전세계 소비자를 만나 평가를 받을 수 있는 좋은 기회가 될 것입니다. <br>FCC 행사를 통해 전세계 유망한 디자이너와 로컬 브랜드는 새로운 기회를 얻게 될 것입니다.")?>
                </p>
                <p class="second">
                    <?=__("새로운 판매처를 소개받고, 더 나은 생산파트너를 만날 수 있으며, 브랜드가 더 성장할 수 있는 재정적, 기술적 지원도 받을 수 있습니다. 또한 평가와 <br>판매의 과정에서 획득한 고객 데이터는 전세계 각 도시의 패션트렌드를 읽을 수 있는 주요한 지표가 될 것입니다. 크라우드 테크놀로지와 빅데이터를 <br>활용하여 트렌드에 맞는 디자이너와 브랜드를 찾아내고,생산과 판매, 관리를 지원하는 종합 시스템을 제공하여 전세계의 디자이너와 소비자를 빠르게 <br>잇는 Fashion Crowd Challenge 온라인 플랫폼은 미래 패션산업의 새로운 가능성을 보여줄 수 있을 것으로 기대됩니다. 글로벌 패션 소싱 플랫폼이자, <br>디자인 성장 플랫폼이 될 Fashion Crowd Challenge에 동참해주십시오. 전세계를 향한 당신의 꿈이 지금 현실로 시작됩니다.")?>
                </p>
                <button type="button" class="more"><?=__("More")?></button>
            </div>

            <h4 class="siteTitle"><?=__("FAMILY SITE")?></h4>

            <ul class="sitelink is-zoom">
                <li class="item">
                    <a href="http://fashioncrowdchallenge.com/" class="link" target="_blank">
                        <div class="cover">
                            <img src="/_res/img/family/cover_fcc2016.jpg" alt="">
                        </div>
                        <p class="desc">
                            <strong class="fcc2016"><?=__("FASHION CROWD CHALLENGE 2016")?></strong>
                            <span><?=__("글로벌 온라인 패션 디자인 공모전 <br>Fashion Crowd Challenge가 2016년 새로운 <br>모습으로 여러분 곁으로 찾아갑니다.")?></span>
                        </p>
                    </a>
                </li>
                <li class="item">
                    <a href="http://fashioncrowdchallenge.com/designs/index" class="link" target="_blank">
                        <div class="cover">
                            <img src="/_res/img/family/cover_fcc2015.jpg" alt="">
                        </div>
                        <p class="desc">
                            <strong class="fcc2015"><?=__("FASHION CROWD CHALLENGE 2015")?></strong>
                            <span><?=__("Fashion Crowd Challenge 2015에서 <br>패션계를 이끌어갈 차세대 패션디자이너의 작품을 감상하세요.")?>
                            </span>
                        </p>
                    </a>
                </li>
                <li class="item">
                    <a href="http://fashioncrowdchallenge.com/mcc2015/result" class="link" target="_blank">
                        <div class="cover">
                            <img src="/_res/img/family/cover_model2015.jpg" alt="">
                        </div>
                        <p class="desc">
                            <strong class="model2015"><?=__("MODEL CROWD CHALLENGE 2015")?></strong>
                            <span><?=__("전세계 모델들이 참여한 Model CC에서 <br>글로벌 탑 모델을 만나보세요.")?></span>
                        </p>
                    </a>
                </li>
            </ul>
        </div>
    </div> <!-- contents -->
</section>
<script>
    $(function(){
        $('.family__headeline .more').on('click', function(e){
            $(this).hide();
            $('.family__headeline .second').fadeIn();
        })
    })
</script>