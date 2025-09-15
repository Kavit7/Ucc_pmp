<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    /**
     * Initialize RBAC: create roles, permissions, and assign permissions to roles
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // Step 1: Create permissions
        $permissions = ['create','edit','delete','assign','view','manage'];
        foreach ($permissions as $permName) {
            if (!$auth->getPermission($permName)) {
                $permission = $auth->createPermission($permName);
                $permission->description = ucfirst($permName);
                $auth->add($permission);
                echo "Permission '$permName' created.\n";
            }
        }

        // Step 2: Create roles (lowercase to match DB/user roles)
        $roles = ['tenant','manager','admin'];
        foreach ($roles as $roleName) {
            if (!$auth->getRole($roleName)) {
                $role = $auth->createRole($roleName);
                $auth->add($role);
                echo "Role '$roleName' created.\n";
            }
        }

        // Step 3: Attach permissions to roles
        // Tenant: no extra permissions
        $tenant = $auth->getRole('tenant');

        // Manager: create, edit, view
        $manager = $auth->getRole('manager');
        foreach (['create','edit','view'] as $permName) {
            $auth->addChild($manager, $auth->getPermission($permName));
        }

        // Admin: all permissions
        $admin = $auth->getRole('admin');
        foreach ($permissions as $permName) {
            $auth->addChild($admin, $auth->getPermission($permName));
        }

        echo "RBAC initialization completed.\n";
    }
}