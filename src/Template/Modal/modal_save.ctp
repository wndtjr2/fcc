<div class="modal__content">
    <div class="modal__content--wrap">
        <h6 class="modal__save-title"><?=__("입력하신 정보를 저장하시겠습니까?")?></h6>
        <div class="modal__bottom is-zoom">
            <div class="left">
                <button type="button" class="buttons cancel" onclick="modal.hide();">
                    <span class="text"><?=__('Cancel')?></span>
                    <span class="size"><?=__('Cancel')?></span>
                    <span class="bg"></span>
                </button>
            </div>
            <div class="right">
                <a href="#" class="buttons save" onclick="$('#editProfile').submit();">
                    <span class="text"><?=__('Save')?></span>
                    <span class="size"><?=__('Save')?></span>
                    <span class="bg"></span>
                </a>
            </div>
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