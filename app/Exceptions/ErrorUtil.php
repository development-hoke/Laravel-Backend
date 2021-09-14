<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;

class ErrorUtil
{
    /**
     * エラーメッセージを形成する
     *
     * @param string $message
     * @param array|string $params
     * @param array $replace
     *
     * @return string
     */
    public static function formatMessage(string $message, $params = [], array $replace = []): string
    {
        if (empty($params)) {
            return __($message, $replace);
        }

        if (is_array($params) || is_object($params)) {
            $additionalInfo = var_export($params, true);
        } else {
            $additionalInfo = (string) $params;
        }

        return sprintf('%s [パラメーター] %s', __($message, $replace), $additionalInfo);
    }

    /**
     * ログ出力
     *
     * @param string $message
     * @param \Exception $exception
     *
     * @return void
     */
    public static function report(string $message, \Exception $exception, array $optionalParams = [])
    {
        $errors = array_merge([$message], static::formatException($exception));

        $previousErrors = static::extractPreviousErrors($exception);

        if (count($previousErrors) > 0) {
            $previousErrors = array_map(function ($errors) {
                return implode("\n", $errors);
            }, $previousErrors);

            $errors[] = implode("\n", array_merge(['[previous]'], $previousErrors));
        }

        Log::error(implode("\n", $errors), $optionalParams);
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    public static function formatException(\Exception $exception)
    {
        return [
            '[exception]' . sprintf(
                '%s(%s) %s at %s:%s',
                get_class($exception),
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ),
            '[trace]' . $exception->getTraceAsString(),
        ];
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    private static function extractPreviousErrors(\Exception $exception)
    {
        $errors = [];

        $previous = $exception->getPrevious();

        if (is_null($previous)) {
            return $errors;
        }

        $errors[] = static::formatException($previous);

        while ($previous = $previous->getPrevious()) {
            $errors[] = static::formatException($previous);
        }

        return $errors;
    }
}
