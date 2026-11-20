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

use Fisharebest\Webtrees\Age;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

use function date;
use function strtoupper;

/**
 * Class ExpressionLanguageProvider - support functions in report expressions.
 */
final class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function __construct(
        private readonly Tree|null $tree = null,
    ) {
    }

    /**
     * @return array<ExpressionFunction>
     */
    public function getFunctions(): array
    {
        return [
            ExpressionFunction::fromPhp('stristr'),
            $this->ageYearsFunction(),
            $this->maxAliveAgeFunction(),
        ];
    }

    /**
     * age_years(gedcom_date) - the number of complete years between a GEDCOM date and today.
     * Returns -1 if the date cannot be parsed.
     */
    private function ageYearsFunction(): ExpressionFunction
    {
        return new ExpressionFunction(
            'age_years',
            static fn (string $date): string => 'age_years(' . $date . ')',
            static function (array $variables, string $date): int {
                $today = new Date(strtoupper(date('d M Y')));

                return (new Age(new Date($date), $today))->ageYears();
            },
        );
    }

    /**
     * max_alive_age() - the tree's "Age at which to assume an individual is dead"
     * preference (MAX_ALIVE_AGE), the same value webtrees itself already uses
     * (see Individual::isDead()) to decide whether an individual without a death
     * date is still shown as living. Falls back to the tree's own default (120)
     * when no tree is in scope, e.g. plain arithmetic evaluation outside a report
     * condition.
     */
    private function maxAliveAgeFunction(): ExpressionFunction
    {
        return new ExpressionFunction(
            'max_alive_age',
            static fn (): string => 'max_alive_age()',
            fn (array $variables): int => (int) ($this->tree?->getPreference('MAX_ALIVE_AGE') ?? 120),
        );
    }
}
