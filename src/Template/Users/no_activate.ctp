<script>
    var repeat = false;
    function resend(){

        if(repeat) return;

        $.ajax({
            url: '/users/noActivate',
            type: 'POST',
            success: function(data){
                repeat = false;
                location.href = "/users/noActivate";
            }
        })
        repeat = true;
    }
</script>


<!--******************************* Contents *******************************-->
<div class="l-fccJoinComplete">
    <div class="fccJoinComplete">
        <h3><?=__('Account Not Verified')?></h3>
        <p><?=__('You already created your account,<br>but you need to verify it via the verification email we sent.')?></p>
        <br>
        <p><?=__('Can\'t find the verification email? <br>Click the button below to send it again.')?></p>
        <br>
        <a href="#" onclick="resend()"><?=__('Resend verification email.')?></a>
    </div>
</div>