<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Elements\AddressEmail;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\CustomEvent;
use Fisharebest\Webtrees\Elements\CustomFact;
use Fisharebest\Webtrees\Elements\DateValue;
use Fisharebest\Webtrees\Elements\NamePersonal;
use Fisharebest\Webtrees\Elements\PlaceName;
use Fisharebest\Webtrees\I18N;

/**
 * Custom GEDCOM tags created by Brother’s Keeper
 *
 * Class CustomTagsBrothersKeeper
 */
class CustomTagsBrothersKeeper extends AbstractModule implements ModuleConfigInterface, ModuleCustomTagsInterface
{
    use ModuleConfigTrait;
    use ModuleCustomTagsTrait;

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * @return array<string,ElementInterface>
     *
     * @see https://wiki-de.genealogy.net/GEDCOM/_Nutzerdef-Tag
     */
    public function customTags(): array
    {
        return [
            'FAM:*:_EVN'       => new CustomElement('Event number'),
            'FAM:CHIL:_FREL'   => new CustomElement('Relationship to father'),
            'FAM:CHIL:_MREL'   => new CustomElement('Relationship to mother'),
            'FAM:_COML'        => new CustomEvent(I18N::translate('Common law marriage')),
            'FAM:_MARI'        => new CustomEvent(I18N::translate('Marriage intention')),
            'FAM:_MBON'        => new CustomEvent(I18N::translate('Marriage bond')),
            'FAM:_NMR'         => new CustomEvent(I18N::translate('Not married'), ['NOTE' => '0:M', 'SOUR' => '0:M']),
            'FAM:_PRMN'        => new CustomElement(I18N::translate('Permanent number')),
            'FAM:_SEPR'        => new CustomEvent(I18N::translate('Separated')),
            'FAM:_TODO'        => new CustomElement(I18N::translate('Research task')),
            'INDI:*:_EVN'      => new CustomElement('Event number'),
            'INDI:NAME:_ADPN'  => new NamePersonal(I18N::translate('Adopted name'), []),
            'INDI:NAME:_AKAN'  => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:NAME:_BIRN'  => new NamePersonal(I18N::translate('Birth name'), []),
            'INDI:NAME:_CALL'  => new NamePersonal('Called name', []),
            'INDI:NAME:_CENN'  => new NamePersonal('Census name', []),
            'INDI:NAME:_CURN'  => new NamePersonal('Current name', []),
            'INDI:NAME:_FARN'  => new NamePersonal(I18N::translate('Estate name'), []),
            'INDI:NAME:_FKAN'  => new NamePersonal('Formal name', []),
            'INDI:NAME:_FRKA'  => new NamePersonal('Formerly known as', []),
            'INDI:NAME:_GERN'  => new NamePersonal('German name', []),
            'INDI:NAME:_HEBN'  => new NamePersonal(I18N::translate('Hebrew name'), []),
            'INDI:NAME:_HNM'   => new NamePersonal(I18N::translate('Hebrew name'), []),
            'INDI:NAME:_INDG'  => new NamePersonal('Indigenous name', []),
            'INDI:NAME:_INDN'  => new NamePersonal('Indian name', []),
            'INDI:NAME:_LNCH'  => new NamePersonal('Legal name change', []),
            'INDI:NAME:_MARN'  => new NamePersonal('Married name', []),
            'INDI:NAME:_MARNM' => new NamePersonal('Married name', []),
            'INDI:NAME:_OTHN'  => new NamePersonal('Other name', []),
            'INDI:NAME:_RELN'  => new NamePersonal('Religious name', []),
            'INDI:NAME:_SHON'  => new NamePersonal('Short name', []),
            'INDI:NAME:_SLDN'  => new NamePersonal('Soldier name', []),
            'INDI:_ADPF'       => new CustomElement(I18N::translate('Adopted by father')),
            'INDI:_ADPM'       => new CustomElement(I18N::translate('Adopted by mother')),
            'INDI:_BRTM'       => new CustomEvent(I18N::translate('Brit milah')),
            'INDI:_BRTM:DATE'  => new DateValue(I18N::translate('Date of brit milah')),
            'INDI:_BRTM:PLAC'  => new PlaceName(I18N::translate('Place of brit milah')),
            'INDI:_EMAIL'      => new AddressEmail(I18N::translate('Email address')),
            'INDI:_EYEC'       => new CustomFact(I18N::translate('Eye color')),
            'INDI:_FRNL'       => new CustomElement(I18N::translate('Funeral')),
            'INDI:_HAIR'       => new CustomFact(I18N::translate('Hair color')),
            'INDI:_HEIG'       => new CustomFact(I18N::translate('Height')),
            'INDI:_INTE'       => new CustomElement(I18N::translate('Interment')),
            'INDI:_MEDC'       => new CustomFact(I18N::translate('Medical')),
            'INDI:_MILT'       => new CustomElement(I18N::translate('Military service')),
            'INDI:_NLIV'       => new CustomFact(I18N::translate('Not living')),
            'INDI:_NMAR'       => new CustomEvent(I18N::translate('Never married'), ['NOTE' => '0:M', 'SOUR' => '0:M']),
            'INDI:_PRMN'       => new CustomElement(I18N::translate('Permanent number')),
            'INDI:_TODO'       => new CustomElement(I18N::translate('Research task')),
            'INDI:_WEIG'       => new CustomFact(I18N::translate('Weight')),
            'INDI:_YART'       => new CustomEvent(I18N::translate('Yahrzeit')),
            // 1 XXXX
            // 2 _EVN ##
            // 1 ASSO @Xnnn@
            // 2 RELA Witness at event _EVN ##
        ];
    }

    /**
     * The application for which we are supporting custom tags.
     *
     * @return string
     */
    public function customTagApplication(): string
    {
        return 'Brother’s Keeper™';
    }
}
