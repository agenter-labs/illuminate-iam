<?php

namespace AgenterLab\IAM\Checkers\User;

use AgenterLab\IAM\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class UserDefaultChecker extends UserChecker
{

    /**
     * @inheritdoc
     */
    public function hasPermission($permission, $company = null, bool $requireAll = false)
    {
        if (is_array($permission)) {
            if (empty($permission)) {
                return true;
            }

            foreach ($permission as $permissionName) {
                $hasPermission = $this->hasPermission($permissionName, $company);

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
        foreach ($this->cachedRoles($company) as $role) {

            $role = Helper::hydrateModel(Config::get('iam.models.role'), $role);

            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Tries to return all the cached roles of the user.
     * If it can't bring the roles from the cache,
     * it brings them back from the DB.
     * @param mixed $company
     * @return \Illuminate\Support\Collection
     */
    protected function cachedRoles($company = null)
    {
        $company = Helper::getIdFor($company, 'company');

        if (!Config::get('iam.cache.enabled')) {
            return $this->getRoles($company);
        }  

        $cacheKey = 'iam_roles_for_user_'. $this->user->getKey();

        if ($company) {
            $cacheKey .= '_' . $company;
        }

        return Cache::store(Config::get('iam.cache.store', 'file'))->remember($cacheKey, Config::get('iam.cache.expiration_time', 60), function () use ($company) {
            return $this->getRoles($company);
        });
    }


    /**
     * Get roles
     * 
     * @param mixed $company
     * @return \Illuminate\Support\Collection
     */
    private function getRoles($company = null) {;

        if ($this->user instanceof Model) {
            if ($company) {
                return $this->user->roles()->wherePivot(Helper::companyForeignKey(), $company)->get();
            } else  {
                return $this->user->roles()->get();
            }
        } else {
            return $this->user->roles();
        }
    }

    /**
     * @inheritdoc
     * 
     * @param mixed $company
     */
    public function flushCache($company = null)
    {
        $company = Helper::getIdFor($company, 'company');

        $cacheKey = 'iam_roles_for_user_'. $this->user->getKey();
        if ($company) {
            $cacheKey .= '_' . $company;
        }

        Cache::store(Config::get('iam.cache.store', 'file'))->forget($cacheKey);
    }
}