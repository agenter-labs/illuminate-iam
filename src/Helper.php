<?php

namespace AgenterLab\IAM;

use InvalidArgumentException;
use Illuminate\Support\Facades\Config;
use AgenterLab\IAM\Checkers\User\UserDefaultChecker;
use AgenterLab\IAM\Checkers\Role\RoleDefaultChecker;
use AgenterLab\IAM\Contracts\IamUserInterface;
use AgenterLab\IAM\Contracts\IamRoleInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Support\Str;

class Helper
{
    /**
     * Permission map
     */
    public static $permissionMap = [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ];

    /**
     * Gets the it from an array, object or integer.
     *
     * @param  mixed  $object
     * @param  string  $type
     * @return int
     */
    public static function getIdFor($object, $type, $column = 'name')
    {
        if (is_null($object)) {
            return null;
        } elseif (is_object($object)) {
            return $object->getKey();
        } elseif (is_array($object)) {
            return $object['id'];
        } elseif (is_numeric($object)) {
            return $object;
        } elseif (is_string($object)) {
            return call_user_func_array([
                Config::get("iam.models.{$type}"), 'where'
            ], [$column, $object])->firstOrFail()->getKey();
        }

        throw new \InvalidArgumentException(
            'getIdFor function only accepts an integer, a Model object or an array with an "id" key'
        );
    }

    /**
     * Return the right checker according to the configuration.
     *
     * @param \AgenterLab\IAM\Contracts\IamUserInterface $user
     * @return \AgenterLab\IAM\Checkers\User\UserChecker
     */
    public static function getUserChecker(IamUserInterface $user)
    {
        switch (Config::get('iam.checker', 'default')) {
            case 'default':
                return new UserDefaultChecker($user);
        }
    }

    /**
     * Return the right checker according to the configuration.
     *
     * @param \AgenterLab\IAM\Contracts\IamRoleInterface $role
     * @return \AgenterLab\IAM\Checkers\Role\RoleChecker
     */
    public static function getRoleChecker(IamRoleInterface $role)
    {
        switch (Config::get('iam.checker', 'default')) {
            case 'default':
                return new RoleDefaultChecker($role);
        }
    }
    
    /**
     * Creates a model from an array filled with the class data.
     *
     * @param string $class
     * @param string|\Illuminate\Database\Eloquent\Model $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function hydrateModel($class, $data)
    {
        if ($data instanceof Model) {
            return $data;
        }

        if (!isset($data['pivot'])) {
            throw new \Exception("The 'pivot' attribute in the {$class} is hidden");
        }

        $model = new $class;
        $primaryKey = $model->getKeyName();

        $model->setAttribute($primaryKey, $data[$primaryKey])->setAttribute('title', $data['title']);
        $model->setRelation(
            'pivot',
            MorphPivot::fromRawAttributes($model, $data['pivot'], 'pivot_table')
        );

        return $model;
    }

    /**
     * Returns the Company's foreign key.
     *
     * @return string
     */
    public static function companyForeignKey()
    {
        return Config::get('iam.foreign_keys.company');
    }

    /**
     * Returns the role's foreign key.
     *
     * @return string
     */
    public static function roleForeignKey()
    {
        return Config::get('iam.foreign_keys.role');
    }

    /**
     * Returns companies by user.
     *
     * @return string
     */
    public static function userCompanies(IamUserInterface $user)
    {
        $roles = $user->roles()->withPivot('company_id')->select('title')->get()->groupBy('pivot.company_id')->map(function($roles){
            return $roles->map(function($role){
                return ['id' => $role->pivot->role_id, 'title' => $role->title];
            });
        });

        $companies = $user->companies->map(function($company) use ($roles) {
            $company->roles = $roles[$company->pivot->company_id] ?? [];
            return $company;
        });

        return $companies;
    }

    /**
     * Returns roles by user.
     *
     * @return string
     */
    public static function userRoles(IamUserInterface $user, int $company)
    {
        $user->roles()->wherePivot('company_id', $company)->select('title')->get()->map(function($roles){
            return $roles->map(function($role){
                return ['id' => $role->pivot->role_id, 'title' => $role->title];
            });
        });
        
        return $user;
    }

    /**
     * Load permission form modules
     * 
     * @param array $modules
     * @param array $permissions
     * 
     * @return array
     */
    public static function loadPermissions(array $modules, array $permissions = []) {

        $sorted = [];


        foreach ($modules as $module) {
            $resource = include resource_path('permissions/' . $module . '.php');

            foreach($resource['groups'] as $gName => $group) {

                $sorted[$gName] = $sorted[$gName] ?? ['title' => Str::headline($group['title']), 'permissions' => []];

                foreach($group['items'] as $item) {

                    $permission = $resource['permissions'][$item] ?? '';

                    if (!$permission) {
                        continue;
                    }

                    foreach (explode(',', $permission) as $perm) {
                        $_perm = (self::$permissionMap[$perm] ?? $prem) . '-' . $item;
                        $sorted[$gName]['permissions'][] = [
                            'name' => $_perm,
                            'title' => Str::headline($_perm),
                            'selected' => in_array($_perm, $permissions)
                        ];
                    }
                }

                
            }
        }

        return array_values($sorted);
        
    }

}

