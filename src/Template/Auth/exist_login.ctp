<div class="l-fccJoin">
    <div class="fccJoin">
        <div class="fccJoin__head">
            <h3><?=__('JOIN')?></h3>
            <p class="name"><?=$this->request->session()->read('user.name');?>,</p>
            <p class="welcome"><?=__('we need a few things to create your account.')?></p>
        </div>
        <form class="fccJoin__form">
            <input type="hidden" name="email" value="<?=$this->request->session()->read('user.verify.email')?>">
            <input type="hidden" name="signup" value="normal">
            <fieldset>
                <legend class="is-skip">FCC Join : create your account</legend>
                <div class="row">
                    <label for="labelPassword_1"><?=__('Password')?></label>
                    <input type="password" id="labelPassword_1" placeholder="<?=__('Password')?>" name="password" maxlength="20">
                    <p class="noti">
                        <span><?=__('Create a password of 8~20 characters (numbers, letters, symbols)')?></span>
                    </p>
                    <p>
                        <label for="labelPassword_1"><?=__('Your password cannot consist of a numerial or alphabetical sequence.')?></label>
                        <label for="labelPassword_1"><?=__('Your password cannot be the same as your user name or email.')?></label>
                    </p>
                </div>
                <div class="submitBtn">
                    <a href="#"><?=__('Create Account')?></a>
                </div>
            </fieldset>
        </form>
        <div class="fccJoin__links">
            <div class="row">
                <span class="normal is-active"><?=__('Are you a member?')?> </span><a href="/auth/login"><?=__('Log in')?></a>
            </div>
        </div>
        <p class="policy"><?=__('By creating an account, <br>I accept FCC’s Terms of Service and Privacy Policy.')?></p>
    </div>
</div>
