# PDF Font Migration Plan (DejaVu -> Noto + Fallback)

This file is the single source of truth for migration progress.
Update checkboxes and notes as work lands so we can resume after interruptions.

## Goals

- Improve script coverage in PDF reports (Thai and other non-Latin scripts).
- Replace the current DejaVu-centered configuration with a Noto-based setup.
- Add deterministic font fallback in report rendering (no OS font dependency).
- Preserve report snapshot confidence during each phase.

## Agreed Decisions

- We will **not** keep backward compatibility for the old single-font config setting.
- We will build a **coverage index during font processing** and store it as JSON for runtime use.
- We will do this in two major stages:
  1. Simple switch to Noto and regenerate report snapshots.
  2. Add runtime font-file switching/fallback and validate no regressions.

---

## Phase A - Baseline Switch To Noto (No Fallback Yet)

### A1. Select initial Noto set and licensing docs

- [x] Choose initial bundled fonts (minimum viable set for current data).
- [x] Confirm license documentation is included for distributed fonts.
- [x] Record font sources and exact versions.

Notes:
- Selected initial set for baseline switch:
  - `NotoSans-Regular.ttf`
  - `NotoSans-Bold.ttf`
  - `NotoSans-Italic.ttf`
  - `NotoSans-BoldItalic.ttf`
  - `NotoSansThai-Regular.ttf`
  - `NotoSansArabic-Regular.ttf`
  - `NotoSansHebrew-Regular.ttf`
  - `NotoSansDevanagari-Regular.ttf`
- Source repository: `https://github.com/notofonts/noto-fonts`
- Pinned source commit for reproducibility: `ffebf8c1ee449e544955a7e813c54f9b73848eac`
- License: SIL Open Font License 1.1 (OFL-1.1). Include OFL text in the repository when bundling the files.
- Deferred scope: CJK-specific Noto families will be evaluated after baseline snapshot refresh due to size impact.

### A2. Convert fonts for tc-lib-pdf

- [x] Add/obtain TTF files in a controlled source path.
- [x] Run existing font conversion utility to generate tc-lib-pdf assets.
- [x] Add generated assets under `resources/fonts`.

Expected outputs per font family/style:
- `*.json`
- `*.z`
- `*.ctg.z`

Completed conversions:
- `notosans`, `notosansb`, `notosansi`, `notosansbi`
- `notosansthai`, `notosansarabic`, `notosanshebrew`, `notosansdevanagari`

### A3. Remove old config setting and replace with new config model

- [x] Remove old report font config setting (no compatibility shim).
- [x] Introduce new explicit setting names for current phase (single active family).
- [x] Update report parser/config classes to use new setting names only.

### A4. Switch default report font to Noto

- [x] Change report default from DejaVu to Noto in report generation path.
- [x] Verify PDF generation works end-to-end with current reports.

Verification note:
- Ran focused regression: `ReportRegressionTest::testReportPdfOutputMatchesSnapshot` for `ahnentafel_report`.
- PDF generation succeeded (header assertion passed); snapshot mismatch is expected until Phase A5 refresh.

### A5. Snapshot-based regression checkpoint

- [x] Regenerate report snapshots after Noto baseline switch.
- [x] Review diffs and confirm expected-only changes.
- [ ] Commit baseline stage.

Validation note:
- Snapshot regeneration: `UPDATE_SNAPSHOTS=1 php vendor/bin/phpunit tests/Unit/Report/ReportRegressionTest.php --no-coverage`
- Verification run: `php vendor/bin/phpunit tests/Unit/Report/ReportRegressionTest.php --no-coverage`
- Result: both runs pass (`34 tests`).

Checkpoint exit criteria:
- Reports still render correctly for existing regression suite.
- No unexplained layout/encoding regressions.

---

## Phase B - Font Coverage Index + Runtime Fallback

### B1. Build coverage index in font-processing utility

- [x] Extend font conversion utility to emit per-font coverage JSON.
- [x] Define stable JSON schema for runtime lookup.
- [x] Store coverage JSON in `resources/fonts`.

Proposed schema (example):

```json
{
  "font": "notosans",
  "format": "tc-lib-pdf-bmp-coverage-v1",
  "ranges": [[32, 126], [160, 383]],
}
```

Implementation note:
- Prefer compact ranges to keep files small.
- Keep schema deterministic for reproducible builds.

### B2. Runtime loader/cache for coverage index

- [x] Add a runtime service to load and cache coverage JSON files.
- [x] Provide APIs to test codepoint support by font family/style.
- [x] Add defensive errors for missing/corrupt coverage files.

Implementation note:
- Added `app/Report/FontCoverageIndex.php` with lazy-loading cache, codepoint support checks, first-supporting-font lookup, and strict validation for missing/corrupt coverage JSON.

### B3. Report config: primary + fallback list

- [x] Add report config fields for `primary_font` and `fallback_fonts[]`.
- [x] Remove any single-font assumptions from config construction.
- [x] Validate configuration inputs early.

Implementation note:
- Added `fallback_fonts` to `Config` and propagated it through renderer config construction.
- `ParserGenerate` now validates `primary_font` and parses/validates `fallback_fonts` from `vars` as a comma-separated list.
- Font selection is now engine-owned: `ParserGenerate` sets internal defaults (`notosans` primary plus script fallbacks) and does not read font names from report XML/setup variables.

### B4. Text run segmentation and font switching

- [x] In PDF adaptor/renderer, split text into runs by available font coverage.
- [x] Select first matching font from `[primary + fallbacks]` per run.
- [x] Keep style/size consistent across run switches.
- [x] Ensure line wrapping and pagination remain stable.

### B5. Missing-glyph behavior and diagnostics

- [x] Add deterministic handling when no font covers a codepoint.
- [x] Add optional debug logging for selected font per run.
- [ ] Add user-visible warning path for unsupported glyphs if needed.

Implementation note:
- `TcLibPdfAdaptor` now keeps unsupported glyphs in output (allowing native tofu fallback if no glyph exists), tracks per-codepoint counts, and exposes optional diagnostics via `unsupportedGlyphDiagnostics()`.

### B6. Snapshot-based regression checkpoint

- [x] Regenerate report snapshots with fallback system enabled.
- [x] Confirm only expected diffs.
- [ ] Commit fallback stage.

Checkpoint exit criteria:
- Mixed-script data renders without tofu for covered scripts.
- Existing reports do not regress unexpectedly.

---

## Phase C - Validation and Hardening

### C1. Script-specific validation set

- [ ] Add multilingual fixture data (Thai, Arabic, Hebrew, Indic, CJK as available).
- [ ] Validate rendered output manually and in snapshots.

### C2. Performance and output-size checks

- [ ] Measure PDF generation time before/after fallback.
- [ ] Measure output size impact and tune subsetting/compression as needed.

### C3. Documentation

- [ ] Document how to add new fonts and regenerate assets/index.
- [ ] Document config options for primary/fallback order.
- [ ] Document known shaping/script limitations (if any).

---

## Work Log

Use this section for chronological status notes.

- 2026-07-11: Plan created and aligned with agreed migration strategy.
- 2026-07-11: Phase A1 completed. Initial Noto font set selected and source commit pinned.
- 2026-07-11: Phase A2 completed. Noto TTF files downloaded at pinned commit and converted with `php index.php convert-font ...`.
- 2026-07-11: Phase A3 completed. Report config moved from `font` to `primary_font` with no compatibility path.
- 2026-07-11: Phase A4 completed. Default report font switched to `notosans`; focused PDF regression confirms generation path works.
- 2026-07-11: Phase A5 checkpoint completed. Report snapshots regenerated and verified.
- 2026-07-11: Phase B1 completed. `convert-font` now emits `*.coverage.json` from tc-lib-pdf `.ctg.z` coverage tables.
- 2026-07-11: Phase B2 completed. Runtime coverage index service added with unit tests.
- 2026-07-11: Phase B3 completed. Report config now supports validated `primary_font` plus `fallback_fonts`.
- 2026-07-11: Phase B4 completed. `TcLibPdfAdaptor` now segments text by Unicode coverage, renders multi-font runs with preserved style/size, and measures width using the same fallback chain to keep wrapping stable.
- 2026-07-11: Phase B5 started. Added unsupported-glyph diagnostics (`unsupportedGlyphDiagnostics()`) and preserved native tofu rendering for missing glyphs.
- 2026-07-11: Engine now owns report PDF font defaults: `ParserGenerate` uses `notosans` plus script fallbacks (`notosansthai`, `notosansarabic`, `notosanshebrew`, `notosansdevanagari`) and no longer reads `primary_font`/`fallback_fonts` from report variables.
- 2026-07-11: Phase B6 checkpoint completed. Report snapshots regenerated and verified with fallback defaults enabled.

---

## Current Status

- Overall: [ ] Not started  [ ] Planning complete  [x] In progress  [ ] Done
- Active phase: `Phase B`
- Next actionable item: `B5 remaining item - user-visible warning path for unsupported glyphs`

