<div class="modal__content">
    <div class="modal__content--wrap">
        <h6 class="modal__save-title"><?=__('Would you like to delete your account?')?></h6>
        <p class="model__description">
            <?=__('Your account will be deleted 10 days after the request is submitted. After 10 days, all emails containing personal information, uploaded pictures, videos, and text will be deleted.If you change your mind about deleting your account, you can Sign in to stop the deletion process within 10 days. If you Sign in again, all deleted information will be automatically restored.')?>
        </p>
        <div class="modal__bottom is-zoom">
            <div class="left">
                <a href="#" type="button" class="buttons cancel" onclick="deleteAccount();">
                    <span class="text"><?=__('Delete')?></span>
                    <span class="size"><?=__('Delete')?></span>
                    <span class="bg"></span>
                </a>
            </div>
            <div class="right">
                <button type="button" class="buttons save" onclick="modal.hide();">
                    <span class="text"><?=__('Cancel')?></span>
                    <span class="size"><?=__('Cancel')?></span>
                    <span class="bg"></span>
                </button>
            </div>
        </div>
    </div>
    <div class="modal__close"><button type="button" class="modal__close--btn" onclick="modal.hide();"><?=__("Close")?></button></div>
</div>
<style>
    .modal__content {}
    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content { width: 560px; }
    }
</style>