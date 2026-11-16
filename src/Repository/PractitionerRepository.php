<?php

namespace Base\Health\Repository;

use Base\Health\Entity\Practitioner;
use Base\Database\Repository\ServiceEntityRepository;
use Base\Database\Repository\Finders;

/** @extends ServiceEntityRepository<Practitioner> */
class PractitionerRepository extends ServiceEntityRepository
{
    public function __construct(Finders $finders)
    {
        parent::__construct($finders, Practitioner::class);
    }

    public function findOneByMember(?object $member): ?Practitioner
    {
        return null === $member ? null : $this->findOneBy(['member' => $member]);
    }

    public function findOneByUser(?object $user): ?Practitioner
    {
        return null === $user ? null : $this->findOneBy(['member.user' => $user]);
    }

    /** By RPPS number: how Pro Santé Connect names a practitioner. */
    public function findOneByRpps(string $rpps): ?Practitioner
    {
        return $this->findOneBy(['member.registryId' => $rpps]);
    }

    /** @return list<Practitioner> the practice's physicians, for "médecin traitant" */
    public function findPhysicians(): array
    {
        return $this->findBy(['professionCode' => '10', 'member.active' => true], ['member.position' => 'ASC']);
    }
}
