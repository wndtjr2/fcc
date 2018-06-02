<?php
namespace App\Model\Table;

use App\Model\Entity\ChProductOptionI18n;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChProductOptionI18n Model
 *
 */
class ChProductOptionI18nTable extends Table
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

        $this->table('ch_product_option_i18n');
        $this->displayField('name');
        $this->primaryKey(['product_option_code', 'language_code']);
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
            ->allowEmpty('product_option_code', 'create');

        $validator
            ->allowEmpty('language_code', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('translate_by_user_yn', 'create')
            ->notEmpty('translate_by_user_yn');

        return $validator;
    }
}
