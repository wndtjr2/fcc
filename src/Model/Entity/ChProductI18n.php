<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChProductI18n Entity.
 */
class ChProductI18n extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'product_code' => false,
        'language_code' => false,
    ];
}
