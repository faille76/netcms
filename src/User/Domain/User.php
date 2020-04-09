<?php

declare(strict_types=1);

namespace App\User\Domain;

use App\Shared\Domain\DateTimeFormat;
use Assert\Assertion;
use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface
{
    private int $id;
    private string $lastName;
    private string $firstName;
    private string $username;
    private ?string $password;
    private ?string $session;
    private string $email;
    private ?string $avatar;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;
    private ?string $gender;
    private ?string $birthday;
    private array $roles = [];
    private ?bool $newsletterEnabled;

    public function __construct(
        int $id,
        string $username,
        string $email,
        string $lastName,
        string $firstName
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials(): void
    {
        $this->password = null;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    public function getNewsletterEnabled(): ?bool
    {
        return $this->newsletterEnabled;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public static function fromArray(array $data): User
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'username');
        Assertion::keyExists($data, 'last_name');
        Assertion::keyExists($data, 'first_name');
        Assertion::keyExists($data, 'email');

        $user = new static($data['id'], $data['username'], $data['email'], $data['last_name'], $data['first_name']);
        if (array_key_exists('password', $data)) {
            $user->password = $data['password'];
        }
        if (array_key_exists('session', $data)) {
            $user->session = $data['session'];
        }
        if (array_key_exists('created_at', $data)) {
            if (is_string($data['created_at'])) {
                $data['created_at'] = \DateTime::createFromFormat(DateTimeFormat::FORMAT, $data['created_at']);
            }
            $user->createdAt = $data['created_at'];
        }
        if (array_key_exists('updated_at', $data)) {
            if (is_string($data['updated_at'])) {
                $data['updated_at'] = \DateTime::createFromFormat(DateTimeFormat::FORMAT, $data['updated_at']);
            }
            $user->updatedAt = $data['updated_at'];
        }
        if (array_key_exists('gender', $data)) {
            $user->gender = $data['gender'];
        }
        if (array_key_exists('birthday', $data)) {
            $user->birthday = $data['birthday'];
        }
        if (array_key_exists('gender', $data)) {
            $user->gender = $data['gender'];
        }
        if (array_key_exists('avatar', $data)) {
            $user->avatar = $data['avatar'];
        }
        if (array_key_exists('newsletter_enabled', $data)) {
            $user->newsletterEnabled = $data['newsletter_enabled'];
        }
        if (array_key_exists('rights', $data)) {
            $user->roles = $data['rights']->getSymfonyRoles();
        }

        return $user;
    }
}
