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
                    required : "<?=__('Please enter your subject.')?>"
                },
                'message' : {
                    required : "<?=__('Please enter your description.')?>"
                }
            },
            wrapper : 'p'
        });
    });

</script>



<section id="sections">
    <a href="#" id="skipPoint" title="메뉴 건너뛰기 이동 포인트"></a>
    <h2 class="is-skip"><?=__("FCCTV 미디어 커머스 : 문의 하기")?></h2>

    <div class="contents">

        <div id="sign">
            <div class="contact__wrap">
                <h3 class="section__title"><span class="inner"><?=__("Q&amp;A")?></span></h3>
                <div class="product__infomation all">
                    <h5><strong><?=__("CHECK UP PLEASE!")?></strong></h5>
                    <ul class="tabs is-zoom">
                        <li class="tab is-select">
                            <button type="button" class="btn" onclick="js.tab_on_off(event, this)">배송정보</button><spam class="arr"></spam>
                            <div class="define">
                                <ul>
                                    <li><?=__("배송은 주문 후 2일이내 발송을 원칙으로 하고 있습니다.")?></li>
                                    <li><?=__("일부 브랜드 업체발송은 상품설명에 별도로 기입된 기간으로 상품이 발송됩니다.")?></li>
                                    <li><?=__("일부 상품, 제주도를 포함한 도서산간 지역은 추가 배송비 입급요청이 있을 수 있습니다.")?></li>
                                    <li><?=__("배송비는 상품에 따라 적용됩니다.")?></li>
                                </ul>
                            </div>
                        </li>
                        <li class="tab is-select">
                            <button type="button" class="btn" onclick="js.tab_on_off(event, this)">교환 / 환불 / A/S안내</button><spam class="arr"></spam>
                            <div class="define">
                                <ul>
                                    <li><?=__("상품 수령일로부터 7일이내 반품/환불 가능합니다.")?></li>
                                    <li><?=__("변심 반품의 경우 왕복배송비를 차감한 금액이 환불되며, 제품 및 포장 상태가 재판매 가능하여야 합니다.")?></li>
                                    <li><?=__("상품 불량인 경우는 배송비를 포함한 전액이 환불 됩니다.")?></li>
                                    <li><?=__("주문제작상품 / 밀봉상품 등은 변심에 따른 반품/환불 어렵습니다.")?></li>
                                    <li><?=__("일부 해외수입 제품에 대해서는 A/S가 불가능합니다. (상품정보 별도 표기)")?></li>
                                </ul>
                            </div>
                        </li>
                        <li class="tab is-select">
                            <button type="button" class="btn" onclick="js.tab_on_off(event, this)">상품고시</button><spam class="arr"></spam>
                            <div class="define">
                                <ul>
                                    <li><?=__("상품 상세페이지 참고.")?></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>

                <form id="contactForm" class="contact" action="/contacts/add" method="POST">
                    <fieldset>
                        <legend class="form__title is-skip"><?=__('Contact')?></legend>
                        <!-- 이메일 주소 -->
                        <div class="form__division">
                            <label for="lb__email" class="input__label first"><?=__('Email Address')?></label>
                            <label for="" class="input__label--second"></label>
                            <input class="input__box" id="contact_email"  placeholder="" name="email" maxlength="255">
                        </div>
                        <input type="hidden" name="language" id="language" value="ko">
                        <!-- 제목 -->
                        <div class="form__division">
                            <label for="lb__subject" class="input__label"><?=__("subject")?></label>
                            <label for="" class="input__label--second"></label>
                            <input type="text"  name="subject"  placeholder="" id="contact_subject" class="input__box" maxlength="255">
                        </div>
                        <!-- 글작성 -->
                        <div class="form__division">
                            <label for="lb__description" class="input__label"><?=__('Description')?></label>
                            <label for="" class="input__label--second"></label>
                            <textarea name="message" placeholder="" id="contact_message"  class="input__textarea desc2"></textarea>
                        </div>
                        <div class="form__submit">
                            <br>
                            <a href="javascript:void(0);" id="contacUsSend" type="button" class="buttons save">
                                <span class="text"><?=__('Send')?></span>
                                <span class="size"><?=__('Send')?></span>
                                <span class="bg"></span>
                            </a>
                        </div>
                    </fieldset>
                </form>
                <p class="sign__title">
                    <strong><?=__("Thank you!")?></strong><br>
                    <span class="sign__title--subtitle">
                        <?=__("넘버원 미디어커머스 <span class='colorBland'>FCCTV</span>에 <br class='only__mobile'>관심을 가져주셔서 감사합니다. <br><br>보내주시는 소중한 의견, 제안은 <br class='only__mobile'>입력된 이메일주소로 <br>빠른 시일내에 회신해 드리겠습니다.")?>
                    </span>
                </p>

            </div>
        </div>

    </div> <!-- contents -->
</section>
<script>
    $("#contacUsSend").on("click",function(){
        $("#contactForm").submit();
    });
</script>
