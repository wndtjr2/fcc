<?php
namespace App\Model\Table;

use App\Model\Entity\ChImageFile;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChImageFile Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ImageFiles
 * @property \Cake\ORM\Association\BelongsTo $Images
 */
class ChImageFileTable extends Table
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

        $this->table('ch_image_file');
        $this->displayField('image_file_id');
        $this->primaryKey('image_file_id');
        $this->belongsTo('ChImage', [
            'foreignKey' => 'image_id'
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
            ->add('seq', 'valid', ['rule' => 'numeric'])
            ->requirePresence('seq', 'create')
            ->notEmpty('seq');

        $validator
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->allowEmpty('url');

        $validator
            ->allowEmpty('surl');

        $validator
            ->allowEmpty('murl');

        $validator
            ->allowEmpty('lurl');

        $validator
            ->allowEmpty('curl');

        $validator
            ->allowEmpty('path');

        $validator
            ->allowEmpty('file_name');

        $validator
            ->requirePresence('file_sizes', 'create')
            ->notEmpty('file_sizes');

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
        $rules->add($rules->existsIn(['image_file_id'], 'ImageFiles'));
        $rules->add($rules->existsIn(['image_id'], 'Images'));
        return $rules;
    }
}
