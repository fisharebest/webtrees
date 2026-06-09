<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Report;

use DomainException;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

use function addcslashes;
use function addslashes;
use function explode;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function preg_replace_callback;
use function sprintf;
use function str_contains;
use function str_replace;
use function trim;

use const PREG_SET_ORDER;

/**
 * Resolves placeholder references in report XML attribute values and expressions.
 *
 * Handles:
 * - `$variable` substitution against the report variable table
 * - `@ID`, `@fact`, `@desc`, `@generation` resolution from GEDCOM context
 * - `I18N::number()`, `I18N::translate()`, `I18N::translateContext()` calls
 * - Arithmetic evaluation via ExpressionLanguage
 */
final class PlaceholderExpander
{
    public function __construct(
        private readonly VariableTable $variables,
        private readonly Tree $tree,
    ) {
    }

    /**
     * Resolve an attribute value that may contain `@`-placeholders and `$`-variables.
     *
     * Used by `<SetVar>` to expand the `value` attribute.  The result is an
     * unquoted string suitable for storing back in the variable table.
     *
     * Resolution order:
     * 1. Exact `@ID`, `@fact`, `@desc`, `@generation` tokens
     * 2. Other `@tag` references resolved from the current GEDCOM record
     * 3. `$variable` references replaced with their current values
     * 4. `I18N::number()` / `I18N::translate()` / `I18N::translateContext()`
     * 5. Arithmetic expressions (e.g. "3 + 4")
     * 6. Any remaining `@` reference clears the value (legacy behavior)
     */
    public function resolveSetVarValue(string $value, string $gedrec, string $fact, string $desc, int $generation): string
    {
        $match = [];

        // Resolve exact @-tokens against the current GEDCOM context
        if ($value === '@ID') {
            if (preg_match('/0 @(.+)@/', $gedrec, $match)) {
                $value = $match[1];
            }
        } elseif ($value === '@fact') {
            $value = $fact;
        } elseif ($value === '@desc') {
            $value = $desc;
        } elseif ($value === '@generation') {
            $value = (string) $generation;
        } elseif (preg_match("/@(\w+)/", $value, $match)) {
            $gmatch = [];
            if (preg_match("/\d $match[1] (.+)/", $gedrec, $gmatch)) {
                $value = str_replace('@', '', trim($gmatch[1]));
            }
        }

        // Resolve $variable references
        $count = preg_match_all("/\\$(\w+)/", $value, $match, PREG_SET_ORDER);
        $i     = 0;
        while ($i < $count) {
            $t     = $this->variables->get($match[$i][1]);
            $value = preg_replace('/\$' . $match[$i][1] . '/', $t, $value, 1);
            $i++;
        }

        // Apply I18N function calls
        $value = $this->applyI18nFunctions($value);

        // Evaluate arithmetic expressions
        $value = $this->evaluateArithmetic($value);

        // Clear any unresolved @-references (legacy behavior)
        if (str_contains($value, '@')) {
            $value = '';
        }

        return $value;
    }

    /**
     * Resolve a condition expression for use in `<if>` elements.
     *
     * Substitutes `$variable` references (quoted for the expression language),
     * replaces `@fact:` prefixes, resolves remaining `@`-tokens to quoted
     * strings, then evaluates the resulting boolean expression.
     *
     * @param string $condition  The raw condition from the `condition` attribute
     * @param string $gedrec    Current GEDCOM record text
     * @param string $fact      Current fact tag (e.g. "BIRT")
     * @param string $desc      Current description text
     * @param int    $generation Current generation number
     *
     * @return bool The result of evaluating the condition
     */
    public function evaluateCondition(string $condition, string $gedrec, string $fact, string $desc, int $generation): bool
    {
        $condition = $this->substituteVars($condition, true);
        $condition = str_replace([' LT ', ' GT ', '@fact:'], ['<', '>', $fact . ':'], $condition);

        // Resolve remaining @-tokens to quoted values
        $match = [];
        $count = preg_match_all("/@([\w:.]+)/", $condition, $match, PREG_SET_ORDER);
        $i     = 0;
        while ($i < $count) {
            $id    = $match[$i][1];
            $value = '""';
            if ($id === 'ID') {
                if (preg_match('/0 @(.+)@/', $gedrec, $match)) {
                    $value = "'" . $match[1] . "'";
                }
            } elseif ($id === 'fact') {
                $value = '"' . $fact . '"';
            } elseif ($id === 'desc') {
                $value = '"' . addslashes($desc) . '"';
            } elseif ($id === 'generation') {
                $value = '"' . $generation . '"';
            } else {
                $level = (int) explode(' ', trim($gedrec))[0];
                if ($level === 0) {
                    $level++;
                }
                $value = GedcomTextReader::getGedcomValue($id, $level, $gedrec, $this->tree);
                if (empty($value)) {
                    $level++;
                    $value = GedcomTextReader::getGedcomValue($id, $level, $gedrec, $this->tree);
                }
                $value = preg_replace('/^@(' . Gedcom::REGEX_XREF . ')@$/', '$1', $value);
                $value = '"' . addslashes($value) . '"';
            }
            $condition = str_replace("@$id", $value, $condition);
            $i++;
        }

        // Evaluate the boolean expression
        $expression_provider = new ExpressionLanguageProvider();
        $expression_cache    = new NullAdapter();
        $expression_language = new ExpressionLanguage($expression_cache, [$expression_provider]);

        return (bool) $expression_language->evaluate($condition);
    }

    /**
     * Replace `$variable` identifiers with their values from the variable table.
     *
     * @param string $expression An expression such as "$foo == 123"
     * @param bool   $quote      Whether to wrap replacement values in single quotes
     */
    public function substituteVars(string $expression, bool $quote,): string
    {
        return preg_replace_callback(
            '/\$(\w+)/',
            function (array $matches) use ($quote, $expression): string {
                if ($this->variables->has($matches[1])) {
                    if ($quote) {
                        return "'" . addcslashes($this->variables->get($matches[1]), "'") . "'";
                    }

                    return $this->variables->get($matches[1]);
                }

                throw new DomainException(sprintf(
                    'Undefined variable $%s in expression %s',
                    $matches[1],
                    $expression,
                ));
            },
            $expression
        );
    }

    /**
     * Apply `I18N::number()`, `I18N::translate()` and `I18N::translateContext()`
     * pseudo-function calls embedded in a string value.
     */
    public function applyI18nFunctions(string $value): string
    {
        if (preg_match('/^I18N::number\((.+)\)$/', $value, $match)) {
            return I18N::number((int) $match[1]);
        }

        if (preg_match('/^I18N::translate\(\'(.+)\'\)$/', $value, $match)) {
            return I18N::translate($match[1]);
        }

        if (preg_match('/^I18N::translateContext\(\'(.+)\', *\'(.+)\'\)$/', $value, $match)) {
            return I18N::translateContext($match[1], $match[2]);
        }

        return $value;
    }

    /**
     * Evaluate simple arithmetic expressions (e.g. "5 + 3", "10 * 2").
     *
     * Only triggers when the value contains a recognizable arithmetic pattern.
     */
    public function evaluateArithmetic(string $value): string
    {
        if (preg_match("/(\d+)\s*([-+*\/])\s*(\d+)/", $value, $match)) {
            $expression_provider = new ExpressionLanguageProvider();
            $expression_cache    = new NullAdapter();
            $expression_language = new ExpressionLanguage($expression_cache, [$expression_provider]);

            return (string) $expression_language->evaluate($value);
        }

        return $value;
    }
}
