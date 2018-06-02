<?php
namespace App\Model\Table;

use App\Model\Entity\ChShipping;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChShipping Model
 *
 */
class ChShippingTable extends Table
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

        $this->table('ch_shipping');
        $this->displayField('purchase_code');
        $this->primaryKey('purchase_code');
        $this->addBehavior('Timestamp');
        $this->hasOne('CodeCountry',[
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
            ->allowEmpty('purchase_code', 'create');

        $validator
            ->requirePresence('zipcode', 'create')
            ->notEmpty('zipcode');

        $validator
            ->requirePresence('country_code', 'create')
            ->notEmpty('country_code');

        $validator
            ->requirePresence('state', 'create')
            ->notEmpty('state');

        $validator
            ->requirePresence('city_name', 'create')
            ->notEmpty('city_name');

        $validator
            ->requirePresence('address', 'create')
            ->notEmpty('address');

        $validator
            ->requirePresence('address2', 'create')
            ->notEmpty('address2');

        $validator
            ->allowEmpty('shipping_company');

        $validator
            ->allowEmpty('tracking_number1');

        $validator
            ->allowEmpty('tracking_number2');

        $validator
            ->allowEmpty('tracking_number3');

        $validator
            ->allowEmpty('tracking_url');

        $validator
            ->requirePresence('creator', 'create')
            ->allowEmpty('creator');

        $validator
            ->requirePresence('modifier', 'create')
            ->allowEmpty('modifier');

        $validator
            ->allowEmpty('tracking_status1');

        $validator
            ->allowEmpty('tracking_status2');

        $validator
            ->allowEmpty('tracking_status3');

        $validator
            ->add('delivered_date1', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('delivered_date1');

        $validator
            ->add('delivered_date2', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('delivered_date2');

        $validator
            ->add('delivered_date3', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('delivered_date3');

        $validator
            ->allowEmpty('deliv_first_name');

        $validator
            ->allowEmpty('deliv_last_name');

        $validator
            ->allowEmpty('deliv_phone_num');

        return $validator;
    }
}
