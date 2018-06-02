<style>
    .hide{display:none}
</style>
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

    $.validator.addMethod("passwordCompareEmail", function(value) {

        var email = '<?=$this->request->session()->read('Auth.User.user_account.emailDecrypt')?>';//이메일을넣는위치
        var firstName = document.getElementById('FirstName').value;
        var lastName = document.getElementById('LastName').value;
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
    $('#loginForm').validate({
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
                passwordTestBasic : "<?=__('Show Password Errors')?>",
                passwordTestRange : "<?=__('Your password cannot consist of a numerial or alphabetical sequence.')?>",
                passwordCompareEmail : "<?=__('Your password cannot be the same as your user name or email.')?>",
                passwordWhiteSpace : "<?=__('Show Password Errors')?>"
            }
        },
        wrapper : 'p',
        submitHandler: function(form){
            var repeat = false;
            if(validateName()){
                if(repeat) return;
                form.submit();
                repeat = true;
            }else{
                return false;
            }
        }
    });

    $(this).keydown(function (e){
        if(e.keyCode == 13){
            $('#loginForm').submit();
        }
    });

    function validateName(){
        var errorcheck = true;
        $('#full_name_required').hide();
        $('#first_name_required').hide();
        $('#last_name_required').hide();
        if(($('#FirstName').val()=='') & ($('#LastName').val()=='')){
            $('#full_name_required').show();
            errorcheck = false;
        }else{
            if($('#FirstName').val()==''){
                $('#first_name_required').show();
                errorcheck = false;
            }
            if($('#LastName').val()==''){
                $('#last_name_required').show();
                errorcheck = false;
            }
        }
        return errorcheck;
    }
});

function fnSendVerification() {
    $('#loginForm').submit();
}
</script>

<!--******************************* Contents *******************************-->
<div class="l-fccJoin">
    <div class="fccJoin">
        <div class="fccJoin__head">
            <h3><?=__('JOIN')?></h3>
            <!--                <p class="accountNotice"><img src="/img/fcc/common/icon_warning2.png" alt="" class="warning">&nbsp; This account has already been created via LinkedIn</p>-->
        </div>
        <form action="/users/firstTimeJoin" id="loginForm" class="fccJoin__form" method="post">
            <input type="hidden" name="signup" value="normal">
            <fieldset>
                <legend class="is-skip">FCC Join : create your account</legend>
                <div class="row">
                    <label for="labelName_1"><?=__('Name')?></label>
                    <input class="is-divide left" type="text" id="FirstName" placeholder="<?=__('First name')?>" name="first_name" maxlength="90">
                    <input class="is-divide right" type="text" id="LastName" placeholder="<?=__('Last name')?>" name="last_name" maxlength="90">
                    <p class="is-divide left hide" id="first_name_required">
                        <label for="first_name"><?=__('Please enter your first name.')?></label>
                    </p>
                    <p class="is-divide right hide" id="last_name_required">
                        <label for="last_name"><?=__('Please enter your last name.')?></label>
                    </p>
                    <p class="is-divide left hide" id="full_name_required">
                        <label for="full_name"><?=__('Please enter your name.')?></label>
                    </p>
                    <?= $this->request->params['pass'][1]?>
                    <input type="hidden" name="email" value="<?=$this->request->params['pass'][0]?>">
                    <input type="hidden" name="signup" value="<?=$this->request->params['pass'][1]?>">
                    <!--                        <p>-->
                    <!--                            <label for="labelName_1">Please enter your first name.</label>-->
                    <!--                            <label for="labelName_2">Please enter your last names.</label>-->
                    <!--                            <label for="labelName_1">Please enter your first and last names.</label>-->
                    <!--                        </p>-->
                    <!--                        <p>-->
                    <!--                            <label for="labelName_1">Please enter your first name.</label>-->
                    <!--                            <label for="labelName_2">Please enter your last names.</label>-->
                    <!--                            <label for="labelName_1">Please enter your first and last names.</label>-->
                    <!--                        </p>-->
                </div>
                <div class="row">
                    <label for="labelPassword_1"><?=__('Password')?></label>
                    <input type="password" id="labelPassword_1" placeholder="<?=__('Password')?>" name="password" maxlength="20">
                    <p class="noti">
                        <span><?=__('Create a password of 8~20 characters (numbers, letters, symbols)')?></span>
                    </p>
                    <!--                        <p>-->
                    <!--                            <label for="labelPassword_1">Your password cannot consist of a numerial or alphabetical sequence.</label>-->
                    <!--                            <label for="labelPassword_1">Your password cannot be the same as your user name or email.</label>-->
                    <!--                        </p>-->
                    <!--                        <p class="noti">-->
                    <!--                            <span>Create a password of 8~20 characters (numbers, letters, symbols)</span>-->
                    <!--                        </p>-->
                    <!--                        <p>-->
                    <!--                            <label for="labelPassword_1">Your password cannot consist of a numerial or alphabetical sequence.</label>-->
                    <!--                            <label for="labelPassword_1">Your password cannot be the same as your user name or email.</label>-->
                    <!--                        </p>-->
                </div>
                <div class="submitBtn">
                    <a href="#" onclick="fnSendVerification();"><?=__('Create Account')?></a>
                </div>
            </fieldset>
        </form>
        <div class="fccJoin__links2">
            <span><?=__('By creating an account, I accept FCC’s.<br>Terms and Conditions and Privacy Policy.')?></span>
        </div>
    </div>
    <div class="fccJoin__links">
        <span class="normal"><?=__('Are you a member?')?> </span>&nbsp;<a href="/auth/login"><?=__('Log in')?></a>
    </div>

</div>
