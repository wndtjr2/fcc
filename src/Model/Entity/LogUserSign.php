<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LogUserSign Entity.
 */
class LogUserSign extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'id' => false,
        'users_id' => true,
        'device' => true,
        'sign' => true
    ];
}
