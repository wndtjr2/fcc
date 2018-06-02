<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * McProductAsk Entity.
 *
 * @property int $id
 * @property string $product_code
 * @property int $reply_id
 * @property \App\Model\Entity\Reply $reply
 * @property string $title
 * @property string $contents
 * @property string $reply_yn
 * @property \Cake\I18n\Time $created
 * @property int $creator
 */
class McProductAsk extends Entity
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
