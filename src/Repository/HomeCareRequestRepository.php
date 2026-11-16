<?php

namespace Base\Health\Repository;

use Base\Health\Entity\HomeCareRequest;
use Base\Database\Repository\Finder\Criterion;
use Base\Database\Repository\Finder\Group;
use Base\Database\Repository\Finder\Parser;
use Base\Database\Repository\ServiceEntityRepository;
use Base\Database\Repository\Finders;

/** @extends ServiceEntityRepository<HomeCareRequest> */
class HomeCareRequestRepository extends ServiceEntityRepository
{
    public function __construct(Finders $finders)
    {
        parent::__construct($finders, HomeCareRequest::class);
    }

    /** @return list<HomeCareRequest> */
    public function findPending(int $limit = 20): array
    {
        return $this->findBy(['status' => \Base\Health\Enum\CareStatus::PENDING], ['createdAt' => 'ASC'], $limit);
    }

    public function countPending(): int
    {
        return $this->count(['status' => \Base\Health\Enum\CareStatus::PENDING]);
    }

    /** @return list<HomeCareRequest> */
    public function findForPatient(object $patient): array
    {
        return $this->findBy(['patient' => $patient], ['createdAt' => 'DESC']);
    }

    /** Removes the requests decided before a day (read, then removed through the unit of work): how many. */
    public function purgeDecidedBefore(\DateTimeInterface $before): int
    {
        $decided = $this->findBy([Group::of(Parser::SEPARATOR_AND,
            Criterion::of('decidedAt', null, Parser::OPTION_NOT_NULL),
            Criterion::of('decidedAt', \Base\Database\Type\Utc::from($before), Parser::OPTION_LOWER),
        )]);
        foreach ($decided as $request) {
            $this->remove($request);
        }
        if ([] !== $decided) {
            $this->flush();
        }

        return \count($decided);
    }
}
