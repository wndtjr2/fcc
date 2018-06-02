<?php
namespace App\Model\Table;

use App\Model\Entity\ChProductI18n;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChProductI18n Model
 *
 */
class ChProductI18nTable extends Table
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

        $this->table('ch_product_i18n');
        $this->displayField('name');
        $this->primaryKey(['product_code', 'language_code']);
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
            ->allowEmpty('language_code', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('content', 'create')
            ->notEmpty('content');

        $validator
            ->requirePresence('default_yn', 'create')
            ->notEmpty('default_yn');

        $validator
            ->requirePresence('translate_by_user_yn', 'create')
            ->notEmpty('translate_by_user_yn');

        return $validator;
    }
}
