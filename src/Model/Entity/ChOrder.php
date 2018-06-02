<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChOrder Entity.
 */
class ChOrder extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'order_code' => true,
        'users_id' => true,
        'total_price' => true,
        'status' => true,
        'creator' => true,
        'modifier' => true,
        'transaction_id' => true
    ];
}
