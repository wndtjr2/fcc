<div class="modal__content">

    <div class="modal__designer--gallerywrap">
        <div class="modal__designer--gallery is-zoom">
            <ul class="images">
                <?php if(isset($images)):?>
                    <?php foreach($images as $k => $v):?>
                        <?php if(is_null($v)) continue;?>
                        <li class="image <?=($k == 0)?'is-select':''?>" onclick="modal_designer_gallery(event, this)"><img src="<?=!is_null($v)?$v:''?>" alt=""></li>
                    <?php endforeach;?>
                <?php endif;?>
            </ul>
            <div class="clickView" style="background-image: url('<?= isset($images[0]) && !is_null($images[0])? $images[0]:'';?>');"></div>
            <div class="guide">
                <img src="/_res/img/guide/product_rate_height.png" alt="">
            </div>

        </div>

        <div class="modal__close"><button type="button" class="modal__close--btn" onclick="modal.hide();"><?=__("Close")?></button></div>
    </div>

</div>

<style>
    .modal__content {
    }
    .modal__close { position: fixed; top: 3px; right: 8px; background: url('/_res/img/common/bg_trans_30_w.png') 0 0 repeat; border-radius: 20px; }
    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content {
            width: auto;
            background-color: transparent;
        }
        .modal__close {
            /*position: absolute; top: 0; right: inherit; left: 0; */
            position: absolute; top: 0; right: 0;
            background: none; border-radius: 0;
        }
        .modal__close .modal__close--btn {
            background-image: url('/_res/img/icon/close_white.png');
        }
        .modal__close .modal__close--btn:hover {
            background-color: #000;
        }
    }
</style>

<script>
    function modal_designer_gallery(e, t) {
        var img = $(t).find('img').attr('src');
        //console.log(img);

        $('.modal__designer--gallery .image').removeClass('is-select');
        $(t).addClass('is-select');
        $('.clickView').css({
            'background-image' : 'url("' + img + '")'
        });
    }
</script>