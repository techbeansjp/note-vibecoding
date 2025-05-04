<?php

namespace App\DTO;

class LoginCredentialsDTO
{
    /**
     * @var string
     */
    public string $email;

    /**
     * @var string
     */
    public string $password;

    /**
     * Create a new DTO instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
