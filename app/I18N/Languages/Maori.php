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
    public function relationships(): array
    {
        // Māori possessive: "o" particle for kinship relationships
        $mi = static fn (string $s): array => [$s, $s . ' o %s'];

        $ordinal = static fn (int $n): string => match ($n) {
            2       => 'tuarua',
            3       => 'tuatoru',
            4       => 'tuawhā',
            5       => 'tuarima',
            6       => 'tuaono',
            7       => 'tuawhitu',
            default => 'tuā-' . $n,
        };

        return [
            // Adopted/fostered (whāngai — the Māori concept encompasses both)
            Relationship::fixed(...$mi('whaea whāngai'))->adoptive()->mother(),
            Relationship::fixed(...$mi('matua whāngai'))->adoptive()->father(),
            Relationship::fixed(...$mi('matua whāngai'))->adoptive()->parent(),
            Relationship::fixed(...$mi('tamāhine whāngai'))->adopted()->daughter(),
            Relationship::fixed(...$mi('tama whāngai'))->adopted()->son(),
            Relationship::fixed(...$mi('tamaiti whāngai'))->adopted()->child(),
            Relationship::fixed(...$mi('whaea whāngai'))->fostering()->mother(),
            Relationship::fixed(...$mi('matua whāngai'))->fostering()->father(),
            Relationship::fixed(...$mi('matua whāngai'))->fostering()->parent(),
            Relationship::fixed(...$mi('tamāhine whāngai'))->fostered()->daughter(),
            Relationship::fixed(...$mi('tama whāngai'))->fostered()->son(),
            Relationship::fixed(...$mi('tamaiti whāngai'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$mi('whaea'))->mother(),
            Relationship::fixed(...$mi('matua'))->father(),
            Relationship::fixed(...$mi('matua'))->parent(),
            // Children
            Relationship::fixed(...$mi('tamāhine'))->daughter(),
            Relationship::fixed(...$mi('tama'))->son(),
            Relationship::fixed(...$mi('tamaiti'))->child(),
            // Siblings
            Relationship::fixed(...$mi('māhanga tuahine'))->twin()->sister(),
            Relationship::fixed(...$mi('māhanga tungāne'))->twin()->brother(),
            Relationship::fixed(...$mi('māhanga'))->twin()->sibling(),
            Relationship::fixed(...$mi('tuakana wahine'))->older()->sister(),
            Relationship::fixed(...$mi('tuakana tāne'))->older()->brother(),
            Relationship::fixed(...$mi('tuakana'))->older()->sibling(),
            Relationship::fixed(...$mi('teina wahine'))->younger()->sister(),
            Relationship::fixed(...$mi('teina tāne'))->younger()->brother(),
            Relationship::fixed(...$mi('teina'))->younger()->sibling(),
            Relationship::fixed(...$mi('tuahine'))->sister(),
            Relationship::fixed(...$mi('tungāne'))->brother(),
            Relationship::fixed(...$mi('tuakana/teina'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$mi('tuahine'))->parent()->daughter(),
            Relationship::fixed(...$mi('tungāne'))->parent()->son(),
            Relationship::fixed(...$mi('tuakana/teina'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$mi('whaea kē'))->parent()->wife(),
            Relationship::fixed(...$mi('matua kē'))->parent()->husband(),
            Relationship::fixed(...$mi('matua kē'))->parent()->married()->spouse(),
            Relationship::fixed(...$mi('tamāhine kē'))->married()->spouse()->daughter(),
            Relationship::fixed(...$mi('tama kē'))->married()->spouse()->son(),
            Relationship::fixed(...$mi('tamaiti kē'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$mi('wahine i wehea'))->divorced()->partner()->female(),
            Relationship::fixed(...$mi('tāne i wehea'))->divorced()->partner()->male(),
            Relationship::fixed(...$mi('hoa i wehea'))->divorced()->partner(),
            Relationship::fixed(...$mi('wahine oati'))->engaged()->partner()->female(),
            Relationship::fixed(...$mi('tāne oati'))->engaged()->partner()->male(),
            Relationship::fixed(...$mi('wahine'))->wife(),
            Relationship::fixed(...$mi('tāne'))->husband(),
            Relationship::fixed(...$mi('hoa rangatira'))->spouse(),
            Relationship::fixed(...$mi('hoa'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$mi('hungawai wahine'))->married()->spouse()->mother(),
            Relationship::fixed(...$mi('hungawai tāne'))->married()->spouse()->father(),
            Relationship::fixed(...$mi('hungawai'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$mi('hunaonga wahine'))->child()->wife(),
            Relationship::fixed(...$mi('hunaonga tāne'))->child()->husband(),
            Relationship::fixed(...$mi('hunaonga'))->child()->married()->spouse(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$mi('taokete wahine'))->spouse()->sister(),
            Relationship::fixed(...$mi('taokete tāne'))->spouse()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$mi('taokete wahine'))->sibling()->wife(),
            Relationship::fixed(...$mi('taokete tāne'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$mi('kuia'))->parent()->mother(),
            Relationship::fixed(...$mi('koroua'))->parent()->father(),
            Relationship::fixed(...$mi('tipuna'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$mi('mokopuna wahine'))->child()->daughter(),
            Relationship::fixed(...$mi('mokopuna tāne'))->child()->son(),
            Relationship::fixed(...$mi('mokopuna'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$mi('whaea kēkē'))->parent()->sister(),
            Relationship::fixed(...$mi('matua kēkē'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$mi('iramutu wahine'))->sibling()->daughter(),
            Relationship::fixed(...$mi('iramutu tāne'))->sibling()->son(),
            Relationship::fixed(...$mi('iramutu'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$mi('whanaunga wahine'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$mi('whanaunga tāne'))->parent()->sibling()->son(),
            Relationship::fixed(...$mi('whanaunga'))->parent()->sibling()->child(),
            // Dynamic: ancestors
            Relationship::dynamic(static fn (int $n) => $mi('tipuna wahine ' . $ordinal($n)))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $mi('tipuna tāne ' . $ordinal($n)))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $mi('tipuna ' . $ordinal($n)))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(static fn (int $n) => $mi('mokopuna wahine ' . $ordinal($n)))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $mi('mokopuna tāne ' . $ordinal($n)))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $mi('mokopuna ' . $ordinal($n)))->descendant(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $mi('whaea kēkē ' . $ordinal($n)))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $mi('matua kēkē ' . $ordinal($n)))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $mi('iramutu wahine ' . $ordinal($n)))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $mi('iramutu tāne ' . $ordinal($n)))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $mi('iramutu ' . $ordinal($n)))->sibling()->descendant(),
        ];
    }
}
