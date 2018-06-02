<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\BelongsTo $UserAccounts
 * @property \Cake\ORM\Association\BelongsTo $CodeLanguage
 * @property \Cake\ORM\Association\BelongsTo $CodeCountry
 */
class UsersTable extends Table
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

        $this->table('users');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('UserAccounts', [
            'foreignKey' => 'user_accounts_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('CodeLanguage', [
            'foreignKey' => 'code_language_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('CodeCountry', [
            'foreignKey' => 'code_country_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('LogUserSign', [
            'foreignKey' => 'users_id',
            'dependent' => true
        ]);
        $this->belongsTo('ChallengeRanking',[
            'foreighKey' => 'id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('ChProductReview', [
            'foreignKey' => 'users_id',
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
            ->allowEmpty('id', 'create','gender');

        $validator
            ->notEmpty('status');

        $validator
            ->notEmpty('first_name');

        $validator
            ->notEmpty('last_name');

//        $validator
//            ->allowEmpty('nickname');
//
//        $validator
//            ->notEmpty('image_storage');
//
//        $validator
//            ->notEmpty('image_path');
//
//        $validator
//            ->notEmpty('image_name');
//
//        $validator
//            ->notEmpty('image_extension');
//
//        $validator
//            ->add('birthday', 'valid', ['rule' => 'date'])
//            ->notEmpty('birthday');
//
//        $validator
//            ->notEmpty('country');
//
//        $validator
//            ->notEmpty('city');
//
//        $validator
//            ->allowEmpty('phone_number');
//
//        $validator
//            ->notEmpty('personaldata_check');
//
//        $validator
//            ->notEmpty('push_onoff');

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
        $rules->add($rules->existsIn(['user_accounts_id'], 'UserAccounts'));
        $rules->add($rules->existsIn(['code_language_id'], 'CodeLanguage'));
        $rules->add($rules->existsIn(['code_country_id'], 'CodeCountry'));
        return $rules;
    }
}
