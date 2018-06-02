<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChImage Entity.
 *
 * @property int $image_id
 * @property \App\Model\Entity\Image $image
 * @property int $creator
 * @property \Cake\I18n\Time $created
 * @property int $modifier
 * @property \Cake\I18n\Time $modified
 * @property \App\Model\Entity\ChImageFile[] $ch_image_file
 */
class ChImage extends Entity
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
        'image_id' => false,
    ];
}
