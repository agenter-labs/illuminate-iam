<?php

namespace AgenterLab\IAM;

use Illuminate\Support\Facades\Facade;

class IamFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'iam';
    }
}
