<?php

namespace baishi\Component\ZhiyiPlus\PlusComponentAPI\Middleware;

use Closure;
use Illuminate\Http\Request;
use Zhiyi\Plus\Models\AuthToken;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Traits\CreateJsonResponseData;

class AuthUserToken
{
    use CreateJsonResponseData;

    /**
     * 验证用户认证入口.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $accessToken = $request->headers->get('ACCESS-TOKEN');

        if (!$accessToken) {
            return response()->json(static::createJsonData([
                'code' => 1016,
            ]))->setStatusCode(401);
        }

        return $this->checkAccessTokenExistedStep($accessToken, function (User $user) use ($next, $request) {
            // 注入用户信息到下一步控制器.
            $request->attributes->set('user', $user);

            return $next($request);
        });
    }

    /**
     * 验证AccessToken是否存在.
     *
     * @param string  $accessToken 请求的token
     * @param Closure $next        下一步回掉方法
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    protected function checkAccessTokenExistedStep(string $accessToken, Closure $next)
    {
        $authToken = AuthToken::byToken($accessToken)
            ->withTrashed()
            ->orderByDesc()
            ->first();

        if (!$authToken) {
            return response()->json(static::createJsonData([
                'code' => 1016,
            ]))->setStatusCode(401);
        }

        return $this->checkAccessTokenIsShutDownStep($authToken, $next);
    }

    /**
     * 验证AccessToken设备是否被下线.
     *
     * @param AuthToken $authToken token数据对象
     * @param Closure   $next      下一步回掉方法
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    protected function checkAccessTokenIsShutDownStep(AuthToken $authToken, Closure $next)
    {
        if ($authToken->state === 0) {
            return response()->json(static::createJsonData([
                'code' => 1015,
            ]))->setStatusCode(401);
        }

        return $this->checkAccessTokenIsInvaildStep($authToken, $next);
    }

    /**
     * 验证AccessToken是否有效.
     *
     * @param AuthToken $authToken token数据对象
     * @param Closure   $next      下一步回掉方法
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    protected function checkAccessTokenIsInvaildStep(AuthToken $authToken, Closure $next)
    {
        $now = $authToken->freshTimestamp();
        if ($authToken->deleted_at || ($authToken->expires && $authToken->created_at->diffInSeconds($now) >= $authToken->expires)) {
            return response()->json(static::createJsonData([
                'code' => 1012,
            ]))->setStatusCode(401);
        }

        return $this->checkUserExistedStep($authToken->user, $next);
    }

    /**
     * 检查与AccessToken绑定的用户是否存在.
     *
     * @param mixed   $user 用户对象
     * @param Closure $next 下一步回掉的
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    protected function checkUserExistedStep($user, Closure $next)
    {
        if (!$user || !$user instanceof User) {
            return response()->json(static::createJsonData([
                'code' => 1005,
            ]))->setStatusCode(401);
        }

        return $next($user);
    }
}
