<?php
namespace App\Model\Table;

use App\Model\Entity\ChCodeGen;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChCodeGen Model
 *
 */
class ChCodeGenTable extends Table
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

        $this->table('ch_code_gen');
        $this->displayField('cdg_kind');
        $this->primaryKey('cdg_kind');
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
            ->allowEmpty('cdg_kind', 'create');

        $validator
            ->allowEmpty('prefix');

        $validator
            ->allowEmpty('last_num');

        $validator
            ->allowEmpty('code_len');

        $validator
            ->allowEmpty('impl_class');

        return $validator;
    }
}
