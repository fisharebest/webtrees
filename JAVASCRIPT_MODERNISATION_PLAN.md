# JavaScript Modernisation Plan

## Goals

- Move internal JavaScript code to ES modules using `import`/`export`.
- Preserve current runtime behavior during migration.
- Keep existing global APIs available until all callers are migrated.
- Deliver incremental, reviewable pull requests with low regression risk.

## Current Constraints

- Keep existing bundle names and load order (`vendor.min.js` then `webtrees.min.js`).
- Do not switch to `type="module"` globally in one step.
- Preserve compatibility globals during transition:
  - `window.webtrees`
  - `window.TreeViewHandler`
  - `window.statistics`

## GUI Test Pages (use throughout)

Replace placeholders with valid values from your instance:
- `{tree}`: a real tree name.
- `{xref}`: a real individual XREF.

Primary pages/URL templates:
- Default layout baseline: `/tree/{tree}/branches`
- Admin layout baseline: `/admin`
- Autocomplete + AJAX/init baseline: `/tree/{tree}/branches?surname=SMITH`
- Map baseline: `/tree/{tree}/place-list`
- Interactive tree baseline: `/module/tree/Chart/{tree}?xref={xref}`
- Statistics baseline: `/module/statistics-chart/Chart/{tree}` then open the `Other` tab (pie/column/geo)

Notes:
- If the statistics module slug differs in your deployment, open the statistics chart page from the Charts menu and continue with the same checks.
- If map data is sparse for a tree, also test `/tree/{tree}/pedigree-map-4/{xref}`.

## PR-by-PR Backlog

### PR 1 - Baseline Contract (no behavior change)

**Purpose**
- Document current runtime expectations before refactoring.

**Likely files**
- `resources/js/webtrees.js`
- `resources/js/treeview.js`
- `resources/js/statistics.js`
- `resources/views/layouts/default.phtml`
- `resources/views/layouts/administration.phtml`
- `app/Module/InteractiveTree/TreeView.php`

**Changes**
- Add concise intent comments around global exports/usages.
- Record required globals and script load-order assumptions.

**Commit**
- `chore(js): document current global runtime contract`

**Verify**
- `npm run build`

**GUI checks**
- Open `/tree/{tree}/branches` and `/admin`; confirm both load without JavaScript errors.
- In browser dev tools, verify `window.webtrees`, `window.TreeViewHandler`, and `window.statistics` still exist.
- Click one representative interactive control (for example a menu toggle or modal trigger) and confirm behavior is unchanged.

### PR 2 - Internal Module Layout

**Purpose**
- Introduce ES module structure while keeping current entrypoints.

**Likely files**
- `resources/js/webtrees.js`
- `resources/js/webtrees/` (new module folder)
- `webpack.config.js` (only if minimal wiring needed)

**Changes**
- Add module files with `export` APIs.
- Keep `resources/js/webtrees.js` as compatibility adapter on `window.webtrees`.

**Commits**
- `refactor(js): add internal webtrees module structure`
- `refactor(js): keep window.webtrees adapter stable`

**Verify**
- `npm run build`

**GUI checks**
- Repeat baseline checks on `/tree/{tree}/branches` and `/admin` to confirm script load order still works.
- Open `/tree/{tree}/branches?surname=SMITH`; confirm autocomplete and page initialization still run via `window.webtrees`.
- Confirm there are no new JavaScript console errors or warnings related to missing globals.

### PR 3 - Extract Webtrees Core Utilities

**Purpose**
- Move low-risk helper logic into modules first.

**Likely files**
- `resources/js/webtrees.js`
- `resources/js/webtrees/http.js` (new)
- `resources/js/webtrees/dom.js` (new)
- `resources/js/webtrees/forms.js` (new)

**Changes**
- Extract pure/helper functions.
- Route legacy method names through the adapter.

**Commits**
- `refactor(js): extract webtrees utility modules`
- `refactor(js): route legacy API through module adapter`

**Verify**
- `npm run build`

**GUI checks**
- On `/tree/{tree}/branches?surname=SMITH`, test a filter/form change and confirm submit flow is unchanged.
- On `/tree/{tree}/branches`, confirm AJAX-loaded sections still populate correctly (network requests and responses look correct).
- Test one DOM utility-driven interaction (show/hide/toggle behavior) and confirm no visual regression.

### PR 4 - Extract Feature Modules (autocomplete/map/init)

**Purpose**
- Modularize higher-risk feature slices in controlled steps.

**Likely files**
- `resources/js/webtrees.js`
- `resources/js/webtrees/autocomplete.js` (new)
- `resources/js/webtrees/map.js` (new)
- `resources/js/webtrees/init.js` (new)

**Changes**
- Move feature code into modules.
- Preserve current initialization timing and behavior.

**Commits**
- `refactor(js): extract feature modules from webtrees bundle`
- `refactor(js): preserve initialization behavior via adapter`

**Verify**
- `npm run build`

**GUI checks**
- On `/tree/{tree}/branches?surname=SMITH`, confirm surname autocomplete suggestions appear and selection works.
- On `/tree/{tree}/place-list` (or `/tree/{tree}/pedigree-map-4/{xref}`), confirm map initialization, markers, and controls still load correctly.
- Reload `/tree/{tree}/branches` and confirm startup initialization runs exactly once (no duplicate bindings).

### PR 5 - Tree View Modernization with Compatibility

**Purpose**
- Export tree view as module while preserving global constructor.

**Likely files**
- `resources/js/treeview.js`
- Optional: `resources/js/treeview/TreeViewHandler.js` (new)
- `app/Module/InteractiveTree/TreeView.php` (only if tiny call-site change needed)

**Changes**
- Convert to `export class TreeViewHandler`.
- Keep `window.TreeViewHandler = TreeViewHandler` compatibility bridge.

**Commits**
- `refactor(js): export TreeViewHandler as module class`
- `refactor(js): keep global TreeViewHandler compatibility`

**Verify**
- `npm run build`

**GUI checks**
- Open `/module/tree/Chart/{tree}?xref={xref}` and confirm initial render matches baseline.
- Expand/collapse branches, navigate between individuals, and confirm event handling remains responsive.
- In browser dev tools, verify `window.TreeViewHandler` is still defined and callable by existing inline/template code.

### PR 6 - Statistics Modernization with Compatibility

**Purpose**
- Module-based statistics implementation with legacy singleton preserved.

**Likely files**
- `resources/js/statistics.js`
- Optional: `resources/js/statistics/Statistics.js` (new)
- `resources/views/statistics/other/charts/pie.phtml`
- `resources/views/statistics/other/charts/column.phtml`
- `resources/views/statistics/other/charts/geo.phtml`

**Changes**
- Export class/functions from modules.
- Keep `window.statistics` bridge for existing chart pages.

**Commits**
- `refactor(js): modularize statistics implementation`
- `refactor(js): preserve global statistics bridge`

**Verify**
- `npm run build`

**GUI checks**
- Open `/module/statistics-chart/Chart/{tree}` and then the `Other` tab; confirm pie, column, and geo charts render without errors.
- Interact with chart controls/tooltips/filters where present and confirm behavior remains unchanged.
- In browser dev tools, verify `window.statistics` is still present for legacy chart page callers.

### PR 7 - Reduce Internal Global Coupling

**Purpose**
- Replace implicit globals internally while keeping external compatibility.

**Likely files**
- `resources/js/vendor.js`
- `resources/js/webtrees/*.js`
- `resources/js/treeview*.js`
- `resources/js/statistics*.js`

**Changes**
- Prefer explicit imports inside modules.
- Retain adapter-based globals for external/template callers.

**Commit**
- `refactor(js): replace internal global assumptions with imports`

**Verify**
- `npm run build`

**GUI checks**
- Re-run smoke tests in one pass on `/tree/{tree}/branches`, `/module/tree/Chart/{tree}?xref={xref}`, and `/module/statistics-chart/Chart/{tree}`.
- Confirm no regressions from import refactors by checking for missing symbol or undefined global errors.
- Validate `/admin` plus one user page to ensure cross-bundle compatibility is still intact.

### PR 8 - Template Migration Pilot (optional)

**Purpose**
- Migrate one low-risk inline-script page to explicit module initializer calls.

**Likely files**
- One selected view under `resources/views/`
- Related JS initializer module

**Changes**
- Reduce inline imperative script usage.
- Initialize behavior via module entry in a controlled pilot.

**Commit**
- `refactor(js): migrate <page> inline script to module initializer`

**Verify**
- `npm run build`

**GUI checks**
- On the selected pilot page (recommended: `/tree/{tree}/branches`), confirm migrated behavior still runs end-to-end without relying on inline script timing.
- Refresh and navigate back to the page to confirm initialization is stable and idempotent.
- Confirm no duplicated event handlers or double-render side effects are introduced.

### PR 9 - Docs and Workflow Alignment

**Purpose**
- Align contributor guidance with actual frontend workflow.

**Likely files**
- `README.md`
- `CONTRIBUTING.md`

**Changes**
- Ensure JS command names and expected workflow are accurate.
- Note compatibility layer and migration direction.

**Commit**
- `docs(js): align frontend build and module workflow guidance`

**Verify**
- `npm run build`

**GUI checks**
- Follow updated documentation steps from a clean terminal session and confirm they produce the expected frontend artifacts.
- Run a final manual smoke test on `/tree/{tree}/branches`, `/admin`, `/module/tree/Chart/{tree}?xref={xref}`, and `/module/statistics-chart/Chart/{tree}`.
- Confirm documented compatibility globals match actual runtime behavior during this transition phase.

## Release Safety Rules

- Keep script includes unchanged in:
  - `resources/views/layouts/default.phtml`
  - `resources/views/layouts/administration.phtml`
- Preserve global API names/signatures until the final cleanup phase.
- Prefer narrow PR scope (one subsystem per PR).

## Suggested Execution Order

1. PR 1
2. PR 2
3. PR 3
4. PR 5
5. PR 6
6. PR 4
7. PR 7
8. PR 8 (optional)
9. PR 9

## Done Criteria

- Internal JS code is modularized with clear boundaries.
- External behavior remains stable throughout migration.
- Key UI paths are verified after each PR.
- Contributor documentation reflects the real workflow.
