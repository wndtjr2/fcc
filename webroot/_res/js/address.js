$(function(){

    var modalDisable = false;

    /** 새배송지모달 팝업 **/

    $("#AddAddress").on("click", function(){
        modal_show2('modalAddressNew');
    });

    $("#ModalAddress").on("click", function(){
        modal_show2('modalAddress');
    });

    /** 새 배송지 저장 **/
    $(document).on("click", "#submitButtonForSave", function(){

//        $(this).click(function(){ return false;});

        $("<input>").attr({
            type : 'hidden',
            id : 'default_addr',
            name : 'default_addr',
            value : 'y'
        }).appendTo('#addressFrm');

        var frmValue = $("#addressFrm").serializeArray();
        var checkForm = true;

        $('.input__box').each(function (){
            fnShowRequired($(this).prop('id'));
            if($(this).val()=='') {
                if(this.name == 'deliv_phone_num'){
                    this.blur();
                }else{
                    $(this).focus();
                }
                checkForm = false;
            }
        });

        if(checkForm){
            $("#submitButtonForSave").prop("disabled", true);
            $.ajax({
                url: '/Orders/addAddressBeforeCheckOut',
                dataType: 'json',
                type : 'Post',
                data : frmValue,
                success: function (rtn) {
                    if(rtn.result==true){
                        location.reload();
                        return false;
                    }
                    $("#submitButtonForSave").prop("disabled", false);
                },
                error: function(xhr, status, error){
                    errorHandling(xhr, status, error);
                }
            });
        }
    });

    /** 주소 변경 버튼 클릭시 **/
    $("#changeBtn").on("click",function(){
        $.ajax({
            url : '/address/getJson',
            dataType : 'json',
            type : 'post',
            success : function(data){
                var addr = data.addressList;
                $("#addrList").html('');
                for(var i=0;i<addr.length;i++){
                    $("#addrList").append(makeAddrHtml(addr[i],true));
                }
                $("#addrList").show();
            }
        });
    });

    /** 주소 추가 **/
    $("#submitBtn").on("click",function(){
        var frmValue = $("#addressFrm").serializeArray();
        $.ajax({
            url: '/address/add',
            dataType: 'json',
            type : 'Post',
            data : frmValue,
            success: function (rtn) {
                if(rtn.result==true){
                    $("#tempAddrArea").html('');
                    $("#changeBtn").click();
                }
            }
        });
    });

    /** 주소수정 저장하기 **/
    $(document).on("click", "#saveEditAddress", function(){

        var EditFormValues = $("#EditAddressFrm").serializeArray();
        var checkForm = true;

        $('.input__box').each(function (){
            fnShowRequired($(this).prop('id'));
            if($(this).val()==''){
                if(this.name == 'deliv_phone_num'){
                    this.blur();
                }else{
                    $(this).focus();
                }
                checkForm = false;
            }
        });

        if(checkForm){
            $(this).click(function(){return false;});
            $.ajax({
                url: '/address/add',
                dataType: 'json',
                type : 'Post',
                data : EditFormValues,
                success: function (rtn) {
                    if(rtn.result==true){
                        modal_hide();
                        modal_show2('modalAddress');
                    }
                },
                error: function (xhr, status, error){
                    errorHandling(xhr, status, error);
                }
            });
        }
    });

});
/** 새 배송지 모달 띄우기 **/
function open2ndNewAddressModal(){
    modal_show2('modalAddressNew');
};

/** 배송입력 밸리데이션 **/
function fnShowRequired(selector) {
    if($.trim($('#'+selector).val())==''){
        $('#'+selector+'_required').show();
    }else{
        $('#'+selector+'_required').hide();
    }
};

/** 주소 목록에서 주소 선택시 **/
function selectAddr(addrIdVal){
    $.ajax({
        url : '/address/getDetail',
        dataType : 'json',
        data : {addrId : addrIdVal},
        type : 'post',
        success : function(data){
            $("#ShippingId").val(data.id);
            $("#tempAddrArea").html(makeAddrHtml(data,false));
        },
        error : function(request, status, msg){
            errorHandling(request, status, msg);
        }
    });

    $("#addrList").hide();
};

/** 주소 화면 생성 **/
function makeAddrHtml(obj){
    $("#NAME")[0].innerHTML = obj.deliv_last_name + " " + obj.deliv_first_name;
    $("#ADDRESS")[0].innerHTML = obj.zipcode + " " + obj.address + " " + obj.address2;
    $("#COUNTRY")[0].innerHTML = obj.code_country.country_name;
    $("#PHONE")[0].innerHTML = "전화번호" + ' : ' + obj.phone_decrypt;
    $("#ShippingId").val(obj.id);
    isTrueToShip(obj.id);
    modal.hide();

};

/** 기본주소 설정 **/
function makeDefaultButton(e){
    $.ajax({
        url: '/address/makedefault',
        dataType: 'json',
        type : 'Post',
        data : {addrId : e},
        success: function (data) {
            console.log(data);
            if(data){
                $("#ShippingId").val(data.id);
                $("#tempAddrArea").html(makeAddrHtml(data,false));;
                return false;
            }else{
                errorHandling(xhr, status, error);
            }
        },
        error: function (xhr, status, error){
            errorHandling(xhr, status, error);
        }
    });
};

/** 주소정보 가져오기 **/
function saveAddress(){
    var selectedObj = $('button.button__radio.is-select');
    if(undefined !== selectedObj[0]){
        var addId = selectedObj[0].id;
        $.ajax({
            url : '/address/getDetail',
            dataType : 'json',
            data : {addrId : addId},
            type : 'post',
            success : function(data){
                $("#ShippingId").val(data.id);
                $("#tempAddrArea").html(makeAddrHtml(data,false));
            },
            error : function(xhr, status, error){
                errorHandling(xhr, status, error);
            }
        });
    }else{
        location.reload();
        return false;
    }
};

/** 주소 삭제하기 **/

function deleteAddress(addrId){
    $("#DeleteAddress").prop("disabled", true);
    if(confirm('정말 삭제하겠습니까?')){
        $.ajax({
            url : '/address/deleteAddress',
            dataType : 'json',
            data : {addrId : addrId},
            type : 'post',
            success : function(data){
                if(data){
                    $("#DeleteAddress").prop("disabled", false);
                    $("#AddressId_"+addrId).remove();
                    //change radio button selected after delete
                    $(".button__radio").first().addClass("is-select");
                }
            },
            error : function(xhr, status, error){
                errorHandling(xhr, status, error);
            }
        });
    }else{
        $("#DeleteAddress").prop("disabled", false);
    }
};

/** 주소수정 모달 띄우기 **/
function editAddress(addrId){
    modal_show2('modalAddressEdit/'+addrId);
};

function errorHandling(xhr, status, error){
    if(xhr.status == 403){
        window.location.reload();
        //return false;
    }else{
        modalalert(error);
        window.location.href = '/';
    }
}

/** 주소API데이터 입력 **/
function addAddr(zipCode,addr){
    $('#address').val(addr);
    $('#zipcode').val(zipCode);
    $('#address2').focus();
}