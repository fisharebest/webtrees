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

use function mb_strtolower;
use function mb_strtoupper;
use function str_repeat;
use function strtr;

final readonly class Turkish extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsPluralForMoreThanOne;

    protected const string    ENDONYM            = 'Trke';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'tr';
    protected const string    LOCALE_CODE        = 'tr_TR@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'yaklaşık %s';
    protected const string    DATE_AFTER         = '%s sonrası';
    protected const string    DATE_BEFORE        = '%s öncesi';
    protected const string    DATE_BETWEEN_AND   = '%s ile %s arasında';
    protected const string    DATE_CALCULATED    = 'hesaplanan %s';
    protected const string    DATE_ESTIMATED     = 'tahmini %s';
    protected const string    DATE_FROM          = '%s tarihinden';
    protected const string    DATE_FROM_TO       = '%s tarihinden %s tarihine';
    protected const string    DATE_INTERPRETED   = 'çevrilmiş %s';
    protected const string    DATE_TO            = '%s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'M.Ö';
    protected const string    ERA_CE             = 'M.S.' . UTF8::NO_BREAK_SPACE . '%s';
    protected const string    LIST_SEPARATOR_AND = ' ve ';
    protected const string    LIST_SEPARATOR_OR  = ' veya ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Ocak',
        'Şubat',
        'Mart',
        'Nisan',
        'Mayıs',
        'Haziran',
        'Temmuz',
        'Ağustos',
        'Eylül',
        'Ekim',
        'Kasım',
        'Aralık',
    ];
    protected const string    PERCENT_FORMAT     = '%%%s';

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tişri',
        'Heşvan',
        'Kislev',
        'Tevet',
        'Şevat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nisan',
        'İyar',
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
        'ek günler',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Muharrem',
        'Safer',
        'Rebiülevvel',
        'Rebiülahir',
        'Cemaziyelevvel',
        'Cemaziyelahir',
        'Receb',
        'Şaban',
        'Ramazan',
        'Şevval',
        'Zilkade',
        'Zilhicce',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Ferverdin',
        'Ordibehişt',
        'Hordad',
        'Tir',
        'Mordad',
        'Şehriver',
        'Mehr',
        'Aban',
        'Azer',
        'Dey',
        'Behmen',
        'Esfend',
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
        'C',
        UTF8::LATIN_CAPITAL_LETTER_C_WITH_CEDILLA,
        'D',
        'E',
        'F',
        'G',
        UTF8::LATIN_CAPITAL_LETTER_G_WITH_BREVE,
        'H',
        'I',
        UTF8::LATIN_CAPITAL_LETTER_I_WITH_DOT_ABOVE,
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
        'P',
        'R',
        'S',
        UTF8::LATIN_CAPITAL_LETTER_S_WITH_CEDILLA,
        'T',
        'U',
        UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
        'V',
        'Y',
        'Z',
    ];

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'C' . UTF8::COMBINING_CEDILLA   => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CEDILLA,
            'G' . UTF8::COMBINING_BREVE     => UTF8::LATIN_CAPITAL_LETTER_G_WITH_BREVE,
            'I' . UTF8::COMBINING_DOT_ABOVE => UTF8::LATIN_CAPITAL_LETTER_I_WITH_DOT_ABOVE,
            'O' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
            'S' . UTF8::COMBINING_CEDILLA   => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CEDILLA,
            'U' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
            'c' . UTF8::COMBINING_CEDILLA   => UTF8::LATIN_SMALL_LETTER_C_WITH_CEDILLA,
            'g' . UTF8::COMBINING_BREVE     => UTF8::LATIN_SMALL_LETTER_G_WITH_BREVE,
            'o' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
            's' . UTF8::COMBINING_CEDILLA   => UTF8::LATIN_SMALL_LETTER_S_WITH_CEDILLA,
            'u' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Turkish genitive: possessive suffix with vowel harmony
        // "büyük" prefix for great-grandparents, repeating for each generation
        $great = static function (int $n, string $nom, string $gen): array {
            $prefix = str_repeat('büyük ', $n);

            return [$prefix . $nom, '%s ' . $prefix . $gen];
        };

        return [
            // Parents
            Relationship::fixed('anne', '%s annenin')->mother(),
            Relationship::fixed('baba', '%s babanın')->father(),
            Relationship::fixed('ebeveyn', '%s ebeveynin')->parent(),
            // Children
            Relationship::fixed('kız', '%s kızın')->daughter(),
            Relationship::fixed('oğul', '%s oğlunun')->son(),
            Relationship::fixed('çocuk', '%s çocuğunun')->child(),
            // Siblings
            Relationship::fixed('abla', '%s ablanın')->older()->sister(),
            Relationship::fixed('ağabey', '%s ağabeyin')->older()->brother(),
            Relationship::fixed('kız kardeş', '%s kız kardeşin')->younger()->sister(),
            Relationship::fixed('erkek kardeş', '%s erkek kardeşin')->younger()->brother(),
            Relationship::fixed('kız kardeş', '%s kız kardeşin')->sister(),
            Relationship::fixed('erkek kardeş', '%s erkek kardeşin')->brother(),
            Relationship::fixed('kardeş', '%s kardeşin')->sibling(),
            // Half-siblings
            Relationship::fixed('üvey kız kardeş', '%s üvey kız kardeşin')->parent()->daughter(),
            Relationship::fixed('üvey erkek kardeş', '%s üvey erkek kardeşin')->parent()->son(),
            Relationship::fixed('üvey kardeş', '%s üvey kardeşin')->parent()->child(),
            // Stepfamily
            Relationship::fixed('üvey anne', '%s üvey annenin')->parent()->wife(),
            Relationship::fixed('üvey baba', '%s üvey babanın')->parent()->husband(),
            Relationship::fixed('üvey ebeveyn', '%s üvey ebeveynin')->parent()->married()->spouse(),
            Relationship::fixed('üvey kız', '%s üvey kızın')->married()->spouse()->daughter(),
            Relationship::fixed('üvey oğul', '%s üvey oğlunun')->married()->spouse()->son(),
            Relationship::fixed('üvey çocuk', '%s üvey çocuğunun')->married()->spouse()->child(),
            Relationship::fixed('üvey kız kardeş', '%s üvey kız kardeşin')->parent()->spouse()->daughter(),
            Relationship::fixed('üvey erkek kardeş', '%s üvey erkek kardeşin')->parent()->spouse()->son(),
            Relationship::fixed('üvey kardeş', '%s üvey kardeşin')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('eski eş', '%s eski eşin')->divorced()->partner()->female(),
            Relationship::fixed('eski eş', '%s eski eşin')->divorced()->partner()->male(),
            Relationship::fixed('eski eş', '%s eski eşin')->divorced()->partner(),
            Relationship::fixed('nişanlı', '%s nişanlının')->engaged()->partner()->female(),
            Relationship::fixed('nişanlı', '%s nişanlının')->engaged()->partner()->male(),
            Relationship::fixed('eş', '%s eşin')->wife(),
            Relationship::fixed('eş', '%s eşin')->husband(),
            Relationship::fixed('eş', '%s eşin')->spouse(),
            Relationship::fixed('partner', '%s partnerin')->partner(),
            // In-laws
            Relationship::fixed('kayınvalide', '%s kayınvalidenin')->married()->spouse()->mother(),
            Relationship::fixed('kayınpeder', '%s kayınpederin')->married()->spouse()->father(),
            Relationship::fixed('kayın ebeveyn', '%s kayın ebeveynin')->married()->spouse()->parent(),
            Relationship::fixed('gelin', '%s gelinin')->child()->wife(),
            Relationship::fixed('damat', '%s damadın')->child()->husband(),
            Relationship::fixed('baldız', '%s baldızın')->spouse()->sister(),
            Relationship::fixed('kayın', '%s kayının')->spouse()->brother(),
            Relationship::fixed('görümce', '%s görümcenin')->sibling()->wife(),
            Relationship::fixed('kayın', '%s kayının')->sibling()->husband(),
            // Grandparents — maternal/paternal
            Relationship::fixed('anneanne', '%s anneannenin')->mother()->mother(),
            Relationship::fixed('dede', '%s dedenin')->mother()->father(),
            Relationship::fixed('babaanne', '%s babaannenin')->father()->mother(),
            Relationship::fixed('dede', '%s dedenin')->father()->father(),
            Relationship::fixed('büyükanne', '%s büyükannenin')->parent()->mother(),
            Relationship::fixed('büyükbaba', '%s büyükbabanın')->parent()->father(),
            Relationship::fixed('büyük ebeveyn', '%s büyük ebeveynin')->parent()->parent(),
            // Grandchildren
            Relationship::fixed('torun', '%s torununun')->child()->child(),
            // Aunts and uncles — maternal/paternal
            Relationship::fixed('teyze', '%s teyzenin')->mother()->sister(),
            Relationship::fixed('dayı', '%s dayının')->mother()->brother(),
            Relationship::fixed('hala', '%s halanın')->father()->sister(),
            Relationship::fixed('amca', '%s amcanın')->father()->brother(),
            Relationship::fixed('teyze', '%s teyzenin')->parent()->sister(),
            Relationship::fixed('amca', '%s amcanın')->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed('yeğen', '%s yeğenin')->sibling()->daughter(),
            Relationship::fixed('yeğen', '%s yeğenin')->sibling()->son(),
            Relationship::fixed('yeğen', '%s yeğenin')->sibling()->child(),
            // Cousins
            Relationship::fixed('kuzen', '%s kuzenin')->parent()->sibling()->daughter(),
            Relationship::fixed('kuzen', '%s kuzenin')->parent()->sibling()->son(),
            Relationship::fixed('kuzen', '%s kuzenin')->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'büyükanne', 'büyükannenin'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'büyükbaba', 'büyükbabanın'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'büyük ebeveyn', 'büyük ebeveynin'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'torun', 'torununun'))->descendant(),
        ];
    }

    public function strtolower(string $string): string
    {
        return mb_strtolower(strtr($string, ['I' => 'ı', 'İ' => 'i']));
    }

    public function strtoupper(string $string): string
    {
        return mb_strtoupper(strtr($string, ['ı' => 'I', 'i' => 'İ']));
    }
}
