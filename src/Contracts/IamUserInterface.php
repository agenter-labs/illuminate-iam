<?php

namespace AgenterLab\IAM\Contracts;


interface IamUserInterface
{
    /**
     * Many-to-Many relations with Company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies();

    /**
     * Many-to-Many relations with Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();

    /**
     * Check if user has a permission by its name.
     *
     * @param  string|array  $permission Permission string or array of permissions.
     * @param  mixed company
     * @param  bool  $requireAll All roles in the array are required.
     * @return bool
     */
    public function hasPermission($permission, $company = null, bool $requireAll = false);

    /**
     * Alias to eloquent many-to-many relation's attach() method.
     *
     * @param mixed $role
     * @param mixed $company
     * @return static
     */
    public function attachRole($role, $company = null);

    /**
     * Alias to eloquent many-to-many relation's detach() method.
     *
     * @param mixed $role
     * @param mixed $company
     * @return static
     */
    public function detachRole($role, $company = null);

    /**
     * Attach multiple roles to a user.
     *
     * @param mixed $roles
     * @param mixed $company
     * @return static
     */
    public function attachRoles($roles = [], $company = null);

    /**
     * Detach multiple roles from a user.
     *
     * @param mixed $roles
     * @param mixed $company
     * @return static
     */
    public function detachRoles($roles = [], $company = null);

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey();
}
