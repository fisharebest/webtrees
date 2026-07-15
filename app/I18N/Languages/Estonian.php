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

use function str_repeat;

final readonly class Estonian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'eesti';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'et';
    protected const string    LOCALE_CODE        = 'et_EE@collation=phonebook';
    protected const int       MINIMUM_GROUPING_DIGITS = 3;
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    NEGATIVE_SYMBOL    = UTF8::MINUS_SIGN;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'umbes %s';
    protected const string    DATE_AFTER         = 'pärast %s';
    protected const string    DATE_BEFORE        = 'enne %s';
    protected const string    DATE_BETWEEN_AND   = 'ajavahemikul %s ja %s';
    protected const string    DATE_CALCULATED    = 'arvutatud %s';
    protected const string    DATE_ESTIMATED     = 'arvestatavalt %s';
    protected const string    DATE_FROM          = 'järgneva poolt %s';
    protected const string    DATE_FROM_TO       = '%s-lt %s-le';
    protected const string    DATE_INTERPRETED   = 'tõlgendatud %s';
    protected const string    DATE_TO            = '%s\'le';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'eKr';
    protected const string    LIST_SEPARATOR_AND = ' ja ';
    protected const string    LIST_SEPARATOR_OR  = ' või ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'jaanuar',
        'veebruar',
        'märts',
        'aprill',
        'mai',
        'juuni',
        'juuli',
        'august',
        'september',
        'oktoober',
        'november',
        'detsember',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'jaanuari',
        'veebruari',
        'märtsi',
        'aprilli',
        'mai',
        'juuni',
        'juuli',
        'augusti',
        'septembri',
        'oktoobri',
        'novembri',
        'detsembri',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'jaanuaris',
        'veebruaris',
        'märtsis',
        'aprillis',
        'mais',
        'juunis',
        'juulis',
        'augustis',
        'septembris',
        'oktoobris',
        'novembris',
        'detsembris',
    ];

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
     * @return array<int,string>
     */
    /** @var array<int,string> */
    protected const array ALPHABET = [
        'A',
        'B',
        'C',
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
        UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
        'Z',
        UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
        'T',
        'U',
        'V',
        'W',
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_TILDE,
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
        UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
        'X',
        'Y',
    ];

    protected function assembleDate(string $day, string $month, string $year): string
    {
        return $this->assembleDateDdotMY($day, $month, $year);
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'S' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'Z' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
            'O' . UTF8::COMBINING_TILDE     => UTF8::LATIN_CAPITAL_LETTER_O_WITH_TILDE,
            'A' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
            'O' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
            'U' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
            's' . UTF8::COMBINING_CARON     => UTF8::LATIN_SMALL_LETTER_S_WITH_CARON,
            'z' . UTF8::COMBINING_CARON     => UTF8::LATIN_SMALL_LETTER_Z_WITH_CARON,
            'o' . UTF8::COMBINING_TILDE     => UTF8::LATIN_SMALL_LETTER_O_WITH_TILDE,
            'a' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_SMALL_LETTER_A_WITH_DIAERESIS,
            'o' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
            'u' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Estonian genitive: nominative + genitive form for possessive constructions
        $rel = static fn (string $nom, string $gen): array => [$nom, '%s ' . $gen];

        // Estonian uses "vana" prefix for great- generations: vanavanaema = great-grandmother
        $vana = static fn (int $n, string $nom, string $gen): array => [
            str_repeat('vana', $n) . $nom,
            '%s ' . str_repeat('vana', $n) . $gen,
        ];

        return [
            // Parents
            Relationship::fixed(...$rel('ema', 'ema'))->mother(),
            Relationship::fixed(...$rel('isa', 'isa'))->father(),
            Relationship::fixed(...$rel('vanem', 'vanema'))->parent(),
            // Children
            Relationship::fixed(...$rel('tütar', 'tütre'))->daughter(),
            Relationship::fixed(...$rel('poeg', 'poja'))->son(),
            Relationship::fixed(...$rel('laps', 'lapse'))->child(),
            // Siblings
            Relationship::fixed(...$rel('õde', 'õe'))->sister(),
            Relationship::fixed(...$rel('vend', 'venna'))->brother(),
            // Half-siblings
            Relationship::fixed(...$rel('poolõde', 'poolõe'))->parent()->daughter(),
            Relationship::fixed(...$rel('poolvend', 'poolvenna'))->parent()->son(),
            Relationship::fixed(...$rel('poolõde', 'poolõe'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$rel('kasuema', 'kasuema'))->parent()->wife(),
            Relationship::fixed(...$rel('kasuisa', 'kasuisa'))->parent()->husband(),
            Relationship::fixed(...$rel('kasutütar', 'kasutütre'))->married()->spouse()->daughter(),
            Relationship::fixed(...$rel('kasupoeg', 'kasupoja'))->married()->spouse()->son(),
            Relationship::fixed(...$rel('kasulaps', 'kasulapse'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$rel('endine naine', 'endise naise'))->divorced()->partner()->female(),
            Relationship::fixed(...$rel('endine mees', 'endise mehe'))->divorced()->partner()->male(),
            Relationship::fixed(...$rel('endine abikaasa', 'endise abikaasa'))->divorced()->partner(),
            Relationship::fixed(...$rel('kihlatu', 'kihlatu'))->engaged()->partner()->female(),
            Relationship::fixed(...$rel('kihlatu', 'kihlatu'))->engaged()->partner()->male(),
            Relationship::fixed(...$rel('naine', 'naise'))->wife(),
            Relationship::fixed(...$rel('mees', 'mehe'))->husband(),
            Relationship::fixed(...$rel('abikaasa', 'abikaasa'))->spouse(),
            Relationship::fixed(...$rel('elukaaslane', 'elukaaslase'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$rel('ämm', 'ämma'))->married()->spouse()->mother(),
            Relationship::fixed(...$rel('äi', 'äia'))->married()->spouse()->father(),
            Relationship::fixed(...$rel('ämm', 'ämma'))->spouse()->mother(),
            Relationship::fixed(...$rel('äi', 'äia'))->spouse()->father(),
            // Children-in-law
            Relationship::fixed(...$rel('minia', 'minia'))->child()->wife(),
            Relationship::fixed(...$rel('väimees', 'väimehe'))->child()->husband(),
            // Siblings-in-law
            Relationship::fixed(...$rel('käli', 'käli'))->spouse()->sister(),
            Relationship::fixed(...$rel('küdi', 'küdi'))->spouse()->brother(),
            Relationship::fixed(...$rel('käli', 'käli'))->sibling()->wife(),
            Relationship::fixed(...$rel('küdi', 'küdi'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$rel('vanaema', 'vanaema'))->parent()->mother(),
            Relationship::fixed(...$rel('vanaisa', 'vanaisa'))->parent()->father(),
            Relationship::fixed(...$rel('vanavanem', 'vanavanema'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$rel('lapselaps', 'lapselapse'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$rel('tädi', 'tädi'))->parent()->sister(),
            Relationship::fixed(...$rel('onu', 'onu'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$rel('vennatütar', 'vennatütre'))->brother()->daughter(),
            Relationship::fixed(...$rel('vennapoeg', 'vennapoja'))->brother()->son(),
            Relationship::fixed(...$rel('õetütar', 'õetütre'))->sister()->daughter(),
            Relationship::fixed(...$rel('õepoeg', 'õepoja'))->sister()->son(),
            Relationship::fixed(...$rel('vennatütar', 'vennatütre'))->sibling()->daughter(),
            Relationship::fixed(...$rel('vennapoeg', 'vennapoja'))->sibling()->son(),
            // Cousins
            Relationship::fixed(...$rel('nõbu', 'nõbu'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $vana($n - 1, 'ema', 'ema'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $vana($n - 1, 'isa', 'isa'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $vana($n - 1, 'vanem', 'vanema'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(static fn (int $n) => [
                str_repeat('lapse', $n - 1) . 'laps',
                '%s ' . str_repeat('lapse', $n - 1) . 'lapse',
            ])->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $vana($n - 1, 'tädi', 'tädi'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $vana($n - 1, 'onu', 'onu'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $vana($n - 1, 'vennatütar', 'vennatütre'))->brother()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $vana($n - 1, 'vennapoeg', 'vennapoja'))->brother()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $vana($n - 1, 'õetütar', 'õetütre'))->sister()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $vana($n - 1, 'õepoeg', 'õepoja'))->sister()->descendant()->male(),
        ];
    }
}
