<?php
namespace App\Model\Entity;

use Cake\Error\Debugger;
use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;
use App\Service\EncryptService;
/**
 * UserAccount Entity.
 */
class UserAccount extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'email' => true,
        'emailDecrypt' => true,
        'password' => true,
        'status' => true,
        'signup' => true,
        'token' => true,
        'user' => true,
        'authentication_code' => true
    ];

    protected $_virtual = ['emailDecrypt'];
    
    /**
     * @param $password
     * @return string
     */
    protected function _setPassword($password) {
        //return $password;
        $hasher = new DefaultPasswordHasher;
        $hashedPassword = $hasher->hash($password);
        return $hashedPassword;
    }

    protected function _setEmail($value){
        $encriptService = EncryptService::Instance();
        $encriptValue = $encriptService->encrypt($value);
        return $encriptValue;
    }

    protected function _getEmailDecrypt(){
        $encriptService = EncryptService::Instance();
        $value = $this->email;
        $decryptValue = $encriptService->decrypt(trim($value));
        return $decryptValue;
    }
}
