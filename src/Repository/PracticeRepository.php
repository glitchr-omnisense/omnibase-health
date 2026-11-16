<?php

namespace Base\Health\Repository;

use Base\Health\Entity\Practice;
use Base\Database\Repository\Finder\Criterion;
use Base\Database\Repository\Finder\Group;
use Base\Database\Repository\Finder\Key;
use Base\Database\Repository\Finder\Parser;
use Base\Database\Repository\ServiceEntityRepository;
use Base\Database\Repository\Finders;

/** @extends ServiceEntityRepository<Practice> */
class PracticeRepository extends ServiceEntityRepository
{
    public function __construct(Finders $finders)
    {
        parent::__construct($finders, Practice::class);
    }

    /** The practice of the main office (a site is one practice). */
    public function findMain(): ?Practice
    {
        return $this->findOneBy(
            [Group::of(Parser::SEPARATOR_AND, Criterion::of('office', Key::of(null), Parser::OPTION_NOT_EQUAL))],
            ['office.main' => 'DESC', 'office.position' => 'ASC'],
        );
    }
}
