---
title: "The back office"
order: 36
---

# The back office

The screens are loaded when omnibase/admin is installed. Each one opens its
writes to a role of the practice with omnibase/office's `OpenToTrait`.

## The screens

| Screen | Fields | Who writes |
|---|---|---|
| `PracticeCrudController` | the office, the type, FINESS, presentation, emergency instructions, out of hours, new patients, teleconsultation, care at home | `ROLE_ADMIN` |
| `PractitionerCrudController` | the member, profession, profession code, specialty, sector, MSSanté, Carte Vitale, third-party payment, new patients; the action "Lire l'Annuaire Santé" | `ROLE_ADMIN` |
| `FeeCrudController` | label, amount, position, practitioner (none: the practice's), category, reimbursement | `ROLE_ADMIN` |
| `PatientProfileCrudController` | names, birth date, account, phone, address, referring physician, sharing opposition; no "new", no delete | `ROLE_STAFF` |
| `HomeCareRequestCrudController` | date, patient, town, care, dates, status, assigned to; read only (no "new", edit or delete) | `ROLE_STAFF` |

## Reading the Annuaire Santé

The practitioners' action `registry` (`/{entityId}/registry`) calls
`PractitionerRegistry::refresh($practitioner)`: with the member's RPPS
number (`registryId`), the Annuaire Santé gives the profession and the
MSSanté address, written on the `Practitioner`. It flashes
`admin.registry.read` with the name and profession, `admin.registry.no_rpps`
without a number, `admin.registry.unknown` when the registry does not know
it, `admin.registry.unavailable` with the error when it cannot be read. The
key is the API keys page's (`api.annuaire_sante.key`), else
`omnistate.annuaire_sante.api_key`.

## The home care requests

`/admin/agenda/soins-a-domicile` (`health_admin_home_care`, `ROLE_STAFF`)
lists the requests waiting, inside the back office's frame. Each is accepted
or refused with a POST to `/admin/agenda/soins-a-domicile/{id}/accept` or
`/refuse` (`health_admin_home_care_decide`): accepting takes the member who
goes and the first visit, and books the series
([Care at home](home-care.md)); the flash says how many visits were booked
and for whom (`admin.home_care.flash.accepted`), or that the request was
refused; a refusal of the booking (a slot taken) is flashed with its own
message.

## The tiles

| Widget | Shows |
|---|---|
| `health_requests` | how many home care requests wait, and the five latest |
| `health_inbox` | the signed-in member's unread messages and the desk's waiting ones, counts only (with omnibase/mailbox) |

## Settings and keys

On omnibase/admin's settings page, `health.legal.hds`: the host certified for
health data (HDS) the practice declares. On the API keys page, the Annuaire
Santé's key.

## Compliance

Six checks are added to omnibase/office's compliance tile:

| Check | Warns when |
|---|---|
| `MailboxKeyCheck` | with omnibase/mailbox: its encryption is off, or on without a key |
| `StaffTwoFactorCheck` | a staff account signs in without a second factor, or nothing requires one of the staff's role |
| `FeesDisplayedCheck` | an active practitioner has no fee, and the practice has none of its own (it names them) |
| `EmergencyCheck` | the main practice has no out-of-hours text |
| `HdsHostCheck` | no HDS-certified host is declared |
| `TeleconsultationCheck` | the built-in video is on and `health.teleconsultation_referenced` is false: it is not ANS-listed |

```php
yield MenuItem::block('health_requests', 'Soins à domicile', 'fa-solid fa-house-medical')->setSize(2);
yield MenuItem::block('health_inbox', 'Messages', 'fa-solid fa-envelope')->setSize(2);
yield MenuItem::linkToCrud(\Base\Health\Entity\PatientProfile::class, 'Patients', 'fa-solid fa-user');
yield MenuItem::linkToRoute('health_admin_home_care', [], 'Soins à domicile', 'fa-solid fa-house-medical');
```
