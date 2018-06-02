<div class="modal__content">
    <div class="modal__vedio">
        <h4>About Video</h4>
        <div class="modal__video--detail">
            <div class="cover" style="background-image: url('<?=$data['imagePath']?>');">
                <div class="summery">
                    <strong><?=$data['videoCategory'][$data['code']]?></strong>
                    <p><?=$data['title']?></p>
                </div>
            </div>
            <div class="info">
                <span class="date">&nbsp;<?//=$data['brdDate']?></span>
                <span class="time">&nbsp;<?//=$data['brdTime']?></span>
                <div class="social__modal--video">
                    <button type="button" class="open" onclick="js.social(event);">SNS</button>
                    <ul class="social">
                        <li class="sns"><a href="javascript:void(0);" class="link w wbShare" title="<?=$data['title']?>">Weibo</a></li>
                        <li class="sns"><a href="javascript:void(0);" class="link t twShare" title="<?=$data['title']?>">Twitter</a></li>
                        <li class="sns"><a href="javascript:void(0);" class="link f fbShare" title="<?=$data['title']?>">Facebook</a></li>
                    </ul>
                </div>
            </div>
            <p class="detail">
                <?=$data['description']?>
<!--                <span class="more-center"><button type="button" class="more">View More</button></span>-->
            </p>
            <p class="design">
                <span class="label"><?=__("Designer")?></span>
                <?=implode(",<br/>",$data['designerInfo'])?>
            </p>
        </div>
    </div>
    <div class="modal__close"><button type="button" class="modal__close--btn" onclick="modal.hide();"><?=__("Close")?></button></div>
</div>

<style>
    .modal__content {}
    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content { width: 700px; }
    }
</style>