<section id="sections">
    <h2 class="is-skip"></h2>
    <div class="contents">

        <div id="sign">
            <div class="sign__wrap">

                <div class="create__board">
                    <h3 class="sign__title longText">
    <?php if(isset($noUser)) : ?>
                            <strong><?=__('Sorry')?></strong><br>
                            <span class="sign__title--subtitle"><?=__('We couldn\'t find your account. Would you like to')?> <a href="/auth/join"><?=__('Create Account')?></a></span>

    <?php endif; ?>
    <?php if(isset($email)) : ?>
                            <strong><?=$email?></strong><br>
                            <span class="sign__title--subtitle"><?=__('귀하의 등록된 이메일 주소는 위와 같습니다.')?> <a href="/auth/login"><?=__('Login')?></a></span>
    <?php endif; ?>
    <?php if(isset($socialJoin)) : ?>
                            <strong>귀하는 <?=$socialType?> 계정으로 가입하셨습니다.</strong><br>
                            <span class="sign__title--subtitle"><?=__('소셜 미디어 계정으로 로그인 하러 가기')?> <a href="/auth/login"><?=__('Login')?></a></span>
    <?php endif; ?>
                    </h3>
                </div>

            </div>
        </div> <!-- #sign -->

    </div> <!-- contents -->

</section>