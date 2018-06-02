<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * McTimVideoViwInfo Entity.
 *
 * @property int $id
 * @property int $mc_video_info_id
 * @property \App\Model\Entity\McVideoInfo $mc_video_info
 * @property \Cake\I18n\Time $st_dtm
 * @property \Cake\I18n\Time $ed_dtm
 * @property \Cake\I18n\Time $created
 */
class McTimVideoViwInfo extends Entity
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
