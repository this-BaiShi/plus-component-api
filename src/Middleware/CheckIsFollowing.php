<?php

namespace baishi\Component\ZhiyiPlus\PlusComponentAPI\Middleware;

use Closure;
use Illuminate\Http\Request;
use Zhiyi\Plus\Models\Following;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Traits\CreateJsonResponseData;

class CheckIsFollowing
{
//    use CreateJsonResponseData;

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
        $user_id = $request->user()->id;
        $following_user_id = $request->user_id;
        if (!Following::where([
                ['user_id', $user_id],
                ['following_user_id', $following_user_id],
            ])
            ->count()
        ) {
            return response()->json([
                'msg' => '您并没有关注此用户',
            ])->setStatusCode(400);
        }

        return $next($request);
    }
}
