<?php
namespace App\Model\Table;

use App\Model\Entity\McNewProduct;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * McNewProduct Model
 *
 */
class McNewProductTable extends Table
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

        $this->table('mc_new_product');
        $this->displayField('id');
        $this->primaryKey('id');

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
            ->allowEmpty('id', 'create');

        $validator
            ->add('seq', 'valid', ['rule' => 'numeric'])
            ->requirePresence('seq', 'create')
            ->notEmpty('seq');

        $validator
            ->requirePresence('view_yn', 'create')
            ->notEmpty('view_yn');

        $validator
            ->requirePresence('product_code', 'create')
            ->notEmpty('product_code');

        return $validator;
    }
}
