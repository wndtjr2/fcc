<div class="modal__content">
    <div class="modal__content--wrap">
        <div class="modal__icon--ok"></div>
        <h6 class="modal__head"><?=__('Your password <br>has been changed')?></h6>
    </div>
    <div class="modal__close"><a href="#" class="modal__close--btn" onclick="modal.hide();"><?=__("Close")?></a></div>
</div>
<style>
    .modal__content {}
    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content { width: 480px; }
    }
</style>