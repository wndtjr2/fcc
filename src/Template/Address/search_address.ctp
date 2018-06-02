<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="author" content="fcctv.co.kr">
    <meta name="application-name" content="FCC TV">
    <title>FCC TV</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="shortcut icon" href="/_res/img/favicon/favicon.ico">
    <link rel="stylesheet" type="text/css" href="/_res/css/style.css">
    <script src="/_res/lib/jquery-1.11.2.min.js"></script>
    <script src="/_res/lib/jquery.mobile-events.min.js"></script>

    <!--[if lt IE 9]>
    <script src="/_res/lib/html5.js"></script>
    <script src="/_res/lib/respond.js"></script>
    <![endif]-->

</head>

<body>
<div class="winpop">
    <h2 class="winpop__title"><?=__("우편번호 찾기")?></h2>
    <div class="address__search is-new">
        <div class="address__new">
            <div class="detail">
                <form id="roadForm" onsubmit="return false;">
                    <fieldset>
                        <div class="only__web--account">
                            <legend class="form__title is-skip"></legend>
                            <div class="form__division is-zoom">
                                <div class="form__division--divide f2-left">
                                    <label for="lb__street" class="input__label--second"><?=__("도로명/읍면동 번지")?></label>
                                    <input type="text" name="sch" placeholder="<?=__("도로명이나 읍면동 지번을 입력하세요.")?>" id="lb__street" class="input__box">
                                </div>
                            </div>
                            <div class="form__submit--left">
                                <button type="button" class="buttons face" id="roadSch">
                                    <span class="text">검색</span>
                                    <span class="bg"></span>
                                </button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>

        <div class="address__result">

            <!--- 검색 결과 노출 영역 -->
            <h3>검색 결과</h3>
            <ul class="address__result--list" id="addrResult">

            </ul>
            <div class="pagging">
                <div class="paging_pages" id="pageResult">

                </div>
            </div>
            <p class="noti">
                도로명주소가 검색되지 않는 경우는 <br>행정안정부 새주소안내시스템 <br>(<a href="http://www.juso.go.kr" target="_blank">http://www.juso.go.kr</a>) 에서 확인하시기 바랍니다.
            </p>

        </div>
    </div>
</div>
<script>
    var pagenum = 1;
    $(function(){
        $('#roadSch').click(function(){
            pagenum=1;
            $('#pageResult').html('');
            search($('#roadForm').serialize());
        });
        $('#addrResult').on("click",".rtnLink",function(){
            var obj=$(this);
            window.opener.addAddr(obj.attr('rtnzip'),obj.attr('rtnaddr'))
            /*
            var openerWin=opener.document;
            $('#address',openerWin).val(obj.attr('rtnaddr'));
            $('#zipcode',openerWin).val(obj.attr('rtnzip'));
            $('#address2',openerWin).focus();
            */
            self.close();
        });
        $('#pageResult').on("click",".page",function(){
            pagenum=$(this).attr('pagnum');
            search($('#roadForm').serialize());
        });
    });
    function search(data){
        data +='&currentPage='+pagenum;
        $('#addrResult').html('');
        $.ajax({
            url :  '/address/searchAddressProc',
            method : 'post',
            data : data,
            dataType : 'json',
            success : function(json){
                viewSearch(json);
            },
            error :  function(x,s,e) {
//                console.log(x+'::'+s+'::'+e);
            }
        });
    }
    function viewSearch(rtn){
         var dataList=rtn.NewAddressListResponse.newAddressListAreaCdSearchAll;
         var msgHeader =  rtn.NewAddressListResponse.cmmMsgHeader
        var html = '';
        if(msgHeader.totalCount==0){
            html ='<li><strong></strong><span><?=__("검색결과가 없습니다")?></span></li>';
        }else{
            if(msgHeader.totalCount==1){
                html = liStr(rtn.callBack,dataList);
            }else{
                $.each(dataList,function(k,v){
                    html += liStr(rtn.callBack,v);
                });
                $('#pageResult').html(pageStr(msgHeader.totalPage));
            }

        }
        $('#addrResult').html(html);
    }
    function liStr(callBack,data){
        var html='';
        html +='<li><a href="#" class="rtnLink" rtnzip="'+data.zipNo+'" rtnaddr="'+data.lnmAdres+'"><strong class="rtnZip">' + data.zipNo + '</strong><span class="rtnAddr">' + data.lnmAdres + '</span><span>지번 주소 : ' + data.rnAdres + '</span></a></li>';
        return html;
    }
    function pageStr(total){
        var html='';
        for(i=1;i<total;i++){
            html+='<a href="#" class="page';
            if(i==pagenum){
                html+=' current';
            }
            html +='" pagnum="'+i+'"> '+i+' </a>';
        }
        return html;
    }
</script>
</body>
</html>