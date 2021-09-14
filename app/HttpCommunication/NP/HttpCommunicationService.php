<?php

namespace App\HttpCommunication\NP;

use App\Exceptions\FatalException;
use App\HttpCommunication\Exceptions\AuthHttpException;
use App\HttpCommunication\Exceptions\HttpException;
use App\HttpCommunication\Exceptions\UnprocessableEntityHttpException;
use App\HttpCommunication\HttpCommunicationService as BaseHttpCommunicationService;
use App\HttpCommunication\Response\Concrete\Response as HttpCommunicationResponse;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Response;

abstract class HttpCommunicationService extends BaseHttpCommunicationService
{
    /**
     * @var array
     */
    protected $defaultHeaders = [
        'Content-Type' => 'application/json',
    ];

    /**
     * @return static
     */
    protected function initialize()
    {
        $config = $this->config;

        $this->defaultOptions['auth'] = [
            $config['shop_code'],
            $config['sp_code'],
        ];

        $this->defaultHeaders['X-NP-Terminal-Id'] = $config['terminal_id'];

        return $this;
    }

    /**
     * @param \GuzzleHttp\Exception\RequestException $exception
     *
     * @return void
     */
    protected function handleRequestError(RequestException $e)
    {
        if (!$e->hasResponse()) {
            throw new FatalException($e->getMessage(), null, $e);
        }

        $response = new HttpCommunicationResponse($e->getResponse());

        switch ($response->getStatusCode()) {
            case Response::HTTP_UNAUTHORIZED:
            case Response::HTTP_FORBIDDEN:
                throw new AuthHttpException($e);
            case Response::HTTP_UNPROCESSABLE_ENTITY:
                throw new UnprocessableEntityHttpException($e);
            default:
                throw new HttpException($e);
        }
    }
}
