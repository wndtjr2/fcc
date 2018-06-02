<?php
namespace App\Model\Entity;

use App\Service\EncryptService;
use Cake\ORM\Entity;

/**
 * CsQuestion Entity.
 *
 * @property int $id
 * @property string $prefix_domain
 * @property string $from_email
 * @property string $email
 * @property string $subject
 * @property string $message
 * @property int $admin_id
 * @property \App\Model\Entity\Admin $admin
 * @property string $is_replied
 * @property string $trash
 * @property \Cake\I18n\Time $created
 */
class CsQuestion extends Entity
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

    protected function _setFromEmail($value){
        $encriptService = EncryptService::Instance();
        $encriptValue = $encriptService->encrypt($value);
        return $encriptValue;
    }
}
