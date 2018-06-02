<?php
namespace App\Model\Table;

use App\Model\Entity\McUserAddrInfo;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * McUserAddrInfo Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 */
class McUserAddrInfoTable extends Table
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

        $this->table('mc_user_addr_info');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'users_id',
            'joinType' => 'INNER'
        ]);

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
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('default_addr', 'create')
            ->notEmpty('default_addr');

        $validator
            ->requirePresence('deliv_first_name', 'create')
            ->notEmpty('deliv_first_name');

        $validator
            ->requirePresence('deliv_last_name', 'create')
            ->notEmpty('deliv_last_name');

        $validator
            ->requirePresence('zipcode', 'create')
            ->notEmpty('zipcode');

        $validator
            ->requirePresence('country_code', 'create')
            ->notEmpty('country_code');
        /*
        $validator
            ->requirePresence('state', 'create')
            ->notEmpty('state');

        $validator
            ->requirePresence('city_name', 'create')
            ->notEmpty('city_name');
        */
        $validator
            ->requirePresence('address', 'create')
            ->notEmpty('address');

        $validator
            ->requirePresence('address2', 'create')
            ->notEmpty('address2');

        $validator
            ->requirePresence('deliv_phone_num', 'create')
            ->notEmpty('deliv_phone_num');

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
