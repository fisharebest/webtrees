<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\I18N;

/**
 * CERTAINTY_ASSESSMENT := {Size=1:1}
 * [0|1|2|3]
 * The QUAY tag's value conveys the submitter's quantitative evaluation of the
 * credibility of a piece of information, based upon its supporting evidence.
 * Some systems use this feature to rank multiple conflicting opinions for
 * display of most likely information first. It is not intended to eliminate
 * the receiver's need to evaluate the evidence for themselves.
 * 0 = Unreliable evidence or estimated data
 * 1 = Questionable reliability of evidence (interviews, census, oral
 *     genealogies, or potential for bias for example, an autobiography)
 * 2 = Secondary evidence, data officially recorded sometime after event
 * 3 = Direct and primary evidence used, or by dominance of the evidence
 */
class CertaintyAssessment extends AbstractElement
{
    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        return [
            ''  => '',
            '0' => /* I18N: Quality of source information - GEDCOM tag “QUAY 0” */ I18N::translate('unreliable evidence'),
            '1' => /* I18N: Quality of source information - GEDCOM tag “QUAY 1” */ I18N::translate('questionable evidence'),
            '2' => /* I18N: Quality of source information - GEDCOM tag “QUAY 2” */ I18N::translate('secondary evidence'),
            '3' => /* I18N: Quality of source information - GEDCOM tag “QUAY 3” */ I18N::translate('primary evidence'),
        ];
    }
}
