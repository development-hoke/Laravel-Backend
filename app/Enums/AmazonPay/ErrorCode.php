<?php

namespace App\Enums\AmazonPay;

use App\Enums\BaseEnum;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/error-codes.html
 */
final class ErrorCode extends BaseEnum
{
    // Amazon Payエラーコード (NOTE: Billing Addressに関するコードは含まれていない)
    const CaptureNotRefundable = 'CaptureNotRefundable';
    const ConstraintsExist = 'ConstraintsExist';
    const DuplicateReferenceId = 'DuplicateReferenceId';
    const DuplicateRequest = 'DuplicateRequest';
    const InternalServerError = 'InternalServerError';
    const InvalidAccountsStatus = 'InvalidAccountsStatus';
    const InvalidAddress = 'InvalidAddress';
    const InvalidAddressConsentToken = 'InvalidAddressConsentToken';
    const InvalidAuthorizationStatus = 'InvalidAuthorizationStatus';
    const InvalidCancelAttempt = 'InvalidCancelAttempt';
    const InvalidCloseAttempt = 'InvalidCloseAttempt';
    const InvalidOrderReferenceId = 'InvalidOrderReferenceId';
    const InvalidOrderReferenceStatus = 'InvalidOrderReferenceStatus';
    const InvalidParameterValue = 'InvalidParameterValue';
    const InvalidSandboxSimulationSpecified = 'InvalidSandboxSimulationSpecified';
    const InvalidTransactionId = 'InvalidTransactionId';
    const MissingAuthenticationToken = 'MissingAuthenticationToken';
    const OrderReferenceNotModifiable = 'OrderReferenceNotModifiable';
    const PaymentMethodNotUpdated = 'PaymentMethodNotUpdated';
    const RequestThrottled = 'RequestThrottled';
    const ServiceUnavailable = 'ServiceUnavailable';
    const TransactionAmountExceeded = 'TransactionAmountExceeded';
    const TransactionCountExceeded = 'TransactionCountExceeded';
    const UnauthorizedAccess = 'UnauthorizedAccess';

    // エラーが返ってきたがレスポンスからエラー内容を解析できなかった場合に使用するエラーコード
    const UnparsableErrorResponse = 'UnparsableErrorResponse';
}
