<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CsTeaserQuestion Entity.
 *
 * @property int $id
 * @property string $from_email
 * @property string $email
 * @property string $subject
 * @property string $message
 * @property int $admin_id
 * @property \App\Model\Entity\Admin $admin
 * @property string $is_replied
 * @property string $trash
 * @property \Cake\I18n\Time $created
 */
class CsTeaserQuestion extends Entity
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
