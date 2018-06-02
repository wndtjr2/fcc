<script src="/js/jquery-validation/dist/jquery.validate.js"></script>
<!--******************************* Contents *******************************-->
<section id="sections">
    <h2 class="is-skip">Password</h2>

    <div class="contents">

        <div id="sign">
            <div class="sign__wrap">

                <div class="create__board">
                    <h3 class="sign__title longText">
                        <strong><?=__("Forgot your E-mail address?")?></strong><br>
                        <span class="sign__title--subtitle"><?=__("아래 정보를 입력하신후 휴대폰 인증을 통해 이메일을 확인하실수 있습니다.")?></span>
                    </h3>

                    <form action="/auth/searchEmail" class="fccJoin__form" id="emailSearch" name="emailSearch" method="post">
                        <input type="hidden" name="plusInfo">
                        <fieldset>
                            <legend class="is-skip"><?=__("Enter your Name")?></legend>
                            <div class="form__division is-zoom">
                                <div class="form__division--divide f2-left">
                                    <label for="lb__name--last" class="input__label--sign"><?=__('Last Name')?></label>
                                    <input type="text" placeholder="<?=__('Last Name')?>" id="lb__name--last" class="input__box" name="last_name" maxlength="90">
                                </div>
                                <div class="form__division--divide f2-right">
                                    <label for="lb__name--first" class="input__label--sign"><?=__('First Name')?></label>
                                    <input type="text" placeholder="<?=__('First Name')?>" id="lb__name--first" class="input__box" name="first_name" maxlength="90">
                                </div>
                            </div>

                            <div class="form__division is-zoom">
                                <label for="lb__phone" class="input__label first"><?=__("휴대폰 인증")?></label>
                                <div class="form__division--divide f2-left">
                                    <input type="text" id="lb__phone" name="phone_number" class="input__box" maxlength="15">
                                    <input type="hidden" id="confirmOk" name="confirmOk">
                                </div>
                                <div class="form__division--divide f2-right">
                                    <a href="javascript:void(0);" id="phoneNoConfirm" class="buttons cancel">
                                        <span class="text" id="phoneNoConfirmBtn" href=""><?=__("인증하기")?></span>
                                        <span class="bg"></span>
                                    </a>
                                </div>
                            </div>

                            <br class="only__web">
                            <br class="only__web">

                            <div class="form__create">
                                <a href="#" class="buttons save" id="submitButton">
                                    <span class="text"><?=__('이메일 찾기')?></span>
                                    <span class="txt"><?=__('이메일 찾기')?></span>
                                    <span class="bg"></span>
                                </a>
                            </div>

                            <br class="only__web">
                            <br class="only__web">
                            <br class="only__web">
                            <br class="only__web">
                        </fieldset>
                    </form>
                </div>

            </div>
        </div> <!-- #sign -->

    </div> <!-- contents -->
</section>
<form id="hiddenFrm" target="mobileConfirm" method="post" action="/auth/userConfirmPop">
    <input type="hidden" id="hUserName" name="userName">
    <input type="hidden" id="hPhoneNo" name="phoneNum">
    <input type="hidden" name="callPage" value="searchId">
</form>
<script>
    function confirmComplete(val) {
        if (val == "Y") {
            $("#phoneNoConfirm").addClass("cancel");
            $("#phoneNoConfirmBtn").text("<?=__("인증완료")?>");
            $("#lb__name--last").attr("readonly", true);
            $("#lb__name--first").attr("readonly", true);
            $("#lb__phone").attr("readonly", true);
        }
    }
    function phoneNoCheck(obj){
        $(obj).val( $(obj).val().replace(/[^0-9]/gi,"") );
        if($("#lb__phone").attr("readonly")!="readonly") {
            if($(obj).val().length >= 10){
                $("#phoneNoConfirm").removeClass("cancel");
            }else{
                $("#phoneNoConfirm").addClass("cancel");
            }
        }
    }
    $("#lb__phone").keyup(function(){
        phoneNoCheck($(this));
    });
    $("#lb__phone").keydown(function(){
        phoneNoCheck($(this));
    });

    $("#submitButton").on("click",function(){

        var fname = $("#lb__name--first").val();
        var lname = $("#lb__name--last").val();

        if(fname == "" || lname == ""){
            modalalert("<?=__("Please enter your name.")?>");
            return false;
        }

        if($("#confirmOk").val()!="Y"){
            modalalert("<?=__("휴대폰 인증이 필요합니다.")?>");
            return false;
        }
        $("#emailSearch").submit();
    });

    $("#phoneNoConfirm").on("click",function(e){

        var fname = $("#lb__name--first").val();
        var lname = $("#lb__name--last").val();

        if(fname == "" || lname == ""){
            modalalert("이름을 입력해주세요.");
            return false;
        }

        if($(this).hasClass("cancel")){
            return false;
        }

        var userName = $("#lb__name--last").val()+$("#lb__name--first").val();
        var phoneNo = $("#lb__phone").val();
        $("#hUserName").val(userName);
        $("#hPhoneNo").val(phoneNo);
        var pop = window.open("","mobileConfirm",'width=425, height=550, resizable=0, scrollbars=no, status=0, titlebar=0, toolbar=0, left=435, top=250');
        if(pop == null){
            modalalert(" ※ 윈도우 XP SP2 또는 인터넷 익스플로러 7 사용자일 경우에는 \n    화면 상단에 있는 팝업 차단 알림줄을 클릭하여 팝업을 허용해 주시기 바랍니다. \n\n※ MSN,야후,구글 팝업 차단 툴바가 설치된 경우 팝업허용을 해주시기 바랍니다.");
        }

        $("#hiddenFrm").submit();

        $("#phone_number_required").hide();

    });

</script>