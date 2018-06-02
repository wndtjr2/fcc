<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * McDesignerCategory Entity.
 *
 * @property int $id
 * @property int $designer_id
 * @property \App\Model\Entity\Designer $designer
 * @property int $category1
 * @property int $category2
 * @property \Cake\I18n\Time $created
 */
class McDesignerCategory extends Entity
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
