<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChProductReview Entity.
 */
class ChProductReview extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'product_code' => false,
        'users_id' => false,
        'purchase_code' => false,
    ];
}
