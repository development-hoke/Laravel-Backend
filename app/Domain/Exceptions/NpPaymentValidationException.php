<?php

namespace App\Domain\Exceptions;

/**
 * バリデーションエラー。
 * この例外に指定するmessageは、クライアントサイドへのレスポンスのバリデーションエラーメッセージとしてそのまま引き継がれる。
 */
class NpPaymentValidationException extends NpPaymentException
{
}
