<script src="/js/jquery-validation/dist/jquery.validate.js"></script>
<style>.hide{display:none}</style>
<script type="text/javascript">
    function termsCheckOk(){
        if(!$("#lb_termsok").is(":checked")){
            $("#lb_termsok").focus();
            modalalert('<?=__("이용약관에 동의 하셔야 합니다.")?>');
            return false;
        }

        return true;
    }

    $(document).ready(function(){
        $.validator.addMethod("passwordTestBasic", function(value) {
            return /^(?=.*[a-zA-Z])(?=.*[0-9]).{8,20}$/.test(value);
        });
        $.validator.addMethod("passwordTestRange", function(value) {
            return !/(\w)\1\1\1/.test(value);
        });
        $.validator.addMethod("passwordWhiteSpace", function(value) {
            return value.indexOf(" ") < 0 && value != "";
        });
        $.validator.addMethod("passwordTestEmail", function(value) {
            var email = '<?=$this->request->session()->read('user.verify.email')?>';//이메일을넣는위치
            return email == value ? false : true;
        });
        $('#firstform').validate({
            rules : {
                'password' : {
                    required : true,
                    passwordTestBasic : true,
                    passwordTestRange : true,
                    passwordTestEmail : true,
                    passwordWhiteSpace : true
                }
            },
            messages : {
                'password' : {
                    required : "<?=__('Please enter your password.')?>",
                    passwordTestBasic : "<?=__('• at least one English alphabet letter (a-z, A-Z),• at least one digit number (0-9),• and no spaces.')?>",
                    passwordTestRange : "<?=__('Your password cannot consist of a numerial or alphabetical sequence.')?>",
                    passwordTestEmail : "<?=__('Your password cannot be the same as your user name or email.')?>",
                    passwordWhiteSpace : "<?=__('• at least one English alphabet letter (a-z, A-Z),• at least one digit number (0-9),• and no spaces.')?>"
                }
            },
            wrapper : 'p',
            submitHandler: function(form){
                if(validateName()){
                    $("#firstForm").submit();
                }
            }
        });
        $(this).keydown(function (e){
            if(e.keyCode == 13){
                if(validateName()){
                    $("#firstForm").submit();
                }
            }
        });
    });

    function reset_password() {
         if(validateName()){
             $("#firstForm").submit();
        }
    }

    <?php if(isset($loggedIn)){ ?>
    window.opener.location.href = "/auth/join";
    window.close();
    <?php } ?>

    function validateName(){
        var errorcheck = true;
        if(!termsCheckOk()){
            return false;
        }
        $('#first_name_required').hide();
        $('#last_name_required').hide();
        if($('#lb__name--first').val()==''){
            $('#first_name_required').show();
            errorcheck = false;
        }
        if($('#lb__name--last').val()==''){
            $('#last_name_required').show();
            errorcheck = false;
        }
        if($("#nickCheckOk").val()!="Y"){
            $("#nickname_msg").text("<?=__("닉네임을 중복체크 해주세요.")?>");
            $("#nickname_msg").show();
            $("#nickname").focus();
            errorcheck = false;
        }

        if($("#confirmOk").val()!="Y"){
            $("#phone_number_required").show();
            $("#lb__phone").focus();
            errorcheck = false;
        }

        return errorcheck;
    }
</script>

<!--******************************* Contents *******************************-->

<article class="section__article">
    <h2 class="section__title"></h2> <!-- z-index: 500 -->

    <div id="contents">

        <div id="sign">
            <div class="sign__wrap">

                <div class="sign__board">
                    <h3 class="sign__title"><?=__('Create Account')?></h3>
                    <form class="fccJoin__form" id="firstForm" name="firstForm" action="/auth/setPassword" method="post">
                        <input type="hidden" name="plusInfo">
                        <?php
                        $query = $this->request->query;
                        //query 없을시 루트로 이동
                        if(empty($query) or $query == ''){
                            header("Location: ".$this->Url->build('/'));
                        }
                        $email = str_replace(' ', '+', $query['e']);
                        ?>
                        <input type="hidden" name="email" value="<?=$email?>">
                        <input type="hidden" name="sns" value="<?=$query['sn']?>">

                        <div class="orders__terms">
                            <p class="orders__terms--text">
                                <?php include($_SERVER['DOCUMENT_ROOT']."/termsofuse/terms_of_use.php");?>
                            </p>
                            <div class="chekboxs infor__8th">
                                <div class="checks">
                                    <input type="checkbox" id="lb_termsok">&nbsp;
                                    <label class="text" for="lb_termsok"><?=__("위 약관에 동의합니다.")?></label>
                                </div>
                            </div>
                        </div>

                        <fieldset>
                            <legend class="is-skip"><?=__("Create your account")?></legend>
                            <div class="form__division is-zoom">
                                <!--                                <label for="lb__name--first" class="input__label first">--><?//=__('Name')?><!--</label>-->
                                <div class="form__division--divide f2-left">
                                    <label for="lb__name--last" class="input__label--sign"><?=__('Last Name')?></label>
                                    <input type="text" placeholder="<?=__('Last Name')?>" id="lb__name--last" class="input__box" name="last_name" maxlength="90" >
                                    <span class="input__validation hide" id="last_name_required"><?=__('Please enter your last name.')?></span>
                                </div>
                                <div class="form__division--divide f2-right">
                                    <label for="lb__name--first" class="input__label--sign"><?=__('First Name')?></label>
                                    <input type="text" placeholder="<?=__('First Name')?>" id="lb__name--first" class="input__box" name="first_name" maxlength="90" >
                                    <span class="input__validation hide" id="first_name_required"><?=__('Please enter your first name.')?></span>
                                </div>
                            </div>

                            <!-- 닉네임 -->
                            <div class="form__division is-zoom">
                                <label for="first_name" class="input__label"><?=__("Nickname")?></label>
                                <div class="form__division--divide f2-left">
                                    <label for="" class="input__label--second"></label>
                                    <input type="text" placeholder="닉네임" class="input__box " id="nickname" name="nickname" value="">
                                    <input type="hidden" name="nickCheckOk" id="nickCheckOk">
                                    <span class="input__validation hide" id="nickname_msg"><?=__("닉네임을 중복체크 해주세요.")?></span>
                                </div>
                                <div class="form__division--divide f2-right">
                                    <label for="" class="input__label--second"></label>
                                    <a href="javascript:void(0);" id="nicknameCheck" class="buttons cancel">
                                        <span class="text" href=""><?=__("중복체크")?></span>
                                        <span class="bg"></span>
                                    </a>
                                </div>
                            </div>

                            <div class="form__division">
                                <label for="lb__password" class="input__label first"><?=__('Password')?></label>
                                <input type="password" placeholder="" id="lb__password" class="input__box" name="password" maxlength="20">
                                <span class="input__guide"><?=__('Password must be 8-20 characters using both numbers and letters.')?></span>
                            </div>

                            <div class="form__division is-zoom">
                                <label for="lb__phone" class="input__label first"><?=__("휴대폰 인증")?></label>
                                <div class="form__division--divide f2-left">
                                    <input type="text" id="lb__phone" name="phone_number" class="input__box" maxlength="15">
                                    <input type="hidden" id="confirmOk" name="confirmOk">
                                    <span class="input__validation hide" id="phone_number_required"><?=__('Please Confirm your Mobile Number.')?></span>
                                </div>
                                <div class="form__division--divide f2-right">
                                    <a href="javascript:void(0);" id="phoneNoConfirm" class="buttons cancel">
                                        <span class="text" href=""><?=__("인증하기")?></span>
                                        <span class="bg"></span>
                                    </a>
                                </div>
                            </div>

                            <div class="form__division">
                                <label for="lb_birthday" class="input__label first"><?=__("Birth Date")?></label>
                                <input type="text" id="lb_birthday" name="birthday" class="input__box"  maxlength="10" readonly>
                            </div>

                            <br>

                            <br class="only__web">

                            <div class="form__create">
                                <a href="javascript:void(0);" id="formsave" class="buttons sign">
                                    <span class="text"><?=__('Create Account')?></span>
                                    <span class="bg"></span>
                                </a>
                                <p class="create__policy">
                                    <?=__('By creating an account, I accept FCC’s.<br>Terms and Conditions and Privacy Policy.')?>
                                </p>
                            </div>
                        </fieldset>
                    </form>
                </div>

            </div>
        </div> <!-- #sign -->

    </div>

</article>

<form id="hiddenFrm" target="mobileConfirm" method="post" action="/auth/userConfirmPop">
    <input type="hidden" id="hUserName" name="userName">
    <input type="hidden" id="hPhoneNo" name="phoneNum">
    <input type="hidden" name="callPage" value="join">
</form>
<script>
    $("#formsave").on("click",function(){
        reset_password();
    });
    function confirmComplete(val) {
        if (val == "Y") {
            $("#phoneNoConfirm").addClass("cancel");
            $("#phoneNoConfirm").find(".text").text("<?=__("인증완료")?>");
            $("#lb__name--last").attr("readonly", true);
            $("#lb__name--first").attr("readonly", true);
            $("#lb__phone").attr("readonly", true);
        }
    }

    function phoneNoCheck(obj){
        if($("#confirmOk").val()!="Y") {
            $(obj).val($(obj).val().replace(/[^0-9]/gi, ""));
            if ($(obj).val().length >= 10) {
                $("#phoneNoConfirm").removeClass("cancel");
            } else {
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

    $("#phoneNoConfirm").on("click",function(e){
        var duplicateNum = false;
        $.ajax({
            url: '/auth/mobileNumCheck',
            type: 'POST',
            data: {
                phone_number: $("#lb__phone").val()
            },
            async : false,
            dataType: 'json',
            success: function (data) {
                if(data.result==true){
                    duplicateNum= false;
                }else{
                    duplicateNum = true;
                }
            }
        });

        if(duplicateNum==true){
            modalalert('<?=__("이미 등록된 전화번호 입니다.")?>');
            return false;
        }

        var fname = $("#lb__name--first").val();
        var lname = $("#lb__name--last").val();

        if(fname == "" || lname == ""){
            modalalert("<?=__("Please enter your name.")?>")
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

    $("#nickname").on("change",function(){
        if($(this).val()!="") {
            $("#nickname_msg").text("<?=__("닉네임을 중복 체크해주세요.")?>");
            $("#nickname_msg").show();
            $("#nicknameCheck").find(".text").text("<?=__("중복체크")?>");
            $("#nicknameCheck").removeClass("cancel");
        }
    });

    $("#nicknameCheck").on("click",function(){
        if($(this).hasClass("cancel")){
            return false;
        }
        var nicknameVal = $("#nickname").val();
        if(nicknameVal==""){
            $("#nickname_msg").text("<?=__("닉네임을 입력해주세요.")?>");
            $("#nickname_msg").show();
            return false;
        }
        $.ajax({
            url: '/auth/nickNameCheck',
            type: 'POST',
            data: {
                nickname: nicknameVal
            },
            async : false,
            dataType: 'json',
            success: function (data) {
                if(data.result==true){
                    $("#nickCheckOk").val("Y");
                    $("#nickname_msg").text("<?=__("사용 가능한 닉네임 입니다.")?>");
                    $("#nickname_msg").show();
                    $("#nicknameCheck").addClass("cancel");
                    $("#nicknameCheck").find(".text").text("<?=__("중복 체크완료")?>");
                }else{
                    $("#nickCheckOk").val("N");
                    $("#nickname_msg").text("<?=__("이미 사용중인 닉네임 입니다.")?>");
                    $("#nickname_msg").show();
                }
            }
        });
    });
</script>