
<script type="text/javascript">
    function passwordAgain(){
        $('#password_again').submit();
    }
    $(document).ready(function(){
        $('#password_again').validate({
            rules : {
                'password' : {
                    required : true
                }
            },
            messages : {
                'password' : {
                    required : "<?=__('Please enter your password')?>"
                }
            },
            wrapper : 'p',
            submitHandler: function(form){
                form.submit();
            }
        })
    })
</script>

<!--******************************* Contents *******************************-->
<div class="l-fccJoin">

    <div class="fccJoin">
        <div class="fccJoin__head">
            <h3><?=__('Log in')?></h3>
            <p class="welcome"><?=__('To protect your information. please enter your password again.')?></p>
            <?php if(isset($password)):?>
                <br>
                <p class="accountNotice"><?=__('Incorrect Password')?></p>
            <?php endif;?>
        </div>
        <form class="fccJoin__form" action="/users/passwordAgain" id="password_again" method="post">
            <fieldset>
                <legend class="is-skip">FCC Login</legend>
                <div class="row">
                    <?php if($userAccount->signup == 'normal'){?>
                        <label class="is-check e" for="labelEmail_1"></label>
                    <?php }elseif($userAccount->signup == 'facebook'){?>
                        <label class="is-check f" for="labelEmail_1"></label>
                    <?php }else{?>
                        <label class="is-check l" for="labelEmail_1"></label>
                    <?php } ?>
                    <input name="email" class="is-check" type="email" id="labelEmail_1" placeholder="Email Address" value="<?=$userAccount->emailDecrypt?>" readonly>
                </div>
                <div class="row">
                    <label class="is-skip" for="labelPassword_1"><?=__('Password')?></label>
                    <input type="password" name="password" placeholder="<?=__('Password')?>" maxlength="20">
                    <input type="hidden" name="signup" value="<?=$userAccount->signup?>">
                </div>
                <div class="submitBtn">
                    <a href="#" onclick="passwordAgain()"><?=__('Done')?></a>
                </div>
            </fieldset>
        </form>
        <div class="fccJoin__links2">
            <a href="/users/forgotPassword"><?=__('Did you forget your password?')?></a>
        </div>

    </div>

</div>