<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * McVideoInfo Entity.
 *
 * @property int $id
 * @property \App\Model\Entity\McTimVideoViwInfo $mc_tim_video_viw_info
 * @property string $view_yn
 * @property string $code
 * @property string $title
 * @property string $video_info
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property string $onair_yn
 * @property int $main_image_id
 * @property int $sub_image_id
 * @property int $video_id
 * @property string $vimeo_id
 * @property string $youtube_id
 * @property int $designer_id
 * @property \App\Model\Entity\McVideoComment[] $mc_video_comment
 * @property \App\Model\Entity\McVideoProductOptionInfo[] $mc_video_product_option_info
 * @property \App\Model\Entity\ChImage $ch_image
 */
class McVideoInfo extends Entity
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
