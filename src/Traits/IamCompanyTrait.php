<?php

namespace AgenterLab\IAM\Traits;

use Illuminate\Support\Facades\Config;

trait IamCompanyTrait
{

    /**
     * Many-to-Many relations with the permission model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function usersRoles()
    {
        return $this->belongsToMany(
            Config::get('iam.models.user'),
            Config::get('iam.tables.user_company'),
            Config::get('iam.foreign_keys.company'),
            Config::get('iam.foreign_keys.user')
        )->withPivot('user_type', 'active', 'is_default', 'invitation_send_at', 'invitation_accepted_at');
    }
}
