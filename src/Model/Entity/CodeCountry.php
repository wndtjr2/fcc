<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CodeCountry Entity.
 */
class CodeCountry extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'id' => true,
        'country_name' => true,
        'country_code' => true,
        'continent_code' => true
    ];
}
