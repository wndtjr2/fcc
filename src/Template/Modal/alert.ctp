<div class="modal__content">
    <div class="modal__content--wrap">
        <?=$msg?>
        <div class="modal__bottom is-zoom">
            <button type="button" class="buttons save" onclick="modal.hide();">
                <span class="text"><?=__("Close")?></span>
                <span class="size"><?=__("Close")?></span>
                <span class="bg"></span>
            </button>
        </div>
    </div>
    <!-- <div class="modal__close"><button type="button" class="modal__close--btn" onclick="modal.hide();">닫기</button></div> -->
</div>

<style>
    .modal__content { max-width: 280px; border-radius: 10px; }
    .modal__content--wrap { padding: 35px 10px 25px; }
    .modal__content--wrap > b { color: #ff4b46; }
    .modal__bottom { margin: 20px 20px 0; }
    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content { max-width: 320px; border-radius: 5px; }
        .modal__content--wrap { font-size: 18px; line-height: 1.4; }
    }
</style>