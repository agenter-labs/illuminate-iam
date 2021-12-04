<?php

namespace AgenterLab\IAM\Checkers\Role;

use AgenterLab\IAM\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class RoleDefaultChecker extends RoleChecker
{

    /**
     * @inheritdoc
     */
    public function hasPermission($permission, bool $requireAll = false)
    {
        if (is_array($permission)) {
            if (empty($permission)) {
                return true;
            }

            foreach ($permission as $permissionName) {
                $hasPermission = $this->hasPermission($permissionName);

                if ($hasPermission && !$requireAll) {
                    return true;
                } elseif (!$hasPermission && $requireAll) {
                    return false;
                }
            }

            // If we've made it this far and $requireAll is FALSE, then NONE of the perms were found.
            // If we've made it this far and $requireAll is TRUE, then ALL of the perms were found.
            // Return the value of $requireAll.
            return $requireAll;
        }

        foreach ($this->cachedPermissions() as $perm) {
            if (Str::is($permission, $perm)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Tries to return all the cached roles of the user.
     * If it can't bring the roles from the cache,
     * it brings them back from the DB.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function cachedPermissions()
    {
        if (!Config::get('iam.cache.enabled')) {
            return $this->role->permissions()->pluck('name');
        }  

        $cacheKey = 'iam_permissions_for_role_'. $this->role->getKey();

        return Cache::store(Config::get('iam.cache.store', 'file'))->remember($cacheKey, Config::get('iam.cache.expiration_time', 60), function () {
            return $this->role->permissions()->pluck('name');
        });
    }

    /**
     * @inheritdoc
     */
    public function flushCache()
    {
        Cache::forget('iam_permissions_for_role_'.$this->role->getKey());
    }
}