<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Service\EncryptService;

/**
 * McDesigner Entity.
 *
 * @property int $id
 * @property string $name
 * @property int $main_image_id
 * @property \App\Model\Entity\MainImage $main_image
 * @property int $logo_image_id
 * @property \App\Model\Entity\LogoImage $logo_image
 * @property string $contents
 * @property string $summury
 * @property int $video_id
 * @property \App\Model\Entity\Video $video
 * @property string $vimeo_id
 * @property \App\Model\Entity\Vimeo $vimeo
 * @property string $youtube_id
 * @property \App\Model\Entity\Youtube $youtube
 * @property \Cake\I18n\Time $created
 * @property int $creator
 * @property string $modified
 * @property int $modifier
 */
class McDesigner extends Entity
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

    protected function _getEncryptId(){
        $encriptService = EncryptService::Instance();
        $value = $this->id;
        $decryptValue = $encriptService->encrypt($value);
        return $decryptValue;
    }
}
