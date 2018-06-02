<div class="l-fccJoinComplete">
    <div class="fccJoinComplete">
        <h3><?=__('Go check your email !!')?></h3>
        <?=__('<p>Dear <span class="user">{0}</span></p>
            <p>We have sent a verification email to <span class="email">{1}</span></p>
            <p>Open the e-mail and click the link to complete the registration process.</p>
            <br>
            <p>If you cannot find the activation email, <br>please check your spam folder or click below.</p>'
            , (isset($name))?$name:''
            , isset($email)?$email:'')?>
        <br>
        <a href="#" onclick="resendForm()"><?=__('Resend verification email.')?></a>
    </div>
</div>
<script>
    var repeat = false;
    function resendForm(){
        if(repeat) return;
        $.ajax({
            url: '/users/resendVerificationEmail',
            type: 'POST',
            success: function(){
                location.href = "/users/resendVerificationEmail";
            }
        })
        repeat = true;
    }
</script>