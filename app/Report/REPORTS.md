Report subsystem
================

The report subsystem spans the following paths:

* `app/Report/`
* `resources/xml/reports/`
* `tests/Unit/Report/`
* `resources/views/layouts/report.phtml` (HTML wrapper for report output)

Read `AGENTS.md` for project guidelines.

This is a living document. Update it as necessary.

Audience and purpose
====================

This document is for future LLMs and developers working on report generation.
It explains how the report subsystem is structured, where the control flow starts,
what invariants must be preserved during refactors, and how to test safely.

High-level overview
===================

The report subsystem renders XML-defined reports in two output formats:

- HTML via `/app/Report/HtmlRenderer.php`
- PDF via `/app/Report/PdfRenderer.php` and `/app/Report/TcLibPdfAdaptor.php`

Report templates (the XML DSL) live in `/resources/xml/reports`.

The subsystem uses two parsing phases followed by rendering:

1) Setup parse (`ParserSetup`) to extract title/description/inputs for the UI form.
2) Generation parse (`ParserGenerate`) to execute report XML into a `ReportDocument` model plus styles.

3) Layout + write (`LayoutEngine` + `HtmlWriter` / `PdfWriter` + `PdfBlockWriter`) to produce final bytes.

HTML files should be self-contained.  All styles/images should be inline.  No external CSS/JS/image files.

Entry points and request flow
=============================

HTTP handlers for reports are in `/app/Http/RequestHandlers`:

- `/app/Http/RequestHandlers/ReportListPage.php`
  - Lists available report modules.
- `/app/Http/RequestHandlers/ReportListAction.php`
  - Validates selected report and redirects to setup page.
- `/app/Http/RequestHandlers/ReportSetupPage.php`
  - Uses `ParserSetup` to build report option controls.
- `/app/Http/RequestHandlers/ReportSetupAction.php`
  - Redirects setup form submissions to generate endpoint.
- `/app/Http/RequestHandlers/ReportGenerate.php`
  - Instantiates `ParserGenerate` with `HtmlRenderer` or `PdfRenderer`.
  - Calls renderer `output()` and returns the generated HTML/PDF response body.

Practical call chain for generation:

1. `ReportGenerate::handle()` resolves module and XML filename.
2. `new ParserGenerate(...)` is constructed (no parsing occurs yet).
3. `->process()` is called, which opens the XML file and runs `parse()`.
4. `ParserGenerate::docEndHandler()` finalizes a `ReportDocument`, applies parsed styles, and passes both to the renderer.
5. `ReportGenerate::handle()` calls `$renderer->output()` to produce final HTML/PDF bytes.

Core architecture in `/app/Report`
==================================

Main roles:

- `AbstractParser`
  - Generic XML pull parser wrapper around `XMLReader`.
  - Dispatches start/end/character events to handler tables.
  - Fails early on unknown XML tags.
  - Supports nested fragment parsing (`parseFragment()`) for loops.

- `ParserSetup`
  - Fast metadata parse: report title, description, `<Input>` definitions.
  - Used only by setup page.

- `ParserGenerate`
  - Full execution engine for report XML DSL.
  - Maintains runtime state (GEDCOM context, loops, variables, condition gates).
  - Creates render elements through backend-agnostic factory interfaces.
  - Builds a `ReportDocument` via `ReportDocumentBuilder` and applies it to the renderer at `</Doc>`.

- `AbstractRenderer`
  - Backend-neutral API for rendering and style/state tracking.
  - Receives `ReportDocument` via `applyReportDocument()` and styles via `addStyle()`.
  - Uses `ReportRenderContext` to hold current style, style map, and parsed sections.

- `HtmlRenderer` / `PdfRenderer`
  - Concrete output backends.

- Element hierarchy (`Cell`, `Text`, `Image`, `TextBox`, `Line`, etc.)
  - Immutable-ish render instructions collected during parse, rendered later.

- Document/layout pipeline
  - `ReportDocumentBuilder` collects header/body/footer elements during parse.
  - `LayoutEngine` computes positioned `LayoutBlock`s and page splits.
  - `HtmlWriter` / `PdfWriter` consume layout blocks to emit final output.

- Utility services:
  - `VariableTable`: strict runtime variable storage; undefined lookups throw.
  - `PlaceholderExpander`: resolves `$variables`, `@tokens`, i18n pseudo-calls, expressions.
  - `ReportListBuilder`: builds `<List>` datasets (SQL + PHP filtering).
  - `GedcomTextReader`: string-based GEDCOM extraction helpers.

Parse model
===========

Phase 1: setup
--------------

`ParserSetup` scans all legal tags but only consumes:

- `<Title>`
- `<Description>`
- `<Input ...>`

Result objects are `InputDefinition` values consumed by setup UI code.

Phase 2: generation
-------------------

`ParserGenerate` executes report semantics. Important invariants:

- Every legal XML tag must be present in both start/end dispatch tables.
- Unknown tags should throw, not silently skip.
- Control-flow tags (`<if>`, `<Gedcom>`, loop tags) use gating counters to suppress nested dispatch when inactive.

Report XML execution model
==========================

The XML DSL is interpreted event-by-event. Major tag families:

- Layout and sections: `<Doc>`, `<Header>`, `<Body>`, `<Footer>`, `<NewPage>`
- Style and text: `<Style>`, `<Text>`, `<Cell>`, `<TextBox>`, `<Line>`, `<br>`
- Dynamic values: `<var>`, `<SetVar>`, `<Now>`, `<PageNum>`, `<TotalPages>`, `<GeneratedBy>`, `<Generation>`
- GEDCOM traversal: `<Gedcom>`, `<GedcomValue>`, `<GetPersonName>`
- Iteration: `<RepeatTag>`, `<Facts>`, `<List>`, `<Relatives>`, `<ListTotal>`
- Footnotes/media: `<Footnote>`, `<FootnoteTexts>`, `<Image>`, `<HighlightedImage>`
- Branding: `<WebtreesLogo>` (clickable webtrees logo; supports `width`/`height` attributes, 4:1 aspect ratio, defaults to 40pt × 10pt)
- Conditionals: `<if condition="...">`

`<Style style="...">` flags are strict and case-sensitive. Only uppercase `B`, `I`, `U`, and `D` are valid. Lowercase flags and any other characters should throw `LogicException` during parsing.

Do not assume SAX/DOM behavior: this is custom pull parsing with explicit state.

Parser state machine details (important)
========================================

`ParserGenerate` has several gate/counter fields that control whether handlers run:

- `$process_footnote` (boolean gate)
- `$process_ifs` (nested `<if>` skip depth)
- `$process_gedcoms` (nested `<Gedcom>` skip depth)
- `$process_repeats` (nested loop skip depth)

When a gate is active, most tags are ignored except the tags needed to close/re-enter the gated scope.
This behavior is implemented in:

- `gateAllowsStart()`
- `gateAllowsEnd()`
- `gateAllowsCharacterData()`

Nested loops and nested GEDCOM contexts are stack-based:

- `RepeatFrame` stack saves loop fragment state.
- `GedcomFrame` stack saves current GEDCOM/fact/desc context.
- `container_stack` tracks nested text-box containers (`ReportDocumentBuilder|TextBox`).

Any refactor that changes these stacks can break nested reports in subtle ways.
Run regression tests before and after.

Variables and expression handling
=================================

Variable sources:

- Setup form inputs (`<Input>` defaults + user values)
- Runtime mutation via `<SetVar>`

`VariableTable` is strict: undefined variable access throws `DomainException`.
This is intentional to surface bad report XML early.

`PlaceholderExpander` handles:

- `$variable` substitution
- `@ID`, `@fact`, `@desc`, `@generation`, and `@tag` resolution
- `I18N::number()`, `I18N::translate()`, `I18N::translateContext()` pseudo-calls
- Arithmetic expression evaluation
- `<if>` expression evaluation via Symfony ExpressionLanguage

Expression functions are supplied by `ExpressionLanguageProvider`.

Dataset/list building
=====================

`<List>` processing is delegated to `ReportListBuilder`.

Supported list roots include:

- `pending`
- `individual`
- `family`

Filtering strategy is two-phase:

1. SQL-side filtering for patterns that can be translated efficiently.
2. PHP-side filtering for conditions that require GEDCOM text inspection.

This split is performance-sensitive. Preserve SQL pre-filtering when adding new filter syntax.

Rendering backends
==================

HTML
----

`HtmlRenderer` runs `LayoutEngine::layoutPaged()` and then `HtmlWriter::renderPaged()`.
It uses an infinite page height for layout, so content is effectively unbounded vertically.

PDF
---

`PdfRenderer` delegates block rendering to `PdfWriter`/`PdfBlockWriter`, and
low-level PDF drawing to `TcLibPdfAdaptor`.

`TcLibPdfAdaptor` contains critical compatibility behavior:

- Own cursor/margin/page-break management.
- Header/footer rendering hooks on page changes.
- Custom text wrapping and line counting to keep measured vs rendered height aligned.
- Replacement of total-page placeholder token (`{{:ptp:}}`) in page streams.
- Deterministic metadata helpers used by tests.

Treat `TcLibPdfAdaptor` as sensitive code: small changes can alter many snapshots.

Bidirectional text (RTL/LTR)
============================

The report XML DSL supports an `rtl` page direction set from the active locale.
When the page is RTL, coordinates are mirrored and text alignment is flipped.

Mixed-direction text can only enter the output in one place:
`ParserGenerate::gedcomValueStartHandler()`. This is where raw
GEDCOM values (names, places, notes) are inserted into the current element.
All other text in reports (labels, headings, translated strings) matches the
overall page direction because it comes from `I18N::translate()`.

Bidi isolation is applied at this injection point using Unicode First Strong
Isolate (U+2068) and Pop Directional Isolate (U+2069) characters:

    $this->current_element->addText(
        UTF8::FIRST_STRONG_ISOLATE . $value . UTF8::POP_DIRECTIONAL_ISOLATE
    );

This prevents consecutive reversed-direction segments from merging and keeps
weak characters (punctuation, parentheses) associated with their logical
segment rather than inheriting direction from adjacent text.

How each backend handles bidi:

- **HTML**: FSI/PDI characters pass through `escapeText()` unchanged.  Modern
  browsers natively implement UAX #9 and render the isolates correctly.

- **PDF**: tc-lib-pdf does not render FSI/PDI (U+2068/U+2069) correctly —
  fonts lack glyphs for these codepoints, producing visible boxes.
  `TcLibPdfAdaptor` replaces FSI→LRE (U+202A) and PDI→PDF (U+202C) before
  text reaches `addTextCell()`.  These older embedding characters ARE in the
  font (zero-width) and are fully handled by tc-lib-unicode's Bidi algorithm.

  LRE/PDF provide directional embedding that keeps neutral characters (commas,
  spaces) at the correct Bidi level within LTR place names like "Mayfair,
  London, England" embedded in RTL paragraphs.  Without this embedding,
  neutral characters resolve to the RTL paragraph level, causing word-order
  reversal.

  The `PdfBlockWriter` text flow renderer passes complete text runs (not
  individual words) to `drawTextBlock()` so that the Bidi algorithm operates
  on full phrases.  Runs are positioned at the run level (RTL cursor for RTL
  pages) while word order within each run is preserved by the Bidi algorithm.

- **Width measurement**: `HtmlTextMeasurer` strips FSI/PDI before counting
  characters (they are zero-width for layout).  `PdfTextMeasurer` delegates
  to `TcLibPdfAdaptor::getStringWidth()` which replaces FSI/PDI with
  LRE/PDF (also zero-width) before measuring with font metrics.

RTL affects coordinate mirroring (x = page_width - x - width) and default
text alignment, but the bidi algorithm handles inline reordering.

Privacy and permissions
=======================

Report generation must honor record visibility.
Common patterns:

- GEDCOM records are privatized before value extraction/rendering.
- Linked records are checked with `canShow()`/`canShowName()`.
- Loop builders count private records and may output visible/total counts.

Do not bypass these checks when adding shortcuts.

Error handling and diagnostics
==============================

The subsystem prefers explicit failures over silent fallback:

- Unknown XML tags throw with file + line context.
- Missing required attributes throw `LogicException`.
- Undefined variables throw `DomainException`.
- Parser errors are wrapped with report filename, approximate line, element name, and record xref.

This context is assembled in `ParserGenerate::addContextToException()`.
Preserve it when modifying exception paths.

Tests
=====

Tests are in `/tests/Unit/Report`.

In addition to regression snapshots, the subsystem has focused unit tests for
parsers, layout, PDF helper services, and document/value objects.

The most important safety net is:

- `/tests/Unit/Report/ReportRegressionTest.php`

It renders bundled reports to HTML and PDF and compares against snapshots.
This catches behavioral/layout regressions that unit tests miss.

Run focused regression test:

```bash
vendor/bin/phpunit tests/Unit/Report/ReportRegressionTest.php
```

Regenerate snapshots only for intentional changes:

```bash
UPDATE_SNAPSHOTS=1 vendor/bin/phpunit tests/Unit/Report/ReportRegressionTest.php
```

Also run full quality suite when practical:

```bash
composer ci
```

Safe refactor checklist for future LLMs
=======================================

Before editing:

1. Read this file and the target parser/renderer classes end-to-end.
   - Include `LayoutEngine`, `HtmlWriter`, `PdfWriter`, and `PdfBlockWriter` when layout/output behavior is affected.
2. Identify whether your change affects setup parse, generation parse, or both.
3. Confirm whether the change can alter snapshot output.

During editing:

1. Keep dispatch tables complete and symmetric.
2. Preserve gate counter semantics and stack push/pop pairing.
3. Prefer throwing on invalid report XML rather than defaulting silently.
4. Keep privacy checks in place.

After editing:

1. Run `ReportRegressionTest`.
2. If output changes are intentional, regenerate snapshots and inspect diffs.
3. Run `composer ci` when time allows.

Common extension tasks
======================

Add a new XML tag
-----------------

1. Add start and end handlers in both `ParserSetup` and `ParserGenerate` dispatch tables.
   - In setup parser, use no-op unless metadata should be consumed.
2. Implement handler behavior in `ParserGenerate`.
3. Add tests (at least targeted parser behavior + regression coverage).
4. Validate both HTML and PDF output paths.

Add a new renderer capability
-----------------------------

1. Extend `AbstractRenderer` contract.
2. Implement in `HtmlRenderer` and `PdfRenderer`.
3. Add or update element classes as needed.
4. Ensure `TcLibPdfAdaptor` supports required semantics.

Add new filter syntax for `<List>`
----------------------------------

1. Extend SQL pre-filter in `ReportListBuilder` where possible.
2. Extend PHP fallback filtering for exact semantics.
3. Add tests with realistic GEDCOM samples.
4. Confirm no privacy regressions.

Known hotspots
==============

These files are high-risk for regressions:

- `/app/Report/ParserGenerate.php`
- `/app/Report/TcLibPdfAdaptor.php`
- `/app/Report/ReportListBuilder.php`
- `/app/Report/GedcomTextReader.php`

Changes here usually require snapshot updates and careful review.
