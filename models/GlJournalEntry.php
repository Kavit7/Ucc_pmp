<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * One business event in the ledger (a bill raised, a payment collected, an
 * expense logged), made up of two or more balanced debit/credit lines.
 * Created only through Ledger::post() - never assembled by hand - so the
 * "always balanced" invariant has exactly one place it can be broken.
 */
class GlJournalEntry extends ActiveRecord
{
    public static function tableName()
    {
        return 'gl_journal_entry';
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
            [['entry_date', 'description'], 'required'],
            [['entry_date'], 'date', 'format' => 'php:Y-m-d'],
            [['description'], 'string', 'max' => 255],
            [['reference_type'], 'string', 'max' => 50],
            [['reference_id', 'created_by'], 'integer'],
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert && empty($this->uuid)) {
            $this->uuid = Yii::$app->security->generateRandomString(16);
        }
        return true;
    }

    public function getLines()
    {
        return $this->hasMany(GlJournalLine::class, ['journal_entry_id' => 'id']);
    }

    public function getCreator()
    {
        return $this->hasOne(Users::class, ['user_id' => 'created_by']);
    }
}
