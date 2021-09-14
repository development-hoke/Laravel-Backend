<?php

namespace App\Domain\Exceptions;

/**
 * 一部返品再登録時のキャンセルに失敗した時に使用する。
 */
class NpFailedCancelForReregisteringException extends NpPaymentException
{
}
