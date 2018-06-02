<?php
    $edit_profile = isset($edit_profile) ? $edit_profile:'';
    $orders = isset($orders) ? $orders:'';
    $shipping_addresses = isset($shipping_addresses) ? $shipping_addresses:'';
    $account = isset($account) ? $account:'';
?>
<div class="screen__2column--left">
    <ul class="users__submenu is-zoom">
        <li class="item"><a href="/users/edit" class="link <?=$edit_profile?>"><?=__('Edit Profile')?></a></li>
        <li class="item"><a href="/myorder/" class="link <?=$orders?>"><?=__('Orders')?></a></li>
        <li class="item"><a href="/address" class="link <?=$shipping_addresses?>"><?=__('Shipping Addresses')?></a></li>
        <li class="item"><a href="/users/account/" class="link <?=$account?>"><?=__('Account')?></a></li>
    </ul>
</div>