<?php

namespace App\DTO;

class UserDTO
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $email;

    /**
     * @var string
     */
    public string $status;

    /**
     * @var string|null
     */
    public ?string $email_verified_at;

    /**
     * Create a new DTO instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->status = $data['status'] ?? 'provisional';
        $this->email_verified_at = $data['email_verified_at'] ?? null;
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at,
        ];
    }
}
