<div class="modal__content">
    <div class="modal__content--wrap">
        <h6 class="modal__save-title"><?=__("반품 / 환불 / 교환 신청")?></h6>
        <div class="model__description">

            <!-- 반품 사유 선택. -->
            <div class="form__division is-zoom">
                <select id="open_type" class="input__select" size="">
                    <?php foreach($claimOpenType as $type){ ?>
                        <option value="<?=$type->code?>"><?=$type->name?></option>
                    <?php } ?>
                    <!--<option value="default">No Tracking Number</option>
                    <option value="">Not Received Yet</option>
                    <option value="">Shipping Package Damaged</option>
                    <option value="">Different from Listing</option>
                    <option value="">Does Not Work</option>
                    <option value="">Don’t Want Anymore</option>
                    <option value="">Other</option>-->
                </select>
            </div>

            <!-- 반품 신청 사유 기타 선택시 노출. -->
            <div class="form__division is-zoom">
                <label for="" class="input__label--second"></label>
                <textarea id="claim_content" class="input__textarea"></textarea>
            </div>

        </div>
        <div class="modal__bottom is-zoom">
            <div class="left">
                <button type="button" class="buttons cancel" onclick="backModal.hide();">
                    <span class="text"><?=__("Cancel")?></span>
                    <span class="size"><?=__("Cancel")?></span>
                    <span class="bg"></span>
                </button>
            </div>
            <div class="right">
                <a href="javascript:void(0)" class="buttons save" id="refundRequestBtn" onclick="fnRefundRequest()">    <!--onclick="backModal.show2('modalOrderReturnSuccess');"-->
                    <input type="hidden" id="purchase_code" name="purchase_code">
                    <input type="hidden" id="amount" name="amount">
                    <span class="text"><?=__("Continue")?></span>
                    <span class="size"><?=__("Continue")?></span>
                    <span class="bg"></span>
                </a>
            </div>
        </div>
    </div>
    <div class="modal__close"><button type="button" class="modal__close--btn" onclick="backModal.hide();"><?=__("Close")?></button></div>
</div>

<style>
    .modal__content {}
    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content { width: 500px; }
    }
</style>
