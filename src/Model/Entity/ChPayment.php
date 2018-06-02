<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChPayment Entity.
 */
class ChPayment extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'payment_id' => true,
        'order_code' => true,
        'transaction_id' => true,
        'parent_transaction_id' => true,
        'gateway' => true,
        'type' => true,
        'method' => true,
        'gateway_status' => true,
        'status' => true,
        'total' => true,
        'shipping' => true,
        'handling' => true,
        'tax' => true,
        'fee' => true,
        'card_type' => true
    ];

    protected function _setMethod($value){
        if($value == 'P101' or $value == 'P102' or $value == 'P103' or $value == 'P104' or $value == 'P000'){
            return 'credit';
        }else{
            return $value;
        }
    }
}
