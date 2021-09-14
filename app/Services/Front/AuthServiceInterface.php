<?php

namespace App\Services\Front;

interface AuthServiceInterface
{
    /**
     * @param array $credentials
     *
     * @return array
     */
    public function attempt(array $credentials);

    /**
     * @param array $data
     * @param string $email
     *
     * @return \App\Models\User
     */
    public function saveAuthorizedUser(array $data, string $email);

    /**
     * @param User $user
     *
     * @return bool
     */
    public function validateUser(\App\Models\User $user);

    /**
     * @param User $user
     *
     * @return array
     */
    public function getMemberDetail(\App\Models\User $user);
}
