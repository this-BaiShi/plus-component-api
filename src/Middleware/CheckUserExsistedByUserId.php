<?php

namespace baishi\Component\ZhiyiPlus\PlusComponentAPI\Middleware;

use Closure;
use Illuminate\Http\Request;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Traits\CreateJsonResponseData;

class CheckUserExsistedByUserId
{
    use CreateJsonResponseData;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user_id) {
            return response()->json([
                'msg' => '目标用户user_id不能为空',
            ])->setStatusCode(400);
        }
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'msg' => '目标用户不存在',
            ])->setStatusCode(404);
        }
        if ($request->user()->id == $request->user_id) {
            return response()->json([
                'message' => '不能对自己进行关注相关操作',
            ])->setStatusCode(400);
        }

        return $next($request);
    }
}
