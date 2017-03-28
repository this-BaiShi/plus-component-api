<?php

namespace baishi\Component\ZhiyiPlus\PlusComponentAPI\Controllers;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Models\UserProfileSetting;

class UserController extends Controller
{
    /**
     * 修改用户密码.
     *
     * @param Request $request 请求对象
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function resetPassword(Request $request)
    {
        $password = $request->input('new_password', '');
        $user = $request->user();
        $user->createPassword($password);
        $user->save();

        return response()->json([
            'msg' => '密码修改成功',
        ])->setStatusCode(201);
    }

    /**
     * 修改用户资料.
     *
     * @Author   Wayne[qiaobin@zhiyicx.com]
     * @DateTime 2017-01-17T17:25:45+0800
     *
     * @param Request $request [description]
     *
     * @return mixed 返回结果
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $profileData = $request->all();
        $profileSettings = UserProfileSetting::whereIn('profile', array_keys($profileData))->get();
        $datas = [];
        foreach ($profileSettings as $profile) {
            $datas[$profile->id] = $request->input($profile->profile);
        }
        $user->syncData($datas);

        return response()->json([
            'msg'    => '修改成功',
        ])->setStatusCode(201);
    }

    /**
     * [get description].
     *
     * @Author   Wayne[qiaobin@zhiyicx.com]
     * @DateTime 2017-01-18T17:54:59+0800
     *
     * @param User $user [description]
     *
     * @return [type] [description]
     */
    public function get(Request $request)
    {
        if(!is_array($request->user_ids)){
            $user_ids = explode(',',$request->user_ids);
        }
        $datas = $users = User::whereIn('id', $user_ids)
            ->with('datas', 'counts')
            ->get()
            ->toArray();
        if (!$datas) {
            return response()->json([
                'message' => '没有相关用户',
            ])->setStatusCode(404);
        }

        return response()->json([
            'data'    => $datas,
        ])->setStatusCode(200);
    }
}
