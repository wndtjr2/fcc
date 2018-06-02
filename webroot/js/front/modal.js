function modal_show(target) {
    var modal = $('#modal');
    modal.fadeIn(100, function(){
        //console.log('배경 열렸음. 컨텐츠를 불러와야함.');
        jQuery.ajax({
            type: "GET",
            url: "../modal/"+target,
            success: function(html){
                //console.log('html 불러오기 성공.');
                var el = $.parseHTML(html);
                jQuery('.modal__contents').append(el);
                //console.log(jQuery(el).find('.nano').hasClass('nano'));
                contents_middle();
                setTimeout(function(){
                    jQuery('.modal__contents').fadeIn(100);
                    ( jQuery(el).find('.nano').hasClass('nano') ) ? jQuery(".nano").nanoScroller() : false;
                    ( jQuery(jQuery(el).find('.modal__content').prevObject[1]).attr('id') == 'modal__sellers' ) ? sellers_detail() : false;
                }, 200)
            }
        })
    });
}
function modal_show2(target) {
    if((typeof modalDisable == 'undefined') || (modalDisable == false)) {
        modalDisable = true;
        jQuery('.modal__contents').html('').fadeOut();
        setTimeout(function () {
            var modal = $('#modal');
            modal.fadeIn(100, function () {
                //console.log('배경 열렸음. 컨텐츠를 불러와야함.');
                jQuery.ajax({
                    type: "GET",
                    url: "/Modal/" + target,
                    success: function (html) {
                        //console.log('html 불러오기 성공.');
                        var el = $.parseHTML(html);
                        jQuery('.modal__contents').append(el);
                        //console.log(jQuery(el).find('.nano').hasClass('nano'));
                        contents_middle();
                        setTimeout(function () {
                            jQuery('.modal__contents').fadeIn(100);
                            if(target == 'modalAddress')js.radios();
                            ( jQuery(el).find('.nano').hasClass('nano') ) ? jQuery(".nano").nanoScroller() : false;
                        }, 200)
                    },
                    error: function (request, status, error) {
                        if (request.status == 403) {
                            window.location.reload();
                        } else {
                            alert(error);
                            window.location.href = '/';
                        }
                    },
                    complete: function(){
                        modalDisable = false;
                    }
                })
            });
        }, 300)
    }
}

function modal_show3(e, id){
    var o = $("#modal");
    o.fadeIn(100,function(){
        $("body").css(
            {
                overflow:"hidden"
            }
        ), jQuery.ajax({
                type : "GET",
                url : "/modal/"+e+"/"+id,
                success : function(e){
                    var o = e;
                    jQuery(".modal__contents").append(o), modal.middle(),setTimeout(function(){
                        jQuery(".modal__contents").fadeIn(100),modal.middle(),js.quantity(),js.radios()
                    },200)
                }
            })
        }
    )
}

function modal_purchase_alert(products){
    var o = $("#modal");
    o.fadeIn(100,function(){
            $("body").css({overflow:"hidden"}),
                jQuery.ajax({
                    data : {product : JSON.stringify(products)},
                    type : "POST",
                    url : "/modal/modalPurchase",
                    success : function(e){
                        var o = e;
                        jQuery(".modal__contents").append(o), modal.middle(),setTimeout(function(){
                            jQuery(".modal__contents").fadeIn(100),modal.middle(),js.quantity(),js.radios()
                        },200)
                    }
                })
        }
    )
}

function modalalert(msg) {
    if((typeof modalDisable == 'undefined') || (modalDisable == false)) {
        modalDisable = true;
        jQuery('.modal__contents').html('').fadeOut();
        setTimeout(function () {
            var modal = $('#modal');
            modal.fadeIn(100, function () {
                jQuery.ajax({
                    type: "POST",
                    url: "/Modal/alert",
                    data : {msg : msg},
                    success: function (html) {
                        var el = $.parseHTML(html);
                        jQuery('.modal__contents').append(el);
                        contents_middle();
                        setTimeout(function () {
                            jQuery('.modal__contents').fadeIn(100);
                            ( jQuery(el).find('.nano').hasClass('nano') ) ? jQuery(".nano").nanoScroller() : false;
                        }, 200)
                    },
                    error: function (request, status, error) {
                        if (request.status == 403) {
                            window.location.reload();
                        } else {
                            alert(error);
                            window.location.href = '/';
                        }
                    },
                    complete: function(){
                        modalDisable = false;
                    }
                })
            });
        }, 300)
    }
}

function modalpayalert(msg, url) {
    if((typeof modalDisable == 'undefined') || (modalDisable == false)) {
        modalDisable = true;
        jQuery('.modal__contents').html('').fadeOut();
        setTimeout(function () {
            var modal = $('#modal');
            modal.fadeIn(100, function () {
                jQuery.ajax({
                    type: "POST",
                    url: "/Modal/payAlert",
                    data : {msg : msg, url : url},
                    success: function (html) {
                        var el = $.parseHTML(html);
                        jQuery('.modal__contents').append(el);
                        contents_middle();
                        setTimeout(function () {
                            jQuery('.modal__contents').fadeIn(100);
                            ( jQuery(el).find('.nano').hasClass('nano') ) ? jQuery(".nano").nanoScroller() : false;
                        }, 200)
                    },
                    error: function (request, status, error) {
                        if (request.status == 403) {
                            window.location.reload();
                        } else {
                            alert(error);
                            window.location.href = '/';
                        }
                    },
                    complete: function(){
                        modalDisable = false;
                    }
                })
            });
        }, 300)
    }
}

function modal_hide() {
    var target = $('#modal');
    target.fadeOut(200, function(){
        jQuery('.modal__contents').html('').fadeOut();
    });
}

function modal_pay_hide(url){
    var target = $('#modal');
    target.fadeOut(200, function(){
        jQuery('.modal__contents').html('').fadeOut();
    });
    if(url == undefined || url == ''){
        opener.location.reload();
        window.close();
    }else{
        window.location = url;
    }
}

/* 언제나 화면 중앙에 위치하도록 */
function contents_middle() {
    $('.modal__contents').css({'margin-top': ($(window).height()-$('.modal__contents').innerHeight())/2})
}

contents_middle();
$(window).resize(function(e){
    contents_middle();
})
