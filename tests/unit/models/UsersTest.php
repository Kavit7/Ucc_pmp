<?php

namespace tests\unit\models;

use app\models\Users;

/**
 * Covers the login/auth bugs fixed this session: password_hash storage,
 * setPassword()/validatePassword() round-tripping, and findByUsername()
 * matching by email and excluding inactive accounts.
 */
class UsersTest extends \Codeception\Test\Unit
{
    private function makeUser(array $overrides = [])
    {
        $user = new Users(array_merge([
            'uuid' => 'UT_' . uniqid(),
            'full_name' => 'Unit Test User',
            'email' => 'unit-test-' . uniqid() . '@example.com',
            'role' => 'tenant',
            'status' => 'active',
        ], $overrides));
        $user->save(false);
        return $user;
    }

    public function testSetPasswordHashesAndValidates()
    {
        $user = $this->makeUser();
        $user->setPassword('correct-horse-battery-staple');
        $user->save(false);

        verify($user->password_hash)->notEquals('correct-horse-battery-staple');
        verify($user->validatePassword('correct-horse-battery-staple'))->true();
        verify($user->validatePassword('wrong-password'))->false();
    }

    public function testPlaintextPasswordAttributeIsHashedOnSave()
    {
        $user = $this->makeUser();
        $user->password = 'another-secret-123';
        $user->save(false);

        verify($user->password_hash)->notEmpty();
        verify($user->validatePassword('another-secret-123'))->true();
    }

    public function testFindByUsernameMatchesEmailNotFullName()
    {
        $user = $this->makeUser(['full_name' => 'Findable Person']);

        verify(Users::findByUsername($user->email))->notNull();
        verify(Users::findByUsername('Findable Person'))->null();
    }

    public function testFindByUsernameExcludesInactiveAccounts()
    {
        $user = $this->makeUser(['status' => 'blocked']);

        verify(Users::findByUsername($user->email))->null();
    }

    public function testAuthKeyIsGeneratedOnInsert()
    {
        $user = $this->makeUser();

        verify($user->auth_key)->notEmpty();
        verify($user->validateAuthKey($user->auth_key))->true();
        verify($user->validateAuthKey('wrong-key'))->false();
    }
}
