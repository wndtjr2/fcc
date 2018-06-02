<div class="modal__content">
    <div class="modal__content--wrap">
        <h6 class="modal__save-title"><?=__("로그인이 필요한 메뉴 입니다.")?></h6>
        <div class="modal__bottom is-zoom">
            <a href="/Auth/login" class="buttons save">
                <span class="text"><?=__('Sign In')?></span>
                <span class="size"><?=__('Sign In')?></span>
                <span class="bg"></span>
            </a>
        </div>
    </div>
    <div class="modal__close"><button type="button" class="modal__close--btn" onclick="modal.hide();"><?=__("Close")?></button></div>
</div>

<style>
    .modal__content { max-width: 480px; }
    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content { max-width: 540px; }
    }
</style>