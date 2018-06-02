<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChPurchase Entity.
 */
class ChPurchase extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'purchase_code' => true,
        'order_code' => true,
        'product_code' => true,
        'product_option_code' => true,
        'seller_id' => true,
        'buyer_id' => true
    ];
}
