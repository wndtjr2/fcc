<?php
namespace App\Model\Table;

use App\Model\Entity\McBasicShippingCharge;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * McBasicShippingCharge Model
 *
 */
class McBasicShippingChargeTable extends Table
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

        $this->table('mc_basic_shipping_charge');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

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
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('country_code');

        $validator
            ->add('shipping_charge', 'valid', ['rule' => 'decimal'])
            ->allowEmpty('shipping_charge');

        $validator
            ->allowEmpty('use_yn');

        $validator
            ->allowEmpty('creator');

        $validator
            ->allowEmpty('modifier');

        return $validator;
    }
}
