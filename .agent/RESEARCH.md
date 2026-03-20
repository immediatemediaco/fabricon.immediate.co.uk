# Schedule restructuring for multi-day and independent track slots is viable but requires a significant schema change — the Slot entity must be redesigned from a paired-track model to a per-track model with an explicit date field

---

## Research Request

The feature request (`_plans/feat-shcedule-changes.txt`) asks for two major changes and one minor change:

**Major:**
1. Multi-day conference schedule support — separate schedule sections per day in the admin, with the number of days derived from the conference date range
2. Independent per-track time slot management — today a single Slot row binds track 1 and track 2 to the same start/end time; the request wants each track's slots to be independently configurable

**Minor:**
- Remove the hard-coded "Track 1 - " / "Track 2 - " text prefix before the configurable track description on the front end

The expected outcome is a working implementation across admin (EasyAdmin CRUD), data model (Doctrine entities + migration), controller logic, and front-end templates.

---

## Findings

### Current State

#### Data Model

The `Slot` entity is the central scheduling unit. It has:

```php
// src/Entity/Slot.php
- id (int, PK)
- startTime (TIME, nullable) — time only, no date component
- endTime (TIME, nullable) — time only, no date component
- track1 (ManyToOne → Talk, nullable)
- track2 (ManyToOne → Talk, nullable)
- breakDetails (string, nullable)
- conference (ManyToOne → Conference, NOT NULL)
```

**Key constraint:** A single Slot row always represents the same time window for both tracks. There is no way to give track 1 and track 2 different start/end times within the current schema.

The `Conference` entity already supports date ranges:

```php
// src/Entity/Conference.php
- startDate (DATE, NOT NULL)
- endDate (DATE, nullable)
- isMultiDay() → returns true if endDate differs from startDate
```

However, Slot has **no date or day field**. Slots store only TIME, so there is no mechanism to assign a slot to a specific day of a multi-day conference. All slots for a conference are loaded as a single flat list ordered by `startTime`.

The `Settings` entity stores global track descriptions:

```php
// src/Entity/Settings.php
- track1Description (text, nullable)
- track2Description (text, nullable)
```

These are global — not per-conference.

#### Admin (EasyAdmin CRUD)

`ScheduleCrudController` (`src/Controller/Admin/ScheduleCrudController.php`) is a standard EasyAdmin CRUD for the `Slot` entity. It:

- Filters the index to show only slots belonging to the "current conference" (stored in `Settings.currentConference`)
- Sorts by `startTime ASC`
- Presents fields: Conference (association), startTime, endTime, track1/track2 (Talk associations), breakDetails
- Has no custom schedule builder, no drag-and-drop, no visual timeline
- Creates/edits one slot at a time via standard EasyAdmin forms

There are no day-based sections, no track-based grouping, and no day tabs or filters.

#### Front-End Display

`ConferenceController::show()` loads all slots for a conference in one query:

```php
$schedule = $this->slots->findByConference($conference, ['startTime' => 'ASC']);
```

Three template variants exist:
- `templates/conference/show.html.twig` — default, uses `components/schedule.twig`
- `templates/conference/june-2023/index.html.twig` — 2023 theme
- `templates/conference/2026/index.html.twig` — 2026 theme

All templates render two tracks side-by-side by iterating slots and accessing `slot.track1` / `slot.track2`. They assume both tracks share the same time. There is no multi-day grouping.

**Hard-coded text to remove:**

In the 2023 and 2026 templates (line 64–65):
```twig
<h3 class="track__title">Track 1 - {{ settings.track1Description }}</h3>
<h3 class="track__title">Track 2 - {{ settings.track2Description }}</h3>
```

In the default template's components (`components/schedule.twig`):
```twig
{% include 'components/track.twig' with { track : 1, colour : "Cyan", description : settings.track1description } only %}
{% include 'components/track.twig' with { track : 2, colour : "Magenta", description : settings.track2description } only %}
```

`components/track.twig` renders a heading like `"Cyan track"` with the description below.

#### SlotRepository

`SlotRepository` (`src/Repository/SlotRepository.php`) has no custom query methods — it relies entirely on Doctrine's magic `findByConference()`. There is no method for fetching slots by day or track.

### Schema Redesign Options

The core challenge is that the current `Slot` schema pairs both tracks into one row. There are two main approaches to decouple this:

#### Option A — Per-track slots (restructure Slot entity)

Replace `track1`/`track2` with a single `talk` reference and add a `track` integer column, plus a `date` column for multi-day support:

```
Slot (redesigned):
- id
- date (DATE) — which day of the conference
- startTime (TIME)
- endTime (TIME)
- track (SMALLINT) — 1 or 2
- talk (ManyToOne → Talk, nullable)
- breakDetails (string, nullable)
- conference (ManyToOne → Conference)
```

**Pros:** Clean normalisation. Each slot is independent. Easy to query by day+track. Natural ordering. Extensible to N tracks in future.
**Cons:** Breaking change to the existing schema. Existing data needs migration. All templates and admin controllers must be rewritten. Breaks the current concept where a break spans both tracks in one row.

#### Option B — Keep paired slots, add date field only

Keep the `track1`/`track2` pairing but add a `date` column to Slot:

```
Slot (extended):
- id
- date (DATE) — which day
- startTime (TIME)
- endTime (TIME)
- track1 (ManyToOne → Talk, nullable)
- track2 (ManyToOne → Talk, nullable)
- breakDetails
- conference (ManyToOne → Conference)
```

**Pros:** Minimal schema change. Existing templates and admin mostly work. Just need day grouping.
**Cons:** Does NOT solve the independent track timing requirement. Track 1 and track 2 still share the same start/end time. This only addresses the multi-day requirement, not the independent tracks requirement.

### Recommendation

**Option A is the correct approach** because the feature request explicitly requires independent time slots per track. Option B cannot deliver that.

However, Option A requires careful handling of breaks — currently a break is represented as a slot where both track fields are null and `breakDetails` is populated. In a per-track model, breaks that span both tracks would need either:
- A break flag/type on the slot plus logic to display it spanning both columns, OR
- Duplicate break rows for each track (simpler data, messier UX)

A `isBreak` boolean or making `track` nullable (where `track = null` means "spans both") would handle this cleanly.

### Front-End Time Synchronisation

The feature request specifies that when displayed, tracks should be visually time-aligned:

> "Given track 1 has a slot of 10:00-11:00 and track 2 has a slot of 10:30-11:30. Then, when displayed, the track 2 slot should show as starting mid way of the track 1 slot."

This means the front-end cannot simply iterate slots sequentially per track. It needs a **time-grid layout** where slot vertical position and height are proportional to actual time. This requires:
1. A timeline approach — calculate slot position as a percentage/pixel offset relative to the day's time range
2. CSS Grid or absolute positioning to place slots at their correct vertical position
3. The controller must prepare the data grouped by day, then pass slot data with positional metadata (or let the template compute it)

This is the most complex part of the front-end work.

### Admin Day-Based Sections

The admin needs to show separate slot management sections per conference day. With EasyAdmin CRUD, this could be done by:
- Adding a filter/tab for day selection on the slot index page
- Grouping slots by date in the listing
- Pre-filling the `date` field when creating a new slot within a day section

EasyAdmin supports custom index page templates and filters, which can achieve this.

---

## Things to Consider

- **Data migration for existing slots:** Existing Slot rows have no `date` or `track` values. A migration must assign date = conference.startDate and split each existing row into two per-track rows (one for track1, one for track2). Empty tracks can be skipped.
- **Break handling in per-track model:** Need to decide whether breaks are stored once (track=null) or duplicated per track. Single-row with track=null is cleaner but requires special rendering logic.
- **Track descriptions in Settings are global, not per-conference.** If different conferences should have different track descriptions, the `Settings` entity or the `Conference` entity would need adjustment. The current feature request does not ask for this, but it's a natural follow-on.
- **The front-end time-grid layout is a significant CSS/JS change.** Current templates use a simple iterative grid. A time-proportional layout needs careful implementation for responsive behaviour and edge cases (overlapping slots, very short slots, etc.).
- **Three template variants need updating** — the default `show.html.twig` + `components/schedule.twig`, the 2023 theme, and the 2026 theme. The 2023 and 2026 templates are nearly identical; consider consolidating them.
- **No existing tests cover the schedule system.** Changes to entities, controllers, and templates are unverified at the test level. Consider whether tests should be added as part of this work.
- **`findByConference` in SlotRepository is auto-generated by Doctrine magic.** After adding `date` and `track`, a custom query method will be needed to return slots grouped and ordered by date, then by startTime, then by track.
- **The EasyAdmin schedule index currently filters by `Settings.currentConference`.** This pattern should be preserved, and the day-based sectioning should work within that filter.
- **Data migration for existing slots:** These changes should all still work in responsive mode for tablet and mobile phone users

---

## Follow-up Questions

- Should breaks always span both tracks, or should there be support for track-specific breaks? (Design decision needed before implementation)
- Should the track descriptions remain global in `Settings`, or move to be per-conference on the `Conference` entity? (The feature request doesn't ask for this, but it may be expected)
- For multi-day front-end display, should all days be shown on one page (scrollable) or should there be tabs/buttons to switch between days? (The request says "shown consecutively" which implies one page)
- What should happen to existing Slot data during migration? Is there production data that needs careful migration, or is this a development-only database?
- Is the default template (`show.html.twig` + `components/schedule.twig`) actively used, or only the themed templates (2023, 2026)? This affects how many templates need updating.

## Answers by user to the Questions above
- Breaks always span both tracks
- The track descriptions should move to be per-conference on the `Conference` entity
- For multi-day front-end display there be tabs/buttons to switch between days
- There is production data that needs careful migration
- Only the themed templates (2023, 2026) are being used

---

## References

- `_plans/feat-shcedule-changes.txt` — feature request specification
- `src/Entity/Slot.php` — current slot entity (track1/track2 paired model)
- `src/Entity/Conference.php` — conference with startDate/endDate and isMultiDay()
- `src/Entity/Settings.php` — global track descriptions
- `src/Controller/Admin/ScheduleCrudController.php` — admin slot CRUD
- `src/Controller/ConferenceController.php` — front-end conference display
- `src/Repository/SlotRepository.php` — slot data access
- `templates/conference/2026/index.html.twig:64-65` — hard-coded "Track 1/2 - " text
- `templates/conference/june-2023/index.html.twig:64-65` — same hard-coded text
- `templates/components/schedule.twig` — default schedule component
- `templates/components/track.twig` — track header component
- Migration `Version20260313104733` — most recent: added endDate, renamed date→startDate
