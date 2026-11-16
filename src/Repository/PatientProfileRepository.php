<?php

namespace Base\Health\Repository;

use Base\Health\Entity\PatientProfile;
use Base\Database\Repository\Finder\Criterion;
use Base\Database\Repository\Finder\Group;
use Base\Database\Repository\Finder\Parser;
use Base\Database\Repository\ServiceEntityRepository;
use Base\Database\Repository\Finders;

/** @extends ServiceEntityRepository<PatientProfile> */
class PatientProfileRepository extends ServiceEntityRepository
{
    public function __construct(Finders $finders)
    {
        parent::__construct($finders, PatientProfile::class);
    }

    public function findOneByUser(?object $user): ?PatientProfile
    {
        return null === $user ? null : $this->findOneBy(['user' => $user]);
    }

    /** @return list<PatientProfile> by name, for the staff's search */
    public function search(string $query, int $limit = 20): array
    {
        $like = '%'.mb_strtolower(trim($query)).'%';

        return $this->findBy([
            Group::of(Parser::SEPARATOR_OR,
                Criterion::of('familyName', $like, Parser::OPTION_INSENSITIVE, Parser::OPTION_PARTIAL),
                Criterion::of('givenNames', $like, Parser::OPTION_INSENSITIVE, Parser::OPTION_PARTIAL),
            ),
            Group::of(Parser::SEPARATOR_AND, Criterion::of('erasedAt', null, Parser::OPTION_NULL)),
        ], ['familyName' => 'ASC'], $limit);
    }
}
