<?php

namespace Base\Health\Repository;

use Base\Health\Entity\Dependent;
use Base\Database\Repository\ServiceEntityRepository;
use Base\Database\Repository\Finders;

/** @extends ServiceEntityRepository<Dependent> */
class DependentRepository extends ServiceEntityRepository
{
    public function __construct(Finders $finders)
    {
        parent::__construct($finders, Dependent::class);
    }

    /** @return list<Dependent> */
    public function findForHolder(object $holder): array
    {
        return $this->findBy(['holder' => $holder], ['givenName' => 'ASC']);
    }
}
