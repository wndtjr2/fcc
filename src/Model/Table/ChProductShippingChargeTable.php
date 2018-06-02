<?php
namespace App\Model\Table;

use App\Model\Entity\ChProductShippingCharge;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChProductShippingCharge Model
 *
 */
class ChProductShippingChargeTable extends Table
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

        $this->table('ch_product_shipping_charge');
        $this->displayField('product_code');
        $this->primaryKey(['product_code', 'country_code']);
        $this->addBehavior('Timestamp');
        $this->hasMany('CodeCountry',[
            'foreignKey' => 'country_code'
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
            ->allowEmpty('product_code', 'create');

        $validator
            ->allowEmpty('country_code', 'create');

        $validator
            ->add('shipping_charge', 'valid', ['rule' => 'decimal'])
            ->requirePresence('shipping_charge', 'create')
            ->notEmpty('shipping_charge');

        $validator
            ->requirePresence('use_yn', 'create')
            ->notEmpty('use_yn');

        $validator
            ->requirePresence('creator', 'create')
            ->notEmpty('creator');

        $validator
            ->requirePresence('modifier', 'create')
            ->notEmpty('modifier');

        return $validator;
    }
}
