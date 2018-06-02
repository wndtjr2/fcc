<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChCodeGen Entity.
 */
class ChCodeGen extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'cdg_kind' => false,
    ];
}
