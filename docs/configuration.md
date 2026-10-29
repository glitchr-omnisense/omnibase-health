---
title: "Configuration reference"
order: 40
---

# Configuration reference

## The `health` tree

Dumped with Symfony's `YamlReferenceDumper` from
`Base\Health\DependencyInjection\HealthConfiguration`:

```yaml
health:

    # The roles the groups give (security.yaml's hierarchy puts both under ROLE_STAFF).
    roles:
        practitioner:         ROLE_PRACTITIONER
        secretary:            ROLE_SECRETARY
        staff:                ROLE_STAFF
        coordination:         ROLE_ADMIN

    # The permanent banner: who to call.
    emergency:
        samu:                 '15'
        europe:               '112'
        deaf:                 '114'
        pharmacy:             '3237'

        # The out-of-hours medical line (permanence des soins).
        oncall:               '116 117'

    # Months each thing is kept once over (health:purge), to be set by the practice with its DPO.
    retention:
        appointments:         36
        home_care:            36
        access_logs:          36
        signals:              1
        documents_after_expiry: 1

    # Whether the video solution is listed by the ANS (reimbursable teleconsultation). The one built in is not: the back office says so.
    teleconsultation_referenced: false
```

| Key | Default | Read by |
|---|---|---|
| `roles.practitioner`, `roles.secretary`, `roles.staff`, `roles.coordination` | `ROLE_PRACTITIONER`, `ROLE_SECRETARY`, `ROLE_STAFF`, `ROLE_ADMIN` | the voter, the care team, the back office's writes, the two-factor check |
| `emergency.samu`, `europe`, `deaf`, `pharmacy`, `oncall` | 15, 112, 114, 3237, 116 117 | the banner on every page and `/urgences` (`health_emergency()`) |
| `retention.appointments`, `home_care`, `access_logs` | 36 months | `health:purge` |
| `retention.signals` | 1 month | `health:purge`: the closed video rooms' handshakes |
| `retention.documents_after_expiry` | 1 month | `health:purge`: documents expired or withdrawn |
| `teleconsultation_referenced` | false | `/teleconsultation` and the compliance check |

Each key is a container parameter (`%health.roles.staff%`,
`%health.retention.appointments%`, `%health.teleconsultation_referenced%`...).
The retention defaults are placeholders, to be set by the practice with its
DPO.

## The command

`bin/console health:purge` removes what has outlived its retention
([Patients' data](patients-data.md)); `--dry-run` says what would go and
removes nothing. Run it from cron, once a day.

## What the bundle sets for omnibase/office

The team's and the member's templates (practitioners, with their fees and
next slots), the contact form's warning (no medical data there), the patient
space's menu. Its services plug into omnibase/office's and
omnibase/mailbox's interfaces:

| Service | Interface or event |
|---|---|
| `Security\CareTeamAudience` | who reads a vault document |
| `Service\DependentBeneficiaries` | omnibase/office's `BeneficiaryProviderInterface`: the dependants in the booking |
| `Mailbox\CareTeamRecipients` | whom a patient may write to besides the secretariat |
| `Service\AppointmentNaming` | `AppointmentEvent::BOOKED`, `REQUESTED` |
| `Service\PatientProfiles` | `ClientAccountEvent::CREATED` |
| `Service\ResultSender` | sends a result to a patient through the vault |
| `Service\HomeCare` | `submit()`, `accept()`, `refuse()` |
| `Service\PatientData` | `export()`, `zip()`, `erase()` |
| `Service\PractitionerRegistry` | `refresh()` |
| `Compliance\*` | the six checks of the compliance tile |
| `Twig\HealthExtension` | the Twig functions |

`Base\Health\Transmission\MssanteTransmitterInterface` is a contract without
an implementation (sending to Mon espace santé takes an operator and a CPS
card).

## Twig functions

| Function | Returns |
|---|---|
| `health_practice()` | the main practice |
| `health_practitioner(member)` | a member's `Practitioner` |
| `health_fees(practitioner = null)` | a practitioner's fees; without one, every fee in order |
| `health_emergency()` | the emergency numbers |
| `health_profile(user)` | the patient's profile |
| `health_dependents(user)` | their dependants |
| `health_care_team(user)` | the members who follow them |
| `health_types(member)` | the appointment types a patient may book with that member (not the staff-only ones) |
| `health_pending_care()` | how many home care requests wait |
| `health_teleconsultation_referenced()` | `health.teleconsultation_referenced` |

## Translations

The domain `health`, in French only (ICU format).
