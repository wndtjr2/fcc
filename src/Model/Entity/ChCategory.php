<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChCategory Entity.
 *
 * @property int $id
 * @property int $parent_id
 * @property \App\Model\Entity\ParentChCategory $parent_ch_category
 * @property int $depth
 * @property string $name
 * @property int $seq
 * @property int $image_id
 * @property \App\Model\Entity\Image $image
 * @property string $launched_yn
 * @property \App\Model\Entity\ChildChCategory[] $child_ch_category
 */
class ChCategory extends Entity
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
        'id' => false,
    ];
}
