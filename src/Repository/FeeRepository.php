<?php

namespace Base\Health\Repository;

use Base\Health\Entity\Fee;
use Base\Database\Repository\ServiceEntityRepository;
use Base\Database\Repository\Finders;

/** @extends ServiceEntityRepository<Fee> */
class FeeRepository extends ServiceEntityRepository
{
    public function __construct(Finders $finders)
    {
        parent::__construct($finders, Fee::class);
    }

    /** @return list<Fee> */
    public function findForPractitioner(object $practitioner): array
    {
        return $this->findBy(['practitioner' => $practitioner], ['position' => 'ASC', 'id' => 'ASC']);
    }

    /**
     * @return list<Fee> every fee, the practice's first then each practitioner's (by the member's
     *                   position, two associations away: sorted here, the fees being few)
     */
    public function findOrdered(): array
    {
        $fees = $this->findBy([], ['position' => 'ASC', 'id' => 'ASC']);
        // ORDER BY m.position ASC put the NULLs (the practice's fees, a practitioner without member) first.
        $rank = static fn (Fee $fee): array => null === ($position = $fee->getPractitioner()?->getMember()?->getPosition()) ? [0, 0] : [1, $position];
        usort($fees, static fn (Fee $a, Fee $b) => [...$rank($a), $a->getPosition(), $a->getId()] <=> [...$rank($b), $b->getPosition(), $b->getId()]);

        return $fees;
    }
}
