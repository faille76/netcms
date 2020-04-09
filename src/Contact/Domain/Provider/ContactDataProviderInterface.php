<?php

declare(strict_types=1);

namespace App\Contact\Domain\Provider;

use App\Contact\Domain\Contact;
use App\Contact\Domain\ContactCollection;
use App\Shared\Domain\Criteria;

interface ContactDataProviderInterface
{
    public function getContact(int $contactId): ?Contact;

    public function findContacts(?Criteria $criteria = null): ContactCollection;
}
