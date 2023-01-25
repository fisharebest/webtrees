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

namespace Fisharebest\Webtrees\CustomTags;

use Fisharebest\Webtrees\Contracts\CustomTagInterface;
use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\NamePersonal;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by FamilyTreeMaker
 *
 * @see https://wiki-de.genealogy.net/GEDCOM/_Nutzerdef-Tag
 */
class FamilyTreeMaker implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'FamilyTreeMaker';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:CHIL:_FREL'              => new CustomElement(I18N::translate('Relationship to father')),
            'FAM:CHIL:_MREL'              => new CustomElement(I18N::translate('Relationship to mother')),
            'FAM:_DETS'                   => new CustomElement(I18N::translate('Death of one spouse')),
            'FAM:_FA1'                    => new CustomElement(I18N::translate('Fact 1')),
            'FAM:_FA10'                   => new CustomElement(I18N::translate('Fact 10')),
            'FAM:_FA11'                   => new CustomElement(I18N::translate('Fact 11')),
            'FAM:_FA12'                   => new CustomElement(I18N::translate('Fact 12')),
            'FAM:_FA13'                   => new CustomElement(I18N::translate('Fact 13')),
            'FAM:_FA2'                    => new CustomElement(I18N::translate('Fact 2')),
            'FAM:_FA3'                    => new CustomElement(I18N::translate('Fact 3')),
            'FAM:_FA4'                    => new CustomElement(I18N::translate('Fact 4')),
            'FAM:_FA5'                    => new CustomElement(I18N::translate('Fact 5')),
            'FAM:_FA6'                    => new CustomElement(I18N::translate('Fact 6')),
            'FAM:_FA7'                    => new CustomElement(I18N::translate('Fact 7')),
            'FAM:_FA8'                    => new CustomElement(I18N::translate('Fact 8')),
            'FAM:_FA9'                    => new CustomElement(I18N::translate('Fact 9')),
            'FAM:_MEND'                   => new CustomElement(I18N::translate('Marriage ending status')),
            'FAM:_MSTAT'                  => new CustomElement(I18N::translate('Marriage beginning status')),
            'FAM:_SEPR'                   => new CustomElement(I18N::translate('Separation')),
            'HEAD:_SCHEMA'                => new CustomElement(I18N::translate('Schema')),
            'HEAD:_SCHEMA:FAM'            => new CustomElement(I18N::translate('Family')),
            'HEAD:_SCHEMA:FAM:_FA*:LABL'  => new CustomElement(I18N::translate('Label')),
            'HEAD:_SCHEMA:FAM:_FA1'       => new CustomElement(I18N::translate('Fact 1')),
            'HEAD:_SCHEMA:FAM:_FA10'      => new CustomElement(I18N::translate('Fact 10')),
            'HEAD:_SCHEMA:FAM:_FA11'      => new CustomElement(I18N::translate('Fact 11')),
            'HEAD:_SCHEMA:FAM:_FA12'      => new CustomElement(I18N::translate('Fact 12')),
            'HEAD:_SCHEMA:FAM:_FA13'      => new CustomElement(I18N::translate('Fact 13')),
            'HEAD:_SCHEMA:FAM:_FA2'       => new CustomElement(I18N::translate('Fact 2')),
            'HEAD:_SCHEMA:FAM:_FA3'       => new CustomElement(I18N::translate('Fact 3')),
            'HEAD:_SCHEMA:FAM:_FA4'       => new CustomElement(I18N::translate('Fact 4')),
            'HEAD:_SCHEMA:FAM:_FA5'       => new CustomElement(I18N::translate('Fact 5')),
            'HEAD:_SCHEMA:FAM:_FA6'       => new CustomElement(I18N::translate('Fact 6')),
            'HEAD:_SCHEMA:FAM:_FA7'       => new CustomElement(I18N::translate('Fact 7')),
            'HEAD:_SCHEMA:FAM:_FA8'       => new CustomElement(I18N::translate('Fact 8')),
            'HEAD:_SCHEMA:FAM:_FA9'       => new CustomElement(I18N::translate('Fact 9')),
            'HEAD:_SCHEMA:FAM:_M*:LABL'   => new CustomElement(I18N::translate('Label')),
            'HEAD:_SCHEMA:FAM:_MEND'      => new CustomElement(I18N::translate('Marriage ending status')),
            'HEAD:_SCHEMA:FAM:_MSTAT'     => new CustomElement(I18N::translate('Marriage beginning status')),
            'HEAD:_SCHEMA:INDI'           => new CustomElement(I18N::translate('Individual')),
            'HEAD:_SCHEMA:INDI:_FA*:LABL' => new CustomElement(I18N::translate('Label')),
            'HEAD:_SCHEMA:INDI:_FA1'      => new CustomElement(I18N::translate('Fact 1')),
            'HEAD:_SCHEMA:INDI:_FA10'     => new CustomElement(I18N::translate('Fact 10')),
            'HEAD:_SCHEMA:INDI:_FA11'     => new CustomElement(I18N::translate('Fact 11')),
            'HEAD:_SCHEMA:INDI:_FA12'     => new CustomElement(I18N::translate('Fact 12')),
            'HEAD:_SCHEMA:INDI:_FA13'     => new CustomElement(I18N::translate('Fact 13')),
            'HEAD:_SCHEMA:INDI:_FA2'      => new CustomElement(I18N::translate('Fact 2')),
            'HEAD:_SCHEMA:INDI:_FA3'      => new CustomElement(I18N::translate('Fact 3')),
            'HEAD:_SCHEMA:INDI:_FA4'      => new CustomElement(I18N::translate('Fact 4')),
            'HEAD:_SCHEMA:INDI:_FA5'      => new CustomElement(I18N::translate('Fact 5')),
            'HEAD:_SCHEMA:INDI:_FA6'      => new CustomElement(I18N::translate('Fact 6')),
            'HEAD:_SCHEMA:INDI:_FA7'      => new CustomElement(I18N::translate('Fact 7')),
            'HEAD:_SCHEMA:INDI:_FA8'      => new CustomElement(I18N::translate('Fact 8')),
            'HEAD:_SCHEMA:INDI:_FA9'      => new CustomElement(I18N::translate('Fact 9')),
            'HEAD:_SCHEMA:INDI:_FREL'     => new CustomElement(I18N::translate('Relationship to father')),
            'HEAD:_SCHEMA:INDI:_M*:LABL'  => new CustomElement(I18N::translate('Label')),
            'HEAD:_SCHEMA:INDI:_MREL'     => new CustomElement(I18N::translate('Relationship to mother')),
            'INDI:*:SOUR:_APID'           => /* I18N: GEDCOM tag _APID */ new CustomElement(I18N::translate('Ancestry.com source identifier')),
            'INDI:*:SOUR:_LINK'           => new CustomElement(I18N::translate('External link')),
            'INDI:NAME:_AKA'              => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:NAME:_MARNM'            => new NamePersonal(I18N::translate('Married name'), []),
            'INDI:_CIRC'                  => new CustomElement(I18N::translate('Circumcision')),
            'INDI:_DCAUSE'                => new CustomElement(I18N::translate('Cause of death')),
            'INDI:_DEG'                   => new CustomElement(I18N::translate('Degree')),
            'INDI:_DNA'                   => new CustomElement(I18N::translate('DNA markers')),
            'INDI:_ELEC'                  => new CustomElement('Elected'),
            'INDI:_EMPLOY'                => new CustomElement('Employment'),
            'INDI:_EXCM'                  => new CustomElement('Excommunicated'),
            'INDI:_FA1'                   => new CustomElement(I18N::translate('Fact 1')),
            'INDI:_FA10'                  => new CustomElement(I18N::translate('Fact 10')),
            'INDI:_FA11'                  => new CustomElement(I18N::translate('Fact 11')),
            'INDI:_FA12'                  => new CustomElement(I18N::translate('Fact 12')),
            'INDI:_FA13'                  => new CustomElement(I18N::translate('Fact 13')),
            'INDI:_FA2'                   => new CustomElement(I18N::translate('Fact 2')),
            'INDI:_FA3'                   => new CustomElement(I18N::translate('Fact 3')),
            'INDI:_FA4'                   => new CustomElement(I18N::translate('Fact 4')),
            'INDI:_FA5'                   => new CustomElement(I18N::translate('Fact 5')),
            'INDI:_FA6'                   => new CustomElement(I18N::translate('Fact 6')),
            'INDI:_FA7'                   => new CustomElement(I18N::translate('Fact 7')),
            'INDI:_FA8'                   => new CustomElement(I18N::translate('Fact 8')),
            'INDI:_FA9'                   => new CustomElement(I18N::translate('Fact 9')),
            'INDI:_MDCL'                  => new CustomElement(I18N::translate('Medical')),
            'INDI:_MILT'                  => new CustomElement(I18N::translate('Military service')),
            'INDI:_MILTID'                => new CustomElement('Military ID number'),
            'INDI:_MISN'                  => new CustomElement('Mission'),
            'INDI:_NAMS'                  => new CustomElement(I18N::translate('Namesake')),
            'INDI:_UNKN'                  => new CustomElement(I18N::translate('Unknown')), // Special individual ID code for later file comparisons
        ];
    }
}
