<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChImageFile Entity.
 */
class ChImageFile extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'image_file_id' => false,
    ];
}
