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
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

use function date;
use function strtoupper;

/**
 * Class ExpressionLanguageProvider - support functions in report expressions.
 */
final class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @return array<ExpressionFunction>
     */
    public function getFunctions(): array
    {
        return [
            ExpressionFunction::fromPhp('stristr'),
            $this->ageYearsFunction(),
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
}
