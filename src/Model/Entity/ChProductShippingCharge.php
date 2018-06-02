<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChProductShippingCharge Entity.
 */
class ChProductShippingCharge extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'product_code' => false,
        'country_code' => false,
    ];
}
