<?php
namespace App\Model\Table;

use App\Model\Entity\ChProductOption;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChProductOption Model
 *
 */
class ChProductOptionTable extends Table
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

        $this->table('ch_product_option');
        $this->displayField('name');
        $this->primaryKey('product_option_code');
        $this->addBehavior('Timestamp');

        $this->belongsTo('ChProduct', [
            'foreignKey' => 'product_code',
            'bindingKey' =>'product_code'
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
            ->allowEmpty('product_option_code', 'create');

        $validator
            ->requirePresence('product_code', 'create')
            ->notEmpty('product_code');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->add('price', 'valid', ['rule' => 'decimal'])
            ->requirePresence('price', 'create')
            ->notEmpty('price');

        $validator
            ->add('stock', 'valid', ['rule' => 'numeric'])
            ->requirePresence('stock', 'create')
            ->notEmpty('stock');

        $validator
            ->add('max_purchase', 'valid', ['rule' => 'numeric'])
            ->requirePresence('max_purchase', 'create')
            ->notEmpty('max_purchase');

        $validator
            ->add('seq', 'valid', ['rule' => 'numeric'])
            ->requirePresence('seq', 'create')
            ->notEmpty('seq');

        $validator
            ->requirePresence('use_yn', 'create')
            ->notEmpty('use_yn');

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
}
