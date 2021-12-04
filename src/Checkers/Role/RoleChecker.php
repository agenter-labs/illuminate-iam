<?php

namespace AgenterLab\IAM\Checkers\Role;

use AgenterLab\IAM\Contracts\IamRoleInterface;

abstract class RoleChecker
{
    /**
     * @var \AgenterLab\IAM\Contracts\IamRoleInterface
     */
    protected $role;

    public function __construct(IamRoleInterface $role)
    {
        $this->role = $role;
    }

    /**
     * Check if role has a permission by its name.
     *
     * @param  string|array  $permission Permission string or array of permissions.
     * @param  bool  $requireAll All roles in the array are required.
     * @return bool
     */
    abstract public function hasPermission($permission, bool $requireAll = false);

    /**
     * Flush cache
     */
    abstract public function flushCache();
}