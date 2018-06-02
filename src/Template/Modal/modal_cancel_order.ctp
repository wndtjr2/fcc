<div class="modal__content">
    <div class="modal__content--wrap">
        <h6 class="modal__save-title"><?=__('Cancel your order?')?></h6>
        <div class="modal__bottom is-zoom">
            <div class="left">
                <button type="button" class="buttons cancel" onclick="closeModal();">
                    <span class="text"><?=__('No')?></span>
                    <span class="size"><?=__('No')?></span>
                    <span class="bg"></span>
                </button>
            </div>
            <div class="right">
                <a href="#" class="buttons save" onclick="cancelOrder()">
                    <span class="text"><?=__('Yes, please.')?></span>
                    <span class="size"><?=__('Yes, please.')?></span>
                    <span class="bg"></span>
                </a>
            </div>
        </div>
    </div>
    <div class="modal__close"><button type="button" class="modal__close--btn" onclick="closeModal();"><?=__("Close")?></button></div>
</div>

<style>
    .modal__content {}
    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content { width: 500px; }
    }
</style>