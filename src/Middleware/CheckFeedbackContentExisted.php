<?php

namespace baishi\Component\ZhiyiPlus\PlusComponentAPI\Middleware;

use Closure;
use Illuminate\Http\Request;
use Zhiyi\Plus\Traits\CreateJsonResponseData;

class CheckFeedbackContentExisted
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
        if (!$request->input('content')) {
            return response()->json([
                'msg' => '反馈内容不能为空',
            ])->setStatusCode(400);
        }

        return $next($request);
    }
}
