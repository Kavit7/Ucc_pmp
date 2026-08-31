<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $uuid
 * @property int|null $user_id
 * @property string $action
 * @property string $model_name
 * @property string $model_id
 * @property string|null $changes
 * @property string $created_at
 *
 * @property Users $user
 */
class AuditLog extends ActiveRecord
{
    public static function tableName()
    {
        return 'audit_log';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => function () { return date('Y-m-d H:i:s'); },
            ],
        ];
    }

    public function rules()
    {
        return [
            [['action', 'model_name', 'model_id'], 'required'],
            [['user_id'], 'integer'],
            [['changes'], 'string'],
            [['created_at'], 'safe'],
            [['action'], 'string', 'max' => 20],
            [['model_name'], 'string', 'max' => 100],
            [['model_id'], 'string', 'max' => 50],
            [['uuid'], 'string', 'max' => 100],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->uuid)) {
                $this->uuid = Yii::$app->security->generateRandomString(32);
            }
            return true;
        }
        return false;
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'user_id']);
    }

    /**
     * @return array decoded [attribute => ['old' => ..., 'new' => ...]] diff,
     * or the raw attribute snapshot for create/delete actions.
     */
    public function getChangesDecoded()
    {
        return json_decode($this->changes, true) ?: [];
    }
}
