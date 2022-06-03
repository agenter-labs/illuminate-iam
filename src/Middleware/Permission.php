<?php

namespace AgenterLab\IAM\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Closure;

class Permission
{
    const DELIMITER = '|';

    /**
     * Handle incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure $next
     * @param  string  $permissions
     * @param  int|null  $company
     * @param  string|null  $options
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions, $company = null, $options = '')
    {
        if (!$this->authorization($permissions, $company, $options)) {
            return $this->unauthorized($permissions);
        }

        return $next($request);
    }

    /**
     * Check if the request has authorization to continue.
     *
     * @param  string $permissions
     * @param  int|null  $company
     * @param  string|null $options
     * @return boolean
     */
    protected function authorization($permissions, $company, $options)
    {
        if (!is_array($permissions)) {
            $permissions = explode(self::DELIMITER, $permissions);
        }

        $requireAll = Str::contains($company, 'require_all') ?: Str::contains($options, 'require_all');
        $guard = Config::get('auth.defaults.guard');

        $isGuest = Auth::guard($guard)->guest();

        $companyId = (!$isGuest && $company) ? Auth::guard($guard)->getCompanyId() : null;

        return !$isGuest
            && Auth::guard($guard)->user()->hasPermission($permissions, $companyId, $requireAll);
    }

    /**
     * The request is unauthorized, so it handles the aborting/redirecting.
     * 
     * @param  string $permissions
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthorized($permissions)
    {
        if (!is_array($permissions)) {
            $permissions = explode(self::DELIMITER, $permissions);
        }

        
        return Response::json([
            'message' => 'User does not have any of the necessary access rights.',
            'permissions' => $permissions,
        ], 403);
    }
}
