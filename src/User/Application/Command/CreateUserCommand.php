<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class CreateUserCommand extends AbstractCommand
{
    private string $lastName;
    private string $firstName;
    private string $username;
    private string $password;
    private string $email;
    private string $gender;
    private string $birthday;
    private bool $newsletterEnabled;

    public function __construct(
        string $lastName,
        string $firstName,
        string $username,
        string $password,
        string $email,
        string $gender,
        string $birthday,
        bool $newsletterEnabled,
        int $occurredOn
    ) {
        parent::__construct(0, $occurredOn);
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->gender = $gender;
        $this->birthday = $birthday;
        $this->newsletterEnabled = $newsletterEnabled;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isNewsletterEnabled(): bool
    {
        return $this->newsletterEnabled;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getBirthday(): string
    {
        return $this->birthday;
    }
}
