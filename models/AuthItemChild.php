<?php

namespace app\models;

use Yii;

/**
 * Model class para la tabla "public.auth_item_child".
 *
 * @property string $parent
 * @property string $child
 *
 * @property AAuthItem $parent0
 * @property AAuthItem $child0
 */
class AuthItemChild extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item_child';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent', 'child'], 'required'],
            [['parent', 'child'], 'string', 'max' => 64],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => AAuthItem::className(), 'targetAttribute' => ['parent' => 'name']],
            [['child'], 'exist', 'skipOnError' => true, 'targetClass' => AAuthItem::className(), 'targetAttribute' => ['child' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parent' => 'Parent',
            'child' => 'Child',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent0()
    {
        return $this->hasOne(AAuthItem::className(), ['name' => 'parent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChild0()
    {
        return $this->hasOne(AAuthItem::className(), ['name' => 'child']);
    }
}
