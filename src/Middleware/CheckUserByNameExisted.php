<?php

namespace baishi\Component\ZhiyiPlus\PlusComponentAPI\Middleware;

use Closure;
use Illuminate\Http\Request;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Traits\CreateJsonResponseData;

class CheckUserByNameExisted
{
//    use CreateJsonResponseData;

    /**
     * 检查用户是否存在中间件.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $name = $request->input('name');
        $user = User::byName($name)->withTrashed()->first();

        // 用户不存在 or 软删除用户
        if (!$user || $user->deleted_at) {
            return response()->json([
                'code' => 1005,
            ])->setStatusCode(404);
        }

        return $next($request);
    }
}
