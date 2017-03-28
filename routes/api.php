<?php

use baishi\Component\ZhiyiPlus\PlusComponentAPI\Middleware;

// 获取手机验证码
Route::post('/auth/phone/code', 'AuthController@sendPhoneCode')
    ->middleware(Middleware\VerifyPhoneNumber::class) // 验证手机号格式是否正确
    ->middleware(Middleware\VerifySendPhoneCodeType::class) // 验证发送验证码类型等集合
;

// 用户登录
Route::post('/auth', 'AuthController@login')
    ->middleware(Middleware\CheckDeviceCodeExisted::class) // 验证设备号是否存在
    ->middleware(Middleware\VerifyPhoneNumber::class) // 验证手机号码是否正确
    ->middleware(Middleware\CheckUserByPhoneExisted::class) // 验证手机号码用户是否存在
;

// 重置token接口
Route::patch('/auth', 'AuthController@resetToken');

// 用户注册
Route::post('/auth/register', 'AuthController@register')
    ->middleware(Middleware\CheckDeviceCodeExisted::class) // 验证设备号是否存在
    ->middleware(Middleware\VerifyPhoneNumber::class) // 验证手机号码是否正确
    ->middleware(Middleware\VerifyUserNameRole::class) // 验证用户名规则是否正确
    ->middleware(Middleware\CheckUserByNameNotExisted::class) // 验证用户名是否被占用
    ->middleware(Middleware\CheckUserByPhoneNotExisted::class) // 验证手机号码是否被占用
    ->middleware(Middleware\VerifyPhoneCode::class) // 验证验证码释放正确
;

// 找回密码
Route::patch('/auth/forgotPassword', 'AuthController@forgotPassword')
    ->middleware(Middleware\VerifyPhoneNumber::class) // 验证手机号格式
    ->middleware(Middleware\CheckUserByPhoneExisted::class) // 验证手机号码用户是否存在
    ->middleware(Middleware\VerifyPhoneCode::class) // 验证手机号码验证码是否正确
;

// 用户相关组
Route::prefix('users')
    ->middleware('auth:api')
    ->group(function () {
        // 修改用户资料
        Route::patch('/', 'UserController@profile')
            ->middleware(Middleware\ChangeUserAvatar::class)
            ->middleware(Middleware\ChangeUserCover::class)
            ->middleware(Middleware\ChangeUsername::class);
        // 修改用户密码
        Route::patch('/password', 'UserController@resetPassword') // 设置控制器
        ->middleware(Middleware\VerifyPassword::class); // 验证用户密码是否正确
        // 获取用户信息
        Route::get('/', 'UserController@get');

        // 关注操作相关
        Route::post('/follow', 'FollowController@doFollow')
            ->middleware(Middleware\CheckUserExsistedByUserId::class)
            ->middleware(Middleware\CheckIsFollow::class);
        Route::delete('/unFollow', 'FollowController@doUnFollow')
            ->middleware(Middleware\CheckUserExsistedByUserId::class)
            ->middleware(Middleware\CheckIsFollowing::class);

        //查看指定用户关注状态
        Route::get('/followstatus', 'FollowController@getFollowStatus');
    });

// 用户关注相关
Route::get('/follows/follows/{user_id}/{max_id?}', 'FollowController@follows');
Route::get('/follows/followeds/{user_id}/{max_id?}', 'FollowController@followeds');

// 获取一个附件资源
Route::get('/storages/{storage}/{process?}', 'StorageController@get');
// 附件储存相关
Route::group([
    'middleware' => 'auth:api',
    'prefix'     => 'storages',
], function () {
    // 创建一个储存任务
    Route::post('/task', 'StorageController@create');
    // 完成一个任务上传通知
    Route::patch('/task/{storage_task_id}', 'StorageController@notice');
    // 删除一个上传任务附件
    Route::delete('/task/{storage_task_id}', 'StorageController@delete');
    // local storage api.
    Route::post('/task/{storage_task_id}', 'StorageController@upload')
        ->name('storage/upload');
});

//系统及配置相关
Route::group([
    'middleware' => 'auth:api',
    'prefix'     => 'system',
], function () {
    //意见反馈
    Route::post('/feedback', 'SystemController@createFeedback')
        ->middleware(Middleware\CheckFeedbackContentExisted::class);
    //获取系统会话列表
    Route::get('/conversations', 'SystemController@getConversations');
    //获取聊天服务器地址  TODO 之后根据后台配置更改
    Route::get('/imserverconfig', 'SystemController@getImServerConfig');
});
