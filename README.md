![Latest version](https://img.shields.io/github/v/release/fisharebest/webtrees?sort=semver)
![Licence](https://img.shields.io/github/license/fisharebest/webtrees)
[![Translation status](translate.webtrees.net/widget/webtrees/svg-badge.svg)](https://translate.webtrees.net/projects/webtrees/)

# webtrees — online collaborative genealogy

**webtrees** is a web application for recording, viewing and sharing your family history.
It imports and exports standard GEDCOM files, making it compatible with every major
desktop genealogy application.

You host it on your own web server, so you stay in control of your data. Multiple
family members can collaborate on the same tree at the same time, and you decide
who can see what through fine-grained privacy rules.

Key features:

* Full editing of individuals, families, sources, media and other GEDCOM records
* Interactive charts — pedigree, fan, descendants, relationships
* Privacy controls per record, per user, per tree
* Multi-language interface (70+ translations)
* Theming support with several built-in themes
* Module system for extending functionality
* Multiple family trees in one installation

For user documentation, screenshots and demos visit **<https://webtrees.net/>**.

This software is licensed under the GNU General Public License v3 or later.
See [LICENSE.md](LICENSE.md) for the full text.

---

## Getting started

### Download

Download the latest stable release from
[github.com/fisharebest/webtrees/releases/latest](https://github.com/fisharebest/webtrees/releases/latest).

### Server requirements

| Component | Requirement |
|-----------|-------------|
| PHP | 8.3 – 8.6 |
| Database | MySQL (recommended), PostgreSQL, SQLite, or SQL Server |
| Web server | Apache, NGINX, IIS or similar (URL rewriting needed for pretty URLs) |
| Disk space | ~100 MB for application files, plus space for media and database |

Required PHP extensions: `ctype`, `curl`, `gd`, `iconv`, `intl`, `json`, `mbstring`,
`pcre`, `pdo`, `session`, `xml`.

Recommended PHP extensions: `exif` (auto-rotate images), `zip` (compress downloads
and use the upgrade wizard), `zlib` (compress HTTP responses).

PHP memory and execution time should be scaled to your tree size:

| Tree size | Memory | Max execution time |
|-----------|--------|--------------------|
| Small (500 individuals) | 16–32 MB | 10–20 s |
| Medium (5 000 individuals) | 32–64 MB | 20–40 s |
| Large (50 000 individuals) | 64–128 MB | 40–80 s |

### Installation

1. Upload and extract the release archive to an empty folder on your web server.
2. Navigate to the URL in your browser — the setup wizard starts automatically.
3. Create your first family tree and optionally import a GEDCOM file.

Full installation and upgrade guides are available at <https://webtrees.net/>.

---

## Technical architecture

### Project structure

```
app/                        Application source (PSR-4 root: Fisharebest\Webtrees\)
├── Cli/Commands/           CLI commands (extend AbstractCommand)
├── Contracts/              Interfaces (*Interface suffix)
├── Enums/                  PHP enumerations
├── Factories/              Object creation (Registry pattern)
├── Http/
│   ├── RequestHandlers/    HTTP controllers (one class per route action)
│   ├── Middleware/         HTTP controllers (one class per route action)
│   └── Routes/             Route definitions
├── Module/                 Modules (charts, reports, sidebars, tabs, themes…)
├── Services/               Domain services
└── …                       Domain classes (Individual, Family, Tree, etc.)

resources/
├── css/                    Stylesheets (compiled by webpack)
├── js/                     JavaScript (compiled by webpack)
├── lang/                   Language files (*.po, compiled into *.php for release)
└── views/                  View templates (*.phtml)

tests/
├── Unit/                   Unit tests
└── Feature/                Feature/integration tests

public/                     Web-accessible assets (compiled CSS/JS, images, fonts)
data/                       Runtime data (config, GEDCOM files, cache — not in VCS)
modules_v4/                 Third-party module directory
```

### Namespace and autoloading

| Namespace | Directory | Standard |
|-----------|-----------|----------|
| `Fisharebest\Webtrees\` | `app/` | PSR-4 |
| `Fisharebest\Webtrees\Tests\` | `tests/` | PSR-4 |

Global helper functions are in `app/Helpers/functions.php` (loaded via composer
`autoload.files`).

### Coding standards

webtrees follows [PSR-12](https://www.php-fig.org/psr/psr-12) with these additional rules:

* Abstract classes are named `Abstract*`.
* Interfaces are named `*Interface`.
* Traits are named `*Trait`.
* Local variables and parameters use `$snake_case`.
* Nullable types are written `Type|null`, not `?Type`.
* PHP function/constant calls never use named arguments.
* Variable names are full English words (`$variables`, not `$vars`).
* Comments and identifiers use US English (`$color`, not `$colour`).
* All PHP functions and constants are imported with `use`; unused imports are removed.
* No two consecutive blank lines.
* Unit test method names are `camelCase`, not `snake_case`.

For javascript, we use [semistandard](https://github.com/standard/semistandard).

PSR standards applied:

| PSR | Topic |
|-----|-------|
| PSR-4 | Autoloading |
| PSR-6 | Caching |
| PSR-7 | HTTP messages |
| PSR-11 | Dependency injection container |
| PSR-12 | Coding style |
| PSR-15 | HTTP request handlers and middleware |
| PSR-17 | HTTP factories |
| PSR-18 | HTTP client |

### Dependencies

All dependencies are pinned to exact version numbers (no ranges).

**Main PHP dependencies** (via Composer):

* `illuminate/database` 
* `symfony/cache`, `symfony/console`, `symfony/mailer`, `symfony/http-client`
* `league/flysystem`
* `league/commonmark`
* `aura/router`

**Main JavaScript dependencies** (via npm / webpack):

* Bootstrap 5
* Font Awesome
* LeafletJs
* DataTables
* Chart.js, Tom Select, Sortable.js
* jQuery 4 (legacy shims; being phased out)

### Building from source

Prerequisites: PHP 8.3+, [Composer](https://getcomposer.org/),
[Node.js](https://nodejs.org/) with npm.

```bash
# Install PHP dependencies
composer install

# Install JS dependencies and compile assets
npm install
npm run build

# Create a .ZIP file containing a release
composer build
```

### Quality and CI

Run the full local CI suite:

```bash
composer ci
```

This executes, in order:

| Tool | Purpose | Config file |
|------|---------|-------------|
| PHP_CodeSniffer | PSR-12 style enforcement | `phpcs.xml.dist` |
| PHPUnit | Unit and feature tests | `phpunit.xml.dist` |
| PHPStan (level 6) | Static analysis | `phpstan.neon.dist` |

Individual commands:

```bash
# Coding style (fix automatically)
vendor/bin/phpcbf

# Tests with coverage
vendor/bin/phpunit

# Static analysis
vendor/bin/phpstan
```

### Testing

Tests live in `tests/Unit/` and `tests/Feature/`. Run a subset with:

```bash
vendor/bin/phpunit --filter=testMethodName
vendor/bin/phpunit tests/Feature/SomeTest.php
```

### HTTP architecture

The application uses PSR-15 middleware and request handlers:

1. `index.php` boots the application and dispatches the request.
2. Routes are defined declaratively in `app/Http/Routes/`.
3. Each route maps to a request handler in `app/Http/RequestHandlers/`.
4. Middleware provides cross-cutting concerns (authentication, CSRF, etc.).

### Module system

Most user-visible features are implemented as modules in `app/Module/`. Modules
implement one or more interfaces (e.g. `ModuleChartInterface`,
`ModuleReportInterface`, `ModuleThemeInterface`) and are auto-discovered by the
`ModuleService`.

Third-party modules can be placed in `modules_v4/`.

### Database

webtrees uses Laravel’s Illuminate Database (query builder and schema builder)
without the full Laravel framework. Supported drivers:

* MySQL / MariaDB
* SQLite
* PostgreSQL
* SQL Server

Schema migrations are in `app/Schema/`. The current schema version is tracked in
`app/Webtrees.php`.

Table names use a configurable prefix so multiple instances can share a database.
