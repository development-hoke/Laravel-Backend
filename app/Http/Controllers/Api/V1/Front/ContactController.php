<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Contact\StoreRequest as ContactRequest;
use App\Services\Front\ContactServiceInterface as ContactService;

class ContactController extends Controller
{
    /**
     * @var ContactService
     */
    private $contactService;

    public function __construct(
        ContactService $contactService
    ) {
        $this->contactService = $contactService;
    }

    public function send(ContactRequest $request)
    {
        $params = $request->validated();

        $this->contactService->send($params);

        return;
    }
}
