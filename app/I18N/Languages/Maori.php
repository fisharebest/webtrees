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

use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Maori extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsPluralForMoreThanOne;

    protected const string    ENDONYM            = 'Māori';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'mi';
    protected const string    LOCALE_CODE        = 'mi_NZ@collation=phonebook';
    protected const string    LIST_SEPARATOR_AND = ' me ';
    protected const string    LIST_SEPARATOR_OR  = ' rānei ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Hānuere',
        'Pēpuere',
        'Māehe',
        'Āperira',
        'Mei',
        'Hune',
        'Hūrae',
        'Ākuhata',
        'Hepetema',
        'Oketopa',
        'Noema',
        'Tīhema',
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

    /**
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function mi(string $s): array
    {
        return [$s, $s . ' o %s'];
    }

    private function ordinal(int $n): string
    {
        return match ($n) {
            2       => 'tuarua',
            3       => 'tuatoru',
            4       => 'tuawhā',
            5       => 'tuarima',
            6       => 'tuaono',
            7       => 'tuawhitu',
            default => 'tuā-' . $n,
        };
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted/fostered (whāngai — the Māori concept encompasses both)
            Relationship::fixed(...$this->mi('whaea whāngai'))->adoptive()->mother(),
            Relationship::fixed(...$this->mi('matua whāngai'))->adoptive()->father(),
            Relationship::fixed(...$this->mi('matua whāngai'))->adoptive()->parent(),
            Relationship::fixed(...$this->mi('tamāhine whāngai'))->adopted()->daughter(),
            Relationship::fixed(...$this->mi('tama whāngai'))->adopted()->son(),
            Relationship::fixed(...$this->mi('tamaiti whāngai'))->adopted()->child(),
            Relationship::fixed(...$this->mi('whaea whāngai'))->fostering()->mother(),
            Relationship::fixed(...$this->mi('matua whāngai'))->fostering()->father(),
            Relationship::fixed(...$this->mi('matua whāngai'))->fostering()->parent(),
            Relationship::fixed(...$this->mi('tamāhine whāngai'))->fostered()->daughter(),
            Relationship::fixed(...$this->mi('tama whāngai'))->fostered()->son(),
            Relationship::fixed(...$this->mi('tamaiti whāngai'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->mi('whaea'))->mother(),
            Relationship::fixed(...$this->mi('matua'))->father(),
            Relationship::fixed(...$this->mi('matua'))->parent(),
            // Children
            Relationship::fixed(...$this->mi('tamāhine'))->daughter(),
            Relationship::fixed(...$this->mi('tama'))->son(),
            Relationship::fixed(...$this->mi('tamaiti'))->child(),
            // Siblings
            Relationship::fixed(...$this->mi('māhanga tuahine'))->multiple()->sister(),
            Relationship::fixed(...$this->mi('māhanga tungāne'))->multiple()->brother(),
            Relationship::fixed(...$this->mi('māhanga'))->multiple()->sibling(),
            Relationship::fixed(...$this->mi('tuakana wahine'))->older()->sister(),
            Relationship::fixed(...$this->mi('tuakana tāne'))->older()->brother(),
            Relationship::fixed(...$this->mi('tuakana'))->older()->sibling(),
            Relationship::fixed(...$this->mi('teina wahine'))->younger()->sister(),
            Relationship::fixed(...$this->mi('teina tāne'))->younger()->brother(),
            Relationship::fixed(...$this->mi('teina'))->younger()->sibling(),
            Relationship::fixed(...$this->mi('tuahine'))->sister(),
            Relationship::fixed(...$this->mi('tungāne'))->brother(),
            Relationship::fixed(...$this->mi('tuakana/teina'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->mi('tuahine'))->parent()->daughter(),
            Relationship::fixed(...$this->mi('tungāne'))->parent()->son(),
            Relationship::fixed(...$this->mi('tuakana/teina'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->mi('whaea kē'))->parent()->wife(),
            Relationship::fixed(...$this->mi('matua kē'))->parent()->husband(),
            Relationship::fixed(...$this->mi('matua kē'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->mi('tamāhine kē'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->mi('tama kē'))->married()->spouse()->son(),
            Relationship::fixed(...$this->mi('tamaiti kē'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->mi('wahine i wehea'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->mi('tāne i wehea'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->mi('hoa i wehea'))->divorced()->partner(),
            Relationship::fixed(...$this->mi('wahine oati'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->mi('tāne oati'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->mi('wahine'))->wife(),
            Relationship::fixed(...$this->mi('tāne'))->husband(),
            Relationship::fixed(...$this->mi('hoa rangatira'))->spouse(),
            Relationship::fixed(...$this->mi('hoa'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$this->mi('hungawai wahine'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->mi('hungawai tāne'))->married()->spouse()->father(),
            Relationship::fixed(...$this->mi('hungawai'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$this->mi('hunaonga wahine'))->child()->wife(),
            Relationship::fixed(...$this->mi('hunaonga tāne'))->child()->husband(),
            Relationship::fixed(...$this->mi('hunaonga'))->child()->married()->spouse(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$this->mi('taokete wahine'))->spouse()->sister(),
            Relationship::fixed(...$this->mi('taokete tāne'))->spouse()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$this->mi('taokete wahine'))->sibling()->wife(),
            Relationship::fixed(...$this->mi('taokete tāne'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->mi('kuia'))->parent()->mother(),
            Relationship::fixed(...$this->mi('koroua'))->parent()->father(),
            Relationship::fixed(...$this->mi('tipuna'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->mi('mokopuna wahine'))->child()->daughter(),
            Relationship::fixed(...$this->mi('mokopuna tāne'))->child()->son(),
            Relationship::fixed(...$this->mi('mokopuna'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->mi('whaea kēkē'))->parent()->sister(),
            Relationship::fixed(...$this->mi('matua kēkē'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->mi('iramutu wahine'))->sibling()->daughter(),
            Relationship::fixed(...$this->mi('iramutu tāne'))->sibling()->son(),
            Relationship::fixed(...$this->mi('iramutu'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$this->mi('whanaunga wahine'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->mi('whanaunga tāne'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->mi('whanaunga'))->parent()->sibling()->child(),
            // Dynamic: ancestors
            Relationship::dynamic(fn (int $n) => $this->mi('tipuna wahine ' . $this->ordinal($n)))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->mi('tipuna tāne ' . $this->ordinal($n)))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->mi('tipuna ' . $this->ordinal($n)))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(fn (int $n) => $this->mi('mokopuna wahine ' . $this->ordinal($n)))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->mi('mokopuna tāne ' . $this->ordinal($n)))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->mi('mokopuna ' . $this->ordinal($n)))->descendant(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->mi('whaea kēkē ' . $this->ordinal($n)))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->mi('matua kēkē ' . $this->ordinal($n)))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->mi('iramutu wahine ' . $this->ordinal($n)))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->mi('iramutu tāne ' . $this->ordinal($n)))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->mi('iramutu ' . $this->ordinal($n)))->sibling()->descendant(),
        ];
    }
}
