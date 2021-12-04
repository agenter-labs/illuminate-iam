<?php

namespace AgenterLab\IAM\Checkers\User;

use AgenterLab\IAM\Contracts\IamUserInterface;

abstract class UserChecker
{
    /**
     * @var \AgenterLab\IAM\Contracts\IamUserInterface
     */
    protected $user;

    public function __construct(IamUserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Check if user has a permission by its name.
     *
     * @param  string|array  $permission Permission string or array of permissions.
     * @param  mixed company
     * @param  bool  $requireAll All roles in the array are required.
     * @return bool
     */
    abstract public function hasPermission($permission, $company = null, bool $requireAll = false);

    /**
     * Flush cache
     * 
     * @param mixed $company
     */
    abstract public function flushCache($company = null);
}