<?php
namespace App\Model\Table;

use App\Model\Entity\ChOrder;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChOrder Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Transactions
 */
class ChOrderTable extends Table
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

        $this->table('ch_order');
        $this->displayField('order_code');
        $this->primaryKey('order_code');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users', [
            'foreignKey' => 'users_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('ChPurchase',[
            'foreignKey' => 'order_code',
        ]);

        $this->hasOne("ChPayment",[
            'foreignKey' => 'order_code',
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
            ->allowEmpty('order_code', 'create');

        $validator
            ->add('total_price', 'valid', ['rule' => 'decimal'])
            ->requirePresence('total_price', 'create')
            ->allowEmpty('total_price');

        $validator
            ->requirePresence('status', 'create')
            ->allowEmpty('status');

        $validator
            ->requirePresence('creator', 'create')
            ->allowEmpty('creator');

        $validator
            ->requirePresence('modifier', 'create')
            ->allowEmpty('modifier');

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
//        $rules->add($rules->existsIn(['transaction_id'], 'Transactions'));
        return $rules;
    }
}
