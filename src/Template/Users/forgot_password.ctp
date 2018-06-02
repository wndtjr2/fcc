<script>
    function resetPass(){
        $('#ResetPass').submit();
    }
</script>

<!--******************************* Contents *******************************-->
<div class="basicSection__contents" id="contents">

    <div class="l-fccJoin l-password">
        <div class="fccJoin">
            <div class="fccJoin__head">
                <h3><?=__('Forget your password?')?></h3>
                <p class="welcome"><?=__('We will message your social media account with a link to reset your password')?></p>
            </div>

            <form class="fccJoin__form" id="ResetPass" method="post" action="/users/forgotPassword">
                <fieldset>
                    <legend class="is-skip">Find password</legend>
                    <div class="row">
                        <?php $userAccount = $this->request->session()->read('Auth.User.user_account');?>
                        <?php if($userAccount['signup'] == 'normal'){?>
                            <label for="labelEmail_1"><?=__('Email')?></label>
                            <label class="is-check e" for="labelEmail_1"><?=__('Email')?></label>
                        <?php }elseif($userAccount['signup'] == 'facebook'){?>
                            <label for="labelEmail_1">Social Media</label>
                            <label class="is-check f" for="labelEmail_1">Facebook</label>
                        <?php }else{?>
                            <label for="labelEmail_1">Social Media</label>
                            <label class="is-check l" for="labelEmail_1">LinkedIn</label>
                        <?php }?>
                        <input name="email" class="is-check" type="email" id="labelEmail_1" placeholder="Email Address" value="<?=$userAccount['emailDecrypt']?>" readonly>
                    </div>
                    <div class="submitBtn">
                        <a href="#" onclick="resetPass()"><?=__('Reset Password')?></a>
                    </div>
                </fieldset>
            </form>

        </div>

    </div>

</div>