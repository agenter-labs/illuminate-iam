<?php

namespace AgenterLab\IAM\Traits;

use AgenterLab\IAM\Scopes\CompanyScope;

trait TenantTrait
{
    /**
     * @var bool
     */
    protected $tenantable = true;

    /**
     * @var bool
     */
    protected $defaultTenantable = false;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function tenantable(): bool
    {
        return $this->tenantable === true;
    }

    public function defaultTenantable(): bool
    {
        return $this->defaultTenantable === true;
    }
}
