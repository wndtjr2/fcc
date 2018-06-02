<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChCode Entity.
 *
 * @property string $cds_kind
 * @property string $code
 * @property string $name
 * @property string $note
 * @property int $seq
 * @property string $use_flag
 * @property string $del_flag
 */
class ChCode extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'cds_kind' => false,
        'code' => false,
    ];
}
