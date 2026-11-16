<?php

namespace Base\Health\Repository;

use Base\Health\Entity\IdentityLink;
use Base\Database\Repository\ServiceEntityRepository;
use Base\Database\Repository\Finders;

/** @extends ServiceEntityRepository<IdentityLink> */
class IdentityLinkRepository extends ServiceEntityRepository
{
    public function __construct(Finders $finders)
    {
        parent::__construct($finders, IdentityLink::class);
    }

    public function findOneBySub(string $provider, string $sub): ?IdentityLink
    {
        return $this->findOneBy(['provider' => $provider, 'sub' => $sub]);
    }

    /** @return list<IdentityLink> */
    public function findForUser(object $user): array
    {
        return $this->findBy(['user' => $user], ['createdAt' => 'ASC']);
    }
}
