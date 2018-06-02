<?php
namespace App\Model\Table;

use App\Model\Entity\ChProduct;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChProduct Model
 *
 * @property \Cake\ORM\Association\BelongsTo $MainImages
 * @property \Cake\ORM\Association\BelongsTo $SubImages
 * @property \Cake\ORM\Association\BelongsTo $Videos
 * @property \Cake\ORM\Association\BelongsTo $Users
 */
class ChProductTable extends Table
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

        $this->table('ch_product');
        $this->displayField('name');
        $this->primaryKey('product_code');
        $this->addBehavior('Timestamp');

        $this->belongsTo('ChImage', [
            'foreignKey' => 'main_image_id'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'users_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('McDesigner', [
            'foreignKey' => 'designer_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('McDesigner', [
            'foreignKey' => 'designer_id',
        ]);
        $this->hasMany('ChProductOption', [
            'foreignKey' => 'product_code',
        ]);
        $this->hasMany('ChProductComment', [
            'foreignKey' => 'product_code'
        ]);
        $this->hasMany('ChProductReview', [
            'foreignKey' => 'product_code'
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
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->requirePresence('reg_type', 'create')
            ->notEmpty('reg_type');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('product_no', 'create')
            ->notEmpty('product_no');

        $validator
            ->requirePresence('content', 'create')
            ->notEmpty('content');

        $validator
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        $validator
            ->requirePresence('country_code', 'create')
            ->notEmpty('country_code');

        $validator
            ->requirePresence('city_code', 'create')
            ->notEmpty('city_code');

        $validator
            ->add('category1', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('category1');

        $validator
            ->add('category2', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('category2');

        $validator
            ->allowEmpty('category3');

        $validator
            ->allowEmpty('custom_category');

        $validator
            ->add('like_count', 'valid', ['rule' => 'numeric'])
            ->requirePresence('like_count', 'create')
            ->notEmpty('like_count');

        $validator
            ->add('view_count', 'valid', ['rule' => 'numeric'])
            ->requirePresence('view_count', 'create')
            ->notEmpty('view_count');

        $validator
            ->add('accure_like_count', 'valid', ['rule' => 'numeric'])
            ->requirePresence('accure_like_count', 'create')
            ->notEmpty('accure_like_count');

        $validator
            ->allowEmpty('address');

        $validator
            ->allowEmpty('address2');

        $validator
            ->allowEmpty('zipcode');

        $validator
            ->allowEmpty('picked_up_country_code');

        $validator
            ->allowEmpty('picked_up_city_name');

        $validator
            ->requirePresence('delivery_yn', 'create')
            ->notEmpty('delivery_yn');

        $validator
            ->add('price', 'valid', ['rule' => 'decimal'])
            ->requirePresence('price', 'create')
            ->notEmpty('price');

        $validator
            ->add('expires_date', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('expires_date');

        $validator
            ->requirePresence('creator', 'create')
            ->notEmpty('creator');

        $validator
            ->requirePresence('modifier', 'create')
            ->notEmpty('modifier');

        $validator
            ->requirePresence('del_yn', 'create')
            ->notEmpty('del_yn');

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
