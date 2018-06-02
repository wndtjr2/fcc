<div class="modal__content">
    <div class="modal__content--wrap">
        <p class="modal__save-title"><?=__('Order has been cancelled.')?></p>
    </div>
    <div class="modal__close"><button type="button" class="modal__close--btn" onclick="location.reload();"><?=__("Close")?></button></div>
</div>

<style>
    .modal__content {}
    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content { width: 500px; }
    }
</style>