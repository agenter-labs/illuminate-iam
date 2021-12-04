<?php

namespace AgenterLab\IAM\Traits;

use AgenterLab\IAM\Helper;
use Illuminate\Support\Facades\Config;

trait IamUserTrait
{

    /**
     * @var \AgenterLab\IAM\Checkers\User\UserChecker
     */
    protected $userChecker;

    /**
     * Boots the role model and attaches event listener to
     * remove the many-to-many records when trying to delete.
     * Will NOT delete any records if the role model uses soft deletes.
     *
     * @return void|bool
     */
    public static function bootIamPermissionTrait()
    {
        $flushCache = function ($user) {
            $user->flushCache();
        };

        // If the user doesn't use SoftDeletes.
        if (method_exists(static::class, 'restored')) {
            static::restored($flushCache);
        }

        static::deleted($flushCache);
        static::saved($flushCache);

        static::deleting(function ($user) {
            if (method_exists($user, 'bootSoftDeletes') && !$user->forceDeleting) {
                return;
            }

            $user->roles()->sync([]);
        });
    }

    /**
     * Many-to-Many relations with the company model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(
            Config::get('iam.models.company'),
            Config::get('iam.tables.user_company'),
            Config::get('iam.foreign_keys.user'),
            Config::get('iam.foreign_keys.company')
        );
    }

    /**
     * Many-to-Many relations with the role model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        $roles = $this->belongsToMany(
            Config::get('iam.models.role'),
            Config::get('iam.tables.user_role'),
            Config::get('iam.foreign_keys.user'),
            Config::get('iam.foreign_keys.role')
        );

        return $roles;
    }

    /**
     * Check if user has a permission by its name.
     *
     * @param  string|array  $permission Permission string or array of permissions.
     * @param  mixed company
     * @param  bool  $requireAll All roles in the array are required.
     * @return bool
     */
    public function hasPermission($permission, $company = null, bool $requireAll = false) {

        return $this->getUserChecker()->hasPermission($permission, $company, $requireAll);
    }

    /**
     * Flush the user's cache.
     * 
     * @param mixed $company
     *
     * @return void
     */
    public function flushCache($company = null)
    {
        return $this->getUserChecker()->flushCache($company);
    }

    /**
     * Return the right checker for the user model.
     *
     * @return \AgenterLab\IAM\Checkers\User\UserChecker
     */
    protected function getUserChecker()
    {
        if (is_null($this->userChecker)) {
            $this->userChecker = Helper::getUserChecker($this);
        }
        
        return $this->userChecker;
    }

    /**
     * Alias to eloquent many-to-many relation's attach() method.
     *
     * @param mixed $role
     * @param mixed $company
     * @return static
     */
    public function attachRole($role, $company = null)
    {
        $role = Helper::getIdFor($role, 'role');

        $attributes = [];
        if (Config::get('iam.company.enabled')) {
            $company = Helper::getIdFor($company, 'company');
            if (
                $this->roles()
                ->wherePivot(Helper::companyForeignKey(), $company)
                ->wherePivot(Helper::roleForeignKey(), $role)
                ->count()
            ) {
                return $this;
            }

            $attributes[Helper::companyForeignKey()] = $company;
        }

        $this->roles()->attach($role, $attributes);
        $this->flushCache($company);

        return $this;
    }

    /**
     * Alias to eloquent many-to-many relation's detach() method.
     *
     * @param mixed $role
     * @param mixed $company
     * @return static
     */
    public function detachRole($role, $company = null)
    {
        $role = Helper::getIdFor($role, 'role');

        $roles = $this->roles();

        if (Config::get('iam.company.enabled')) {
            $roles->wherePivot(
                Helper::companyForeignKey(),
                Helper::getIdFor($company, 'company')
            );
        }
        
        $roles->detach($role);
        $this->flushCache($company);

        return $this;
    }

    /**
     * Attach multiple roles to a user.
     *
     * @param mixed $roles
     * @param mixed $company
     * @return static
     */
    public function attachRoles($roles = [], $company = null)
    {
        foreach ($roles as $role) {
            $this->attachRole($role, $company);
        }
        return $this;
    }

    /**
     * Detach multiple roles from a user.
     *
     * @param mixed $roles
     * @param mixed $company
     * @return static
     */
    public function detachRoles($roles = [], $company = null)
    {
        foreach ($roles as $role) {
            $this->detachRole($role, $company);
        }

        return $this;
    }
}
