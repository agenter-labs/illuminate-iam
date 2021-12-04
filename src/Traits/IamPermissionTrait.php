<?php

namespace AgenterLab\IAM\Traits;

use Illuminate\Support\Facades\Config;

trait IamPermissionTrait
{

    /**
     * Boots the role model and attaches event listener to
     * remove the many-to-many records when trying to delete.
     * Will NOT delete any records if the role model uses soft deletes.
     *
     * @return void|bool
     */
    public static function bootIamPermissionTrait()
    {
        static::deleting(function ($permission) {
            if (method_exists($permission, 'bootSoftDeletes') && !$permission->forceDeleting) {
                return;
            }

            $permission->roles()->sync([]);
        });
    }

    /**
     * Many-to-Many relations with the permission model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            Config::get('iam.models.role'),
            Config::get('iam.tables.role_permission'),
            Config::get('iam.foreign_keys.permission'),
            Config::get('iam.foreign_keys.role')
        );
    }
}
