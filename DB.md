# Database query-builder migration

Replace remaining Laravel `DB::queryBuilder()` calls with the project's
`DB::query()` library, one file or coupled query flow at a time. This checklist
was rebuilt from the current `develop` branch; the earlier document is not
available in Git history. Counts are occurrences of `DB::queryBuilder(`, **not**
estimates of SELECT statements: they include INSERT, UPDATE, DELETE, subqueries,
and builders passed to other services. An unchecked file may already have some
queries migrated. Remove an item only when no old-builder calls remain in it.

Find the precise call sites with `rg -n 'DB::queryBuilder\(' path/to/file`.
Follow assigned builders to their terminal operations and callers before
changing them. Do not convert a SELECT in isolation if a write, datatable,
UNION, or helper still requires a Laravel builder. Keep unrelated refactoring
out of each batch.

This migration also tests and develops the new database library. Identify
missing features and behavioral differences as you encounter them; the library
will be extended to support the queries we need. For each gap, record the
affected call sites, the required behavior (including result shape and database
dialects), and what the library currently cannot do. Leave those queries on
the Laravel builder until the library supports an equivalent conversion, then
revisit them.

## Conversion rules

- Write SELECT chains in natural SQL order: `DB::query()->select(...)->from(...)
  ->join(...)->where...->groupBy(...)->orderBy(...)`. Put each chained method
  on its own line. The new builder is immutable; assign the result when adding
  conditional clauses, and return a builder from nested callbacks.
- Preserve joins, bindings, ordering, grouping, filters, dialect-specific
  behavior, and return types. `get()` and `pluck()` return arrays by default;
  pass `in: Collection::class` when callers need Laravel Collections.
  `get()`, `first()`, and `exists()` require an explicit projection;
  `pluck()`, `value()`, and `count()` choose their own projection.
- Check the installed library before declaring a feature missing. `forUpdate()`
  handles row locks, `insertFrom()` handles INSERT-from-SELECT, and `upsert()`
  is appropriate only where the matching columns have the intended unique
  constraint. Do not substitute `upsert()` for arbitrary `updateOrInsert()`.
- Preserve error handling when migrating writes: the new library throws
  `Fisharebest\Database\Exceptions\QueryException` for database failures.
  Migrate coupled queries and their exception handling together.
- Add or update focused database-backed tests, and check the changed PHP with
  the repository's PHPUnit, PHPCS, and PHPStan commands. Do not approximate
  unsupported queries or silently change their SQL semantics.

## Known coupled work and gaps

- `DatatablesService::handleQuery()` and all its callers now use the new
  builder. The shared `PendingChangesService::changesQuery()` and
  `SiteLogsService::logsQuery()` helpers also feed CSV and delete requests;
  deletes select matching primary keys through a derived subquery because
  the new builder does not compile joined deletes.
- The new library's `delete()` does not compile JOINs. The joined deletes in
  `ModuleDeleteSettings`, `GedcomImportService`, `TreeService`, and
  `UserService` now use `whereInSubquery()` to select the matching
  rows. `GedcomImportService` uses a distinct derived subquery to materialize
  IDs from its DELETE target for MySQL. The library now supports limited
  deletes, and `GedcomLoad` uses them to delete up to 10,000 rows per batch,
  retaining its affected-row count and timeout handling.
- `RenumberTree.php` now uses bound `REPLACE(...)` expressions and correlated
  `EXISTS` filters in place of joined updates.
- `Migration44.php` now imports only places reachable from legacy roots using
  a recursive CTE, ordered by ID to insert parents before children. Its
  invalid-coordinate and place-name updates use bound `NOT REGEXP` predicates
  and an expression-valued `SUBSTRING(...)` assignment. `Migration45.php`
  uses expression-valued `UPPER(...)` assignments.
- `CheckTree.php` now selects the latest pending `change_id` per `xref` and
  orders the complete `UNION ALL` by priority so stored records are processed
  before pending changes. No ordered UNION branch is required.
- `ListBuilder.php` now uses conditional joins and bound `whereLike()` filters
  for individual and family reports. For the family-name inner join, the
  `(husband OR wife)` match is a grouped `WHERE` condition.

The production checklist excludes test fixtures. Migrate their remaining
`DB::queryBuilder()` calls using the separate checklist below.

## Remaining files

Occurrence counts below are a snapshot. Recount after each batch with
`rg -c 'DB::queryBuilder\(' app resources --glob '*.php' --glob '*.phtml'`.
At reconstruction: **359 occurrences across 61 production files** (tests
excluded). After migrating the views, compatible deletes, inserts, and
updates: **188 occurrences across 28 production files**.
After migrating compatible `StatisticsData` queries: **147 occurrences across
28 production files**.
After replacing the four `joinSub()` calls with CTEs: **118 occurrences across
26 production files**.
After migrating the datatable query flow: **112 occurrences across 21
production files**.
After migrating joined deletes with matching-key subqueries: **107 occurrences
across 17 production files**.
After migrating `GedcomLoad`'s limited deletes: **94 occurrences across 16
production files**.
After migrating compatible statistics, module, and schema queries: **61
occurrences across 9 production files**. Every remaining production file
has a blocker described above.
After migrating the search-and-replace and shared data-fix queries: **47
occurrences across 7 production files**.
After migrating `MediaFileService::allFilesInDatabase()`: **46 occurrences
across 6 production files**.
After migrating `LinkedRecordService::allLinkedRecords()`: **44 occurrences
across 5 production files**.
After migrating `CheckTree::get()`: **38 occurrences across 4 production files**.
After migrating the expression updates in `Migration44` and `Migration45`:
**34 occurrences across 3 production files**.
After migrating the unjoined updates in `RenumberTree`: **25 occurrences
across 3 production files**.
After replacing the joined updates in `RenumberTree` with `EXISTS` filters:
**3 occurrences across 2 production files**.
After migrating the individual and family queries in `ListBuilder`:
**1 occurrence across 1 production file**.
After migrating the orphan cleanup in `Migration44`:
**0 occurrences across 0 production files**.

### Domain and infrastructure

No remaining calls.

### HTTP controllers

No remaining calls.

### Reports and schema migrations

No remaining calls.

## Test files

At inventory: **113 occurrences across 34 files** under `tests/`; after
migration: **0 occurrences across 0 files**. Counts are
occurrences of `DB::queryBuilder(`, including fixture setup and assertions.
Recount with `rg -c 'DB::queryBuilder\(' tests --glob '*.php'`; check off a
file only when no old-builder calls remain in it.

### Shared test infrastructure

- [x] `tests/TestCase.php` (12)

### HTTP controller tests

- [x] `tests/Unit/Http/Controllers/AbstractModuleComponentTest.php` (3)
- [x] `tests/Unit/Http/Controllers/GedcomLoadTest.php` (3)
- [x] `tests/Unit/Http/Controllers/MapDataExportCSVTest.php` (2)
- [x] `tests/Unit/Http/Controllers/MapDataExportGeoJsonTest.php` (2)
- [x] `tests/Unit/Http/Controllers/MapDataSaveTest.php` (3)
- [x] `tests/Unit/Http/Controllers/MergeFactsTest.php` (2)
- [x] `tests/Unit/Http/Controllers/MergeTreesTest.php` (17)
- [x] `tests/Unit/Http/Controllers/PendingChangesLogTest.php` (1)
- [x] `tests/Unit/Http/Controllers/SearchGeneralTest.php` (4)
- [x] `tests/Unit/Http/Controllers/SiteLogsTest.php` (3)
- [x] `tests/Unit/Http/Controllers/SitemapIndexXmlTest.php` (1)
- [x] `tests/Unit/Http/Controllers/TreePageBlockTest.php` (2)
- [x] `tests/Unit/Http/Controllers/TreePageTest.php` (4)
- [x] `tests/Unit/Http/Controllers/TreePreferencesTest.php` (2)
- [x] `tests/Unit/Http/Controllers/TreePrivacyTest.php` (3)
- [x] `tests/Unit/Http/Controllers/UnconnectedTest.php` (1)
- [x] `tests/Unit/Http/Controllers/UserPageBlockTest.php` (2)
- [x] `tests/Unit/Http/Controllers/UserPageTest.php` (4)

### Module tests

- [x] `tests/Unit/Module/AbstractModuleTest.php` (3)
- [x] `tests/Unit/Module/FamilyTreeFavoritesModuleTest.php` (1)
- [x] `tests/Unit/Module/FrequentlyAskedQuestionsModuleTest.php` (4)
- [x] `tests/Unit/Module/HitCountFooterModuleTest.php` (2)

### Schema tests

- [x] `tests/Unit/Schema/Migration37Test.php` (2)
- [x] `tests/Unit/Schema/Migration43Test.php` (3)
- [x] `tests/Unit/Schema/SeedDefaultResnTableTest.php` (4)
- [x] `tests/Unit/Schema/SeedGedcomTableTest.php` (4)
- [x] `tests/Unit/Schema/SeedUserTableTest.php` (4)

### Service tests

- [x] `tests/Unit/Services/GedcomImportServiceTest.php` (2)
- [x] `tests/Unit/Services/MapDataServiceTest.php` (1)
- [x] `tests/Unit/Services/TreeServiceTest.php` (5)

### Other unit tests

- [x] `tests/Unit/SiteTest.php` (1)
- [x] `tests/Unit/TreeTest.php` (4)
- [x] `tests/Unit/UserTest.php` (2)
