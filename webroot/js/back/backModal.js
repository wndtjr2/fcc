var backModal = {

    // -------------------------------------------* 지정한 HTML 불러오기.
    show : function(target) {
        // modal.videoStop();
        var m = $('#modal');
        m.fadeIn(100, function(){
            $('body').css({'overflow':'hidden'});

            jQuery.ajax({
                type: "GET",
                url: "/modal/"+target,
                success: function(html){
                    var el = $.parseHTML(html);
                    jQuery('.modal__contents').append(el);
                    modal.middle();
                    setTimeout(function(){
                        jQuery('.modal__contents').fadeIn(100);
                        modal.middle();
                        //console.log('touchmove');
                        js.quantity();
                    }, 200)
                }
            })
        });
    },

    // -------------------------------------------* 현재 모달이 뛰어진 상태에서, 지정한 HTML 다시 불러오기.
    show2 : function(target) {
        jQuery('.modal__contents').scrollTop(0).fadeOut(300, function(){
            jQuery('.modal__contents').html('');
        });
        setTimeout(function(){
            modal.show(target);
        }, 500);
    },

    // -------------------------------------------* 비디오 보기 전용 모달 열기.
    show_video : function(target, n) {
        // festival, fashion show, showroom 체크하여 해당 mp4 파일 호출.
        var url = '/_res/mov/festival2016_'+n+'.mp4';

        var m = $('#modal');
        m.fadeIn(100, function(){
            jQuery.ajax({
                type: "GET",
                url: "/modal/"+target,
                success: function(html){
                    var el = $.parseHTML(html);
                    jQuery('.modal__contents').append(el);

                    // 특정된 mp4 파일 호출.
                    $('#player1').attr({'src':url});

                    modal.middle();
                    setTimeout(function(){
                        jQuery('.modal__contents').fadeIn(100);
                        modal.middle();
                    }, 200)
                }
            })
        });
    },

    // -------------------------------------------* 디자이너 상세 갤러리용 모달 열기.
    show_gallery : function(target, n, lang) {
        // modal.videoStop();
        var m = $('#modal');
        m.fadeIn(100, function(){
            $('body').css({'overflow':'hidden'});

            jQuery.ajax({
                type: "GET",
                url: "/_modal/"+target,
                success: function(html){
                    var el = $.parseHTML(html);
                    jQuery('.modal__contents').append(el);
                    // modal.middle();
                    setTimeout(function(){
                        jQuery('.modal__contents').fadeIn(300, function(){
                            // console.log('갤러리 모달 열림.');
                            // modal.middle();
                            gallery.show(n, lang);
                        });
                    }, 400)
                }
            })
        });
    },

    // -------------------------------------------* 디자이너 상세 갤러리용 모달이 열려있는 상태에서 next, prev 버튼 킈리
    // show_gallery2 : function(target, n) {
    //     jQuery('.modal__contents').scrollTop(0).fadeOut(300, function(){
    //         jQuery('.modal__contents').html('');
    //     });
    //     setTimeout(function(){
    //         modal.show_gallery(target, n);
    //     }, 500);
    // },

    // -------------------------------------------* 패션쇼 사진 보기.
    show_photos : function(target, n, lang) {
        var m = $('#modal');
        m.fadeIn(100, function(){
            $('body').css({'overflow':'hidden'});

            jQuery.ajax({
                type: "GET",
                url: "/_modal/"+target,
                success: function(html){
                    var el = $.parseHTML(html);
                    jQuery('.modal__contents').html(el);
                    // modal.middle();
                    setTimeout(function(){
                        jQuery('.modal__contents').fadeIn(300, function(){
                            // console.log('갤러리 모달 열림.');
                            // modal.middle();
                            photos.show(n, lang);
                        });
                    }, 400)
                }
            })
        });
    },

    // -------------------------------------------* 디자이너 상세 갤러리용 모달이 열려있는 상태에서 next, prev 버튼 킈리
    // show_photos2 : function(target, n) {
    //     jQuery('.modal__contents').scrollTop(0).fadeOut(300, function(){
    //         jQuery('.modal__contents').html('');
    //     });
    //     setTimeout(function(){
    //         modal.show_photos(target, n);
    //     }, 500);
    // },

    // -------------------------------------------* 모달 닫기.
    hide : function() {
        var target = $('#modal');
        target.fadeOut(200, function(){
            jQuery('.modal__contents').html('').fadeOut();
            $('body').css({'overflow':''});
        });
    },

    // -------------------------------------------* 항상 중앙에 위치하도록 Margin 값 조정.
    middle : function() {
        // console.log(getBrowserType());

        var w = $(window).height();
        t = $('.modal__contents');
        m = $('.modal__content').innerHeight();

        // console.log('window : '+ w);
        // console.log('contents : '+ m);

        // var iphone = ( detector.device() == 'iphone' ) ? getType() : 0;
        // function getType() {
        //     if ( getBrowserType() == 'Safari' ) {
        //         return 70;
        //     }else if ( getBrowserType() == 'Chrome' ) {
        //         return 0;
        //     }else{
        //         return 0;
        //     }
        // }

        var iphone = 0;
        // console.log(iphone);

        // 화면보다 컨텐츠 길이가 길 경우.
        if ( w <= m ) {
            t.css({
                'margin-top': 0,
                'margin-bottom': 0,
                'height': w+iphone,
                'overflow': 'auto'
            });
            return;
        }
        // 화면보다 컨텐츠 길이가 적을 경우.
        var mt = (w-m)/2;
        t.css({
            'margin-top': mt
        });
    },

    // -------------------------------------------* 하루만 보기 체크박스 설정.
    aDay : function(n, v, d) {
        modal.hide();
        cookie.set(n, v, d);
    },

    // -------------------------------------------* video.js 가 있을 경우. 동영상 스톱.
    videoStop : function() {
        // ------------------------* 모바일 체크.
        if ( !detector.mobile() ) {
            for( x in festival.plays ) {
                festival.plays[x].pause();
            }
        }
    }
}

// -------------------------------------------* 화면 사이즈 변경 혹은 orientationchange 이벤트 발생.
$(window).bind('orientationchange resize', function(e){
    switch (e.type) {
        case 'orientationchange' :
            modal.hide();
            break;
        case 'resize' :
            modal.middle();
            // modal.hide();
            break;
    }
});


