<?php
namespace App\Model\Table;

use App\Model\Entity\McDesigner;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * McDesigner Model
 *
 * @property \Cake\ORM\Association\BelongsTo $MainImages
 * @property \Cake\ORM\Association\BelongsTo $LogoImages
 * @property \Cake\ORM\Association\BelongsTo $Videos
 * @property \Cake\ORM\Association\BelongsTo $Vimeos
 * @property \Cake\ORM\Association\BelongsTo $Youtubes
 */
class McDesignerTable extends Table
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

        $this->table('mc_designer');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasOne('ChImage', [
            'bindingKey' => 'main_image_id',
            'foreignKey' => 'image_id',
            'joinType' => 'INNER'
        ]);
//        $this->hasOne('ChImage', [
//            'bindingKey' => 'logo_image_id',
//            'foreignKey' => 'image_id',
//            'joinType' => 'INNER'
//        ]);
        $this->hasMany('ChProduct', [
            'foreignKey' => 'designer_id'
        ]);

        $this->hasMany('McDesignerGallery', [
            'foreignKey' => 'designer_id'
        ]);
        $this->hasMany('McDesignerCategory', [
            'foreignKey' => 'designer_id'
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
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('contents', 'create')
            ->notEmpty('contents');

        $validator
            ->requirePresence('summury', 'create')
            ->notEmpty('summury');

        $validator
            ->requirePresence('creator', 'create')
            ->notEmpty('creator');

        $validator
            ->requirePresence('modifier', 'create')
            ->notEmpty('modifier');

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
        $rules->add($rules->existsIn(['main_image_id'], 'MainImages'));
        $rules->add($rules->existsIn(['logo_image_id'], 'LogoImages'));
        $rules->add($rules->existsIn(['video_id'], 'Videos'));
        $rules->add($rules->existsIn(['vimeo_id'], 'Vimeos'));
        $rules->add($rules->existsIn(['youtube_id'], 'Youtubes'));

        return $rules;
    }
}
