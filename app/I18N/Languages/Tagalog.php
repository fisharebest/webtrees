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

namespace Fisharebest\Webtrees\I18N\Languages;

use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Tagalog extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsTagalog;

    protected const string    ENDONYM            = 'Tagalog';
    protected const PaperSize PAPER_SIZE         = PaperSize::USLetter;
    protected const string    LANGUAGE_TAG       = 'tl';
    protected const string    LOCALE_CODE        = 'tl_PH@collation=phonebook';
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    LIST_SEPARATOR_AND = ' at ';
    protected const string    LIST_SEPARATOR_OR  = ' o ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Enero',
        'Pebrero',
        'Marso',
        'Abril',
        'Mayo',
        'Hunyo',
        'Hulyo',
        'Agosto',
        'Setyembre',
        'Oktubre',
        'Nobyembre',
        'Disyembre',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tishrei',
        'Heshvan',
        'Kislev',
        'Tevet',
        'Shevat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nissan',
        'Iyar',
        'Sivan',
        'Tamuz',
        'Av',
        'Elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Vendémiaire',
        'Brumaire',
        'Frimaire',
        'Nivôse',
        'Pluviôse',
        'Ventôse',
        'Germinal',
        'Floréal',
        'Prairial',
        'Messidor',
        'Thermidor',
        'Fructidor',
        'jours complémentaires',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Muharram',
        'Safar',
        'Rabi’ al-awwal',
        'Rabi’ al-thani',
        'Jumada al-awwal',
        'Jumada al-thani',
        'Rajab',
        'Sha’aban',
        'Ramadan',
        'Shawwal',
        'Dhu al-Qi’dah',
        'Dhu al-Hijjah',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Farvardin',
        'Ordibehesht',
        'Khordad',
        'Tir',
        'Mordad',
        'Shahrivar',
        'Mehr',
        'Aban',
        'Azar',
        'Dey',
        'Bahman',
        'Esfand',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

        /** @return array{string, string} */
    private function tl(string $s): array
    {
        return [$s, '%s ng ' . $s];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->tl('inang-ampon'))->adoptive()->mother(),
            Relationship::fixed(...$this->tl('amang-ampon'))->adoptive()->father(),
            Relationship::fixed(...$this->tl('magulang na ampon'))->adoptive()->parent(),
            Relationship::fixed(...$this->tl('anak na ampon na babae'))->adopted()->daughter(),
            Relationship::fixed(...$this->tl('anak na ampon na lalaki'))->adopted()->son(),
            Relationship::fixed(...$this->tl('anak na ampon'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->tl('inang-kalinga'))->fostering()->mother(),
            Relationship::fixed(...$this->tl('amang-kalinga'))->fostering()->father(),
            Relationship::fixed(...$this->tl('magulang na kalinga'))->fostering()->parent(),
            Relationship::fixed(...$this->tl('anak na kalinga na babae'))->fostered()->daughter(),
            Relationship::fixed(...$this->tl('anak na kalinga na lalaki'))->fostered()->son(),
            Relationship::fixed(...$this->tl('anak na kalinga'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->tl('nanay'))->mother(),
            Relationship::fixed(...$this->tl('tatay'))->father(),
            Relationship::fixed(...$this->tl('magulang'))->parent(),
            // Children
            Relationship::fixed(...$this->tl('anak na babae'))->daughter(),
            Relationship::fixed(...$this->tl('anak na lalaki'))->son(),
            Relationship::fixed(...$this->tl('anak'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$this->tl('kambal na babae'))->multiple()->sister(),
            Relationship::fixed(...$this->tl('kambal na lalaki'))->multiple()->brother(),
            Relationship::fixed(...$this->tl('kambal'))->multiple()->sibling(),
            Relationship::fixed(...$this->tl('ate'))->older()->sister(),
            Relationship::fixed(...$this->tl('kuya'))->older()->brother(),
            Relationship::fixed(...$this->tl('nakababatang kapatid na babae'))->younger()->sister(),
            Relationship::fixed(...$this->tl('nakababatang kapatid na lalaki'))->younger()->brother(),
            Relationship::fixed(...$this->tl('kapatid na babae'))->sister(),
            Relationship::fixed(...$this->tl('kapatid na lalaki'))->brother(),
            Relationship::fixed(...$this->tl('kapatid'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$this->tl('kapatid na babae sa ama'))->father()->daughter(),
            Relationship::fixed(...$this->tl('kapatid na lalaki sa ama'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$this->tl('kapatid na babae sa ina'))->mother()->daughter(),
            Relationship::fixed(...$this->tl('kapatid na lalaki sa ina'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$this->tl('kapatid na babae sa magulang'))->parent()->daughter(),
            Relationship::fixed(...$this->tl('kapatid na lalaki sa magulang'))->parent()->son(),
            Relationship::fixed(...$this->tl('kapatid sa magulang'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->tl('madrasta'))->parent()->wife(),
            Relationship::fixed(...$this->tl('padrasto'))->parent()->husband(),
            Relationship::fixed(...$this->tl('anak na babae ng asawa'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->tl('anak na lalaki ng asawa'))->married()->spouse()->son(),
            Relationship::fixed(...$this->tl('anak ng asawa'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->tl('dating asawa'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->tl('dating asawa'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->tl('dating asawa'))->divorced()->partner(),
            Relationship::fixed(...$this->tl('nobya'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->tl('nobyo'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->tl('asawa'))->wife(),
            Relationship::fixed(...$this->tl('asawa'))->husband(),
            Relationship::fixed(...$this->tl('asawa'))->spouse(),
            Relationship::fixed(...$this->tl('katuwang'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$this->tl('biyenang babae'))->husband()->mother(),
            Relationship::fixed(...$this->tl('biyenang lalaki'))->husband()->father(),
            Relationship::fixed(...$this->tl('biyenang babae'))->wife()->mother(),
            Relationship::fixed(...$this->tl('biyenang lalaki'))->wife()->father(),
            Relationship::fixed(...$this->tl('biyenan'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$this->tl('manugang na babae'))->child()->wife(),
            Relationship::fixed(...$this->tl('manugang na lalaki'))->child()->husband(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$this->tl('hipag'))->husband()->sister(),
            Relationship::fixed(...$this->tl('bayaw'))->husband()->brother(),
            Relationship::fixed(...$this->tl('hipag'))->wife()->sister(),
            Relationship::fixed(...$this->tl('bayaw'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$this->tl('hipag'))->brother()->wife(),
            Relationship::fixed(...$this->tl('bayaw'))->sister()->husband(),
            // Grandparents
            Relationship::fixed(...$this->tl('lola'))->parent()->mother(),
            Relationship::fixed(...$this->tl('lolo'))->parent()->father(),
            Relationship::fixed(...$this->tl('lolo/lola'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->tl('apo na babae'))->child()->daughter(),
            Relationship::fixed(...$this->tl('apo na lalaki'))->child()->son(),
            Relationship::fixed(...$this->tl('apo'))->child()->child(),
            // Aunts/Uncles
            Relationship::fixed(...$this->tl('tita'))->parent()->sister(),
            Relationship::fixed(...$this->tl('tito'))->parent()->brother(),
            Relationship::fixed(...$this->tl('tito/tita'))->parent()->sibling(),
            // Nieces/Nephews
            Relationship::fixed(...$this->tl('pamangkin na babae'))->sibling()->daughter(),
            Relationship::fixed(...$this->tl('pamangkin na lalaki'))->sibling()->son(),
            Relationship::fixed(...$this->tl('pamangkin'))->sibling()->child(),
            // Cousins — flat (one term for all degrees)
            Relationship::fixed(...$this->tl('pinsan na babae'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->tl('pinsan na lalaki'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->tl('pinsan'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->tl($n > 2 ? 'tita sa ika-' . $n . ' salinlahi' : 'tita'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->tl($n > 2 ? 'tito sa ika-' . $n . ' salinlahi' : 'tito'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->tl($n > 2 ? 'pamangkin na babae sa ika-' . $n . ' salinlahi' : 'pamangkin na babae'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->tl($n > 2 ? 'pamangkin na lalaki sa ika-' . $n . ' salinlahi' : 'pamangkin na lalaki'))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->tl($n > 2 ? 'pamangkin sa ika-' . $n . ' salinlahi' : 'pamangkin'))->sibling()->descendant(),
            // Dynamic: ancestors — lola/lolo sa tuhod (great-grand), then ordinals
            Relationship::dynamic(fn (int $n) => $this->tl(match (true) {
                $n === 3 => 'lola sa tuhod',
                default  => 'lola sa ika-' . $n . ' salinlahi',
            }))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->tl(match (true) {
                $n === 3 => 'lolo sa tuhod',
                default  => 'lolo sa ika-' . $n . ' salinlahi',
            }))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->tl(match (true) {
                $n === 3 => 'lolo/lola sa tuhod',
                default  => 'ninuno sa ika-' . $n . ' salinlahi',
            }))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(fn (int $n) => $this->tl(match (true) {
                $n === 3 => 'apo sa tuhod na babae',
                default  => 'apo na babae sa ika-' . $n . ' salinlahi',
            }))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->tl(match (true) {
                $n === 3 => 'apo sa tuhod na lalaki',
                default  => 'apo na lalaki sa ika-' . $n . ' salinlahi',
            }))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->tl(match (true) {
                $n === 3 => 'apo sa tuhod',
                default  => 'apo sa ika-' . $n . ' salinlahi',
            }))->descendant(),
        ];
    }
}
