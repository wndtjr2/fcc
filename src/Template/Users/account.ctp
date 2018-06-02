<script src="/js/jquery-validation/dist/jquery.validate.js"></script>
<script src="/js/front/modal.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

        //어카운트 탭 밸리데이션
        $.validator.addMethod("passwordTestBasic", function(value) {
            return /^(?=.*?[a-zA-Z])(?=.*?[0-9]).{8,20}$/.test(value)
        });
        $.validator.addMethod("passwordTestRange", function(value) {
            return !/(\w)\1\1\1/.test(value);
        });
        $.validator.addMethod("passwordCompareEmail", function(value) {
            var email = '<?=$this->request->session()->read('Auth.User.user_account.emailDecrypt')?>';//이메일을넣는위치
            if(email == value){
                return false;
            }
            var first = '<?=$this->request->session()->read('Auth.User.first_name')?>';
            var last = '<?=$this->request->session()->read('Auth.User.last_name')?>';
            if(value.indexOf(first) > -1 || value.indexOf(last) > -1){
                return false;
            }
            return true;
        });
        $.validator.addMethod("notEqual", function(value, element) {
            return $('#lb__password').val() != $('#lb__password2').val()
        });

        $('#reset_password').validate({
            rules : {
                'current_password' : {
                    required : true
                },
                'new_password' : {
                    required : true,
                    passwordTestBasic : true,
                    passwordTestRange : true,
                    passwordCompareEmail : true,
                    notEqual : true
                },
                'confirm_password' : {
                    required : true,
                    equalTo: "#lb__password2"
                }
            },
            messages : {
                'current_password' : {
                    required : '<?=__('Please enter your password.')?>'
                },
                'new_password' : {
                    required : '<?=__('Please enter a new password.')?>',
                    passwordTestBasic : "<?=__('• at least one English alphabet letter (a-z, A-Z),• at least one digit number (0-9),• and no spaces.')?>",
                    passwordTestRange : "<?=__('Your password cannot consist of a numerial or alphabetical sequence.')?>",
                    passwordCompareEmail : "<?=__('Your password cannot be the same as your user name or email.')?>",
                    notEqual : "<?=__('New password cannot be the same as your current password')?>"
                },
                'confirm_password' : {
                    required : '<?=__('Please enter your password again.')?>',
                    equalTo: "<?=__('New password and confirmation do not match')?>"
                }
            },
            wrapper : 'p',
            submitHandler: function(form){
                document.getElementById('incorrectPassword').style.visibility = 'hidden';
                $.ajax({
                    type:'post',
                    url: '/users/account',
                    data: $('#reset_password').serialize(),
                    success: function(res){
                        document.getElementById("lb__password").value = "";
                        document.getElementById("lb__password2").value = "";
                        document.getElementById("lb__password3").value = "";
                        if(res == 'complete'){
                            modal.show('../modal/accountChanged');//TODO 하단의 셋타임으로 변경
                            //modal.show('modal_account_changed.html');
//                            setTimeout(function(){
//                                modal_hide();
//                            }, 2500);
                        }else if(res == 'incorrect'){
                            document.getElementById('incorrectPassword').style.visibility = 'visible';
                        }else if(res == 'notSaved'){
                            modal.show('../modal/accountChangeFailed');//TODO 하단의 셋타임으로 변경
//                            setTimeout(function(){
//                                modal_hide();
//                            }, 2500);
                        }
                    }
                })
            }
        });
    });
</script>
<section id="sections">
    <h2 class="is-skip"><?=__('Account')?></h2>

    <div class="contents">

        <div id="sign">
            <div class="sign__wrap">
            <!-- TIP1: 화면 분할, 2 컬럼. -->
                <h3 class="section__title"><span class="inner">나의 계정</span></h3>
                <div class="screen__2column is-zoom">
                    <?=$this->element('usersMenu',[
                        'account'=>'is-current'
                    ]);?>
                    <div class="screen__2column--right">

                        <!-- 분할 컨텐츠. -->
                        <div class="userpage__wrap account">
                            <p class="userpage__title"><?=__('Account')?></p>

                            <!-- Account 컨텐츠 -->
                            <form id="reset_password">
                                <fieldset>
                                    <div class="only__web--account">
                                        <legend class="form__title is-skip"></legend>

                                        <div class="account_email">
                                            <p class="input__label first"><strong><?=__('Email Address')?></strong></p>
                                            <p class="account_email--confirm"><?= $this->request->session()->read('Auth.User.user_account.emailDecrypt')?></p>
                                        </div>

                                        <!-- 이름 -->
                                        <div class="form__division is-zoom">
                                            <div class="form__division--divide f2-left">
                                                <label for="lb__password" class="input__label first"><b><?=__('Change Password')?></b></label>
                                                <label for="lb__password" class="input__label--second"><?=__('Old Password')?></label>
                                                <input type="password" placeholder="" id="lb__password" name="current_password" class="input__box" maxlength="20">
                                                <span id="incorrectPassword" class="input__validation" style="visibility: hidden"><?=__('Incorrect Password')?></span>
                                            </div>
                                            <div class="form__division--divide f2-right"></div>
                                        </div>

                                        <div class="form__division is-zoom">
                                            <div class="form__division--divide f2-left">
                                                <label for="lb__password2" class="input__label--second"><?=__('New Password')?></label>
                                                <input type="password" placeholder="" id="lb__password2" name="new_password" class="input__box" maxlength="20">
                                                <!--<span class="input__validation">at least one English alphabet letter (a-z, A-Z),</span>-->
                                            </div>
                                            <div class="form__division--divide f2-right">
                                                <label for="lb__password3" class="input__label--second"><?=__('Confirm Password')?></label>
                                                <input type="password" placeholder="" id="lb__password3" name="confirm_password" class="input__box" maxlength="20">
                                                <!--<span class="input__validation">New password and confirmation do not match</span>-->
                                            </div>
                                        </div>
                                        <div class="form__division is-zoom">
                                            <span class="input__guide"><?=__('Password must be 8-20 characters using both numbers and letters.')?></span>
                                        </div>

                                        <div class="form__submit--left">
                                            <button id="DoneButton" type="button" class="buttons save" onclick="event.preventDefault(); $('#reset_password').submit();">
                                                <span class="text"><?=__('Change')?></span>
                                                <span class="size"><?=__('Change')?></span>
                                                <span class="bg"></span>
                                            </button>

                                            <div class="account_delete">
                                                <p class="input__label first"><strong><?=__('Delete Account')?></strong></p>
                                                <p class="account_delete--noti"><?=__('Your account will be deleted 10 days <br> after the request is submitted.')?></p>
                                                <button href="#" type="button" class="buttons more"  data-toggle="modal" aria-hidden="true" data-target="#deleteYourAccount" onclick="event.preventDefault(); deleteRequest()">
                                                    <span class="text"><?=__('Delete')?></span>
                                                    <span class="size"><?=__('Delete')?></span>
                                                    <span class="bg"></span>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </fieldset>
                            </form>

                        </div>
                    </div>
                </div>
            <!-- TIP1 -->
            </div>
        </div>

    </div> <!-- contents -->
</section>

<script>
    function deleteRequest(){
        modal_show2('accountDelete');
    }
</script>
<script>
    function deleteAccount(){
        $.ajax({
            type:'post',
            url: '/users/delete',
            success: function(rtn){
                if (rtn == 'ok') {
                    //$('#deleteYourAccount').modal('hide');
                    modal_show2('accountBye');
                    setTimeout(function(){
                        location.href = '/';
                    }, 5000);
                } else {
                    modalalert("<?=__("Internal Server Error. Please try again later.")?>");
                }

            }
        });
    }
</script>