<?php

declare(strict_types=1);

namespace App\User\Domain;

use App\Shared\Domain\ValueObject;
use Assert\Assertion;

final class Rights implements ValueObject
{
    private bool $configuration = false;
    private bool $users = false;
    private bool $pages = false;
    private bool $articles = false;
    private bool $pictures = false;
    private bool $partners = false;
    private bool $contact = false;
    private bool $upload = false;
    private bool $comments = false;
    private bool $rights = false;
    private bool $admin = false;

    public function toArray(): array
    {
        return [
            'configuration' => $this->configuration,
            'users' => $this->users,
            'pages' => $this->pages,
            'articles' => $this->articles,
            'pictures' => $this->pictures,
            'partners' => $this->partners,
            'contact' => $this->contact,
            'upload' => $this->upload,
            'comments' => $this->comments,
            'rights' => $this->rights,
            'admin' => $this->admin,
        ];
    }

    public static function fromArray(array $data): Rights
    {
        Assertion::keyExists($data, 'configuration');
        Assertion::keyExists($data, 'users');
        Assertion::keyExists($data, 'pages');
        Assertion::keyExists($data, 'articles');
        Assertion::keyExists($data, 'pictures');
        Assertion::keyExists($data, 'partners');
        Assertion::keyExists($data, 'contact');
        Assertion::keyExists($data, 'upload');
        Assertion::keyExists($data, 'comments');
        Assertion::keyExists($data, 'rights');
        Assertion::keyExists($data, 'admin');

        $rights = new static();
        $rights->admin = $data['admin'];
        $rights->configuration = $data['configuration'];
        $rights->users = $data['users'];
        $rights->pages = $data['pages'];
        $rights->articles = $data['articles'];
        $rights->pictures = $data['pictures'];
        $rights->partners = $data['partners'];
        $rights->contact = $data['contact'];
        $rights->upload = $data['upload'];
        $rights->comments = $data['comments'];
        $rights->rights = $data['rights'];

        return $rights;
    }

    public function getSymfonyRoles(): array
    {
        $rights = $this->toArray();

        $roles = [];
        $roles[] = 'ROLE_USER';
        foreach ($rights as $role => $enabled) {
            if ($enabled === true) {
                $roles[] = 'ROLE_' . strtoupper($role);
            }
        }

        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
