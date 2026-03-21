<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class PlanModuleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $moduleName = null): Response
    {
        $user = Auth::user();
        if (!$user) {
            return $next($request);
        }

        // Skip check for superadmin
        if ($user->hasRole('superadmin')) {
            return $next($request);
        } elseif ($user->hasRole('company')) {
            if (($user->plan_expire_date && now()->gt($user->plan_expire_date)) || ($user->active_plan == 0)) {
                // Plan expired - only allow essential plan routes
                $allowedRoutes = ['users.leave-impersonation', 'plans.index', 'plans.subscribe', 'plans.start-trial', 'plans.apply-coupon', 'payment.*.store', 'payment.*.status', 'bank-transfer.index', 'plans.assign-free'];
                if (!$request->routeIs($allowedRoutes)) {
                    return redirect()->route('plans.index')
                        ->with('error', 'Your plan has expired. Please renew your subscription.');
                }
            }
        } else {
            // For sub-users - check creator's plan
            $creator = $user->createdBy;
            if ($creator && ($creator->plan_expire_date && now()->gt($creator->plan_expire_date) || ($creator->active_plan == 0))) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Company plan has expired. Please contact your administrator.');
            }

            // Check if creator exists and has no valid plan
            if (!$creator || $creator->active_plan == 0) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'No active company plan found. Please contact your administrator.');
            }
        }

        if ($moduleName != null) {
            $moduleName =  explode('-', $moduleName);
            $status = false;

            // For sub-users, check the creator's (company's) modules
            $checkUser = $user;
            if (!$user->hasRole('company') && !$user->hasRole('superadmin') && $user->createdBy) {
                $checkUser = $user->createdBy;
            }

            foreach ($moduleName as $m) {
                // Check if module is enabled globally first
                $status = module_is_active($m);

                // If globally enabled, check if it's available for this user
                if ($status == true) {
                    $availableModules = (new \App\Models\Plan())->getAvailableModulesForUser($checkUser->id);
                    if (in_array($m, $availableModules)) {
                        $response = $next($request);
                        return $response;
                    }
                }
            }
            // For non-company users (staff, client, vendor), redirect to login with error
            // instead of plans.index to avoid redirect loops
            if (!$user->hasRole('company')) {
                Auth::logout();
                return redirect()->route('login')->with('error', __('You do not have access to this module. Please contact your administrator.'));
            }
            return redirect()->route('plans.index')->with('error', __('Permission denied '));
        }

        $response = $next($request);
        return $response;
    }
}
