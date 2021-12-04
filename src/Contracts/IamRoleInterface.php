<?php

namespace AgenterLab\IAM\Contracts;

interface IamRoleInterface
{
    /**
     * Many-to-Many relations with the permission model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions();

    /**
     * Many-to-Many relations with the permission model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();

    /**
     * Save the inputted permissions.
     *
     * @param  array  $permissions
     * @return \AgenterLab\IAM\Contracts\IamRoleInterface
     */
    public function syncPermissions(array $permissions);

    /**
     * Checks if the role has a permission by its name.
     *
     * @param  string|array  $permission       Permission name or array of permission names.
     * @param  bool  $requireAll       All permissions in the array are required.
     * @return bool
     */
    public function hasPermission($permission, bool $requireAll = false);

    /**
     * Attach permission to current role.
     *
     * @param  object|array  $permission
     * @return void
     */
    public function attachPermission($permission);

    /**
     * Detach permission from current role.
     *
     * @param  object|array  $permission
     * @return void
     */
    public function detachPermission($permission);

    /**
     * Attach multiple permissions to current role.
     *
     * @param mixed $permissions
     * @return void
     */
    public function attachPermissions($permissions);

    /**
     * Detach multiple permissions from current role
     *
     * @param mixed $permissions
     * @return void
     */
    public function detachPermissions($permissions);

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey();
}
