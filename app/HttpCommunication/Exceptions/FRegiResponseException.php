<?php

namespace App\HttpCommunication\Exceptions;

use App\HttpCommunication\Response\FRegiResponseInterface as Response;

class FRegiResponseException extends Exception
{
    /**
     * @var Response
     */
    protected $response;

    public function __construct(Response $response, $message = '', $code = 0, $previous = null)
    {
        $body = $response->getBody();

        $message = $message ?: implode(' ', (array) ($body ?? null));

        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getResponseStatusCode()
    {
        return $this->getResponse()->getStatusCode();
    }

    /**
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->getResponse()->getHeaders();
    }

    /**
     * @return mixed
     */
    public function getResponseBody()
    {
        return $this->getResponse()->getBody();
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->getResponse()->getErrorCode();
    }

    /**
     * @return bool
     */
    public function isClientError()
    {
        switch ($this->getErrorCode()) {
            case \App\Enums\FRegi\ErrorCode::EmptyCardNumber:
            case \App\Enums\FRegi\ErrorCode::InvalidCardNumber:
            case \App\Enums\FRegi\ErrorCode::EmptyExpiryMonth:
            case \App\Enums\FRegi\ErrorCode::InvalidExpiryMonth:
            case \App\Enums\FRegi\ErrorCode::EmptyExpiryYear:
            case \App\Enums\FRegi\ErrorCode::InvalidExpiryYear:
            case \App\Enums\FRegi\ErrorCode::Expired:
            case \App\Enums\FRegi\ErrorCode::FailedToIdentifyCardType:
            case \App\Enums\FRegi\ErrorCode::FailedToAuthorizeCard:
            case \App\Enums\FRegi\ErrorCode::InvalidCardTypeDebit:
            case \App\Enums\FRegi\ErrorCode::InvalidCardTypePrepaid:
            case \App\Enums\FRegi\ErrorCode::NotAllowedToUseForeignCard:
            case \App\Enums\FRegi\ErrorCode::EmptySecurityCode:
            case \App\Enums\FRegi\ErrorCode::TooLongSecurityCode:
            case \App\Enums\FRegi\ErrorCode::InvalidSecurityCode:
            case \App\Enums\FRegi\ErrorCode::InvalidToken:
            case \App\Enums\FRegi\ErrorCode::ExpiredToken:
            case \App\Enums\FRegi\ErrorCode::InvalidCard1:
            case \App\Enums\FRegi\ErrorCode::InvalidCard2:
            case \App\Enums\FRegi\ErrorCode::Pending:
            case \App\Enums\FRegi\ErrorCode::InvalidPinCode:
            case \App\Enums\FRegi\ErrorCode::InvalidSecurityCode2:
            case \App\Enums\FRegi\ErrorCode::EmptySecurityCode2:
            case \App\Enums\FRegi\ErrorCode::ExceedUsageCountLimit:
            case \App\Enums\FRegi\ErrorCode::ExceedUsageAmountLimit:
            case \App\Enums\FRegi\ErrorCode::InvalidCard3:
            case \App\Enums\FRegi\ErrorCode::Accident:
            case \App\Enums\FRegi\ErrorCode::InvalidCard4:
            case \App\Enums\FRegi\ErrorCode::InvalidMemberNumber:
            case \App\Enums\FRegi\ErrorCode::InvalidItemCode:
            case \App\Enums\FRegi\ErrorCode::InvalidPriceAmount:
            case \App\Enums\FRegi\ErrorCode::InvalidTaxOrOthers:
            case \App\Enums\FRegi\ErrorCode::InvalidBonusCount:
            case \App\Enums\FRegi\ErrorCode::InvalidBonusMonth:
            case \App\Enums\FRegi\ErrorCode::InvalidBonusAmount:
            case \App\Enums\FRegi\ErrorCode::InvalidStartMonth:
            case \App\Enums\FRegi\ErrorCode::InvalidSplitCount:
            case \App\Enums\FRegi\ErrorCode::InvalidSplitPrice:
            case \App\Enums\FRegi\ErrorCode::InvalidFirstPrice:
            case \App\Enums\FRegi\ErrorCode::InvalidBusinessSegument:
            case \App\Enums\FRegi\ErrorCode::InvalidPaymentMethod:
            case \App\Enums\FRegi\ErrorCode::InvalidCancelSegument:
            case \App\Enums\FRegi\ErrorCode::InvalidTreatmentSegument:
            case \App\Enums\FRegi\ErrorCode::InvalidExpiry:
            case \App\Enums\FRegi\ErrorCode::InvalidAuthorizationNumber:
            case \App\Enums\FRegi\ErrorCode::InvalidCAFISAgent:
            case \App\Enums\FRegi\ErrorCode::InvalidCardCompany:
            case \App\Enums\FRegi\ErrorCode::CyclicSerialNumbering:
            case \App\Enums\FRegi\ErrorCode::CardCompanyNotAvailable:
            case \App\Enums\FRegi\ErrorCode::Denied:
            case \App\Enums\FRegi\ErrorCode::InvalidBusinessTarget:
            case \App\Enums\FRegi\ErrorCode::DeniedConnection:
                return true;

            default:
                return false;
        }
    }
}
