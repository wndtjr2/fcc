<?php
namespace App\Model\Table;

use App\Model\Entity\ChPayment;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChPayment Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Payments
 * @property \Cake\ORM\Association\BelongsTo $Transactions
 * @property \Cake\ORM\Association\BelongsTo $ParentTransactions
 */
class ChPaymentTable extends Table
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

        $this->table('ch_payment');
        $this->displayField('payment_id');
        $this->primaryKey('payment_id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('ChOrder',[
            'foreignKey' => 'order_code'
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
            ->requirePresence('order_code', 'create')
            ->notEmpty('order_code');

        $validator
            ->requirePresence('gateway', 'create')
            ->notEmpty('gateway');

        $validator
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->requirePresence('method', 'create')
            ->notEmpty('method');

        $validator
            ->requirePresence('gateway_status', 'create')
            ->notEmpty('gateway_status');

        $validator
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        $validator
            ->add('total', 'valid', ['rule' => 'decimal'])
            ->requirePresence('total', 'create')
            ->notEmpty('total');

        $validator
            ->add('shipping', 'valid', ['rule' => 'decimal'])
            ->requirePresence('shipping', 'create')
            ->allowEmpty('shipping');

        $validator
            ->add('handling', 'valid', ['rule' => 'decimal'])
            ->allowEmpty('handling');

        $validator
            ->add('tax', 'valid', ['rule' => 'decimal'])
            ->allowEmpty('tax');

        $validator
            ->add('fee', 'valid', ['rule' => 'decimal'])
            ->allowEmpty('fee');

        $validator
            ->allowEmpty('card_type');

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
        return $rules;
    }
}
