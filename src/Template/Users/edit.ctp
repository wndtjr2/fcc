    <!-- 도시 자동완성 -->
<!--<link rel="stylesheet" href="/css/jquery-ui-1.10.4.custom.min.css?time=<?//=RESOURCE_VERSION?>" />-->
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/front/modal.js"></script>

<!--<script src="/js/jquery.mobile-events.min.js"></script> 모바일 파일 선택시 사용 -->

<script src="/js/jquery-validation/dist/jquery.validate.js"></script>
<script type="text/javascript">
    var maxFileSize = 5242880;      // 1024*1024*5
    // 연속 서브밋 방지
    var canSubmit = true;

    $(function(){
        $('#editProfile').validate({
            submitHandler: function(form){
                modal.hide();
                if(validateCustom()){
                    if(!uploading) {
                        modalalert("<?=__("Profile image uploading. Please wait.")?>");
                        return false;
                    }

                    if(canSubmit) {    // 중복 전송 방지
                        canSubmit = false;
                        form.submit();
                    }

                }else{

                    return false;
                }
            }
        });
    });

    /** 입력 값 변화시 에러체크 */
    $(function(){
        $('#first_name').keyup(function(){
            fnShowRequired('first_name');
        });

        $('#last_name').keyup(function(){
            fnShowRequired('last_name');
        });

        $('#month, #day, #year').change(function(){
            if(chkDate($('#month').val(),$('#day').val(),$('#year').val())){
                $('#birthday_required').show();
            }else{
                $('#birthday_required').hide();
            }
        });

        $('input.gender').change(function(){
            var genderVal = $('input.gender').filter(function(){
                return $(this).prop('checked') == true;
            }).val();


            if(typeof genderVal == 'undefined'){
                $('#gender_required').show();
            }else{
                $('#gender_required').hide();
            }
            $('label.gender').removeClass('is-select');
            $('label.gender.'+genderVal).addClass('is-select');
        });

        $('#code_language_id').change(function(){
            if ($('#code_language_id').val() == 0) {
                $('#code_language_id_required').show();
            }else{
                $('#code_language_id_required').hide();
            }
        });

    });

    // 에러 메시지 출력
    function fnShowRequired(selector) {
        if($.trim($('#'+selector).val())==''){
            $('#'+selector+'_required').show();
        }else{
            $('#'+selector+'_required').hide();
        }
    }
    /** END : 입력 값 변화시 에러체크 */


    function validateCustom(){
        var errorcheck = true;

        if($.trim($('#last_name').val())==''){
            $('#last_name_required').show();
            $('#last_name').focus();
            errorcheck = false;
        }else{
            $('#last_name_required').hide();
        }

        if($.trim($('#first_name').val())==''){
            $('#first_name_required').show();
            $('#first_name').focus();
            errorcheck = false;
        }else{
            $('#first_name_required').hide();
        }

        if($("#nickCheckOk").val()=="N"){
            $("#nickname_msg").text("<?=__("닉네임을 중복 체크해주세요.")?>");
            $("#nickname_msg").show();
            errorcheck = false;
        }else{
            $("#nickname_msg").hide();
        }


        if (errorcheck) {
            $('#checkOff').hide();
            $('#checkOn').show();
        } else {
            $('#checkOn').hide();
            $('#checkOff').show();
        }

        return errorcheck;
    }

    function chkDate(m,d,y){
        m = m - 1;
        if(y<1900 || y>2015) {
            return true;
        }
        var vDate = new Date();
        vDate.setFullYear(y);
        vDate.setMonth(m);
        vDate.setDate(d);
        if( vDate.getFullYear() != y ||
            vDate.getMonth()    != m ||
            vDate.getDate()     != d ){
            return true;
        }
        return false;
    }




    /** 이미지 바로 변경 */
    $(function(){
        $('#uploadObject').change(function(){
            profileImageUpload(this);
        });
    });

    var uploading = true;
    function profileImageUpload(obj) {
        if(!uploading) {
            modalalert("<?=__("Profile image uploading. Please wait.")?>");
            return false;
        }
        var data = new FormData();
        var filesToUpload = obj.files;
        $.each(filesToUpload,function(k,file){
            if(!file.type.match(/image.*/)) {
                modalalert("<?=__("Invalid image format.")?>");
                $('#uploadObject').val("");
                return false;
            }
            if(file.size>maxFileSize){
                modalalert("<?=__('File is too large. Max file size is 5MB.')?>");
                $('#uploadObject').val("");
                return false;
            }
            data.append('file_data', file);
            uploadImage(data);
        });
    }

    function uploadImage(obj) {
        uploading = false;
        $.ajax({
            url: '/users/profileImageUpload',
            type: 'POST',
            data: obj,
            cache: false,
            dataType: 'json',
            //async : false,
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            beforeSend : function (){
                $('#previewImage').attr('src','/_res/img/loading/loading.gif');
            },
            success: function(data, textStatus, jqXHR)
            {
                var rtn = data.result;
                if(rtn=='success'){
                    $('#previewImage').attr('src',data.image_uri+'-crop');
                    $('#btnUpload').hide();
                    $('#btnDelete').show();

                    $('.profile__edit--photo .photo').css('background-image', 'none');
                }
                if(rtn=='ERR'){
                    modalalert("<?=__("An unknown error occurred, possibly due to excessive size or an unpermitted file. Please try again.")?>");
                    $('#previewImage').attr('src','/_res/img/update/profile_user_photo_116_116.png');
                }
                if(rtn=='saveERR'){
                    modalalert("<?=__("An error occurred. Please try again.")?>");
                    $('#previewImage').attr('src','/_res/img/update/profile_user_photo_116_116.png');
                }
                if(rtn=='allowERR'){
                    modalalert("<?=__("You have been automatically logged out. Please sign in again.")?>");
                    $('#previewImage').attr('src','/_res/img/update/profile_user_photo_116_116.png');
                    location.reload();
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                modalalert("<?=__("Error Has Occurred. Please try again.")?>");
            },
            complete : function(){
                uploading = true;
            }
        });
    }
    /** ===== END : 이미지 바로 변경 */

    // 프로필 이미지 삭제 기능
    function btnImgDelete() {
        uploading = false;
        $.ajax({
            url: '/users/profileImageDelete',
            type: 'POST',
            cache: false,
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                var rtn = data.result;
                if(rtn=='success'){
                    $('#previewImage').attr('src','/_res/img/update/profile_user_photo_116_116.png');
                    $('#btnDelete').hide();
                    $('#btnUpload').show();

                    $('.profile__edit--photo .photo').css('background-image', '');
                }
                if(rtn=='saveERR'){
                    modalalert("<?=__("An error occurred while deleting the image. Please try again.")?>");
                }
                if(rtn=='allowERR'){
                    modalalert("<?=__("You have been automatically logged out. Please sign in again.")?>");
                    location.reload();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                modalalert("<?=__("Error Has Occurred. Please try again.")?>");
            },
            complete : function(){
                /** 같은 파일 재 요청시에도 event 발생하도록 새로운 태그로 교체 후 이벤트 설정 */
                var clone = $('#uploadObject').clone();
                $('#uploadObject').replaceWith(clone);
                $('#uploadObject').change(function(){
                    profileImageUpload(this);
                });

                uploading = true;
            }
        });
    }

    $(document).ready(function(){
        $("#nickname").on("change",function(){
            if($("#nickCheckOk").attr("org_nickname")==$(this).val()){
                $("#nickCheckOk").val("Y");
                $("#nickname_msg").hide();
                $("#nicknameCheck").find(".text").text("<?=__("사용중인 닉네임")?>");
                $("#nicknameCheck").addClass("cancel");
            }else {
                $("#nickCheckOk").val("N");
                $("#nickname_msg").text("<?=__("닉네임을 중복 체크해주세요.")?>");
                $("#nickname_msg").show();
                $("#nicknameCheck").find(".text").text("<?=__("중복체크")?>");
                $("#nicknameCheck").removeClass("cancel");
            }
        });

        $("#nicknameCheck").on("click",function(){
            if($(this).hasClass("cancel")){
                return false;
            }
            var nicknameVal = $("#nickname").val();
            if(nicknameVal==""){
                $("#nickname_msg").text("<?=__("닉네임을 입력해주세요.")?>");
                $("#nickname_msg").show();
                return false;
            }
            $.ajax({
                url: '/auth/nickNameCheck',
                type: 'POST',
                data: {
                    nickname: nicknameVal
                },
                async : false,
                dataType: 'json',
                success: function (data) {
                    if(data.result==true){
                        $("#nickCheckOk").val("Y");
                        $("#nickname_msg").text("<?=__("사용 가능한 닉네임 입니다.")?>");
                        $("#nicknameCheck").find(".text").text("<?=__("중복 체크완료")?>");
                        $("#nicknameCheck").addClass("cancel");
                        $("#nickname_msg").show();
                    }else{
                        $("#nickCheckOk").val("N");
                        $("#nickname_msg").text("<?=__("이미 사용중인 닉네임 입니다.")?>");
                        $("#nickname_msg").show();
                    }
                }
            });
        });
    });

</script>


<!-- 에러 문구 숨기기 -->
<style> .err_msg { display: none; }</style>

<section id="sections">
<h2 class="is-skip"><?=__('Edit Profile')?></h2>

    <div class="contents">

        <div id="sign">
            <div class="sign__wrap">
                <h3 class="section__title"><span class="inner"><?=__('Edit Profile')?></span></h3>
        <!-- TIP1: 화면 분할, 2 컬럼. -->
                <div class="screen__2column is-zoom">
                    <?=$this->element('usersMenu',[
                        'edit_profile'=>'is-current'
                    ]);?>
                    <div class="screen__2column--right">

                        <!-- 분할 컨텐츠. -->
                        <div class="userpage__wrap account">
                            <p class="userpage__title"><?=__('Edit Profile')?></p>

                            <!-- Profile 컨텐츠 -->
                            <form id="editProfile" method="post" enctype="multipart/form-data">
                                <fieldset>
                                    <div class="only__web--account">

                                        <!-- 사진 업로드 -->
                                        <div class="form__division is-full">
                                            <dl class="profile__edit--photo">
                                                <dt class="photo">
                                                    <span class="frame"><?=__("Your photo")?></span>
                                                    <?php if($user['image_path'] == ''){ ?>
                                                    <img src="/_res/img/update/profile_user_photo_116_116.png" alt="" id="previewImage">
                                                    <?php }else{ ?>
                                                    <img src="<?=FILE_URI.$user['image_path']?>-crop" alt="" id="previewImage">
                                                    <script>
                                                        $(function(){
                                                            $('#btnUpload').hide();
                                                            $('#btnDelete').show();
                                                        });

                                                        $('.profile__edit--photo .photo').css('background-image', 'none');
                                                    </script>
                                                    <?php } ?>
                                                </dt>
                                                <dd class="info"><?=__('Allowed file types JPG, GIF or PNG.<br>Maximum size of 5MB')?></dd>
                                                <dd class="upload" id="btnUpload">
                                                    <!-- 업로드 버튼 -->
                                                    <div class="fileUpload">
                                                        <div class="fileUpload_true">
                                                            <input type="file" class="" id="uploadObject" accept="image/gif,image/jpeg,image/png" name="file_data">
                                                        </div>
                                                        <div class="fileUpload_style"><?=__('Upload')?></div>
                                                    </div>
                                                </dd>
                                                <dd id="btnDelete" style="display: none;">
                                                    <!-- 이미지 삭제 버튼 -->
                                                    <div class="fileUpload">
                                                        <div class="fileUpload_style" onclick="btnImgDelete()"><?=__('Delete')?></div>
                                                    </div>
                                                </dd>
                                            </dl>
                                        </div>
                                        <!-- 닉네임 -->
                                        <div class="form__division is-zoom">
                                            <label for="first_name" class="input__label"><?=__("Nickname")?></label>
                                            <div class="form__division--divide f2-left">
                                                <label for="" class="input__label--second"></label>
                                                <input type="text" placeholder="닉네임" class="input__box " id="nickname" name="nickname" value="<?=$user['nickname']?>" maxlength="10">
                                                <input type="hidden" name="nickCheckOk" id="nickCheckOk" value="Y" org_nickname="<?=$user['nickname']?>">
                                                <span class="input__validation err_msg" id="nickname_msg"><?=__("닉네임을 중복 체크해주세요.")?></span>
                                            </div>
                                            <div class="form__division--divide f2-right">
                                                <label for="" class="input__label--second"></label>
                                                <a href="javascript:void(0);" id="nicknameCheck" class="buttons cancel">
                                                    <span class="text" href=""><?=__("중복체크")?></span>
                                                    <span class="bg"></span>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- 이름 -->
                                        <div class="form__division is-zoom">
                                            <div class="form__division--divide f2-left">
                                                <label for="last_name" class="input__label--second"><?=__('Last Name')?></label>
                                                <input type="text" placeholder="<?=__('Last Name')?>" class="input__box" id="last_name" name="last_name" value="<?=$user['last_name']?>" disabled="disabled">
                                                <span class="input__validation err_msg" id="last_name_required"><?=__('Please enter your last name.')?></span>
                                            </div>
                                            <div class="form__division--divide f2-right">
                                                <label for="first_name" class="input__label--second"><?=__('First Name')?></label>
                                                <input type="text" placeholder="<?=__('First Name')?>" class="input__box" id="first_name" name="first_name" value="<?=$user['first_name']?>" disabled="disabled">
                                                <span class="input__validation err_msg" id="first_name_required"><?=__('Please enter your first name.')?></span>
                                            </div>
                                        </div>

                                        <!-- 생년월일 -->
                                        <?php
                                            $birthDay = $user['birthDecrypt'];
                                            $day = substr($birthDay,6,2);
                                            $month = substr($birthDay,4,2);
                                            $year = substr($birthDay,0,4);
                                        ?>
                                        <div class="form__division">
                                            <label for="birthday" class="input__label"><?=__('Birth Date')?></label>
                                            <label for="" class="input__label--second"></label>
                                            <input type="text" placeholder="<?=__("Birth Date")?>" class="input__box " id="birthday" name="birthday" value="<?=$year?><?=__("Year")?> <?=$month?><?=__("Month")?> <?=$day?><?=__("Day")?>" readonly disabled="disabled">
                                        </div>

                                        <!-- 언어, 전화번호 -->
                                        <div class="form__division is-zoom">
                                            <div class="form__division">
                                                <label for="lb__phone" class="input__label"><?=__('Phone Number')?></label>
                                                <label for="" class="input__label--second"></label>
                                                <input type="text" placeholder="<?=__('(Country Code) Phone number')?>" class="input__box" id="phone_number" name="phone_number" value="<?=$user['phoneDecrypt']?>" disabled="disabled">
                                            </div>
                                        </div>

                                        <!-- 저장 -->
                                        <div class="form__submit">
                                            <br class="only__web">
                                            <br class="only__web">
                                            <button type="button" class="buttons save" onclick="modal_show2('modalSave');">
                                                <span class="text"><?=__('Save')?></span>
                                                <span class="size"><?=__('Save')?></span>
                                                <span class="bg"></span>
                                            </button>
                                        </div>

                                    </div>

                                </fieldset>
                            </form>

                        </div>
                    </div>
                </div>
        <!-- TIP1 -->
            </div>
        </div>

    </div> <!-- contents -->
</section>
