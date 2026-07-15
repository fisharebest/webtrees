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

use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Uzbek extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsPluralForMoreThanOne;

    protected const string    ENDONYM            = 'o‘zbek';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'uz';
    protected const string    LOCALE_CODE        = 'uz_UZ@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    LIST_SEPARATOR_AND = ' va ';
    protected const string    LIST_SEPARATOR_OR  = ' yoki ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Yanvar',
        'Fevral',
        'Mart',
        'Aprel',
        'May',
        'Iyun',
        'Iyul',
        'Avgust',
        'Sentabr',
        'Oktabr',
        'Noyabr',
        'Dekabr',
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
        'Robiul-avval',
        'Robiul-oxir',
        'Jumodul-avval',
        'Jumodul-oxir',
        'Rajab',
        'Sha’bon',
        'Ramazon',
        'Shavvol',
        'Zulqa’da',
        'Zulhijja',
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
     * @return array<int,string>
     */
    /** @var array<int,string> */
    protected const array ALPHABET = [
        'A',
        'B',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'X',
        'Y',
        'Z',
        'O' . UTF8::MODIFIER_LETTER_TURNED_COMMA,
        'G' . UTF8::MODIFIER_LETTER_TURNED_COMMA,
        'SH',
        'CH',
        'NG',
    ];

    public function initialLetter(string $string): string
    {
        if (str_starts_with($string, 'O' . UTF8::MODIFIER_LETTER_TURNED_COMMA)) {
            return 'O' . UTF8::MODIFIER_LETTER_TURNED_COMMA;
        }

        if (str_starts_with($string, 'G' . UTF8::MODIFIER_LETTER_TURNED_COMMA)) {
            return 'G' . UTF8::MODIFIER_LETTER_TURNED_COMMA;
        }

        if (str_starts_with($string, 'SH')) {
            return 'SH';
        }

        if (str_starts_with($string, 'CH')) {
            return 'CH';
        }

        if (str_starts_with($string, 'NG')) {
            return 'NG';
        }

        return mb_substr($string, 0, 1);
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Uzbek genitive: possessive suffix with -ning
        // "katta" prefix for great-grandparents, repeating for each generation
        $great = static function (int $n, string $nom, string $gen): array {
            $prefix = str_repeat('katta ', $n);

            return [$prefix . $nom, '%s ' . $prefix . $gen];
        };

        return [
            // Parents
            Relationship::fixed('ona', '%s onaning')->mother(),
            Relationship::fixed('ota', '%s otaning')->father(),
            Relationship::fixed('ota-ona', '%s ota-onaning')->parent(),
            // Children
            Relationship::fixed('qiz', '%s qizning')->daughter(),
            Relationship::fixed('o\'g\'il', '%s o\'g\'ilning')->son(),
            Relationship::fixed('farzand', '%s farzandning')->child(),
            // Siblings — elder/younger
            Relationship::fixed('opa', '%s opaning')->older()->sister(),
            Relationship::fixed('aka', '%s akaning')->older()->brother(),
            Relationship::fixed('singil', '%s singilning')->younger()->sister(),
            Relationship::fixed('uka', '%s ukaning')->younger()->brother(),
            Relationship::fixed('opa', '%s opaning')->sister(),
            Relationship::fixed('aka', '%s akaning')->brother(),
            Relationship::fixed('aka-uka', '%s aka-ukaning')->sibling(),
            // Half-siblings
            Relationship::fixed('o\'gay opa', '%s o\'gay opaning')->parent()->daughter(),
            Relationship::fixed('o\'gay aka', '%s o\'gay akaning')->parent()->son(),
            Relationship::fixed('o\'gay aka-uka', '%s o\'gay aka-ukaning')->parent()->child(),
            // Stepfamily
            Relationship::fixed('o\'gay ona', '%s o\'gay onaning')->parent()->wife(),
            Relationship::fixed('o\'gay ota', '%s o\'gay otaning')->parent()->husband(),
            Relationship::fixed('o\'gay ota-ona', '%s o\'gay ota-onaning')->parent()->married()->spouse(),
            Relationship::fixed('o\'gay qiz', '%s o\'gay qizning')->married()->spouse()->daughter(),
            Relationship::fixed('o\'gay o\'g\'il', '%s o\'gay o\'g\'ilning')->married()->spouse()->son(),
            Relationship::fixed('o\'gay farzand', '%s o\'gay farzandning')->married()->spouse()->child(),
            Relationship::fixed('o\'gay opa', '%s o\'gay opaning')->parent()->spouse()->daughter(),
            Relationship::fixed('o\'gay aka', '%s o\'gay akaning')->parent()->spouse()->son(),
            Relationship::fixed('o\'gay aka-uka', '%s o\'gay aka-ukaning')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('sobiq turmush o\'rtog\'i', '%s sobiq turmush o\'rtog\'ining')->divorced()->partner()->female(),
            Relationship::fixed('sobiq turmush o\'rtog\'i', '%s sobiq turmush o\'rtog\'ining')->divorced()->partner()->male(),
            Relationship::fixed('sobiq turmush o\'rtog\'i', '%s sobiq turmush o\'rtog\'ining')->divorced()->partner(),
            Relationship::fixed('unashtirilgan', '%s unashtirilganning')->engaged()->partner()->female(),
            Relationship::fixed('unashtirilgan', '%s unashtirilganning')->engaged()->partner()->male(),
            Relationship::fixed('xotin', '%s xotinning')->wife(),
            Relationship::fixed('er', '%s erning')->husband(),
            Relationship::fixed('turmush o\'rtog\'i', '%s turmush o\'rtog\'ining')->spouse(),
            Relationship::fixed('sherik', '%s sherikning')->partner(),
            // In-laws
            Relationship::fixed('qaynona', '%s qaynonaning')->married()->spouse()->mother(),
            Relationship::fixed('qaynota', '%s qaynotaning')->married()->spouse()->father(),
            Relationship::fixed('qayin ota-ona', '%s qayin ota-onaning')->married()->spouse()->parent(),
            Relationship::fixed('kelin', '%s kelinning')->child()->wife(),
            Relationship::fixed('kuyov', '%s kuyovning')->child()->husband(),
            Relationship::fixed('baldiz', '%s baldizning')->spouse()->sister(),
            Relationship::fixed('qayin', '%s qayinning')->spouse()->brother(),
            Relationship::fixed('kelinoy', '%s kelinoyning')->sibling()->wife(),
            Relationship::fixed('qayin', '%s qayinning')->sibling()->husband(),
            // Grandparents — maternal/paternal
            Relationship::fixed('buvi', '%s buvining')->mother()->mother(),
            Relationship::fixed('bobo', '%s boboning')->mother()->father(),
            Relationship::fixed('buvi', '%s buvining')->father()->mother(),
            Relationship::fixed('bobo', '%s boboning')->father()->father(),
            Relationship::fixed('buvi', '%s buvining')->parent()->mother(),
            Relationship::fixed('bobo', '%s boboning')->parent()->father(),
            Relationship::fixed('buvi yoki bobo', '%s buvi yoki boboning')->parent()->parent(),
            // Grandchildren
            Relationship::fixed('nevara', '%s nevaraning')->child()->daughter(),
            Relationship::fixed('nevara', '%s nevaraning')->child()->son(),
            Relationship::fixed('nevara', '%s nevaraning')->child()->child(),
            // Aunts and uncles — maternal/paternal
            Relationship::fixed('xola', '%s xolaning')->mother()->sister(),
            Relationship::fixed('tog\'a', '%s tog\'aning')->mother()->brother(),
            Relationship::fixed('amma', '%s ammaning')->father()->sister(),
            Relationship::fixed('amaki', '%s amakining')->father()->brother(),
            Relationship::fixed('xola', '%s xolaning')->parent()->sister(),
            Relationship::fixed('amaki', '%s amakining')->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed('jiyan', '%s jiyanning')->sibling()->daughter(),
            Relationship::fixed('jiyan', '%s jiyanning')->sibling()->son(),
            Relationship::fixed('jiyan', '%s jiyanning')->sibling()->child(),
            // Cousins
            Relationship::fixed('amakivachcha', '%s amakivachchaning')->parent()->sibling()->daughter(),
            Relationship::fixed('amakivachcha', '%s amakivachchaning')->parent()->sibling()->son(),
            Relationship::fixed('amakivachcha', '%s amakivachchaning')->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'buvi', 'buvining'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'bobo', 'boboning'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'buvi yoki bobo', 'buvi yoki boboning'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nevara', 'nevaraning'))->descendant(),
        ];
    }
}
