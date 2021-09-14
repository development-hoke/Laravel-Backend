<?php

namespace App\Domain;

use App\Domain\Utils\AdminLog as AdminLogUtil;
use App\Exceptions\FatalException;
use App\Models\Staff;
use App\Repositories\AdminLogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class AdminLog implements AdminLogInterface
{
    /**
     * @var array
     */
    private $excludes = [
        'admin.contentImage.store',
    ];

    /**
     * @var AdminLogRepository
     */
    private $adminLogRepository;

    /**
     * @param AdminLogRepository $adminLogRepository
     */
    public function __construct(AdminLogRepository $adminLogRepository)
    {
        $this->adminLogRepository = $adminLogRepository;
    }

    /**
     * @param Request $request
     * @param Staff $staff
     * @param array|null $options
     *
     * @return \App\Models\AdminLog|null
     */
    public function write(Request $request, Staff $staff, ?array $options = [])
    {
        $routeName = $request->route()->getName();

        if (!$this->shouldWrite($routeName)) {
            return;
        }

        if (AdminLogUtil::resolveRouteNameToTitle($routeName) === null) {
            throw new FatalException(error_format('error.route_title_cannot_be_resolved', [
                'routeName' => $routeName,
            ]));
        }

        $actionText = Lang::get('action.admin')[$routeName];

        if (isset($options['action_text'])) {
            $actionText = $actionText . ' ' . $options['action_text'];
        }

        $attributes = [
            'staff_id' => $staff->id,
            'action_text' => $actionText,
            'action' => $routeName,
            'url' => $request->route()->uri(),
            'type' => $this->getRequestTypeByMethod($request->method()),
            'ip' => $request->ip(), // FIXME: インフラ設計によっては、Request::setTrustedProxiesを設定する。
            'referer' => $request->headers->get(AdminLogUtil::REFERER_HEADER),
        ];

        return $this->adminLogRepository->create($attributes);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function getRequestTypeByMethod(string $method)
    {
        if (($crud = AdminLogUtil::m2c($method)) === null) {
            throw new FatalException(error_format('error.not_supported_http_method', ['method' => $method]));
        }

        return $crud;
    }

    /**
     * @param string $routeName
     *
     * @return bool
     */
    private function shouldWrite($routeName)
    {
        return !in_array($routeName, $this->excludes, true);
    }
}
