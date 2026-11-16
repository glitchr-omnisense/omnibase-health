<?php

namespace Base\Health\Service;

use App\Entity\User;
use Base\Health\Repository\PatientProfileRepository;
use Base\Office\Entity\Booking\Appointment;
use Base\Office\Entity\Member;
use Base\Office\Enum\AppointmentStatus;
use Base\Office\Repository\MemberRepository;
use Base\Database\Repository\Finder\Criterion;
use Base\Database\Repository\Finder\Group;
use Base\Database\Repository\Finder\Key;
use Base\Database\Repository\Finder\Parser;
use Base\Database\Repository\Repositories;

/**
 * Who follows a patient - their care team in the practice: the members
 * they have, or had, an appointment with (not a cancelled one), and the
 * physician of the practice they declared as their "médecin traitant".
 * It is what lets a practitioner read a patient's documents, and what a
 * patient is offered to write to.
 */
class CareTeam
{
    public function __construct(
        private readonly Repositories $repositories,
        private readonly MemberRepository $members,
        private readonly PatientProfileRepository $profiles,
    ) {
    }

    /** @return list<Member> */
    public function members(User $patient): array
    {
        $members = [];
        // The members of the patient's appointments (not a cancelled one), each once, by position.
        foreach ($this->repositories->of(Appointment::class)->findBy([
            'client' => Key::of($patient),
            Group::of(Parser::SEPARATOR_AND, Criterion::of('status', AppointmentStatus::CANCELLED, Parser::OPTION_NOT_EQUAL)),
            'member.active' => true,
        ], ['member.position' => 'ASC']) as $appointment) {
            $member = $appointment->getMember();
            $members[$member->getId()] ??= $member;
        }
        $referring = $this->profiles->findOneByUser($patient)?->getReferringPhysician()?->getMember();
        if (null !== $referring && $referring->isActive()) {
            $members[$referring->getId()] = $referring;
        }

        return array_values($members);
    }

    /** Whether that account is a member who follows the patient. */
    public function follows(User $staff, User $patient): bool
    {
        $member = $this->members->findOneByUser($staff);
        if (null === $member) {
            return false;
        }
        foreach ($this->members($patient) as $followed) {
            if ($followed->getId() === $member->getId()) {
                return true;
            }
        }

        return false;
    }

    /** The patient objected to their documents being shared within the team. */
    public function hasOpposition(User $patient): bool
    {
        return true === $this->profiles->findOneByUser($patient)?->isSharingOpposition();
    }
}
