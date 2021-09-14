<?php

namespace App\Domain;

use App\Models\Staff;
use Illuminate\Http\Request;

interface AdminLogInterface
{
    /**
     * @param Request $request
     * @param Staff $staff
     *
     * @return \App\Models\AdminLog
     */
    public function write(Request $request, Staff $staff);

    /**
     * @param Request $request
     *
     * @return string
     */
    public function getRequestTypeByMethod(string $method);
}
