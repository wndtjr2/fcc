<?php
namespace App\Model\Table;

use App\Model\Entity\ChCode;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChCode Model
 *
 */
class ChCodeTable extends Table
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

        $this->table('ch_code');
        $this->displayField('name');
        $this->primaryKey(['cds_kind', 'code']);

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
            ->allowEmpty('cds_kind', 'create');

        $validator
            ->allowEmpty('code', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('note', 'create')
            ->notEmpty('note');

        $validator
            ->add('seq', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('seq');

        $validator
            ->allowEmpty('use_flag');

        $validator
            ->allowEmpty('del_flag');

        return $validator;
    }
}
