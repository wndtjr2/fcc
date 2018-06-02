<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChProductOptionI18n Entity.
 */
class ChProductOptionI18n extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'product_option_code' => false,
        'language_code' => false,
    ];
}
