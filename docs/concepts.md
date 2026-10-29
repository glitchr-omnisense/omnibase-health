---
title: "Concepts"
order: 20
---

# Concepts

omnibase/health adds seven entities beside omnibase/office's (`Office`,
`Member`, the agenda, the vault's `Document`): each one completes an
office's record rather than repeating it.

## The practice and its team

| Entity | Table | Holds |
|---|---|---|
| `Practice` | `health_practice` | one to one with omnibase/office's `Office`: its `type` (`PracticeType`), FINESS number, presentation, emergency instructions, out-of-hours text, whether it accepts new patients, offers teleconsultation, care at home |
| `Practitioner` | `health_practitioner` | one to one with an office `Member`: profession code and profession (from the Annuaire Santé), specialty, `sector` (`Sector`), Carte Vitale, third-party payment, accepts new patients, MSSanté address |
| `Fee` | `health_fee` | a practitioner's fee, or the practice's when it has none: category, label, amount and reimbursement as printed, position |

`PracticeType`: `msp`, `health_centre`, `medical`, `nursing`,
`physiotherapy`, `midwifery`, `paramedical`. `Practice::isHomeCareFirst()` is
true when the practice offers care at home and its type puts it first (a
nursing practice): a site then shows care at home before appointments.

`Sector`: `sector_1`, `sector_2`, `optam`, `not_contracted`, `contracted`.
`Practitioner::getRpps()` is the member's `registryId`;
`isPhysician()` is profession code `10`.

## The patients

| Entity | Table | Holds |
|---|---|---|
| `PatientProfile` | `health_patient_profile` | one to one with the account: family name, given names, birth date, phone, address, the referring physician (a `Practitioner`), the opposition to sharing within the team, when it was erased |
| `Dependent` | `health_dependent` | someone the account books for: names, birth date, `relation` (`Relation`: `child`, `parent`, `spouse`, `other`) |
| `IdentityLink` | `health_identity_link` | an account's identity at FranceConnect or Pro Santé Connect: provider, `sub` (unique per provider), the level (`acr`), first and last use (`used()`) |

A profile is not a medical record: nothing clinical is kept in it.
`PatientProfiles::of($user)` makes it when first needed, and on
omnibase/office's `ClientAccountEvent::CREATED` (an account opened from the
practice's invitation). `PatientProfile::erase()` empties it.

An appointment booked or requested online takes the patient's name as their
profile writes it ("Camille ÉRABLE"), not the account's identifier
(`AppointmentNaming`, on `AppointmentEvent::BOOKED` and `REQUESTED`).
`DependentBeneficiaries` offers the dependants to omnibase/office's booking.

## Care at home

`HomeCareRequest` (`health_home_care_request`): the patient (and the
dependant it is for), the address and phone, `careType` (`injection`,
`dressing`, `blood_test`, `hygiene`, `infusion`, `monitoring`, `medication`,
`other`), `frequency` (`CareFrequency`: `once`, `daily`, `every_other_day`,
`weekly`), start and end dates, preferred time, the prescription (a vault
`Document`), the notes (encrypted, `notesCipher`), `status` (`CareStatus`:
`pending`, `accepted`, `refused`, `cancelled`), the service area it fell in,
who was assigned, the series of appointments made, when it was sent and
decided. `getVisitCount()` counts the visits its frequency gives between the
dates, in calendar days ([Care at home](home-care.md)).

## The roles

| Role (`health.roles`) | Default | Given by |
|---|---|---|
| `practitioner` | `ROLE_PRACTITIONER` | the group "Praticiens" |
| `secretary` | `ROLE_SECRETARY` | the group "Secrétariat" |
| `staff` | `ROLE_STAFF` | both, through the role hierarchy |
| `coordination` | `ROLE_ADMIN` | the practice's administration |

What each one reads of a patient's documents is the voter's
([Medical secrecy](secrecy.md)).
