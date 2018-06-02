$(function(){
    // -------------------------------------------* 달력 확장 버튼 기능.
    $('.fcctv__calendar--btn').bind('click', function(e){
        ftv.cover('up');
        if ( cal.t.hasClass('show-month') ) {
            cal.t.removeClass('show-month')
            cal.d.hide()
            setTimeout(function(){
                cal.d.show(200);
            },300)
        }else{
            cal.t.addClass('show-month');
            cal.d.hide()
            setTimeout(function(){
                cal.d.show(200);
            },300)
        }
        $('.fcctv__days').removeClass('srh-month');
    })

    $('.fcctv__searchbar .text').focus(function(){
        ftv.cover('up');
    });

    // -------------------------------------------* 이전 달, 다음 달 보기 테스트 기능. 삭제 요망.
    $('#month__prev, #month__next').bind('click', function(e){
        console.log('이전, 다음달 보기를 선택하였습니다.')
        $('.fcctv__days').toggleClass('srh-month');
    })

    // -------------------------------------------* 달력, 라이브러리 열기.
    cal.init();
    $(window).resize(function(){
        cal.init();
    });


    // -------------------------------------------* DOM 완료후, 방송 리스트 영역 Size init.
    ftv.view();

    if ( detector.mobile() ) {
        // -------------------------------------------* DOM 완료후, 생방송 커버영역, 날짜 영역, 검색영역에 터치 이벤트 설정. 
        $('.fcctv__cover, .fcctv__search').bind('tap taphold swipe swipeleft swiperight swipeup swipedown', function(e){
            ( detector.mobile() ) ? execute() : false;
            function execute() {
                console.log('execute');
                var evt = e.type;
                switch (evt) {
                    case 'swipeup' : 
                        ftv.cover('up');
                        break;
                    case 'swipedown' :
                        ftv.cover('down');
                        break;
                }
            }
        });
        // -------------------------------------------* DOM 완료후, 방송 리스트 영역만의 터치 이벤트 설정. 
        $('.fcctv__lists').bind('swipeup', function(e){
            ( detector.mobile() ) ? execute() : false;
            function execute() {
                var boo = $('.fcctv__wrap').hasClass('is-cut');
                ( boo ) ? false : ftv.cover('up');
            }
        });
    }

    // -------------------------------------------* On air 시간 중앙정렬.
    function onair_center() {
        var h = ( $('.fcctv__onair').height() - $('.fcctv__onair .position').height() ) / 2;
        var h2 = (h<0) ? 0 : h;
        $('.fcctv__onair .position').css({
            'margin-top' : h2
        })
    }
    window.addEventListener('load', function(event){
        onair_center();
    });
    $(window).resize(function(){
        onair_center();
    });
});


// -------------------------------------------* 달력
var cal = {
    t : $('.calendar'),
    d : $('.fcctv__days--list'),
    c : 0,              // 현재 표기 되는 날짜. 초기 설정은 오늘.
    f : 0,              // 날짜 이동 버튼으로 이동이 불가능한 날짜 위치 값을 알기위한 변수.
    boo_next : false,
    boo_prev : false,
    w : 62,             // 일자 width 크기 초기 설정.
    wd : 1920,          // 달력 슬라이드 박스의 width 크기 초기 설정.

    init : function() {
        cal.w = ( $(window).width() > 1024 ) ? 62 : $('.fcctv__days').width() / 6; //cal.d.find('.link').width(),
        cal.c = cal.d.find('.link.is-current').attr('name');
        cal.boo_next = ( cal.d.find('.link.is-current').parent().next().find('.link').hasClass('is-disabled') ) ? false : true;
        cal.wd = cal.w * ( cal.d.find('.link:last').attr('name') + 1 ); 

        cal.d.css({
            'position' : 'absolute',
            'top' : 0,
            'left' : 0,
            'width' : cal.wd
        }).find('.day').css({
            'width' : cal.w
        })

        cal.move(cal.c, cal.w);

        $('#day_next, #day_prev').bind('click', function(e){
            if ( $(e.target).parent().attr('id') == 'day_next' ) {
                if ( cal.boo_next ) {
                    cal.c++;
                    cal.move(cal.c, cal.w);
                }
                return;
            }else if( cal.boo_prev ) {
                cal.c--;
                cal.move(cal.c, cal.w);
            }
        })

        cal.test(); // 화면 테스트용. 주석처리 요청.
    },
    move : function(i,w) {
        var go = ( i == undefined ) ? cal.d.find('.link.is-today').attr('name') : i; // 표기할 날짜가 없을 경우, 오늘 날짜로 설정.
        cal.d.css({
            'left' : -(go-1)*w
        })
        cal.f = go-1;
        cal.boo_next = ( cal.d.find('.link').eq(cal.f+2).hasClass('is-disabled') ) ? false : true;
        cal.boo_prev = ( cal.d.find('.link').eq(cal.f).hasClass('is-disabled') ) ? false : true;
    },
    // -------------------------------------------* 화면 테스트용.
    test : function() {
        var check_day = params.get();
        $('.fcctv__days--list .link strong').each(function(idx){
            if ( $(this).text() == check_day["day"] ) {
                $('.fcctv__days--list .link').removeClass('is-current');
                $(this).parent().addClass('is-current');
                cal.c = cal.d.find('.link.is-current').attr('name');
                cal.move(cal.c, cal.w);
            }
        })
    }
};

// -------------------------------------------* 달력
var ftv = {
    // -------------------------------------------* 상단 커버 영역 확장용 Class Toggle.
    class_update : function() {
        $('.fcctv__wrap').toggleClass('is-cut');
    },
    // -------------------------------------------* 상단 커버 영역 확장과 방송 리스트 영역 업데이트.
    cover : function(t) {
        var boo = $('.fcctv__wrap').hasClass('is-cut');
        if ( t == 'up' ) {
            ( boo ) ? false : ftv.class_update();
            setTimeout(ftv.view,300)
            return;
        }
        ( boo ) ? ftv.class_update() : false;
        setTimeout(ftv.view,300)
    },
    // -------------------------------------------* 방송 리스트 영역 Size Toggle.
    view : function() {
        var head = 40,
            cover = $('.fcctv__cover').height(),
            srh = $('.fcctv__search').height(),
            brow = $(window).height(),
            view = brow - (head+cover+srh);

        var wrap = $('.fcctv__lists');

        wrap.css({
            'height' : view
        })
    }
};
