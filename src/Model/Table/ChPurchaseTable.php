<?php
namespace App\Model\Table;

use App\Model\Entity\ChPurchase;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChPurchase Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Sellers
 * @property \Cake\ORM\Association\BelongsTo $Buyers
 */
class ChPurchaseTable extends Table
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

        $this->table('ch_purchase');
        $this->displayField('purchase_code');
        $this->primaryKey('purchase_code');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users', [
            'foreignKey' => 'users_id'
        ]);

        $this->belongsTo('ChProduct',[
            'foreignKey' => 'product_code'
        ]);

        $this->belongsTo('ChProductOption',[
            'foreignKey' => 'product_option_code'
        ]);

//        $this->belongsTo('ChOrder',[
//            'foreignKey' => 'order_code'
//        ]);

        $this->hasOne('ChShipping',[
            'foreignKey' => 'purchase_code'
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
            ->allowEmpty('purchase_code', 'create');

        $validator
            ->requirePresence('order_code', 'create')
            ->notEmpty('order_code');

        $validator
            ->requirePresence('product_code', 'create')
            ->notEmpty('product_code');

        $validator
            ->requirePresence('product_option_code', 'create')
            ->notEmpty('product_option_code');

        $validator
            ->add('amount', 'valid', ['rule' => 'decimal'])
            ->requirePresence('amount', 'create')
            ->notEmpty('amount');

        $validator
            ->add('unit_price', 'valid', ['rule' => 'decimal'])
            ->requirePresence('unit_price', 'create')
            ->notEmpty('unit_price');

        $validator
            ->add('quantity', 'valid', ['rule' => 'numeric'])
            ->requirePresence('quantity', 'create')
            ->notEmpty('quantity');

        $validator
            ->add('shipping_price', 'valid', ['rule' => 'decimal'])
            ->requirePresence('shipping_price', 'create')
            ->notEmpty('shipping_price');

        $validator
            ->requirePresence('status', 'create')
            ->notEmpty('status');

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
