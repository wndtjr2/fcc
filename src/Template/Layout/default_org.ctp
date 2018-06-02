<!DOCTYPE html>
<html lang="en" <?=(isset($colorBlack))?"class='color__black'":""?>>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="author" content="crowdchallenge.com">
    <meta name="application-name" content="FCC 2016">


    <title>Fcc TV</title>

    <link rel="shortcut icon" href="/_res/img/favicon/favicon.ico">
    <!-- facebook -->
    <?php if(isset($snsShare) && sizeof($snsShare) >0) { ?>
        <meta property="og:url" content="<?=$snsShare['url'] ?>"/>
        <meta property="og:title" content="<?=$snsShare['title']?>"/>
        <meta property="og:description" content="<?=$snsShare['desc']?>"/>
        <meta property="og:image" content="<?=$snsShare['image']?>"/>
        <meta property="og:site_name" content="미디어커머스 FCC TV"/>
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
            '"너도 나도 할 수 있다." 패션 디자이너들이 직접 판매하는 온라인 미디어 커머스',
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
        <meta property="og:title" content="패션 미디어커머스 FCCTV"/>
        <meta property="og:description" content='<?=$textArray[0]?>'/>
        <meta property="og:image" content="http://fashion.crowdchallenge.com/_res/img/sns/social.jpg"/>
        <meta property="og:site_name" content="패션 미디어커머스 FCCTV"/>
        <!--<meta property="fb:app_id" content="FCC"/>-->
    <?php } ?>
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
    <script src="/_res/lib/jquery.mobile-events.min.js"></script>

    <!--[if lt IE 9]>
    <script src="/_res/lib/html5.js"></script>
    <script src="/_res/lib/respond.js"></script>
    <![endif]-->
    <script src="/js/front/modal.js"></script>

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
$dropdownList = array_keys($langs);
$selectedLang = \Cake\I18n\I18n::locale();
$key = array_search($selectedLang, $dropdownList);
if ($key !== false) {   // 방문자 언어설정이 목록중에 있으면
    unset($dropdownList[$key]);     // 목록의 처음으로
    array_unshift($dropdownList, $selectedLang);
}
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
    <?php if(isset($mobileHead) && $mobileHead==true){ ?>
    <div class="mobile__navigation is-zoom">
        <a href="/" class="mobile__navigation--leftbtn">뒤로</a>
        <p  class="title"><strong><?=$videoCategory[$videoWithProduct['video']->code]?></strong> <?=$videoWithProduct['video']->title?></p>
        <div class="cart">
            <button type="button" class="mobile__navigation--cart" name="rnb"  onclick="modal.show('../cart')">카트 <span class="noti cartCnt" style="display: none;">0</span></button>
        </div>
    </div>
    <?php } ?>
    <section id="section">

        <!-- ************************************** #header 영역 공통 사용, z-index : 500 -->
        <header id="header">
            <div class="header__wrap">
                <div class="position">
                    <h1 class="header__logo">
                        <a href="/" class="link">FCC <span>TV</span></a>
                    </h1>

                    <div class="only__web">
                        <nav>
                            <ul class="header__navigation is-zoom">
                                <li class="item"><a href="/" class="link <?=(isset($isTvMenu) && $isTvMenu==true)?"is-select":""?>">TV</a></li>
                               <li class="item header__challenge" onmouseover="js.drops_navigation(event,this);" onmouseleave="js.drops_navigation(event,this);">

                                    <a href="http://fashioncrowdchallenge.com/" target="_blank" class="link challenge__btn"><?=__('CHALLENGE')?></a>
                                    <ul class="challenge__menu">
                                        <li class="list"><a href="http://fashioncrowdchallenge.com/" target="_blank" class="sublink">FCC 2016</a></li>
                                        <li class="list"><a href="http://fashioncrowdchallenge.com/fcc2015/" target="_blank" class="sublink">FCC 2015</a></li>
                                        <li class="list"><a href="http://fashioncrowdchallenge.com/modelcc2015/" target="_blank" class="sublink">ModelCC 2015</a></li>
                                    </ul>
                                </li>
                                <li class="item"><a href="http://story.crowdchallenge.com/" class="link" target="_blank"><?=__('STORY')?></a></li>
<!--                                <li class="item"><a href="/faq" class="link">FAQs</a></li>-->
                            </ul>
                        </nav>

                        <div class="header__links is-zoom" onmouseover="js.drops_navigation(event,this);" onmouseleave="js.drops_navigation(event,this);">

                            <button type="button" class="header__links--btn">메뉴</button>
                            <ul class="header__menus">
<!--                                <li class="item"><a href="/about" class="link">--><?//=__('ABOUT')?><!--</a></li>-->
<!--                                <li class="item"><a href="/press" class="link">--><?//=__('PRESS')?><!--</a></li>-->
                                <li class="item"><a href="/contact" class="link"><?=__('Contact Us')?></a></li>
                                <li class="item"><a href="/terms" class="link"><?=__('TERMS AND CONDITIONS')?></a></li>
<!--                                <li class="item"><a href="/policy" class="link">--><?//=__('PRIVACY POLICY')?><!--</a></li>-->
                            </ul>
                        </div>

                        <div class="header__users is-zoom">
                            <?php $auth = $this->request->session()->read('Auth.User')?>

                            <button type="button" class="header__users--cart" onclick="modal.show('../cart')">카트 <span class="noti cartCnt" style="display: none;">0</span></button>
                            <div class="header__users--account" onmouseover="js.drops_navigation(event,this);" onmouseleave="js.drops_navigation(event,this);">
                                <button type="button" class="header__account--btn">
                                    <?php if(isset($auth)){?>
                                        <?= $auth['nickname']?>
                                        <!-- 사용자 등록 사진이 없을 경우. 아래 span 자체를 사용하지 않음. -->
                                        <?php if($auth['image_path']!=''){?>
                                            <span class="users" name="rnb" style="background-image: url('<?=FILE_URI.$auth['image_path']?>');"></span>
                                        <?php }?>
                                    <?php }?>
                                    <span class="frame"></span>
                                </button>

                                <div class="header__account--menu">
                                    <?php if(isset($auth)){?>
                                        <div class="mobile__rnb--sign">
                                            <a href="/users/edit" class="mobile__rnb--label2">
                                                <!-- 사용자 등록 사진이 없을 경우. 아래 span 에서 style='background-image' 속성 제거. -->
                                        <span class="photo"
                                            <?php if($auth['image_path']!=''){?>
                                                style='background-image: url("<?=FILE_URI.$auth['image_path']?>")'
                                            <?php }?>
                                            ><span class="frame"></span></span>

                                                <span><?=__('Hello,')?></span><br>
                                                <?= $auth['nickname']?>
                                            </a>

                                            <ul class="menus is-zoom">
                                                <!--<li class="menu"><a href="/my/" class="link">My Challenge</a></li>-->
                                                <li class="menu"><a href="/myorder/" class="link"><?=__('Orders')?></a></li>
                                                <li class="menu" style="border-bottom: none;">
                                                    <span class="link"><?=__('Settings')?></span>
                                                    <a href="/address/index" class="sublink"><?=__('Shipping Addresses')?></a>
                                                    <a href="/users/account" class="sublink"><?=__('Account')?></a>
                                                </li>
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
                                                    <a href="/auth/join" class="buttons create">
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
                    </div>

                </div>
                <span class="bottom__line"></span>
            </div>

        </header>

        <!-- ************************************** 페이지별 컨텐츠 (가변영역) **************************************-->

        <!-- 필요시에만 라이브러리 로팅.
        <script src="/_res/lib/jquery.easing.1.3.min.js"></script>
        <script src="/_res/lib/mousewheel.js"></script>
        <script src="/_res/lib/jquery.mobile-events.min.js"></script>
        <link rel="stylesheet" type="text/css" href="/_res/lib/colorbox.css">
        <script src="/_res/lib/jquery.colorbox-min.js"></script>
        <link rel="stylesheet" type="text/css" href="/_res/owl.carousel.css">
        <script src="/_res/lib/owl.carousel.min.js"></script>
        -->
        <?= $this->fetch('content') ?>

        <!-- ************************************** 페이지별 컨텐츠 (가변영역) **************************************-->

        <!-- ******************************************** #footer 영역 공통 사용, z-index : 500 -->
        <footer id="footer">
            <div class="footer__wrap is-zoom">
                <div class="footer__first">
                    <a href="/" class="footer__head"><strong><?=__('FCC TV')?></strong></a>
                    <div class="footer__head"><strong><?=__('CHALLENGE')?></strong>
                        <div>
                            <a href="http://fashioncrowdchallenge.com/" class="footer__head--sub" target="_blank">FCC 2016</a>
                            <a href="http://fashioncrowdchallenge.com/designs/index" class="footer__head--sub">FCC 2015</a>
                            <a href="http://fashioncrowdchallenge.com/mcc2015/result" class="footer__head--sub">ModelCC 2015</a>
                        </div>
                    </div>
                    <!-- <span class="footer__head"><strong>SHOWROOM</strong></span> -->
                    <a href="http://story.crowdchallenge.com/" target="_blank" class="footer__head"><strong><?=__('STORY')?></strong></a>
<!--                    <a href="/faq/" class="footer__head"><strong>--><?//=__('FAQs')?><!--</strong></a>-->
                </div>
                <div class="footer__second">
<!--                    <a href="/about" class="footer__head"><strong>--><?//=__('ABOUT')?><!--</strong></a>-->
<!--                    <a href="/press" class="footer__head"><strong>--><?//=__('PRESS')?><!--</strong></a>-->
                    <a href="/contact" class="footer__head"><strong><?=__('Contact Us')?></strong></a>
<!--                    <a href="/policy" class="footer__head"><strong>--><?//=__('PRIVACY POLICY')?><!--</strong></a>-->
                    <a href="/terms" class="footer__head"><strong><?=__('TERMS AND CONDITIONS')?></strong></a>
                </div>
                <div class="footer__third">
<!--                    <div class="footer__head"><strong>--><?//=__('OUR SERVICES')?><!--</strong>-->
<!--                        <a href="http://market.crowdchallenge.com/" target="_blank" class="footer__head--sub">--><?//=__('Market Crowd Challenge')?><!--</a>-->
<!--                        <a href="http://music.crowdchallenge.com/" target="_blank" class="footer__head--sub">--><?//=__('Music Crowd Challenge')?><!--</a>-->
<!--                    </div>-->
                    <div class="footer__head is-zoom"><strong><?=__('SOCIAL MEDIA')?></strong>
                        <div class="footer__head--snss">
                            <a href="https://www.facebook.com/crowdchallenges" class="footer__head--sns"><?=__('Facebook')?><span class="icon f"></span></a>
                            <a href="https://twitter.com/crowdchallenge" class="footer__head--sns"><?=__('Twitter')?><span class="icon t"></span></a>
                            <a href="http://www.weibo.com/CrowdChallenge?is_hot=1" class="footer__head--sns"><?=__('Weibo')?><span class="icon w"></span></a>
                            <a href="https://vimeo.com/crowdchallenge" class="footer__head--sns"><?=__('Vimeo')?><span class="icon v"></span></a>
<!--                            <a href="#" class="footer__head--sns">--><?//=__('VK')?><!--<span class="icon k"></span></a>-->
                        </div>
                    </div>
                </div>
                <div class="footer__fourth">
                    <div class="footer__head"><strong class="cc__logo"><?=__('FCC TV')?></strong>
                        <p class="footer__text"><?=__('All the designs submitted to FCC are protected under copyright law; any unauthorized use or copying of the material may result in legal action.')?></p>
                    </div>
                    <div class="footer__head"><strong><?=__('LANGUAGE')?></strong>
                        <div class="footer__language"> <!-- .is-mobile : 모바일 화면일 경우 js 로 해당 클래스 오버라이드-->
                            <div class="footer__language--wrap">
                                <select name="language" onchange="js.select_on_change(event,this);">
                                    <?php
                                    for ($i=1; $i < count($langs); $i++) :?>
                                        <option data-value="<?=$dropdownList[$i]?>" onclick="fnChangeLanguage(event)"><?=$langs[$dropdownList[$i]]?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="footer__language--web" onmouseover="js.drops_lang(event,this);">
                                <button type="button"><?=$langs[$dropdownList[0]]?><span class="arr"></span></button>
                                    <!-- 현재 선택된 Drop 메뉴에 .is-current 클래스 붙임. -->
                                    <ul class="selects" onmouseleave="js.drops_lang(event,this);">
                                        <?php
                                        for ($i=1; $i < count($langs); $i++) :?>
                                            <li class="lists">
                                                <a data-value="<?=$dropdownList[$i]?>" href="#" onclick="fnChangeLanguage(event)" class="link"><?=$langs[$dropdownList[$i]]?></a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="co__info">
                <div class="position">
                    <span>법인명(상호) : (주)에프씨씨티브이</span>
                    <span>대표자(성명) : 이승민</span>
                    <span>사업자 등록번호 : 582-87-00288</span>
                    <span>통신판매업 신고 : 제 2016-서울강남-01161 호</span>
                    <br>
                    <span>전화 : 070-5015-6380</span>
                    <span>E-MAIL : help@fcctvhq.com</span>
<!--                    <span>전화 : 070-5015-6383</span>-->
                    <span>주소 : 서울특별시 강남구 학동로 331, 3층 1호 </span>
                    Copyright © FCC TV Co.,Ltd.
                </div>
            </div>
        </footer>

    </section>
    <a class="gotoTop" href="#top"><?=__('TOP')?></a>

    <!-- ******************************************** 페이지별 모달 레이어 (가변영역), z-index : 1000 -->
    <aside id="modal">
        <div class="modal__board"><div class="modal__contents"></div></div>
        <div class="modal__bg"></div>
    </aside>

    <!-- ******************************************** #mobile 전용 영역 공통 사용, z-index : 400 -->
    <aside id="mobile">
        <button type="button" class="mobile__button--lnb" name="lnb" onclick="js.menu(this);"><?=__('LNB Menu')?></button>
        <div id="mobile__lnb">
            <button type="button" class="close" name="lnb" onclick="js.menu(this);"><?=__('LNB')?></button>
            <ul class="menus is-zoom">
                <!-- 현재 페이지에 해당되는 메뉴에 .is-select 붙어야함. -->
                <li class="menu"><a href="/tv/" class="link"><?=__('TV')?></a></li>
                <li class="menu">
                    <div class="link">
                        <?=__('CHALLENGE')?>
                        <ul class="submenus__links is-zoom">
                            <li class="submenu"><a href="http://fashioncrowdchallenge.com/" class="sublink">FCC 2016</a></li>
                            <li class="submenu"><a href="http://fashioncrowdchallenge.com/designs/index" class="sublink">FCC 2015</a></li>
                            <li class="submenu"><a href="http://fashioncrowdchallenge.com/mcc2015/result" class="sublink">ModelCC 2015</a></li>
                        </ul>
                    </div>
                </li>
                <!-- <li class="menu"><span class="link">SHOWROOM <span class="comming">coming soon</span></span></li>
                <li class="menu"><span class="link">FCC TV <span class="comming">coming soon</span></span></li> -->
                <li class="menu"><a href="http://story.crowdchallenge.com/" class="link" target="_blank"><?=__('STORY')?></a></li>
<!--                <li class="menu"><a href="/faq/" class="link">--><?//=__('FAQs')?><!--</a></li>-->
                <li class="menu">
<!--                    <a href="/about/" class="pages subs">--><?//=__('ABOUT')?><!--</a>-->
<!--                    <a href="/press/" class="pages subs">--><?//=__('PRESS')?><!--</a>-->
                    <a href="/contact/" class="pages subs"><?=__('Contact Us')?></a>
                    <a href="/terms/" class="pages subs"><?=__('TERMS AND CONDITIONS')?></a>
<!--                    <a href="/policy/" class="pages subs">--><?//=__('PRIVACY POLICY')?><!--</a>-->
                </li>
                <li class="menu">
                    <br>
                    <!-- <span class="pages subs">LANGUAGE</span> -->
                    <div class="selectbox">
                        <div class="mobile__language"> <!-- .is-mobile : 모바일 화면일 경우 js 로 해당 클래스 오버라이드-->
                            <div class="mobile__language--wrap">
                                <select name="language" onchange="js.select_on_change(event,this);">
                                    <?php
                                    for ($i=0; $i < count($langs); $i++) :?>
                                    <option data-value="<?=$dropdownList[$i]?>" onclick="fnChangeLanguage(event)"><?=$langs[$dropdownList[$i]]?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="mobile__language--web" onClick="js.drops_lang(event,this);">
                                    <button type="button"><?=$langs[$dropdownList[0]]?><span class="arr"></span></button>
                                    <!-- 현재 선택된 Drop 메뉴에 .is-current 클래스 붙임. -->
                                    <ul class="selects">

                                        <?php
                                        for ($i=1; $i < count($langs); $i++) :?>
                                            <li class="langs">
                                                <a data-value="<?=$dropdownList[$i]?>" href="#" onclick="fnChangeLanguage(event)" class="lang"><?=$langs[$dropdownList[$i]]?></a>
                                            </li>
                                        <?php endfor; ?>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="menu">
                    <!-- <span class="pages subs">SOCIAL MEDIA</span> -->
                    <div class="selectbox">
                        <div class="mobile__language"> <!-- .is-mobile : 모바일 화면일 경우 js 로 해당 클래스 오버라이드-->
                            <div class="mobile__language--wrap service">
                                <select name="social" onchange="js.select_on_change(event,this);">
                                    <option value="facebook" selected>Facebook</option>
                                    <option value="Twitter"><?=__('Twitter')?></option>
                                    <option value="Vk"><?=__('Vk')?></option>
                                    <option value="Weibo"><?=__('Weibo')?></option>
                                </select>
                                <div class="mobile__language--web" onClick="js.drops_lang(event,this);">
                                <button type="button"><?=__('SOCIAL MEDIA')?><span class="arr"></span></button>
                                    <ul class="selects">
                                        <li class="langs"><a href="https://www.facebook.com/crowdchallenges" target="_blank" class="lang"><?=__('Facebook')?></a></li>
                                        <li class="langs"><a href="https://twitter.com/crowdchallenge" target="_blank" class="lang"><?=__('Twitter')?></a></li>
<!--                                        <li class="langs"><a href="http://vk.com/crowd_challenge" target="_blank" class="lang">--><?//=__('Vk')?><!--</a></li>-->
                                        <li class="langs"><a href="http://weibo.com/CrowdChallenge" target="_blank" class="lang"><?=__('Weibo')?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- 모바일 우측 메뉴 -->
        <button type="button" class="mobile__button--rnb" name="rnb" onclick="js.menu(this);">
        <?php if(isset($auth)){?>
                <?= $auth['nickname']?>
                <!-- 사용자 등록 사진이 없을 경우. 아래 span 자체를 사용하지 않음. -->
                <?php if($auth['image_path']!=''){?>
                    <span class="users" name="rnb" style="background-image: url('<?=FILE_URI.$auth['image_path']?>');"></span>
                <?php }?>
            <?php }?>
            <span class="frame"></span>
        </button>
        <div id="mobile__rnb">
            <div class="menubar is-zoom">
                <?php if(isset($auth)){?>
                    <div class="mobile__rnb--sign">
                        <a href="/users/edit" class="mobile__rnb--label2">
                            <!-- 사용자 등록 사진이 없을 경우. 아래 span 에서 style='background-image' 속성 제거. -->
                        <span class="photo"
                            <?php if($auth['image_path']!=''){
                                echo "style='background-image: url(".FILE_URI.$auth['image_path'].");'";
                            }?>
                            ></span>
                            <span class="frame"></span>
                            <span><?=__('Hello,')?></span><br>
                            <?= $auth['nickname']?>
                        </a>
                        <ul class="menus is-zoom" style="border-top: #d8d8d8 1px solid;">
                            <!--<li class="menu"><a href="/my/" class="link">My Challenge</a></li>-->
                            <li class="menu"><a href="/myorder/" class="link"><?=__('Orders')?></a></li>
                            <li class="menu">
                                <span class="link"><?=__('Settings')?></span>
                                <a href="/address/index" class="sublink"><?=__('Shipping Addresses')?></a>
                                <a href="/users/account" class="sublink"><?=__('Account')?></a>
                            </li>
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
                            <?__('Welcome')?>
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
                                <a href="/auth/join" class="buttons create">
                                    <span class="text"><?=__('Create Account')?></span>
                                    <span class="txt"><?=__('Create Account')?></span>
                                    <span class="bg"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php }?>
            </div>
            <button type="button" class="close toggle__navi" name="rnb">RNB</button>
        </div>

        <!-- 모바일 우측 메뉴, 카트 -->
        <button type="button" class="mobile__button--cart" name="rnb" onclick="modal.show('../cart')">카트 <span class="noti cartCnt" style="display: none;">0</span></button>

        <span id="mobile__black"><span class="black"></span></span>
    </aside>
</div>
<!-- id="wrap" -->

<!-- ******************************************** 페이용 스크립트 공통, 혹은 가변 영역 -->
<script src="/_res/js/script.js"></script>

<script>

    function number_format(numval) {
        return numval.toString().replace(/(\d)(?=(\d{3})+$)/g, "$1,");
    }

    var currency = '<?=currency?>';
    function currencyStr(price){
        switch(currency){
            case 'WON':
                price = number_format(price)+' 원';
                break;
            case 'USD':
                price = '$'+price;
                break;

        }
        return price;
    }

    $(function(){
        <?php if(isset($auth)){?>
        cartCnt();
        <?php }?>
    });
    function quantity(change,el) {
        cartQuantityChange($(el).find('.count'),$(el.context).html());
    }
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
    function removeCart(cartId){
        $.ajax({
            url: '/cart/remove',
            dataType: 'json',
            type : 'Post',
            data : { cartId : cartId},
            success: function (rtn) {
                if(rtn.result==true){
                    $('li.item[cartId='+cartId+']').remove();
                    $('.cartCnt').html(rtn.cartCnt);
                    if(rtn.cartCnt < 1){
                        $(".cartCnt").hide();
                        $(".modal__mycart--lists").html('');
                        var emptyCartHtml ='<div class="modal__mycart--empty">' +
                            '<span class="icon"></span>' +
                            '<p class="text">카트에 담긴 물건이 없습니다.</p>' +
                            '</div>';
                        $(".modal__mycart--lists").append(emptyCartHtml);
                    }else{
                        $(".cartCnt").show();
                    }
                    $('#cartTotalPrice').html(currencyStr(rtn.totalPrice));
                    if(rtn.totalPrice>0){
                        $('.modal__mycart--bottom').show();
                    }else{
                        $('.modal__mycart--bottom').hide();
                    }
                }else{
                    alert('System Problem');
                }
            }
        });
    }
    function cartQuantityChange(obj,callBack){
        var changeQuantity = $(obj).val();
        if(changeQuantity<1){
            alert("수량은 1 이상이 되어야 합니다.");
            changeQuantity = 1 ;
            $(obj).val(1);
            //return false;
        }
        var cartIdVal = $(obj).attr("cart_id");
        $.ajax({
            url: '/cart/quantityChange',
            dataType: 'json',
            type : 'Post',
            async : false,
            data : {
                cartId : cartIdVal,
                quantity : changeQuantity
            },
            success: function (rtn) {
                if (rtn.result == true) {
                    $('.quantityPrice[cart_id=' + cartIdVal + ']').html(currencyStr(rtn.price));
                    $('#cartTotalPrice').html(currencyStr(rtn.totalPrice));
                } else {
                    if(callBack=='+'){
                        $(obj).val(changeQuantity-1);
                    }
                    if (rtn.msg == "OutOfStock") {
                        alert("죄송합니다.\n 모든 수량이 매진되었습니다.");
                    } else if (rtn.msg == "MoreThenStock") {
                        alert("현재 재고 보다 많은 수량을 선택하셨습니다.");
                    } else if (rtn.msg == "MaxPurchase") {
                        alert("최대구매수량을 초과하였습니다.");
                    } else {
                        alert("System Fail");
                    }
                }
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

    function closePop(){
        setCookie('commingSoon', 'closed', 1);
        modal.hide();
    }

    if(getCookie("commingSoon")!="closed") {
        modal.show2('commingSoon');
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
<!-- Google Analytics -->
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-75732921-1', 'auto');
    ga('send', 'pageview');
</script>
<!-- Google Analytics -->
<span id="scrollTop"></span>
</body>
</html>













