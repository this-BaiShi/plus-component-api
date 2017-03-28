<?php

namespace baishi\Component\ZhiyiPlus\PlusComponentAPI\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Zhiyi\Plus\Handler\SendMessage;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\AuthToken;
use Zhiyi\Plus\Models\LoginRecord;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Models\VerifyCode;
use Zhuzhichao\IpLocationZh\Ip;

class AuthController extends Controller
{
    protected function findIp($ip): array
    {
        return (array) Ip::find($ip);
    }

    /**
     * 发送手机验证码.
     *
     * @param Request $request 请求对象
     *
     * @return Response 返回对象
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function sendPhoneCode(Request $request)
    {
        $vaildSecond = 1;
        $phone = $request->input('phone');
        $verify = VerifyCode::byAccount($phone)->byValid($vaildSecond)->orderByDesc()->first();

        if ($verify) {
            return response()->json([
                'data'    => $verify->makeSurplusSecond($vaildSecond),
            ])->setStatusCode(422);
        }

        $verify = new VerifyCode();
        $verify->account = $phone;
        $verify->makeVerifyCode();
        $verify->save();

        $type = 'phone';
        $message = new SendMessage($verify, $type);

        return $message->send();
    }

    /**
     * 用户登录.
     *
     * @Author   Wayne[qiaobin@zhiyicx.com]
     * @DateTime 2016-12-27T16:57:18+0800
     *
     * @param Request $request 请求对象
     *
     * @return Response 响应对象
     */
    public function login(Request $request)
    {
        $phone = $request->input('phone');
        $password = $request->input('password', '');

        $user = User::byPhone($phone)->first();
        if (!$user->verifyPassword($password)) {
            return response()->json([
                'msg'    => '用户名或者密码错误',
            ])->setStatusCode(422);
        }

        $deviceCode = $request->input('device_code');
        $token = new AuthToken();
        $token->token = md5($deviceCode.str_random(32));
        $token->refresh_token = md5($deviceCode.str_random(32));
        $token->user_id = $user->id;
        $token->expires = 0;
        $token->state = 1;

        // 登录记录
        $clientIp = $request->getClientIp();
        $loginrecord = new LoginRecord();
        $loginrecord->ip = $clientIp;

        // 保留测试ip
        // $location = (array)Ip::find($clientIp);
        $location = $this->findIp('61.139.2.69');
        array_filter($location);
        $loginrecord->address = trim(implode(' ', $location));
        $loginrecord->device_system = $request->input('device_system');
        $loginrecord->device_name = $request->input('device_name');
        $loginrecord->device_model = $request->input('device_model');
        $loginrecord->device_code = $deviceCode;

        DB::transaction(function () use ($token, $user, $loginrecord) {
            $user->tokens()->update(['state' => 0]);
            $user->tokens()->delete();
            $token->save();
            $user->loginRecords()->save($loginrecord);
        });

        //返回数据
        $data = [
            'token'         => $token->token,
            'refresh_token' => $token->refresh_token,
            'created_at'    => $token->created_at->getTimestamp(),
            'expires'       => $token->expires,
            'user_id'       => $user->id,
        ];

        return response()->json([
            'data'    => $data,
        ])->setStatusCode(201);
    }

    /**
     * 重置token.
     *
     * @param Request $request 请求对象
     *
     * @return Response 返回对象
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function resetToken(Request $request)
    {
        $shutDownState = 0;
        $refresh_token = $request->input('refresh_token');

        if (!$refresh_token || !($token = AuthToken::withTrashed()->byRefreshToken($refresh_token)->orderByDesc()->first())) {
            return response()
                ->json([
                    'msg' => '操作失败',
                ])->setStatusCode(404);
        } elseif ($token->state === $shutDownState) {
            return response()->json([
                'msg' => '请重新登录',
            ])->setStatusCode(401);
        }

        DB::transaction(function () use ($token, $shutDownState) {
            $token->state = $shutDownState;
            $token->save();
        });

        return $this->login($request);
    }

    /**
     * 注册用户.
     *
     * @param Request $request 请求对象
     *
     * @return Response 返回对象
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function register(Request $request)
    {
        $name = $request->input('name');
        $phone = $request->input('phone');
        $password = $request->input('password', '');
        $user = new User();
        $user->name = $name;
        $user->phone = $phone;
        $user->createPassword($password);
        $user->save();

        return $this->login($request);
    }

    /**
     * 找回密码控制器.
     *
     * @param Request $request 请求对象
     *
     * @return any 返回对象
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function forgotPassword(Request $request)
    {
        $password = $request->input('password', '');
        $user = $request->attributes->get('user');
        $user->createPassword($password);
        $user->save();

        return response()->json([
            'msg' => '重置密码成功',
        ])->setStatusCode(201);
    }
}
