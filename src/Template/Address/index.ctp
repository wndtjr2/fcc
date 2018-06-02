<script src="/js/jquery-ui.min.js"></script>

<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip"><?=__("Shipping Address")?></h2>
    <div class="contents">
        <div id="sign">
            <div class="sign__wrap">
                <h3 class="section__title"><span class="inner"><?=__("Shipping Address")?></span></h3>
            <!-- TIP1: 화면 분할, 2 컬럼. -->
                <div class="screen__2column is-zoom">
                    <?=$this->element('usersMenu',[
                    'shipping_addresses'=>'is-current'
                    ]);?>
                    <div class="screen__2column--right">

                        <!-- 분할 컨텐츠. -->
                        <div class="userpage__wrap account">
                            <p class="userpage__title"><?=__("Shipping Address")?></p>

                            <!-- 주소록 -->
                            <ul class="address__lists is-zoom">
                                <li class="list">
                                    <a href="javascript:void(0);" class="address__lists--add" onclick="modal.show('../address/modalAddress');"><span class="icon"></span><?=__("Add New Address")?></a>
                                </li>

                                <!-- TIP2 : 추가된 주소록 -->
                                <?php
                                $i=0;
                                foreach($addressList as $address){ ?>
                                <li class="list">
                                    <dl class="address__list">
                                        <dt>
                                            <?php
                                             if($address->default_addr=="y"){
                                                echo __("Default Address");
                                             }else{
                                                $i++;
                                                echo __("Additional Address")."(".$i.")";
                                             }
                                            ?>
                                        </dt>
                                        <dd class="detail">
                                            <p class="name"><?=$address->deliv_last_name?> <?=$address->deliv_first_name?></p>
                                            <address class="location">
                                                <?=$this->FccTv->addressStr($address->zipcode,$address->address,$address->address2)?>
                                            </address>
                                            <p class="nation"></p>
                                            <p class="phone"><?=__("Phone Number")?> : <?=$address->phone_decrypt?></p>
                                        </dd>
                                        <dd class="func">
                                            <button type="button" class="btn edit editBtn" addrId="<?=$address->id?>"><?=__("Edit")?></button>
                                            <?php if($address->default_addr=="n"){?>
                                                <button type="button" class="btn delete deleteBtn" addrId="<?=$address->id?>"><?=__("Delete")?></button>
                                                <a href="javascript:void(0);" class="btn default makedefaultBtn" addrId="<?=$address->id?>"><?=__("Make Default")?></a>
                                            <?php } ?>
                                        </dd>
                                    </dl>
                                </li>
                                <?php } ?>
                                <!-- TIP2 -->
                            </ul>
                        </div>
                    </div>
                </div>
            <!-- TIP1 -->
            </div>
        </div>

    </div> <!-- contents -->
</section>
<script>

    $(".makedefaultBtn").on("click",function(){
        var idVal = $(this).attr("addrId");
        $.ajax({
            url: '/address/makedefault',
            dataType: 'json',
            type : 'Post',
            data : {addrId : idVal},
            success: function (rtn) {
                location.reload();
                return false;
            }
        });
    });

    $("#resetBtn").on("click",function(){
        $("#addressFrm")[0].reset();
        $("#type").val("regist");
    });
    $(".editBtn").on("click",function(){
        var idVal = $(this).attr("addrId");
        var showModal = modal.show('../address/modalEdit?addrId='+idVal);
        $.when(showModal).done(function(){
//            getDetail(idVal);
        });
    });

    $(".deleteBtn").on("click",function(){
        if(confirm("<?=__("정말 삭제 하겠습니까?")?>")){
            var idVal = $(this).attr("addrId");
            $.ajax({
                url: '/address/deleteAddress',
                dataType: 'json',
                type : 'Post',
                data : {addrId : idVal},
                success: function (rtn) {
                    location.reload();
                    return false;
                }
            });
        }
    });


    function saveAddr() {

        var frmValue = $("#addressFrm").serializeArray();
        var checkForm = true;

        $('.input__box').each(function (){
            fnShowRequired($(this).prop('id'));
            if($(this).val()==''){
                checkForm = false;
            }
        });

        if(checkForm){
            $.ajax({
                url: '/address/add',
                dataType: 'json',
                type : 'Post',
                data : frmValue,
                success: function (rtn) {
                    if(rtn.result==true){
                        location.reload();
                        return false;
                    }
                }
            });
        }
    }

    function fnShowRequired(selector) {
        if($.trim($('#'+selector).val())==''){
            $('#'+selector+'_required').show();
        }else{
            $('#'+selector+'_required').hide();
        }
    }
    function addAddr(zipCode,addr){
        $('#address').val(addr);
        $('#zipcode').val(zipCode);
        $('#address2').focus();
    }
</script>