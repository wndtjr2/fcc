<script>
    function resendEmail(){
        $.ajax({
            url: '/auth/resendPassword',
            type: 'POST',
            success: function(data){
                location.href = "/auth/resendPassword";
            }
        })
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
                        <strong><?=__('Go check your email !!')?></strong><br>
                        <span class="sign__title--subtitle"><?=__('We sent you a link to reset your password. Please check your email.')?></span>
                        <span class="sign__title--subtitle"><?=__('If you cannot find the password reset email, <br>please check your spam folder or click below.')?> <a id="resend" href="#" class="link__belongto--email" onclick="resendEmail()"><?=__('Resend email')?></a></span>
                    </h3>
                </div>

            </div>
        </div> <!-- #sign -->

    </div> <!-- contents -->
</section>