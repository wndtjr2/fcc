<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Service\EncryptService;
/**
 * User Entity.
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
protected $_accessible = [
    '*' => true,

];
    protected function _setPhoneNumber($value){
        $encriptService = EncryptService::Instance();
        $encriptValue = $encriptService->encrypt($value);
        return $encriptValue;
    }

    protected function _setBirthday($value){
        $encriptService = EncryptService::Instance();
        $encriptValue = $encriptService->encrypt($value);
        return $encriptValue;
    }

    protected function _getPhoneDecrypt(){
        $encriptService = EncryptService::Instance();
        $value = $this->phone_number;
        $decryptValue = $encriptService->decrypt(trim($value));
        return $decryptValue;
    }

    protected function _getBirthDecrypt(){
        $encriptService = EncryptService::Instance();
        $value = $this->birthday;
        $decryptValue = $encriptService->decrypt(trim($value));
        return $decryptValue;
    }
}
