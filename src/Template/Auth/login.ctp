<script src="/js/jquery-validation/dist/jquery.validate.js"></script>
<script type="text/javascript">
    // common variable
    var defaultOption = 'top=300, left=300, toolbar=no, menubar=no, location=no, directories=no, status=no, resizable=no, scrollbars=yes';

    function JoinFb() {
        window.open('<?php $_SERVER['HTTP_HOST']?>/auth/loginFacebook?type=login&from=page', '_blank', 'width=800, height=500,' + defaultOption);
    }
    function JoinLinkedin(){
        window.open('<?php $_SERVER['HTTP_HOST']?>/auth/loginLinkedin?type=login&from=page', '_blank', 'width=800, height=500,'+ defaultOption);
    }


    //로그인 폼 체크
    function pageLogin() {
        $('#Incorrect').hide();
        $('#Login').submit();
    }

    var submitCheck = true;

    var keycode;

    $(document).ready(function(){
        /* email check rules 추가*/
        $.validator.addMethod("emailUriCheck", function(value) {
            return /\S+@\S+\.\S+/.test(value)
        });
        /* email check rules 추가*/
        $('#Login').validate({
            rules : {
                'email' : {
                    required : true,
                    email : true,
                    emailUriCheck : true
                },
                'password' : {
                    required : true
                    //incorrect : true
                }
            },
            messages : {
                'email' : {
                    required : "<?=__('Please enter your email.')?>",
                    email : "<?=__('Invalid email format.')?>",
                    emailUriCheck : "<?=__('Invalid email format.')?>"
                },
                'password' : {
                    required : "<?=__('Please enter your Password.')?>"
                    //incorrect : 'hi'
                }
            },
            wrapper : 'p',
            submitHandler: function(form){
                submitCheck = false;
                form.submit();
            }
        });
        $('#submitButton').click(function () {
            if(submitCheck){
                $('#Login').submit();
//                $('#Login').submit();
            }else{
                return false;
            }
        });
        $(this).keydown(function (e){
            if(e.keyCode == 13){
                $('#Login').submit();
            }
        });

        //로그인 리다이렉트
        <?php if (isset($this->request->query['redirect'])) {?>
            var redirect = "<?=$this->request->query['redirect']?>";
            $("#lastPage").val(redirect);
            redirectUrl = redirect;
        <?php } elseif(!is_null($this->request->session()->read('Auth.redirect'))){?>
            var authRedirect = "<?=$this->request->session()->read('Auth.redirect')?>";
            $("#lastPage").val(authRedirect);
            redirectUrl = authRedirect;
        <?php } elseif(isset($this->request->query['signes']) and ($this->request->query['signes']) == "noUser"){?>
            var noUser = "/";
            $("#lastPage").val(noUser);
            redirectUrl = noUser;
        <?php } elseif(isset($_SERVER['HTTP_REFERER'])) {?>
            var referer = "<?=$_SERVER['HTTP_REFERER']?>";
            $("#lastPage").val(referer);
            redirectUrl = referer;
        <?php } else {?>
            redirectUrl = "/";
        <?php }?>
    });
</script>
<!--******************************* Contents *******************************-->
<section id="sections">
    <h2 class="is-skip">Sign</h2>

    <div class="contents">

        <div id="sign">
            <div class="sign__wrap">

                <?php $query = $this->request->query?>
                <?php if(isset($query['signes'])) {?>
                    <?php if ($query['signes'] == 'noUser') {?>
                        <p class="sign__warning">
                            <?=__("We couldn't find your account. Would you like to")?> <a href="/auth/join" class="link__belongto--alert"><?=__('Create Account')?></a>
                        </p>
                    <?php } else{?>
                        <p class="sign__warning">
                            <?=__('This account has already been created via {0}', [$query['signes']])?>
                        </p>
                    <?php }?>
                <?php }elseif (isset($query['password']) and ($query['password'] == 'incorrect')){?>
                    <p class="sign__warning">
                        <?=__('Incorrect Password')?>
                    </p>
                <?php }?>
                <?php if(isset($status) and ($status == 'bolt' || $status == 'boltrequest')){?>
                    <p class="sign__warning">

                        <?php
                            if($status=="boltrequest"){
                                echo __('This account was deleted');
                            }else{
                                echo __('이 계정은 탈퇴한 계정입니다.');
                            }
                        ?>
                    </p>
                <?php }?>

                <div class="sign__board">
                    <h3 class="sign__title"><?=__('Sign In')?></h3>

                    <div class="sign__social is-zoom">
                        <div class="left">
                            <a href="#" class="buttons face" onclick="JoinFb()">
                                <span class="text">Facebook</span>
                                <span class="bg"></span>
                            </a>
                        </div>
                        <div class="right">
                            <a href="#" class="buttons linked" onclick="JoinLinkedin()">
                                <span class="text">LinkedIn</span>
                                <span class="bg"></span>
                            </a>
                        </div>
                    </div>

                    <div class="cut__division--or"><span class="text"><b><?=__('or')?></b></span><span class="line"></span></div>

                    <form class="fccJoin__form" id="Login" method="post" action="<?=SSLURL?>/auth/login?types=page">
                        <input type="hidden" name="signup" value="normal">
                        <input id="lastPage" type="hidden" name="lastPage">
                        <fieldset>
                            <legend class="is-skip"><?=__("Sign In, Input your account")?></legend>
                            <div class="form__division">
                                <label for="lb__email" class="input__label"><?=__('Email Address')?></label>
                                <input autocapitalize="off" autocomplete="off" type="email" placeholer="Email" id="lb__email" class="input__box" name="email" value="<?= (isset($email)?$email:'')?>" maxlength="245">
                                <!--<span class="input__validation">Please enter your email.</span>-->
                            </div>
                            <div class="form__division">
                                <label for="lb__password" class="input__label"><?=__('Password')?></label>
                                <input autocomplete=off name="password" type="password" placeholer="Email" id="lb__password" class="input__box" maxlength="245">
                                <!--<span class="input__validation">Please enter your password.</span>-->
                            </div>
                            <div class="form__check">
                                <input type="checkbox" class="input__check" id="li__remember" name="remember" value="check" <?= (isset($email)?'checked':'')?>>
                                <label class="input__label--check" for="li__remember"><?=__('Remember Me')?></label>
                            </div>
                            <div class="form__sign">
                                <a href="#" class="buttons save" id="submitButton">
                                    <span class="text"><?=__('Sign In')?></span>
                                    <span class="bg"></span>
                                </a>
                                <a href="/auth/email_forgot" class="notice__bottom--forget">
                                    <?=__("이메일 찾기")?>
                                </a>
                                <a href="/auth/password_forgot" class="notice__bottom--forget">
                                    <?=__("비밀번호 찾기")?>
                                </a>
                            </div>
                        </fieldset>
                    </form>
                </div>

                <div class="sign__sign">
                    <p><?=__('Not a member yet?')?></p>
                    <a href="/auth/join" class="buttons create">
                        <span class="text"><?=__('join us?')?></span>
                        <span class="bg"></span>
                    </a>
                </div>

            </div>
        </div> <!-- #sign -->

    </div> <!-- contents -->
</section>