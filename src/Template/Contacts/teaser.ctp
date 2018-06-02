<script src="/js/jquery-validation/dist/jquery.validate.js"></script>
<script type="text/javascript">

    $(document).ready(function(){
        /* email check rules 추가*/
        $.validator.addMethod("emailUriCheck", function(value) {
            return /\S+@\S+\.\S+/.test(value)
        });
        /* email check rules 추가*/
        $('#contactForm').validate({
            rules : {
                'email' : {
                    required : true,
                    email : true,
                    emailUriCheck : true
                },
                'subject' : {
                    required : true
                },
                'message' : {
                    required : true
                }
            },
            messages : {
                'email' : {
                    required : "<?=__('Please enter your email.')?>",
                    email : "<?=__('Invalid email format.')?>",
                    emailUriCheck : "<?=__('Invalid email format.')?>"
                },
                'subject' : {
                    required : "<?=__('This field is required')?>"
                },
                'message' : {
                    required : "<?=__('This field is required')?>"
                }
            },
            wrapper : 'p'
        });
    });

</script>
<style>
    p .error{
        font-size: 1px;
        display: block;
        padding: 0;
        margin: 0;
        font-weight: 400;
        color: #ff4b46;
    }
</style>




<!--******************************* Contents *******************************-->
<div class="basicSection__contents" id="contents">

    <div class="policy">
        <div class="l-centerMargin">
            <div class="row">
                <div class="col-sm-2">
                    <article class="l-leftLocalMenu">
                        <h3 class="is-skip">Left Navigation Bar</h3>
                        <div class="leftLocalMenu">
                            <ul class="leftLocalMenu__list">
                                <li class="leftLocalMenu__list__tab">
                                    <a href="/fcc/about">About</a>
                                </li>
                                <li class="leftLocalMenu__list__tab">
                                    <a href="/press">Press</a>
                                </li>
                                <li class="leftLocalMenu__list__tab">
                                    <a href="/fcc/faq">FAQs</a>
                                </li>
                                <li class="leftLocalMenu__list__tab is-active">
                                    <a href="/contact">Contact</a>
                                </li>
                                <li class="leftLocalMenu__list__tab">
                                    <a href="/fcc/terms">Terms and Conditions</a>
                                </li>
                                <li class="leftLocalMenu__list__tab">
                                    <a href="/fcc/privacy">Privacy Policy</a>
                                </li>
                            </ul>
                        </div>
                    </article>
                </div>
                <div class="col-sm-10">
                    <div class="l-rightLocalContents">

                        <article class="l-curriculum">

                            <div class="termsAndPrivacy">
                                <h3 class="termsAndPrivacy__title"><?=__('Contact')?></h3>
                                <p class="contactHeadline__description">
                                    <?=__('If you have any inquiries, please do not hesitate to contact us.<br>
                    We will be happy to assist you.')?>
                                </p>
                            </div>


                            <form id="contactForm" class="contact" action="/contacts/teaser" method="POST">
                                <fieldset>
                                    <legend class="is-skip">Please, Send us for your e-mail</legend>
                                    <!--
                                        Validation Check Error : 'has-error' class add.
                                    -->
                                    <div class="form-group">
                                        <label for="contact_email"><?=__('Email')?></label>
                                        <input class="form-control" id="contact_email" aria-describedby="helpBlock1" placeholder="" name="email" maxlength="255">
                                    </div>
                                    <div class="form-group">
                                        <label for="language"><?=__('Language')?></label>
                                        <select class="form-control" name="language" id="language">
                                            <option value="E">English</option>
                                            <option value="K">Korean</option>
                                            <option value="C">Chinese</option>
                                            <option value="J">Japanese</option>
                                            <option value="F">French</option>
                                            <option value="R">Russian</option>
                                            <option value="S">Spanish</option>
                                        </select>
                                        <span id="helpBlock2" class="help-block"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_subject"><?=__('Subject')?></label>
                                        <input type="text" class="form-control" id="contact_subject" aria-describedby="helpBlock2" placeholder="" name="subject" maxlength="255">
                                        <span id="helpBlock2" class="help-block"><?=__('Please enter your subject.')?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_message"><?=__('Description')?></label>
                                        <textarea rows="15" class="form-control" id="contact_message" aria-describedby="helpBlock3" name="message" placeholder=""></textarea>
                                        <span id="helpBlock3" class="help-block"><?=__('Please enter your description.')?></span>
                                    </div>
                                    <div class="l-submit">
                                        <button type="submit" class="sendBtn" onclick=""><?=__('Send')?></button>
                                    </div>
                                </fieldset>
                            </form>
                        </article>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

