<?php
namespace App\Model\Entity;

use App\Service\EncryptService;
use Cake\ORM\Entity;

/**
 * McUserAddrInfo Entity.
 *
 * @property int $id
 * @property int $users_id
 * @property \App\Model\Entity\User $user
 * @property string $default_addr
 * @property string $deliv_first_name
 * @property string $deliv_last_name
 * @property string $zipcode
 * @property string $country_code
 * @property string $state
 * @property string $city_name
 * @property string $address
 * @property string $address2
 * @property string $deliv_phone_num
 * @property \Cake\I18n\Time $created
 */
class McUserAddrInfo extends Entity
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
        'id' => true,
    ];

    protected function _setDelivPhoneNum($value){
        $encriptService = EncryptService::Instance();
        $encriptValue = $encriptService->encrypt($value);
        return $encriptValue;
    }

    protected function _getPhoneDecrypt(){
        $encriptService = EncryptService::Instance();
        $value = $this->deliv_phone_num;
        $decryptValue = $encriptService->decrypt(trim($value));
        return $decryptValue;
    }
}
