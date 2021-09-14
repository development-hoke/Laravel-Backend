<?php

namespace App\HttpCommunication\Ymdy\Traits;

trait HasTokenHeader
{
    /**
     * @var array
     */
    protected $tokenHeader = [];

    /**
     * トークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    protected function setTokenHeader($name, string $token)
    {
        $this->tokenHeader = [$name => $token];

        return $this;
    }

    /**
     * optionにトークンを入れる
     *
     * @param array $options
     * @param string $endpointKey
     *
     * @return array
     */
    protected function mergeTokenHeaderToOptions(array $options, string $endpointKey)
    {
        if ($this->isNeededCredientialToken($endpointKey)) {
            $options['headers'] = array_merge(
                $this->tokenHeader,
                $options['headers'] ?? []
            );
        }

        return $options;
    }

    /**
     * @param array $endpointKey
     *
     * @return bool
     */
    public function isNeededCredientialToken($endpointKey)
    {
        if (!property_exists($this, 'needsToken')) {
            return false;
        }

        return in_array($endpointKey, $this->needsToken, true);
    }
}
