<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChCart Entity.
 *
 * @property int $cart_id
 * @property \App\Model\Entity\Cart $cart
 * @property int $users_id
 * @property \App\Model\Entity\User $user
 * @property string $product_option_code
 * @property int $quantity
 * @property int $creator
 * @property \Cake\I18n\Time $created
 * @property int $modifier
 * @property \Cake\I18n\Time $modified
 */
class ChCart extends Entity
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
        'cart_id' => false,
        'users_id' => true,
    ];
}
