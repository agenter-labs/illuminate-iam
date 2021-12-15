<?php

namespace AgenterLab\IAM\Contracts;

interface TenantInterface
{
    /**
     * Check is records available
     * 
     * @return bool
     */
    public function tenantable(): bool;

    /**
     * Check is default records available
     * 
     * @return bool
     */
    public function defaultTenantable(): bool;
}
