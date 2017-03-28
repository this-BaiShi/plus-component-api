<?php

namespace baishi\Component\ZhiyiPlus\PlusComponentAPI\Middleware;

use Closure;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Traits\CreateJsonResponseData;

class VerifyPassword
{
//    use CreateJsonResponseData;

    /**
     * 验证用户密码正确性中间件.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $password = $request->input('password', '');
        $user = $request->user();

        if (!$user->verifyPassword($password)) {
            return response()->json([
                'code' => 1006,
            ])->setStatusCode(401);
        }

        return $next($request);
    }
}
