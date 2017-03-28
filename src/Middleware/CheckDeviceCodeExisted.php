<?php

namespace baishi\Component\ZhiyiPlus\PlusComponentAPI\Middleware;

use Closure;
use Validator;

class CheckDeviceCodeExisted
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $validator = Validator::make($request->all(), [
            'device_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'msg' => '设备号不能为空',
            ])->setStatusCode(422);
        }

        return $next($request);
    }
}
