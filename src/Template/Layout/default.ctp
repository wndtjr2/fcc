<?php
$hotItemSelect = isset($hotItemSelect) ? $hotItemSelect : '';
$designersSelect = isset($designersSelect) ? $designersSelect : '';
$productSelect = isset($productSelect) ? $productSelect : '';
$tvSelect = isset($tvSelect) ? $tvSelect : '';
?>
<!DOCTYPE html>
<html lang="ko" <?=(isset($colorBlack))?"class='color__black'":""?>>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="author" content="fcctv.com">
    <meta name="application-name" content="FCC TV">
    <title>FCC TV</title>
    <link rel="shortcut icon" href="/_res/img/favicon/favicon.ico">
    <!-- facebook -->
    <?php if(isset($snsShare) && sizeof($snsShare) >0) { ?>
        <meta property="og:url" content="<?=$snsShare['url'] ?>"/>
        <meta property="og:title" content="<?=$snsShare['title']?>"/>
        <meta property="og:description" content="<?=$snsShare['desc']?>"/>
        <meta property="og:image" content="<?=$snsShare['image']?>"/>
        <meta property="og:site_name" content="<?=__("패션 미디어커머스 FCCTV")?>"/>
        <?php if(isset($snsShare['video']) && $snsShare['video']==true) { ?>
            <meta property="og:type" content="video">
            <meta property="og:video:url" content="<?=$snsShare['url'] ?>">
            <meta property="og:video:type" content="text/html">
            <meta property="og:video:width" content="1280">
            <meta property="og:video:height" content="720">
        <?php }else{ ?>
            <meta property="og:type" content="website"/>
        <?php }?>
        <!-- e:facebook -->
    <?php }else{
        $rootUrl = \Cake\Routing\Router::url("/",true);
        $textArray = array(
            '전문 크리에이터보다 전문적인 패션 크리에이터들이 온다. 곽현주, 황재근 그들이 말하는 첫 번째 패션 이야기',
            'FCC TV의 패션 컨텐츠엔 진짜 패션 디자이너가 있다.',
            '왠지 모르게 까칠할 것만 같았던 패션 디자이너. 그들의 진짜 모습을 확인한다.',
            '너도 나도 할 수 있다. 패션 디자이너들이 직접 판매하는 온라인 미디어 커머스',
            '듣지도 보지도 못한 패션 디자이너들이 직접 판매하는 온라인 미디어 커머스',
        );
        shuffle($textArray);

        $searchKeyword =array(
            "미디어커머스",
            "FCCTV",
            "FCC",
            "FashioncrowdChallenge",
            "에프씨씨티브이",
            "에프씨씨",
            "패션 미디어커머스",
            "패션",
            "CrowdChallenge",
            "드레스",
            "슈즈",
            "맨투맨",
            "가방",
            "블루종",
            "황재근",
            "곽현주",
            "장서희",
            "김민",
            "구연주",
            "최진우",
            "에프씨씨티비",
            "티비 에프씨씨",
            "디자이너",
            "패션 디자이너",
            "패션 크리에이터",
            "온라인 미디어커머스",
            "온라인 커머스",
            "패피",
            "패션피플",
        );
        ?>
        <meta name="description" content="<?=$textArray[0]?>">
        <meta name="keywords" content="<?=implode(",",$searchKeyword)?>">
        <meta property="og:url" content="<?=$rootUrl?>"/>
        <meta property="og:type" content="website"/>
        <meta property="og:title" content="<?=__("패션 미디어커머스 FCCTV")?>"/>
        <meta property="og:description" content='<?=$textArray[0]?>'/>
        <meta property="og:image" content="http://fashion.crowdchallenge.com/_res/img/sns/social.jpg"/>
        <meta property="og:site_name" content="<?=__("패션 미디어커머스 FCCTV")?>"/>
        <!--<meta property="fb:app_id" content="FCC"/>-->
    <?php } ?>
    <!-- 네이버 검색을 위한 인증 코드 -->
    <meta name="naver-site-verification" content="c997bb6f4a59772cc65cbf7c516ef2585a7b4b29"/>

    <link rel="stylesheet" type="text/css" href="/_res/css/style.css">
    <script src="/_res/lib/jquery-1.11.2.min.js"></script>
    <script>
        $.ajaxSetup({
            error: function (xhr, ajaxOptions, thrownError) {
                if(xhr.status==403) {
                    location.href='/auth/login?redirect=<?=$this->request->here?>';
                }
            }
        });
    </script>
    <!--[if lt IE 9]>
    <script src="/_res/lib/html5.js"></script>
    <script src="/_res/lib/respond.js"></script>
    <![endif]-->
    <script src="/js/front/modal.js"></script>
    <script src="/_res/lib/owl.carousel.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/_res/lib/owl.carousel.css">

</head>

<body class="open">

<!--언어선택-->
<script>
    $(function(){
        try{
            var x = getLang('lang');
            var lang = $(this).find('#mobile_lang option[value='+x+']').text();
        }catch(err){
            var lang = 'English'
        }
        $('#currentLangText').html(lang);
    });

    <?php
        /*===============  다국어  ================*/
            $langs = [  // 기본 순서
                //'ar'  => 'العربية',
                //'zh' => '中文',
                //'en' => 'English',
                //'fil'  => 'Filipino (Filipino)',
                //'fr' => 'Français',
                //'de'  => 'Deutsch (German)',
                //'ja' => '日本語',
                'ko' => '한국어',
    //'ru' => 'Русский',
    //'es' => 'Español',
    //'in' => 'Bahasa',
    //'pt' => 'Português'
    //'tr' => 'Türkçe (Turkish)',
    ];
//    $dropdownList = array_keys($langs);
//    $selectedLang = \Cake\I18n\I18n::locale();
//    $key = array_search($selectedLang, $dropdownList);
//    if ($key !== false) {   // 방문자 언어설정이 목록중에 있으면
//        unset($dropdownList[$key]);     // 목록의 처음으로
//        array_unshift($dropdownList, $selectedLang);
//    }
    ?>
    function getLang(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
        }
        return "";
    }

    function fnLanguageOptSelect(e) {
        fnLangAjax($(e.target).val());
    }

    /* 언어 변경 */
    function fnChangeLanguage(e) {
        e.preventDefault();
        var lang = $(e.target).attr('data-value');
        fnLangAjax(lang);
    }

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }

    function fnLangAjax(lang) {
        setCookie("lang", lang, 1);
        location.reload();
    }



</script>


<!--[if lt IE 10]>
<p id="underIE10"><?=__("This website requires Internet Explorer 10 or higher.")?></p>
<![endif]-->

<div id="wrap">
    <section id="section">
        <!-- ************************************** #header 영역 공통 사용, z-index : 500 -->
        <header id="header">
            <div class="header__bar">
                <!-- 로고 공통. -->
                <h1 class="logo"><a href="/">FCCTV</a></h1>
                <!-- 네비게이션, 웹에만 노출. -->
                <p class="skip_to_point"><a href="#skipPoint"><?=__("본문으로 바로 가기")?></a></p>
                <ul class="navigation">
                    <li class="item"><a href="/tv/" class="link <?=$tvSelect?>"><?=__('FCC TV')?></a></li>
                    <li class="item"><a href="/hot/" class="link <?=$hotItemSelect?>"><?=__("HOT ITEM")?></a></li>
                    <li class="item"><a href="/designers/" class="link <?=$designersSelect?>"><?=__('DESIGNER')?></a></li>
                    <li class="item"><a href="/product/" class="link <?=$productSelect?>"><?=__('PRODUCT')?></a></li>

                </ul>
                    <!-- 사용자 메뉴 : 우측, 웹 전용. -->
                    <div class="header__users is-zoom">
                        <?php $auth = $this->request->session()->read('Auth.User'); ?>
                        <a href="/contact/" class="header__users--menu"><?=__("Q&amp;A")?> <span class="leftline"></span></a>
                        <a href="/cart/" class="header__users--mycart"><?=__('My Cart')?> <?php if(isset($auth) && isset($auth['id'])){?><span class="cart__counter cartCnt"><?=$cartCnt?></span><?php } ?></a>

                        <div class="header__users--account" onmouseover="js.drops_navigation(event, this);" onmouseleave="js.drops_navigation(event, this);">
                            <button type="button" class="header__account--btn" onclick="js.key_mymenu(event, this)">
                                <?php if(isset($auth) && isset($auth['id'])){?>
                                    <span class="icon"></span>
                                    <span class="nick"><?=__('My Page')?></span>
                                <?php }else{?>
                                    <span class="icon"></span>
                                    <span class="login"><?=__('Log in')?></span>
                                <?php }?>
                                <span class="rightline"></span>
                            </button>

                            <div class="header__account--menu">
                                <?php if(isset($auth)  && isset($auth['id'])){?>
                                    <div class="mobile__rnb--sign">
                                        <a href="/users/edit" class="mobile__rnb--label2">
                                            <!-- 사용자 등록 사진이 없을 경우. 아래 span 에서 style='background-image' 속성 제거. -->
                                            <span class="photo" <?php if($auth['image_path']!=''){?>style='background-image: url("<?=FILE_URI.$auth['image_path']?>-crop")'<?php }?>>
                                                <span class="frame"></span>
                                            </span>
                                            <span><?=__('Hello,')?></span><br>
                                            <?= $auth['nickname']?>
                                        </a>
                                        <ul class="menus is-zoom">
                                            <!-- <li class="menu"><a href="/my/" class="link">My Challenge</a></li> -->
                                            <li class="menu"><a href="/users/edit" class="link"><?=__("Edit Profile")?></a></li>
                                            <li class="menu"><a href="/myorder/" class="link"><?=__('Orders')?></a></li>
                                            <li class="menu"><a href="/address/index" class="link"><?=__('Shipping Addresses')?></a></li>
                                            <li class="menu"><a href="/users/account" class="link"><?=__('Account')?></a></li>
                                            <li class="menu">
                                                <a href="/auth/logout" class="buttons create">
                                                    <span class="text"><?=__('Sign Out')?></span>
                                                    <span class="txt"><?=__('Sign Out')?></span>
                                                    <span class="bg"></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                <?php }else{?>
                                <div class="mobile__rnb--sign">
                                    <div class="mobile__rnb--label">
                                        <?=__('WELCOME')?>
                                    </div>
                                    <ul class="menus is-zoom">
                                        <li class="menu">
                                            <span class="title"><?=__('Are you a member?')?></span>
                                            <a href="/auth/login" class="buttons sign">
                                                <span class="text"><?=__('Sign In')?></span>
                                                <span class="txt"><?=__('Sign In')?></span>
                                                <span class="bg"></span>
                                            </a>
                                        </li>
                                        <li class="menu">
                                            <span class="title"><?=__('Not a member yet?')?></span>
                                            <a href="/auth/join/" class="buttons create">
                                                <span class="text"><?=__('Create Account')?></span>
                                                <span class="txt"><?=__('Create Account')?></span>
                                                <span class="bg"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                <!-- 햄버거메뉴 : 좌측 서브페이지, 공통. -->
                    <button type="button" class="header__btn--left" name="lnb" onclick="js.menu(this, event);">LNB opener</button>

                    <div id="header__navigationBar">
                        <div class="menus is-zoom">
                            <?php if(isset($auth)){?>
                                <div class="menus__login--after is-zoom">
                                    <a href="/users/edit" class="user">
                                        <?php if($auth['image_path']!=''){?>
                                            <span class="photo" style="background-image: url('<?=FILE_URI.$auth['image_path']?>-crop');"></span>
                                        <?php }else{?>

                                        <?php }?>

                                        <p class="name"><?=__('Hello,')?><br><?= $auth['nickname']?></p>
                                    </a>
                                    <div class="cart">
                                        <a class="link" href="/cart/"><?=__('My Cart')?> <span class="cart__counter cartCnt"><?=$cartCnt?></span></a>
                                    </div>
                                    <ul class="sub">
                                        <li class="item"><a class="link" href="/users/edit"><?=__("Edit Profile")?></a></li>
                                        <li class="item"><a class="link" href="/myorder/"><?=__('Orders')?></a></li>
                                        <li class="item"><a class="link" href="/address/"><?=__('Shipping Addresses')?></a></li>
                                        <li class="item"><a class="link" href="/users/account/"><?=__('Account')?></a></li>
                                    </ul>
                                </div>
                            <?php }else{?>

                                <div class="menus__login is-zoom">
                                    <a href="/auth/login" class="login"><?=__('Sign In')?></a>
                                    <a href="/auth/join" class="create"><?=__('Create Account')?></a>
                                </div>

                            <?php }?>
                            <div class="menus__navigations">
                                <p class="heading is-skip">MENUS</p>
                                <ul>
                                    <li class="item"><a href="/tv/" class="link"><?=__("FCC TV")?></a></li>
                                    <li class="item"><a href="/hot/" class="link"><?=__("HOT ITEM")?></a></li>
                                    <li class="item"><a href="/designers/" class="link"><?=__("DESIGNER")?></a></li>
                                    <li class="item"><a href="/product/" class="link"><?=__("PRODUCT")?></a></li>

                                </ul>
                            </div>

                            <div class="menus__subpages">
                                <p class="heading is-skip">SUBMENUS</p>
                                <ul>

                                    <li class="item"><a href="/about/" class="link"><?=__("ABOUT FCC TV")?></a></li>
                                    <li class="item"><a href="/contact/" class="link"><?=__("Q&amp;A")?></a></li>
                                    <li class="item"><a href="/terms/" class="link"><?=__("Privacy Statement")?></a></li>
                                    <li class="item"><a href="/policy/" class="link"><?=__("TERMS AND CONDITIONS")?></a></li>
                                    <li class="item"><a href="http://story.crowdchallenge.com/" class="link" target="_blank"><?=__("STORY")?></a></li>
                                    <li class="item"><a href="/family/" class="link"><?=__("FAMILY SITE")?></a></li>

                                </ul>
                            </div>
                             <?php if(isset($auth)){?>
                            <div class="menus__logout">
                                <a href="/auth/logout"><?=__("Log Out")?></a>
                            </div>
                            <?php }?>

                        </div>
                        <button type="button" class="header__btn--left" name="lnb" onclick="js.menu(this, event);">LNB closer</button>
                    </div>

                    <!-- 검색, 공통. -->
                    <div class="searchbar">
                        <button type="button" class="search__onoff" onclick="show_header_search();"><?=__("Search")?></button>
                        <div class="search__input">
                            <input id="searchBox" type="text" class="search" placeholder="Search" value="<?=isset($keyword) ?$keyword:''?>" onkeydown="searchEnter(event)" maxlength="20">
                            <a href="#"  onclick="searchKeyword();" class="btn"><?=__("Search")?></a>
                        </div>
                    </div>
                <!-- 검정바, 공통 -->
                    <span id="mobile__black"><span class="black"></span></span>
                </div>
            <script>
                function show_header_search() {
                    if ( $('.searchbar').hasClass('showsearch') ) {
                        return;
                    }
                    $('.searchbar').addClass('showsearch');
                }
                function select_on_change(location, url, name) {

                }
                function searchEnter(event){
                    if (event.keyCode == 13)
                        searchKeyword();
                }
                function searchKeyword(){
                    var searchClass = ".searchbar .search";
                    var key = $(searchClass).val();
                    var pattern = /^[\w\s가-힣ㄱ-ㅎㅏ-ㅣ]+$/;
                    var element = document.getElementById("searchBox");
                    if(key.length < 2){
                        //2자 이하 얼럿
                        //alert("검색 글자수는 2자 이상이어야 합니다.");
                        element.value = '';
                        if(element.className == "search"){
                            element.className += " " + "warning";
                        }
                        element.placeholder = "<?=__("검색어는 2자 이상입니다.")?>";
                        return;
                    }
                    else if(key.length > 20){
                        //20자 이상 얼럿
                        //alert("검색 글자수는 20자를 넘을 수 없습니다.");
                        element.value = '';
                        if(element.className == "search"){
                            element.className += " " + "warning";
                        }
                        element.placeholder = "<?=__("검색어는 20자를 넘을 수 없습니다.")?>";
                        return;
                    }
                    else if(!key.match(pattern)){
                        //alert("검색시 특수문자를 입력할 수 없습니다.");
                        element.value = '';
                        if(element.className == "search"){
                            element.className += " " + "warning";
                        }
                        element.placeholder = "<?=__("지원하지 않는 검색어입니다.")?>";
                        return;
                    }
                    window.location.href = '/fccTv/search?keyword='+key;
                }

            </script>
            <!-- 플레이스홀더 스타일 적용 -->
            <style>
                .search.warning::-webkit-input-placeholder { /* WebKit, Blink, Edge */
                    color: red;
                }
                .search.warning:-ms-input-placeholder { /* Mozilla Firefox 4 to 18 */
                    color: red;
                    opacity:  1;
                }
                .search.warning:-moz-placeholder { /* Mozilla Firefox 19+ */
                    color: red;
                    opacity:  1;
                }
                .search.warning::-moz-placeholder { /* Internet Explorer 10-11 */
                    color: red;
                }
            </style>
            </header>


<!-- ************************************** 페이지별 컨텐츠 (가변영역) **************************************-->
        <?= $this->fetch('content') ?>

<!-- ************************************** 페이지별 컨텐츠 (가변영역) **************************************-->



<!-- ******************************************** #footer 영역 공통 사용, z-index : 500 -->
<footer id="footer">
    <div class="position is-zoom">
        <div class="f1">
            <h5 class="footer"><a href="/">FCCTV</a></h5>
            <p class="warningtxt"><?=__("FCC 에 제출 된 모든 디자인은 저작권법의 보호를 받고 있습니다. 무단 도용 및 복제를 금합니다.")?></p>
        </div>
        <nav>
            <div class="f2">
                <h6 class="is-skip">FCCTV 전체 메뉴</h6>
                <ul class="pages is-zoom">
                    <li class="page p1"><a href="/about/"><?=__("ABOUT FCC TV")?></a><span class="line"></span></li>
                    <li class="page p2"><a href="/contact/"><?=__("Contact Us")?></a><span class="line"></span></li>
                    <li class="page p3"><a href="/policy/"><?=__("TERMS AND CONDITIONS")?></a></li>
                    <li class="page p4"><a href="/terms/"><?=__("Privacy Statement")?></a><span class="line"></span></li>
                    <li class="page p5"><a href="http://story.crowdchallenge.com/" target="_blank"><?=__("STORY")?></a><span class="line"></span></li>
                    <li class="page p6"><a href="/family/"><?=__("FAMILY SITE")?></a></li>

                </ul>
            </div>
            <hr class="is-skip">
            <div class="f3">
                <h6 class="">SOCIAL MEDIA</h6>
                <div class="socials">
                    <ul class="socials__list is-zoom">
                        <li class="sns f">
                            <a href="http://www.facebook.com/fcctvhq" target="_blank" class="link"><span class="line"></span><span class="num"><?=$fbCounter?></span><span class="name">Facebook</span><span class="line"></span><span class="cate">Like</span></a>
                        </li>
<!--                        <li class="sns i">-->
<!--                            <a href="#" class="link"><span class="line"></span><span class="num">999999</span><span class="name">Instargram</span><span class="line"></span><span class="cate">Follow</span></a>-->
<!--                        </li>-->
                        <li class="sns y">
                            <a href="https://www.youtube.com/channel/UCzZbPlbim0tlbMvSD0zO_5g" target="_blank" class="link"><span class="line"></span><span class="num"><?=$youtubeCounter?></span><span class="name">Youtube</span><span class="line"></span><span class="cate">Subscribe</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <div class="co__info">
        <div class="position">
            <cite>
                <span>&gt; 법인명(상호) : (주)에프씨씨티브이</span><br class="only__mobile">
                <span>&gt; 사업자등록번호 : 587-87-00288</span><br class="only__mobile">
                <span>&gt; 통신판매업신고 : 제 2016- 서울 강남 -01161 호</span><br>
                <span>&gt; 대표자 (성명) : 이승민</span>
                <span>&gt; 개인정보책임자 (성명) : 함세희</span>
            </cite>
            <address>
                <span>&gt; 전화 : 070-5015-6383</span>
                <span>&gt; E-MAIL : <a href="mailTo:help@fcctvhq.com">help@fcctvhq.com</a></span><br class="only__mobile">
                <span>&gt; 주소 : 서울특별시 강남구 학동로 331, 3층 1호 </span><br class="only__mobile">
                <span>&gt; 저작권 © FCC TV (주)</span>
            </address>
        </div>
    </div>

    <div class="banners">
        <div class="position">
            <a href="http://www.hanguomianshui.com/" class="banner" target="_blank"><img src="/_res/img/banner/arca_dfs.jpg" alt="ARCA DFS" class="arca"></a>
            <span class="line"></span>
            <a href="http://www.lotte.com/main/viewMain.lotte?dpml_no=1" class="banner" target="_blank"><img src="/_res/img/banner/lotte.jpg" alt="롯데닷컴" class="lotte"></a>
        </div>
    </div>
</footer>

</section>
<a class="gotoTop" href="#top">TOP</a>

<!-- ******************************************** 페이지별 모달 레이어 (가변영역), z-index : 1000 -->
<aside id="modal">
    <div class="modal__board"><div class="modal__contents"></div></div>
    <div class="modal__bg"></div>
</aside>

</div>
<!-- id="wrap" -->

<!-- ******************************************** 페이용 스크립트 공통, 혹은 가변 영역 -->
<script src="/_res/js/script.js"></script>

<script>


    function cartCnt(){
        $.getJSON('/cart/cartCnt',function(data){
            if(data.cartCnt > 0){
                $('.cartCnt').html(data.cartCnt);
                $(".cartCnt").show();
            }else{
                $(".cartCnt").hide();
            }
        });
    }
    function setCookie(name, value, expiredays) {
        var today = new Date();
        today.setDate(today.getDate() + expiredays);
        document.cookie = name + '=' + value + '; path=/; expires=' + today.toGMTString() + ';'
    }

    function getCookie(name){
        var wcname = name + '=';
        var wcstart, wcend, end;
        var i = 0;
        while(i <= document.cookie.length) {
            wcstart = i;
            wcend   = (i + wcname.length);
            if(document.cookie.substring(wcstart, wcend) == wcname) {
                if((end = document.cookie.indexOf(';', wcend)) == -1)
                    end = document.cookie.length;
                return document.cookie.substring(wcend, end);
            }
            i = document.cookie.indexOf('', i) + 1;
            if(i == 0)
                break;
        }
        return '';
    }

</script>

<script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>
<script>
    function searchAddress(){
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullAddr = ''; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    fullAddr = data.roadAddress;

                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    fullAddr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
                if(data.userSelectedType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('zipcode').value = data.zonecode; //5자리 새우편번호 사용
                document.getElementById('address').value = fullAddr;

                // 커서를 상세주소 필드로 이동한다.
                document.getElementById('address2').focus();
            }
        }).open();
    }
</script>
<!-- ******************************************** 페이용 스크립트 공통, 혹은 가변 영역 -->
<!-- Google Analytics -->
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-75732921-1', 'auto');
    ga('send', 'pageview');

</script>
<!-- Google Analytics -->

<span id="scrollTop"></span>

</body>
</html>



