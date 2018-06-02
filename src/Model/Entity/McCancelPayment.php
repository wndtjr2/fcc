<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * McCancelPayment Entity.
 *
 * @property int $id
 * @property string $transaction_id
 * @property \App\Model\Entity\Transaction $transaction
 * @property string $order_code
 * @property string $message
 * @property string $status
 * @property string $err_msg
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class McCancelPayment extends Entity
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
