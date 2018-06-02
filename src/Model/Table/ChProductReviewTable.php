<?php
namespace App\Model\Table;

use App\Model\Entity\ChProductReview;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChProductReview Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 */
class ChProductReviewTable extends Table
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

        $this->table('ch_product_review');
        $this->displayField('product_code');
        $this->primaryKey(['product_code', 'users_id', 'purchase_code']);
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users', [
            'foreignKey' => 'users_id',
        ]);
        $this->belongsTo('ChProduct',[
            'foreignKey' => 'product_code',
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
            ->allowEmpty('purchase_code', 'create');

        $validator
            ->add('rating', 'valid', ['rule' => 'numeric'])
            ->requirePresence('rating', 'create')
            ->notEmpty('rating');

        $validator
            ->requirePresence('content', 'create')
            ->notEmpty('content');

        $validator
            ->requirePresence('del_yn', 'create')
            ->notEmpty('del_yn');

        $validator
            ->requirePresence('creator', 'create')
            ->notEmpty('creator');

        $validator
            ->requirePresence('modifier', 'create')
            ->notEmpty('modifier');

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
