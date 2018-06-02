<div class="modal__content">
    <h5 class="modal__head40"><?=__('Change Address')?></h5>

    <div class="modal__address">
        <div class="modal__address--add">

            <!-- 주소록 -->
            <ul class="address__lists is-zoom">
                <li class="list">
                    <a href="#" class="address__lists--add" onclick="open2ndNewAddressModal();"><span class="icon"></span><?=__('Add New Address')?></a>
                </li>

                <!-- TIP2 : 추가된 주소록 -->
                <?php $int = 1;?>
                <?php $int1 = 1;?>
                <?php foreach($addressList as $address):?>
                    <li class="list" id="AddressId_<?=$address->id?>">
                        <dl class="address__list">
                            <dt>
                                <?= ($address->default_addr =='y')?__('Default Address'):__('Additional Address').' (' . $int.')'?>
                                <!-- TIP: Checkout > Change 선택 > 주소선택을 위해 노출. -->
                                <button type="button" class="button__radio <?=($address->default_addr == 'y')?'is-select':''?>" name="address" id="<?=$address->id?>">
                                    <?=__("선택")?>
                                    <span class="chk"></span>
                                </button>
                            </dt>
                            <dd class="detail">
                                <p class="name"><?=$address->deliv_last_name . ' ' . $address->deliv_first_name?></p>
                                <address class="location">
                                    <?=$this->FccTv->addressStr($address->zipcode,$address->address,$address->address2)?>
                                </address>
                                <p class="nation"><?=$countryList[$address->country_code]?></p>
                                <p class="phone"><?=__('Phone Number')?>: <?=$address->phone_decrypt?></p>
                            </dd>
                            <dd class="func">
                                <button type="button" class="btn edit" onclick="editAddress(<?=$address->id?>);"><?=__('Edit')?></button>
                                <?php if($address->default_addr =='n'):?>
                                    <button type="button" class="btn delete" id="DeleteAddress" onclick="deleteAddress(<?=$address->id?>);"><?=__('Delete')?></button>

                                    <a id="makeDefaultButtonId" href="javascript:void(0);" onclick="makeDefaultButton(<?=$address->id?>)" class="btn default" addrId="<?=$address->id?>"><?=__('Make Default')?></a>
                                <?php endif;?>
                            </dd>
                        </dl>
                    </li>
                    <?php ($address->default_addr != 'y')?$int++:$int;?>
                    <?php $int1++?>
                <?php endforeach;?>
                <!-- TIP2 -->
            </ul>

        </div>
        <div class="modal__bottom">
            <a href="javascript:void(0);" class="buttons save" onclick="saveAddress()">
                <span class="text"><?=__("선택")?></span>
                <span class="size"><?=__("선택")?></span>
                <span class="bg"></span>
            </a>
        </div>
    </div>
    <div class="modal__close">
        <!--<button id="AddressListClose" type="button" class="modal__close--btn white" onclick="location.reload();return false;">닫기</button>-->
        <button id="AddressListClose" type="button" class="modal__close--btn white" onclick="modal_hide();"><?=__("Close")?></button>
    </div>
</div>

<style>
    .modal__content {}
    .modal__address {padding: 0;}
    .address__lists { padding-bottom: 10px; }
    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content { width: 700px; }
        .address__lists { padding-bottom: 0; background-color: #fff; }
        .modal__bottom { padding-bottom: 20px; text-align: center; }
        .modal__bottom .buttons { width: 220px; }
        .modal__address--add { padding-bottom: 5px; }
    }
</style>