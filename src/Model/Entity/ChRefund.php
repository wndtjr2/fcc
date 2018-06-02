<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChRefund Entity.
 *
 * @property int $refund_id
 * @property string $purchase_code
 * @property string $transaction_id
 * @property string $status
 * @property float $amount
 * @property string $content
 * @property string $creator
 * @property \Cake\I18n\Time $created
 * @property string $modifier
 * @property \Cake\I18n\Time $modified
 */
class ChRefund extends Entity
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
    ];
}
