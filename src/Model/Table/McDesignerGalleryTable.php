<?php
namespace App\Model\Table;

use App\Model\Entity\McDesignerGallery;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * McDesignerGellery Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Designers
 * @property \Cake\ORM\Association\BelongsTo $Images
 */
class McDesignerGalleryTable extends Table
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

        $this->table('mc_designer_gallery');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('McDesigner', [
            'foreignKey' => 'designer_id',
            'joinType' => 'INNER'
        ]);
        $this->hasOne('ChImage', [
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
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('gallery_type', 'create')
            ->notEmpty('gallery_type');

        $validator
            ->requirePresence('collection', 'create')
            ->notEmpty('collection');

        $validator
            ->requirePresence('creator', 'create')
            ->notEmpty('creator');

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
        $rules->add($rules->existsIn(['designer_id'], 'Designers'));
        $rules->add($rules->existsIn(['image_id'], 'Images'));
        return $rules;
    }
}
