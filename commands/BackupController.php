<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Database backups, stored outside the web-accessible directory. Intended
 * to run daily via a scheduled task (cron / Windows Task Scheduler):
 *   php yii backup/run
 */
class BackupController extends Controller
{
    /** @var int how many backup files to keep before pruning the oldest */
    public $retentionCount = 14;

    public function options($actionID)
    {
        return ['retentionCount'];
    }

    public function actionRun()
    {
        $dsn = Yii::$app->db->dsn;
        if (!preg_match('/dbname=([^;]+)/', $dsn, $dbMatch) || !preg_match('/host=([^;]+)/', $dsn, $hostMatch)) {
            $this->stderr("Could not parse database name/host from the configured DSN.\n");
            return ExitCode::CONFIG;
        }
        $dbName = $dbMatch[1];
        $host = $hostMatch[1];
        $username = Yii::$app->db->username;
        $password = Yii::$app->db->password;

        $mysqldump = $this->findMysqldump();
        if (!$mysqldump) {
            $this->stderr("Could not locate mysqldump. Set params['backup.mysqldumpPath'] in config/params.php.\n");
            return ExitCode::UNAVAILABLE;
        }

        $backupDir = Yii::getAlias('@app/runtime/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        $filename = $dbName . '_' . date('Ymd_His') . '.sql';
        $filePath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $command = [$mysqldump, '--host=' . $host, '--user=' . $username];
        if ($password !== '') {
            $command[] = '--password=' . $password;
        }
        $command[] = '--single-transaction';
        $command[] = '--routines';
        $command[] = $dbName;

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', $filePath, 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes);
        if (!is_resource($process)) {
            $this->stderr("Failed to start mysqldump.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        fclose($pipes[0]);
        $errorOutput = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            @unlink($filePath);
            $this->stderr("mysqldump failed (exit {$exitCode}): {$errorOutput}\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $sizeKb = round(filesize($filePath) / 1024, 1);
        $this->stdout("Backup created: {$filename} ({$sizeKb} KB)\n");

        $this->pruneOldBackups($backupDir, $dbName);

        return ExitCode::OK;
    }

    private function findMysqldump()
    {
        $configured = Yii::$app->params['backup.mysqldumpPath'] ?? null;
        if ($configured && is_file($configured)) {
            return $configured;
        }

        $candidates = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/usr/local/mysql/bin/mysqldump',
        ];
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        // Last resort: rely on PATH.
        $which = stripos(PHP_OS, 'WIN') === 0 ? 'where mysqldump' : 'which mysqldump';
        $found = trim(shell_exec($which . ' 2>&1') ?? '');
        if ($found && is_file(explode("\n", $found)[0])) {
            return explode("\n", $found)[0];
        }

        return null;
    }

    private function pruneOldBackups($backupDir, $dbName)
    {
        $files = glob($backupDir . DIRECTORY_SEPARATOR . $dbName . '_*.sql');
        if (count($files) <= $this->retentionCount) {
            return;
        }

        usort($files, function ($a, $b) {
            return filemtime($a) <=> filemtime($b);
        });

        $toDelete = array_slice($files, 0, count($files) - $this->retentionCount);
        foreach ($toDelete as $file) {
            @unlink($file);
            $this->stdout('Pruned old backup: ' . basename($file) . "\n");
        }
    }
}
