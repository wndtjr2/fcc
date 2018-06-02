<?php
namespace App\Model\Table;

use App\Model\Entity\McTimVideoViwInfo;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * McTimVideoViwInfo Model
 *
 * @property \Cake\ORM\Association\BelongsTo $McVideoInfos
 */
class McTimVideoViwInfoTable extends Table
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

        $this->table('mc_tim_video_viw_info');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasOne('McVideoInfo', [
            'foreignKey' => 'id',
            'bindingKey' => 'mc_video_info_id',
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
            ->allowEmpty('id', 'create');

        $validator
            ->add('st_dtm', 'valid', ['rule' => 'datetime'])
            ->requirePresence('st_dtm', 'create')
            ->notEmpty('st_dtm');

        $validator
            ->add('ed_dtm', 'valid', ['rule' => 'datetime'])
            ->requirePresence('ed_dtm', 'create')
            ->notEmpty('ed_dtm');

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
        return $rules;
    }
}
