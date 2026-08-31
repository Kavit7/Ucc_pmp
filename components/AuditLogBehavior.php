<?php

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\models\AuditLog;

/**
 * Attach to an ActiveRecord's behaviors() to record every create/update/
 * delete of that model into the audit_log table, e.g.:
 *
 *   public function behaviors()
 *   {
 *       return [
 *           'audit' => ['class' => AuditLogBehavior::class],
 *       ];
 *   }
 *
 * Use $excludeAttributes to keep sensitive fields (password hashes,
 * tokens) out of the logged diff.
 */
class AuditLogBehavior extends Behavior
{
    /** @var string[] attribute names never included in a logged diff/snapshot */
    public $excludeAttributes = ['password_hash', 'auth_key', 'password_reset_token'];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'logInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'logUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'logDelete',
        ];
    }

    public function logInsert($event)
    {
        $this->write('create', $this->filtered($event->sender->getAttributes()));
    }

    public function logUpdate($event)
    {
        $diff = [];
        foreach ($event->changedAttributes as $attr => $oldValue) {
            if (in_array($attr, $this->excludeAttributes, true)) {
                continue;
            }
            $newValue = $event->sender->getAttribute($attr);
            if ($oldValue !== $newValue) {
                $diff[$attr] = ['old' => $oldValue, 'new' => $newValue];
            }
        }

        if ($diff) {
            $this->write('update', $diff);
        }
    }

    public function logDelete($event)
    {
        $this->write('delete', $this->filtered($event->sender->getAttributes()));
    }

    private function filtered($attributes)
    {
        return array_diff_key($attributes, array_flip($this->excludeAttributes));
    }

    private function write($action, $data)
    {
        $log = new AuditLog();
        $log->user_id = (Yii::$app->has('user') && !Yii::$app->user->isGuest) ? Yii::$app->user->id : null;
        $log->action = $action;
        $log->model_name = get_class($this->owner);
        $pk = $this->owner->getPrimaryKey();
        $log->model_id = is_array($pk) ? json_encode($pk) : (string) $pk;
        $log->changes = json_encode($data);
        $log->save(false);
    }
}
