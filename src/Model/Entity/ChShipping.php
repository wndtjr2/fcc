<?php
namespace App\Model\Entity;

use App\Service\EncryptService;
use Cake\ORM\Entity;

/**
 * ChShipping Entity.
 */
class ChShipping extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'purchase_code' => true,
    ];

    protected function _getPhoneDecrypt(){
        $encriptService = EncryptService::Instance();
        $value = $this->deliv_phone_num;
        $decryptValue = $encriptService->decrypt(trim($value));
        return $decryptValue;
    }
}
