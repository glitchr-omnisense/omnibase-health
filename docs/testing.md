---
title: "Testing"
order: 60
---

# Testing

## Running the suite

Inside a host application that has omnibase/office (the suite boots the
host's kernel once, for omnisense/omnibase's class aliases):

```bash
php vendor/bin/phpunit -c vendor/omnibase/health/phpunit.xml.dist
```

The bootstrap also reads the omnibase/* packages the bundle requires or
suggests from beside the checkout, when the host has not installed them.

omnisense/omnibase-harness has no omnibase/office: its `test health` stops at
the first class it needs (`Base\Office\Controller\Admin\OpenToTrait`). With
omnibase/office's checkout on the harness's autoloader and no kernel, 14 of
the 23 tests pass; the 9 others (`CareTeamAudienceTest`, four of
`StaffTwoFactorCheckTest`) need the kernel's `App\Entity\User` aliases, so a
host with omnibase/office.

## What the tests check

| Test | Checks |
|---|---|
| `Compliance\StaffTwoFactorCheckTest` | missing while nothing requires a second factor of the staff; a role the staff do not all hold does not count; a warning while staff accounts have none; satisfied once every one has one; a role under the staff's covers them |
| `Controller\CrudRequiredFieldsTest` | omnibase/admin's `CrudFieldsTestCase` on the five screens |
| `Demo\HealthDemoAccountsTest` | one demonstration account for each role of the practice; nobody above the practice's administrator; each has its label and its sentence in the catalogue |
| `Enum\CareFrequencyTest` | visits counted in calendar days; the hour and the timezone of the first visit add no day; through the night the clocks change; a request's visit count |
| `Security\CareTeamAudienceTest` | a practitioner who follows the patient reads; one who does not, does not; the patient's opposition closes the team's access; the secretariat sees the title, not the content; nobody outside the staff |
