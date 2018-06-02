<?php
namespace App\Model\Table;

use App\Model\Entity\McVideoInfo;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * McVideoInfo Model
 *
 * @property \Cake\ORM\Association\HasMany $McTimVideoViwInfo
 * @property \Cake\ORM\Association\HasMany $McVideoComment
 * @property \Cake\ORM\Association\HasMany $McVideoProductOptionInfo
 */
class McVideoInfoTable extends Table
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

        $this->table('mc_video_info');
        $this->displayField('title');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('McVideoComment', [
            'foreignKey' => 'mc_video_info_id'
        ]);
        $this->hasMany('McVideoProductOptionInfo', [
            'foreignKey' => 'mc_video_info_id'
        ]);

        $this->belongsTo('McTimVideoViwInfo',[
            'foreignKey' => 'id',
            'bindingKey' => 'mc_video_info_id',
            'joinType' => 'Left'
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
            ->requirePresence('view_yn', 'create')
            ->notEmpty('view_yn');

        $validator
            ->requirePresence('code', 'create')
            ->notEmpty('code');

        $validator
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        $validator
            ->requirePresence('video_path', 'create')
            ->notEmpty('video_path');

        $validator
            ->requirePresence('image_path_1', 'create')
            ->notEmpty('image_path_1');

        $validator
            ->requirePresence('image_path_2', 'create')
            ->notEmpty('image_path_2');

        $validator
            ->requirePresence('image_path_3', 'create')
            ->notEmpty('image_path_3');

        $validator
            ->requirePresence('image_path_4', 'create')
            ->notEmpty('image_path_4');

        $validator
            ->requirePresence('image_path_5', 'create')
            ->notEmpty('image_path_5');

        $validator
            ->requirePresence('video_info', 'create')
            ->notEmpty('video_info');

        $validator
            ->requirePresence('onair_yn', 'create')
            ->notEmpty('onair_yn');

        return $validator;
    }
}
