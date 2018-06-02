<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChOrderClaim Entity.
 *
 * @property string $order_claim_code
 * @property string $purchase_code
 * @property string $type
 * @property int $users_id
 * @property \App\Model\Entity\User $user
 * @property string $status
 * @property int $creator
 * @property \Cake\I18n\Time $created
 * @property int $modifier
 * @property \Cake\I18n\Time $modified
 * @property string $open_type
 * @property string $seller_close_yn
 * @property string $buyer_close_yn
 */
class ChOrderClaim extends Entity
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
