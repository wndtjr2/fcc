<script src="/js/jquery-validation/dist/jquery.validate.js"></script>
<style>.hide{display:none}</style>
<script type="text/javascript">
    // common variable
    $(document).ready(function(){


        var defaultOption = 'top=300, left=300, toolbar=no, menubar=no, location=no, directories=no, status=no, resizable=no, scrollbars=yes';

        function termsCheckOk(){

            if(!$("#lb_policy_ok").is(":checked")){
                $("#lb_policy_ok").focus();
                modalalert('이용약관 에 동의 하셔야 합니다.')
                return false;
            }

            if(!$("#lb_termsok").is(":checked")){
                $("#lb_termsok").focus();
                modalalert('개인정보 취급방침 에 동의 하셔야 합니다.')
                return false;
            }

            return true;
        }

        $(".face").on("click",function(){
            window.open('<?php $_SERVER['HTTP_HOST']?>/auth/loginFacebook?type=join&from=page', '_blank', 'width=800, height=500,' + defaultOption);
        });

        $(".linked").on("click",function(){
            window.open('<?php $_SERVER['HTTP_HOST']?>/auth/loginLinkedin?type=join&from=page', '_blank', 'width=800, height=500,' + defaultOption);
        });


        //Join 폼

        /* email check rules 추가*/
        $.validator.addMethod("emailUriCheck", function(value) {
            return /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/.test(value)
        });
        /* email check rules 추가*/
        $('#id').hide();
        $('#fccJoin').validate({
            rules : {
                'email' : {
                    required : true,
                    email : true,
                    emailUriCheck : true
                }
            },
            messages : {
                'email' : {
                    required : "<?=__('Please enter your email.')?>",
                    email : "<?=__('Invalid email format.')?>",
                    emailUriCheck : "<?=__('Invalid email format.')?>"
                }
            },
            wrapper : 'p'
//            submitHandler: function(form){
//                submitCheck = false;
//            }
        });

        //join 폼 End

        //firstTime 폼

        $.validator.addMethod("passwordTestBasic", function(value) {
            return /^(?=.*?[a-zA-Z])(?=.*?[0-9]).{8,20}$/.test(value);
        });
        $.validator.addMethod("passwordTestRange", function(value) {
            return !/(\w)\1\1\1/.test(value);
        });

        $.validator.addMethod("passwordWhiteSpace", function(value) {
            return value.indexOf(" ") < 0 && value != "";
        });

        $.validator.addMethod("passwordCompareEmail", function(value) {

            var email = document.getElementById('lb__email').value;//이메일을넣는위치
            var firstName = document.getElementById('lb__name--first').value;
            var lastName = document.getElementById('lb__name--last').value;
            if(email == value){
                return false;
            }
            if(value.search(firstName) > -1){
                if(firstName == ''){
                    return true;
                }
                return false;
            }
            if(value.search(lastName) > -1){
                if(lastName == ''){
                    return true;
                }
                return false;
            }
            return true;
        });
        $('#firstForm').validate({
            rules : {
                'password' : {
                    required : true,
                    passwordTestBasic : true,
                    passwordTestRange : true,
                    passwordCompareEmail : true,
                    passwordWhiteSpace : true
                }
            },
            messages : {
                'password' : {
                    required : "<?=__('Please enter your password.')?>",
                    passwordTestBasic : "<?=__('• at least one English alphabet letter (a-z, A-Z),• at least one digit number (0-9),• and no spaces.')?>",
                    passwordTestRange : "<?=__('Your password cannot consist of a numerial or alphabetical sequence.')?>",
                    passwordCompareEmail : "<?=__('Your password cannot be the same as your user name or email.')?>",
                    passwordWhiteSpace : "<?=__('• at least one English alphabet letter (a-z, A-Z),• at least one digit number (0-9),• and no spaces.')?>"
                }
            },

            wrapper : 'p',
            submitHandler: function(form){
                var repeat = false;
                if(validateName()){
                    if(repeat) return;

                    if(termsCheckOk()) {
                        if(confirm('입력하신 정보대로 가입하시겠습니까?')){
                            form.submit();
                            repeat = true;
                        }
                    }
                    return false;
                }else{
                    return false;
                }
            }
        });

        $(this).keydown(function (e){
            if(e.keyCode == 13){
                event.preventDefault();
                $("#signJoin").click();
            }
        });

        /*/
        $(this).keydown(function (e){
            if(e.keyCode == 13){
                $('#firstForm').submit();
            }
        });
        /*/

        function validateName(){
            var errorcheck = true;
//            $('#full_name_required').hide();
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

            if($("#confirmOk").val()!="Y"){
                $("#phone_number_required").show();
                $("#lb__phone").focus();
                errorcheck = false;
            }

            if($("#nickCheckOk").val()!="Y"){
                $("#nickname_msg").text("닉네임을 중복체크 해주세요.");
                $("#nickname_msg").show();
                $("#nickname").focus();
                errorcheck = false;
            }


            if($("#lb__password").val()!=$("#lb__password_chk").val()){
                $("#password_check").text('비밀번호가 일치하지 않습니다.');
                $("#lb__password_chk").focus();
                errorcheck =false;
            }

            return errorcheck;
        }

        $("#signJoin").on("click",function(){
                if ($('#fccJoin').valid()) {
                    var email = document.getElementById('lb__email').value;
                    var signup = document.getElementById('signup').value;

                    $.ajax({
                        url: '/auth/checkEmailIfExist',
                        type: 'POST',
                        data: {
                            email: email
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data == 'new') {
                                document.getElementById('firstEmail').value = email;
                                document.getElementById('firstSignup').value = signup;
                                $('#join').hide();
                                $('#first_time').show();
                            } else if (data == 'bolt') {
                                document.location.href = '/Auth/join?status=bolt';
                            } else {
                                document.location.href = '/Auth/join?signup=' + data;
                            }
                        }
                    });
                }
        });

        $("#nickname").on("change",function(){
            if($(this).val()!="") {
                $("#nickname_msg").text("닉네임을 중복 체크해주세요.");
                $("#nickname_msg").show();
                $("#nicknameCheck").removeClass("cancel");
            }
        });

        $("#nicknameCheck").on("click",function(){
            if($(this).hasClass("cancel")){
                return false;
            }
            var nicknameVal = $("#nickname").val();
            if(nicknameVal==""){
                $("#nickname_msg").text("닉네임을 입력해주세요.");
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
                        $("#nickname_msg").text("사용 가능한 닉네임 입니다.");
                        $("#nickname_msg").show();
                        $("#nicknameCheck").addClass("cancel");
                        $("#nicknameCheck").find(".text").text("중복 체크완료");
                    }else{
                        $("#nickCheckOk").val("N");
                        $("#nickname_msg").text("이미 사용중인 닉네임 입니다.");
                        $("#nickname_msg").show();
                    }
                }
            });
        });
    });


    function createAccount() {
        $('#firstForm').submit();
    }

</script>

<section class="section__article" id="join">
    <h2 class="is-skip"><?=__('Account')?></h2>

    <div class="contents">

        <div id="sign">
            <div class="sign__wrap">
                <?php $query = $this->request->query?>
                <?php if(!empty($query)) :?>
                    <?php if(isset($query['signup'])):?>
                    <p class="sign__warning">
                        <?php switch ($query['signup']){
                                    case 'email':
                                        $type = 'email';
                                        break;
                                    case 'linkedin':
                                        $type = 'LinkedIn';
                                        break;
                                    case 'facebook':
                                        $type = 'Facebook';
                                        break;
                                }?>
                        <?=__('This account has already been created via {0}', [$type])?>
                        <br>
                        <a href="/auth/login" class="link__belongto--alert"><?=__('Sign In')?></a>
                    </p>
                    <?php endif; ?>
                    <?php if(isset($query['status']) and ($query['status'] == 'bolt')):?>
                    <p class="sign__warning">
                        <?=__('This account was deleted')?>
                    </p>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="sign__board">
                    <h3 class="sign__title"><?=__('Create Account')?></h3>

                   <div class="sign__social is-zoom">
                        <div class="left">
                            <a class="buttons face" href="javascript:void(0);">
                                <span class="text">Facebook</span>
                                <span class="bg"></span>
                            </a>
                        </div>
                        <div class="right">
                            <a class="buttons linked" href="javascript:void(0);">
                                <span class="text">LinkedIn</span>
                                <span class="bg"></span>
                            </a>
                        </div>
                    </div>

                    <div class="cut__division--or"><span class="text"><b><?=__('or')?></b></span><span class="line"></span></div>

                    <form action="/auth/join" method="post" id="fccJoin" name="fccJoinName">
                        <fieldset>
                            <legend class="is-skip">Create your account</legend>
                            <div class="form__division">
                                <label for="lb__email" class="input__label"><?=__('Email Address')?></label>
                                <input autocapitalize="off" autocomplete="off" type="email" placeholer="Email" id="lb__email" class="input__box" name="email" maxlength="245">
                                <input type="hidden" id="signup" value="1">
                            </div>
                            <div class="form__sign">
                                <a class="buttons sign" id="signJoin" href="javascript:void(0)">
                                    <span class="text"><?=__('Create Account')?></span>
                                    <span class="bg"></span>
                                </a>
                            </div>
                            <br>
                            <br>
                        </fieldset>
                    </form>
                </div>

                <div class="sign__sign">
                    <p><?=__('Are you a member?')?></p>
                    <a href="/auth/login" class="buttons create">
                        <span class="text"><?=__('Sign In')?></span>
                        <span class="bg"></span>
                    </a>
                </div>

            </div>
        </div> <!-- #sign -->

    </div> <!-- contents -->
</section>





<section class="section__article hide" id="first_time">
    <h2 class="is-skip"><?=__('Account')?></h2>

    <div class="contents">

        <div id="sign">
            <div class="sign__wrap">

                <div class="sign__board">
                    <h3 class="sign__title"><?=__('Create Account')?></h3>
                       <form action="<?=SSLURL?>/auth/join" id="firstForm" name="firstForm" class="fccJoin__form" method="post">
                           <input type="hidden" name="plusInfo">
                           <div class="orders__terms">
                               <p class="title"><?=__("이용약관 동의")?></p>

                               <p class="orders__terms--text"><?php include($_SERVER['DOCUMENT_ROOT']."/termsofuse/policy.php");?></p>
                               <div class="chekboxs infor__8th">
                                   <div class="checks">
                                       <input type="checkbox" id="lb_policy_ok">&nbsp;
                                       <label class="text" for="lb_policy_ok"><?=__("동의합니다.")?></label>
                                   </div>
                               </div>

                               <p class="title"><?=__("개인정보 취급방침 동의")?></p>
                               <p class="orders__terms--text"><?php include($_SERVER['DOCUMENT_ROOT']."/termsofuse/terms_of_use.php");?></p>
                               <div class="chekboxs infor__8th">
                                   <div class="checks">
                                       <input type="checkbox" id="lb_termsok">&nbsp;
                                       <label class="text" for="lb_termsok"><?=__("동의합니다.")?></label>
                                   </div>
                               </div>
                           </div>

                           <input type="hidden" id="firstEmail" name="email">
                        <input type="hidden" id="firstSignup" name="signup">
                        <fieldset>
                            <legend class="is-skip"><?=__("Create your account")?></legend>
                            <div class="form__division is-zoom">
                                <div class="form__division--divide f2-left">
                                    <label for="lb__name--last" class="input__label"><?=__('Last Name')?></label>
                                    <input type="text" placeholder="<?=__('Last Name')?>" id="lb__name--last" class="input__box" name="last_name" maxlength="90">
                                    <span class="input__validation hide" id="last_name_required"><?=__('Please enter your last name.')?></span>
                                </div>
                                <div class="form__division--divide f2-right">
                                    <label for="lb__name--first" class="input__label"><?=__('First Name')?></label>
                                    <input type="text" placeholder="<?=__('First Name')?>" id="lb__name--first" class="input__box" name="first_name" maxlength="90">
                                    <span class="input__validation hide" id="first_name_required"><?=__('Please enter your first name.')?></span>
                                </div>
                            </div>

                            <!-- 닉네임 -->
                            <div class="form__division is-zoom">
                                <label for="first_name" class="input__label"><?=__("닉네임")?></label>
                                <div class="form__division--divide f2-left">
                                    <label for="" class="input__label--second"></label>
                                    <input type="text" placeholder="닉네임" class="input__box " id="nickname" name="nickname" value="" maxlength="10">
                                    <input type="hidden" name="nickCheckOk" id="nickCheckOk">
                                    <span class="input__validation hide" id="nickname_msg"><?=__("닉네임을 중복 체크해주세요.")?></span>
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

                            <div class="form__division">
                                <label for="lb__password" class="input__label first"><?=__("비밀번호확인")?></label>
                                <input type="password" placeholder="" id="lb__password_chk" class="input__box" name="password_chk" maxlength="20">
                                <span class="input__guide" id="password_check"><?=__("비밀번호를 한번더 입력해주세요.")?></span>
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
                                        <span class="text" id="phoneNoConfirmBtn" href=""><?=__("인증하기")?></span>
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
                                <a href="javascript:void(0);" onclick="createAccount();" class="buttons sign">
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

    </div> <!-- contents -->
</section>

<form id="hiddenFrm" target="mobileConfirm" method="post" action="/auth/userConfirmPop">
    <input type="hidden" id="hUserName" name="userName">
    <input type="hidden" id="hPhoneNo" name="phoneNum">
    <input type="hidden" name="callPage" value="join">
</form>
<script>
    function confirmComplete(val) {
        if (val == "Y") {
            $("#phoneNoConfirm").addClass("cancel");
            $("#phoneNoConfirmBtn").text("인증완료");
            $("#lb__name--last").attr("readonly", true);
            $("#lb__name--first").attr("readonly", true);
            $("#lb__phone").attr("readonly", true);
        }
    }

    function phoneNoCheck(obj){
        $(obj).val( $(obj).val().replace(/[^0-9]/gi,"") );
        if($(obj).val().length >= 10){
            $("#phoneNoConfirm").removeClass("cancel");
        }else{
            $("#phoneNoConfirm").addClass("cancel");
        }
    }
    $("#lb__phone").keyup(function(){
        phoneNoCheck($(this));
    });
    $("#lb__phone").keydown(function(){
        phoneNoCheck($(this));
    });

    var duplicateNum = false;
    function dupCheckPNo(){
        if(duplicateNum==true){
            modalalert("이미 등록된 전화번호 입니다.");
            return false;
        }

        return true;
    }
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

        $.ajax({
            url: '/auth/mobileNumCheck',
            type: 'POST',
            data: {
                phone_number: $("#lb__phone").val(),
            },
            async : false,
            dataType: 'json',
            success: function (data) {
                if(data.result==true){
                    duplicateNum= false;
                }else{
                    duplicateNum = true;
                }

                if(dupCheckPNo()){
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
                }
            }
        });

    });

</script>