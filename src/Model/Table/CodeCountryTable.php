<?php
namespace App\Model\Table;

use App\Model\Entity\CodeCountry;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CodeCountry Model
 *
 * @property \Cake\ORM\Association\HasMany $Users
 */
class CodeCountryTable extends Table
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

        $this->table('code_country');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->hasMany('Users', [
            'foreignKey' => 'code_country_id'
        ]);

        $this->belongsTo('ChProductShippingCharge',[
            'foreignKey' => 'country_code'
        ]);

        $this->belongsTo('McUserAddrInfo',[
            'foreignKey' => 'country_code'
        ]);
        $this->belongsTo('ChShipping',[
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
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('country_name', 'create')
            ->notEmpty('country_name');

        $validator
            ->requirePresence('country_code', 'create')
            ->notEmpty('country_code');

        return $validator;
    }
}
