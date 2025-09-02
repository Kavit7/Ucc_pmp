<?php
// Simple test script for admin login

use Yii;
use app\models\User;

// 1. Connect DB (uses Yii2 db config automatically)
$db = Yii::$app->db;

try {
    $db->open();
    echo "✅ Database connection successful.\n";
} catch (\Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

// 2. Check if admin user exists
$username = 'admin';
$user = User::findOne(['username' => $username, 'status' => 10]);

if (!$user) {
    echo "❌ Admin user not found in database.\n";
    exit;
} else {
    echo "✅ Admin user found: {$user->username}\n";
}

// 3. Check password
$inputPassword = 'admin123'; // password you want to test

if (Yii::$app->getSecurity()->validatePassword($inputPassword, $user->password_hash)) {
    echo "✅ Password is correct!\n";
} else {
    echo "❌ Password mismatch!\n";
}

// 4. Optional: test login session
if (Yii::$app->user->login($user)) {
    echo "✅ Admin successfully logged in, session active.\n";
    // you can test redirect after login here
} else {
    echo "❌ Login failed.\n";
}
