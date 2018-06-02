<style> .err_msg { display: none; }</style>
<div class="modal__content">
    <h5 class="modal__head40"><?=__("Add New Address")?></h5>
    <form method="post" id="addressFrm">
    <input type="hidden" name="type" id="type" value="regist">
    <input type="hidden" name="id" id="id" value="0">
    <div class="modal__address">
        <div class="modal__address--add">

<!--            <h6>Name</h6>-->
            <!-- 이름 입력 -->
            <div class="form__division is-zoom">
                <div class="profile__sect--left">
                    <label for="deliv_last_name" class="input__label--second"><?=__('Last Name')?></label>
                    <input type="text" class="input__box" name="deliv_last_name" id="deliv_last_name" value="<?=$userInfo['last_name']?>">
                    <span class="input__validation err_msg" id="deliv_last_name_required"><?=__('Please enter your last name.')?></span>
                </div>
                <div class="profile__sect--right">
                    <label for="deliv_first_name" class="input__label--second"><?=__('First Name')?></label>
                    <input type="text" class="input__box" name="deliv_first_name" id="deliv_first_name" value="<?=$userInfo['first_name']?>">
                    <span class="input__validation err_msg" id="deliv_first_name_required"><?=__('Please enter your first name.')?></span>
                </div>

            </div>
            <br>

            <h6><?=__('Address')?></h6>
            <!-- 주소입력 -->
            <div class="form__division is-zoom">
                <div class="profile__sect--left">
                    <label for="address" class="input__label--second"><?=__("Address")?> 1</label>
                    <input type="text" id="address" class="input__box"  name="address" readonly>
                    <span class="input__validation err_msg" id="address_required"><?=__("Please enter your address.")?></span>
                </div>
                <div class="profile__sect--right">
                    <label for="address2" class="input__label--second"><?=__("Address")?> 2</label>
                    <input type="text" id="address2" class="input__box" name="address2">
                    <span class="input__validation err_msg" id="address2_required"><?=__("Please enter your address2.")?></span>

                </div>
            </div>
            <!-- 도시 입력 -->
            <!--
            <div class="form__division is-zoom">
                <div class="profile__sect--left">
                    <label for="city_name" class="input__label--second"><?=__("City")?></label>
                    <input type="text" name="city_name" id="city_name" class="input__box">
                    <span class="input__validation err_msg" id="city_name_required"><?=__('Please enter your city.')?></span>
                </div>
                <div class="profile__sect--right">
                    <label for="state" class="input__label--second">State</label>
                    <input type="text" name="state" id="state" class="input__box">
                    <span class="input__validation err_msg" id="state_required"><?=__('Please enter your state')?></span>
                </div>
            </div>
            -->
            <!-- 나라 입력 -->
            <div class="form__division is-zoom">
                <div class="profile__sect--left">
                    <label for="zipcode" class="input__label--second"><?=__('Zip Code')?></label>
                    <input type="text" name="zipcode" id="zipcode" class="input__box" readonly>
                    <span class="input__validation err_msg" id="zipcode_required"><?=__('Please enter your zip code.')?></span>
                </div>
                <div class="profile__sect--right">
                    <a href="#" class="buttons round face" onclick="searchAddress();">
                        <span class="text"><?=__("우편번호 검색")?></span>
                        <span class="bg"></span>
                    </a>
                </div>
                <input type="hidden" name="country_code" id="country_code" value="KR">
            </div>

            <br>

            <h6><?=__("Phone Number")?></h6>
            <div class="form__division is-zoom">
                <label for="deliv_phone_num" class="input__label--second"></label>
                <input type="text" name="deliv_phone_num" id="deliv_phone_num" class="input__box">
                <span class="input__validation err_msg" id="deliv_phone_num_required"><?=__('Please enter your telephone number')?></span>
            </div>
        </div>
    </div>

    <div class="modal__address--bottom is-zoom">
        <div class="chk">
            <input type="checkbox" id="default_addr" name="default_addr" value="y">
            <label for="default_addr"><?=__("Set as default shipping address")?></label>
        </div>
        <a href="#" class="buttons save" id="submitBtn" onclick="saveAddr()">
            <span class="text"><?=__('Save')?></span>
            <span class="size"><?=__('Save')?></span>
            <span class="bg"></span>
        </a>
    </div>
    <div class="modal__close"><button type="button" class="modal__close--btn white" onclick="modal.hide()"><?=__("Close")?></button></div>
    </form>
</div>

<style>
    .modal__content {}
    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content { width: 700px; }
    }
</style>
