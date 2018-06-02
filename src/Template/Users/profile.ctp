<article class="section__article">
    <h2 class="section__title"><span class="inner"><?=__('Profile')?></span></h2> <!-- z-index: 400 -->
    <div id="contents">
        <div class="submission_yours">
            <div class="only__web--account">
                <div class="profile__yours">
                    <dl class="profile__edit--photo is-profile">
                        <dt class="photo">
                            <span class="frame">Your photo</span>
                            <?php if(!empty($users['image_path'])): ?>
                                <img src="<?=FILE_URI.$users['image_path']?>-crop" alt="">
                                <script>
                                    $('.profile__edit--photo .photo').css('background-image', 'none');
                                </script>
                            <?php endif;?>
                        </dt>
                        <dd class="name"><?=strip_tags($users['first_name'])?><br><?=strip_tags($users['last_name'])?></dd>
                        <dd class="location" id="Location">
                            <?php if($users['country'] == '' || $users['city'] == ''){
                                echo strip_tags($users['country']).strip_tags($users['city']);
                            }else{
                                echo strip_tags($users['country']). ', ' . strip_tags($users['city']);
                            }?>
                        </dd>
                    </dl>
                    <dl class="profile__user--detail">
                        <dt class="is-skip">Your detail</dt>

                        <dd class="detail is-zoom">
                            <span class="label">
                                <?=__('Birth Date')?>
                            </span>
                            <span class="output">
                                <?php if(!is_null($users['birthday'])){?>
                                    <?=strip_tags(date_format($users['birthday'], 'Y.m.d'))?>
                                <?php }else{?>
                                    -
                                <?php }?>
                            </span>
                        </dd>
                        <dd class="detail is-zoom">
                            <span class="label">
                                <?=__('Gender')?>
                            </span>
                            <span class="output">
                                <?php if(($users['gender'] != '')){?>
                                    <?=strip_tags(ucfirst($users['gender']))?>
                                <?php }else{?>
                                    -
                                <?php }?>
                            </span>
                        </dd>
                        <dd class="detail is-zoom">
                            <span class="label">
                                <?=__('Languages')?>
                            </span>
                            <span class="output">
                                <?php if($language != ''){?>
                                <?=strip_tags($language)?>
                                <?php }else{?>
                                -
                                <?php }?>
                            </span>
                        </dd>
                        <dd class="detail is-zoom">
                            <span class="label">
                                <?=__('Phone Number')?>
                            </span>
                            <span class="output">
                                <?php if($users['phoneDecrypt'] != ''){?>
                                <?=strip_tags($users['phoneDecrypt'])?>
                                <?php }else{?>
                                -
                                <?php }?>
                            </span>
                        </dd>
                    </dl>
                    <div class="profile__user--editbtn">
                        <a href="/users/edit" class="buttons more">
                            <span class="text"><?=__('Edit')?></span>
                            <span class="size"><?=__('Edit')?></span>
                            <span class="bg"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>