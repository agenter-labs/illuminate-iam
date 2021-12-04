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

class Helper
{
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
}