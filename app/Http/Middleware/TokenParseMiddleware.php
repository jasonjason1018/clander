<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use App\Services\AccountService;
use Illuminate\Support\Facades\Redis;
use Mockery\Exception;

class TokenParseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $accessToken = $request->bearerToken();

        $this->validateAccessToken($accessToken);

        $tokenInfo = $this->getTokenInfo($accessToken);

        $this->validateTokenInfo($tokenInfo);

        $request->merge([
            'id_account' => $tokenInfo['id_account'],
        ]);

        return $next($request);
    }

    private function getTokenInfo($accessToken)
    {
        $accountService = new AccountService();

        return $accountService->getTokenInfo($accessToken);
    }

    private function validateAccessToken($accessToken) :void
    {
        if (!Redis::get($accessToken)) {
            throw new \Exception('Invalid Access token');
        }
    }

    private function validateTokenInfo($tokenInfo) :void
    {
        if (!isset($tokenInfo['id_account'])) {
            throw new Exception('Invalid token');
        }

        if (Carbon::now()->gt(Carbon::parse($tokenInfo['expired_time']))) {
            throw new Exception('Expired token');
        }
    }
}
