<?php
namespace App\Model\Table;

use App\Model\Entity\ChCategory;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChCategory Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ParentChCategory
 * @property \Cake\ORM\Association\BelongsTo $Images
 * @property \Cake\ORM\Association\HasMany $ChildChCategory
 */
class ChCategoryTable extends Table
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

        $this->table('ch_category');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->belongsTo('ChImage', [
            'foreignKey' => 'image_id',
            'joinType' => 'INNER'
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
            ->add('depth', 'valid', ['rule' => 'numeric'])
            ->requirePresence('depth', 'create')
            ->notEmpty('depth');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->add('seq', 'valid', ['rule' => 'numeric'])
            ->requirePresence('seq', 'create')
            ->notEmpty('seq');

        $validator
            ->requirePresence('launched_yn', 'create')
            ->notEmpty('launched_yn');

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
        $rules->add($rules->existsIn(['parent_id'], 'ParentChCategory'));
        $rules->add($rules->existsIn(['image_id'], 'Images'));
        return $rules;
    }
}
