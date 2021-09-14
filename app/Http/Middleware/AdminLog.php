<?php

namespace App\Http\Middleware;

use App\Domain\AdminLogInterface;
use Closure;

class AdminLog
{
    /**
     * @var AdminLogInterface
     */
    private $adminLog;

    /**
     * @param AdminLogInterface $adminLog
     */
    public function __construct(AdminLogInterface $adminLog)
    {
        $this->adminLog = $adminLog;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth('admin_api')->check()) {
            $this->adminLog->write($request, auth('admin_api')->user());
        }

        return $next($request);
    }
}
