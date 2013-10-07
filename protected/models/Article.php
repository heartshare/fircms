<?php

/**
 * @version   Article.php  17:53 2013年09月11日
 * @author    poctsy <pocmail@foxmail.com>
 * @copyright Copyright (c) 2013 poctsy
 * @link      http://www.fircms.com
 */


/**
 * This is the model class for table "{{article}}".
 *
 * The followings are the available columns in table '{{article}}':
 * @property integer $id
 * @property integer $base_id
 * @property string $content
 */
class Article extends FActiveRecord {

    /**
     * @return string the associated database table name
     */

    public $base_title;
    public $base_thumb;
    public $base_keyword;
    public $base_description;
    public $base_catalog_id;
    public $base_catalog_name;
    public function tableName() {
        return '{{article}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('base_id', 'unique'),
            array('base_catalog_name,base_title,base_thumb,base_keyword,base_description,base_catalog_id','safe'),
            array('base_catalog_name,base_title,base_thumb,base_keyword,base_description,base_catalog_id', 'filter', 'filter' => array($this, 'Purify')),
            array('base_id, content', 'required'),
            array('content', 'filter', 'filter' => array($this, 'contentPurify')),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, content,base_catalog_name,base_title', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'base' => array(self::BELONGS_TO, 'Base', 'base_id',),

        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'base_catalog_name'=>'栏目名称',
            'content' => '文章内容',
            'base_catalog_id'=>'栏目分类',
            'base_title' => '网页标题',
            'base_thumb'=>'缩略图',
            'base_keyword'=>'网页关键字',
            'base_description'=>'网页描述',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;
        $criteria->with= array('base','base.catalog');
        $criteria->compare('catalog.name', $this->base_catalog_name, true);
        $criteria->compare('base.title', $this->base_title, true);
        $criteria->compare('id', $this->id);
        $criteria->compare('content', $this->content, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination'=>array(
                'pageSize'=>20,
            )
        ));
    }




    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Article the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    public function afterFind(){
        //新建时，只允许获取存在的分类，不允许获取联表的当前文章值

        if($this->base->catalog !=NULL)
        $this->base_catalog_name=$this->base->catalog->name;
        if(!$this->isNewRecord){
         //获取联表的值在update动作使用
        $this->base_title=$this->base->title;
        $this->base_thumb=$this->base->thumb;
        $this->base_keyword=$this->base->keyword;
        $this->base_description=$this->base->description;
        $this->base_catalog_id=$this->base->catalog_id;
        }
    }

}
