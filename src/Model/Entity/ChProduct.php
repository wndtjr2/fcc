<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChProduct Entity.
 *
 * @property string $product_code
 * @property string $type
 * @property string $reg_type
 * @property string $name
 * @property string $product_no
 * @property string $content
 * @property string $status
 * @property int $main_image_id
 * @property \App\Model\Entity\MainImage $main_image
 * @property int $sub_image_id
 * @property \App\Model\Entity\SubImage $sub_image
 * @property int $video_id
 * @property \App\Model\Entity\Video $video
 * @property int $users_id
 * @property \App\Model\Entity\User $user
 * @property string $country_code
 * @property string $city_code
 * @property int $category1
 * @property int $category2
 * @property string $category3
 * @property string $custom_category
 * @property int $like_count
 * @property int $view_count
 * @property int $accure_like_count
 * @property string $address
 * @property string $address2
 * @property string $zipcode
 * @property string $picked_up_country_code
 * @property string $picked_up_city_name
 * @property string $delivery_yn
 * @property float $price
 * @property \Cake\I18n\Time $expires_date
 * @property int $creator
 * @property \Cake\I18n\Time $created
 * @property int $modifier
 * @property \Cake\I18n\Time $modified
 * @property string $del_yn
 * @property string $designer_name
 * @property string $adult_yn
 * @property int $designer_id
 * @property \App\Model\Entity\Designer $designer
 * @property string $model_name
 * @property string $washing_info
 * @property int $washing_info_image_id
 * @property \App\Model\Entity\WashingInfoImage $washing_info_image
 * @property string $size_info
 * @property int $size_info_image_id
 * @property \App\Model\Entity\SizeInfoImage $size_info_image
 * @property string $delivery_info
 * @property int $delivery_info_image_id
 * @property \App\Model\Entity\DeliveryInfoImage $delivery_info_image
 * @property string $refund_info
 * @property int $refund_info_image_id
 * @property \App\Model\Entity\RefundInfoImage $refund_info_image
 * @property string $notice_info
 * @property int $notice_info_image_id
 * @property \App\Model\Entity\NoticeInfoImage $notice_info_image
 * @property string $vimeo_id
 * @property \App\Model\Entity\Vimeo $vimeo
 * @property string $youtube_id
 * @property \App\Model\Entity\Youtube $youtube
 */
class ChProduct extends Entity
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
        'product_code' => false,
    ];
}
