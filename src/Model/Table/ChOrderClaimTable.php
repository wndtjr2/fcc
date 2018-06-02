<?php
namespace App\Model\Table;

use App\Model\Entity\ChOrderClaim;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChOrderClaim Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 */
class ChOrderClaimTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('ch_order_claim');
        $this->displayField('order_claim_code');
        $this->primaryKey('order_claim_code');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'users_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('order_claim_code', 'create');

        $validator
            ->requirePresence('purchase_code', 'create')
            ->notEmpty('purchase_code');

        $validator
            ->allowEmpty('type');

        $validator
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        $validator
            ->requirePresence('creator', 'create')
            ->notEmpty('creator');

        $validator
            ->requirePresence('modifier', 'create')
            ->notEmpty('modifier');

        $validator
            ->requirePresence('open_type', 'create')
            ->notEmpty('open_type');

        $validator
            ->requirePresence('seller_close_yn', 'create')
            ->notEmpty('seller_close_yn');

        $validator
            ->requirePresence('buyer_close_yn', 'create')
            ->notEmpty('buyer_close_yn');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['users_id'], 'Users'));
        return $rules;
    }
}
