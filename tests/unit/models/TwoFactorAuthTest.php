<?php

namespace tests\unit\models;

use app\models\LoginForm;
use app\models\Users;
use OTPHP\TOTP;

/**
 * Covers the two-factor authentication flow added this session:
 * LoginForm::login() must defer a normal login and hand back
 * 'pending_2fa' for accounts with TOTP enabled, and must log in
 * normally (unaffected) for accounts without it. Also covers the TOTP
 * secret round-trip itself, since a library upgrade silently changing
 * verify() behavior would otherwise go unnoticed.
 */
class TwoFactorAuthTest extends \Codeception\Test\Unit
{
    private function makeUser(array $overrides = [])
    {
        $user = new Users(array_merge([
            'uuid' => 'UT_' . uniqid(),
            'full_name' => '2FA Test User',
            'email' => '2fa-test-' . uniqid() . '@example.com',
            'role' => 'tenant',
            'status' => 'active',
        ], $overrides));
        $user->setPassword('correct-horse-battery-staple');
        $user->save(false);
        return $user;
    }

    protected function _after()
    {
        \Yii::$app->user->logout();
        \Yii::$app->session->remove('2fa_pending_uid');
        \Yii::$app->session->remove('2fa_remember_duration');
    }

    public function testTotpAcceptsTheCurrentCodeAndRejectsAWrongOne()
    {
        $secret = TOTP::generate(null, 20)->getSecret();
        $totp = TOTP::createFromSecret($secret);

        verify($totp->verify($totp->now()))->true();
        verify($totp->verify('000000'))->false();
    }

    public function testLoginDefersAndDoesNotAuthenticateWhenTotpIsEnabled()
    {
        $secret = TOTP::generate(null, 20)->getSecret();
        $user = $this->makeUser(['totp_secret' => $secret, 'totp_enabled' => 1]);

        $form = new LoginForm(['username' => $user->email, 'password' => 'correct-horse-battery-staple']);
        $result = $form->login();

        verify($result)->equals('pending_2fa');
        verify(\Yii::$app->user->isGuest)->true();
        verify(\Yii::$app->session->get('2fa_pending_uid'))->equals($user->user_id);
    }

    public function testLoginProceedsNormallyWhenTotpIsNotEnabled()
    {
        $user = $this->makeUser();

        $form = new LoginForm(['username' => $user->email, 'password' => 'correct-horse-battery-staple']);
        $result = $form->login();

        verify($result)->true();
        verify(\Yii::$app->user->isGuest)->false();
        verify(\Yii::$app->session->get('2fa_pending_uid'))->null();
    }
}
