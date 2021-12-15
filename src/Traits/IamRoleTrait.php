<?php

namespace AgenterLab\IAM\Traits;

use AgenterLab\IAM\Helper;
use Illuminate\Support\Facades\Config;

trait IamRoleTrait
{
    /**
     * @var \AgenterLab\IAM\Checkers\Role\RoleChecker
     */
    protected $roleChecker;


    /**
     * Boots the role model and attaches event listener to
     * remove the many-to-many records when trying to delete.
     * Will NOT delete any records if the role model uses soft deletes.
     *
     * @return void|bool
     */
    public static function bootIamRoleTrait()
    {
        static::deleting(function ($role) {
            if (method_exists($role, 'bootSoftDeletes') && !$role->forceDeleting) {
                return;
            }

            $role->permissions()->sync([]);

            $role->users()->sync([]);
        });
    }

    /**
     * Many-to-Many relations with the permission model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Config::get('iam.models.permission'),
            Config::get('iam.tables.role_permission'),
            Config::get('iam.foreign_keys.role'),
            Config::get('iam.foreign_keys.permission')
        );
    }

    /**
     * Many-to-Many relations with the permission model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            Config::get('iam.models.user'),
            Config::get('iam.tables.user_role'),
            Config::get('iam.foreign_keys.role'),
            Config::get('iam.foreign_keys.user')
        );
    }

    /**
     * Save the inputted permissions.
     *
     * @param  array  $permissions
     * @return \AgenterLab\IAM\Contracts\IamRoleInterface
     */
    public function syncPermissions(array $permissions)
    {
        $mappedPermissions = [];

        foreach ($permissions as $permission) {
            $mappedPermissions[] = Helper::getIdFor($permission, 'permission');
        }

        $this->permissions()->sync($mappedPermissions);
        $this->flushCache();

        return $this;
    }

    /**
     * Checks if the role has a permission by its name.
     *
     * @param  string|array  $permission       Permission name or array of permission names.
     * @param  bool  $requireAll       All permissions in the array are required.
     * @return bool
     */
    public function hasPermission($permission, $requireAll = false)
    {
        return $this->getRoleChecker($this)->hasPermission($permission, $requireAll);
    }

    /**
     * Return the right checker for the role model.
     *
     * @return \AgenterLab\IAM\Checkers\Role\RoleChecker
     */
    protected function getRoleChecker()
    {
        if (is_null($this->roleChecker)) {
            $this->roleChecker = Helper::getRoleChecker($this);
        }
        
        return $this->roleChecker;
    }

    /**
     * Attach permission to current role.
     *
     * @param  object|array  $permission
     * @return void
     */
    public function attachPermission($permission)
    {
        $permission = Helper::getIdFor($permission, 'permission');

        $this->permissions()->syncWithoutDetaching([$permission]);
        $this->flushCache();

        return $this;
    }

    /**
     * Attach multiple permissions to current role.
     *
     * @param  mixed  $permissions
     * @return void
     */
    public function attachPermissions($permissions)
    {
        $mappedPermissions = [];

        foreach ($permissions as $permission) {
            $mappedPermissions[] = Helper::getIdFor($permission, 'permission');
        }

        $this->permissions()->attach($mappedPermissions);
        $this->flushCache();

        return $this;
    }

    /**
     * Detach permission from current role.
     *
     * @param  object|array  $permission
     * @return void
     */
    public function detachPermission($permission)
    {
        $permission = Helper::getIdFor($permission, 'permission');

        $this->permissions()->detach($permission);
        $this->flushCache();

        return $this;
    }

    /**
     * Detach multiple permissions from current role
     *
     * @param  mixed  $permissions
     * @return void
     */
    public function detachPermissions($permissions = null)
    {
        $mappedPermissions = [];

        if ($permissions) {
            foreach ($permissions as $permission) {
                $mappedPermissions[] = Helper::getIdFor($permission, 'permission');
            }
        }

        $this->permissions()->detach($mappedPermissions);
        $this->flushCache();

        return $this;
    }
    

    /**
     * Flush the role's cache.
     *
     * @return void
     */
    public function flushCache()
    {
        return $this->getRoleChecker()->flushCache();
    }

}
