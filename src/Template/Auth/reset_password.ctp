<script src="/js/jquery-validation/dist/jquery.validate.js"></script>
<script type="text/javascript">
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
            var email = '<?= $email?>';
            var firstName = '<?= $user->first_name?>';
            var lastName = '<?= $user->last_name?>';
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
        $('#fccJoin__form').validate({
            rules : {
                'new_password' : {
                    required : true,
                    passwordTestBasic : true,
                    passwordTestRange : true,
                    passwordTestEmail : true,
                    passwordWhiteSpace : true
                },
                'confirm_password' : {
                    required : true,
                    equalTo: "#lb__password"
                }
            },
            messages : {
                'new_password' : {
                    required : "<?=__('Please enter your password.')?>",
                    passwordTestBasic : "<?=__('• at least one English alphabet letter (a-z, A-Z),• at least one digit number (0-9),• and no spaces.')?>",
                    passwordTestRange : "<?=__('Your password cannot consist of a numerial or alphabetical sequence.')?>",
                    passwordTestEmail : "<?=__('Your password cannot be the same as your user name or email.')?>",
                    passwordWhiteSpace : "<?=__('• at least one English alphabet letter (a-z, A-Z),• at least one digit number (0-9),• and no spaces.')?>"
                },
                'confirm_password' : {
                    required : "<?=__('Please enter your password again.')?>",
                    equalTo: "<?=__('New password and confirmation do not match')?>"
                }
            },
            wrapper : 'p',
            submitHandler: function(form){
                form.submit();
            }
        });
    });

    function reset_password() {
        $('#fccJoin__form').submit();
    }
</script>


<!--******************************* Contents *******************************-->
<section id="sections">
    <h2 class="is-skip">Password</h2>
    <div class="contents">
        <div id="sign">
            <div class="sign__wrap">
                <div class="create__board">
                    <h3 class="sign__title longText">
                        <strong><?=__('Create a new password.')?></strong>
                    </h3>
                    <?php $token = $this->request->query['token']?>
                    <form id="fccJoin__form" action="/resetPassword?token=<?=$token?>" method="post">
                        <fieldset>
                            <legend class="is-skip">Enter your email</legend>

                            <div class="form__division">
                                <label for="lb__password" class="input__label--sign"><?=__('New Password')?></label>
                                <input type="password" placeholer="Email" id="lb__password" class="input__box"  name="new_password" maxlength="20">
                                <span class="input__guide"><?=__('Password must be 8-20 characters using both numbers and letters.')?></span>
                                <!--<span class="input__validation">at least one English alphabet letter (a-z, A-Z),</span>-->
                                <!--<span class="input__validation">at least one digit number (0-9),</span>-->
                                <!--<span class="input__validation">and no spaces.</span>-->
                            </div>

                            <div class="form__division">
                                <label for="lb__password2" class="input__label--sign"><?=__('Confirm Password')?></label>
                                <input type="password" placeholer="Email" id="lb__password2" class="input__box" name="confirm_password" maxlength="20">
                                <!--<span class="input__validation">Please enter your password again.</span>-->
                            </div>

                            <br class="only__web">
                            <br class="only__web">

                            <div class="form__create">
                                <a href="#" onclick="reset_password()" class="buttons save">
                                    <span class="text"><?=__('Reset Password')?></span>
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