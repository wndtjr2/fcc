<?php
    $title = "";
    $msg = "";
    $coverClass = "";
    $mobileCover = "";
    $webCover = "";

    switch($coverType){
        case "tv":
            $coverClass = "fcctv__cover";
            $msg = __("디자이너와 소비자가 만나다. <br class='only__web'>FCC TV는 디자이너가 직접 크리에이터가 되어 자신의 아이템 혹은 브랜드에 대한 <br class='only__web'>이야기를 소비자에게 영상 컨텐츠로 소개하는 공간입니다.");
            $title = __("<span class='line1'>FCC</span><span class='line2'>TV</span>");
            $mobileCover = "/_res/img/tv_cover/tv_cover_mobile.jpg";
            $webCover = "/_res/img/tv_cover/tv_cover_web.jpg";
            break;
        case "designer":
            $coverClass = "designer__cover";
            $msg = __("FCC TV를 통해 만나보는 패션 디자이너 27번의 서울패션위크 컬렉션에 참가한 패턴의 여왕 곽현주, <br class='only__web'>3년여의 공백기를 깨고 화려하게 컴백한 예란지 또는 파리가 주목하고 있는 신예 디자이너 김인태 등 <br class='only__web'>지금껏 그 어디에서도 공개되지 않았던 그들의 스토리를 직접 감상하세요.");
            $title = __("<span class='line1'>MEET YOUR</span><span class='line2'>DESIGNERS</span>");
            $mobileCover = "/_res/img/designer_cover/designer_cover_mobile.jpg";
            $webCover = "/_res/img/designer_cover/designer_cover_web.jpg";
            break;
        case "hotitem":
            $coverClass = "hotitem__cover";
            $msg = __("FCC TV가 선정한 디자이너의 특별한 아이템 “이번 시즌 트렌드는 바로 이것!” <br class='only__web'>우리 모두가 궁금해하던 트렌드 아이템을 한눈에! FCC TV와 국내 유명 패션 디자이너들이 <br class='only__web'>추천하는 믿을 수 있는 베스트 아이템을 지금 바로 확인하세요.");
            $title = __("<span class='red'>HOT</span><span class='white'>ITEM</span>");
            $mobileCover = "/_res/img/hotitem/hotitem_page_cover_mobile.jpg";
            $webCover = "/_res/img/hotitem/hotitem_page_cover_web.jpg";
            break;
    }
?>

<div class="<?=$coverClass?>">
    <div class="position">
        <div class="js_h_middle">
            <h3 class="title"><?=$title?></h3>
            <p class="headeline">
                <span class="line"></span>
                <?=$msg?>
            </p>
        </div>
    </div>
    <img src="<?=$mobileCover?>" alt="" class="cover">
    <img src="<?=$webCover?>" alt="" class="cover web">
</div>

