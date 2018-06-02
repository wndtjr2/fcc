<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChProductOption Entity.
 */
class ChProductOption extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'product_option_code' => true,
    ];
}
