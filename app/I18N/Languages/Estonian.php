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
    protected const string    DIGITS_SEPARATOR   = UTF8::NARROW_NO_BREAK_SPACE;
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
    /** @return array{string, string} */
    private function rel(string $nominative, string $genitive): array
    {
        return [$nominative, '%s ' . $genitive];
    }

    /**
     * Generate nominative and genitive forms for a dynamic relationship
     * using the repeated "vana" prefix.
     *
     * vanaema → vanavanaema → vanavanavanaema
     *
     * @return array{string, string}
     */
    private function vana(int $n, string $nominative, string $genitive): array
    {
        return [
            str_repeat('vana', $n) . $nominative,
            '%s ' . str_repeat('vana', $n) . $genitive,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {

        return [
            // Parents
            Relationship::fixed(...$this->rel('ema', 'ema'))->mother(),
            Relationship::fixed(...$this->rel('isa', 'isa'))->father(),
            Relationship::fixed(...$this->rel('vanem', 'vanema'))->parent(),
            // Children
            Relationship::fixed(...$this->rel('tütar', 'tütre'))->daughter(),
            Relationship::fixed(...$this->rel('poeg', 'poja'))->son(),
            Relationship::fixed(...$this->rel('laps', 'lapse'))->child(),
            // Siblings
            Relationship::fixed(...$this->rel('õde', 'õe'))->sister(),
            Relationship::fixed(...$this->rel('vend', 'venna'))->brother(),
            // Half-siblings
            Relationship::fixed(...$this->rel('poolõde', 'poolõe'))->parent()->daughter(),
            Relationship::fixed(...$this->rel('poolvend', 'poolvenna'))->parent()->son(),
            Relationship::fixed(...$this->rel('poolõde', 'poolõe'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->rel('kasuema', 'kasuema'))->parent()->wife(),
            Relationship::fixed(...$this->rel('kasuisa', 'kasuisa'))->parent()->husband(),
            Relationship::fixed(...$this->rel('kasutütar', 'kasutütre'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('kasupoeg', 'kasupoja'))->married()->spouse()->son(),
            Relationship::fixed(...$this->rel('kasulaps', 'kasulapse'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->rel('endine naine', 'endise naise'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->rel('endine mees', 'endise mehe'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->rel('endine abikaasa', 'endise abikaasa'))->divorced()->partner(),
            Relationship::fixed(...$this->rel('kihlatu', 'kihlatu'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->rel('kihlatu', 'kihlatu'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->rel('naine', 'naise'))->wife(),
            Relationship::fixed(...$this->rel('mees', 'mehe'))->husband(),
            Relationship::fixed(...$this->rel('abikaasa', 'abikaasa'))->spouse(),
            Relationship::fixed(...$this->rel('elukaaslane', 'elukaaslase'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$this->rel('ämm', 'ämma'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->rel('äi', 'äia'))->married()->spouse()->father(),
            Relationship::fixed(...$this->rel('ämm', 'ämma'))->spouse()->mother(),
            Relationship::fixed(...$this->rel('äi', 'äia'))->spouse()->father(),
            // Children-in-law
            Relationship::fixed(...$this->rel('minia', 'minia'))->child()->wife(),
            Relationship::fixed(...$this->rel('väimees', 'väimehe'))->child()->husband(),
            // Siblings-in-law
            Relationship::fixed(...$this->rel('käli', 'käli'))->spouse()->sister(),
            Relationship::fixed(...$this->rel('küdi', 'küdi'))->spouse()->brother(),
            Relationship::fixed(...$this->rel('käli', 'käli'))->sibling()->wife(),
            Relationship::fixed(...$this->rel('küdi', 'küdi'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->rel('vanaema', 'vanaema'))->parent()->mother(),
            Relationship::fixed(...$this->rel('vanaisa', 'vanaisa'))->parent()->father(),
            Relationship::fixed(...$this->rel('vanavanem', 'vanavanema'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->rel('lapselaps', 'lapselapse'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->rel('tädi', 'tädi'))->parent()->sister(),
            Relationship::fixed(...$this->rel('onu', 'onu'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->rel('vennatütar', 'vennatütre'))->brother()->daughter(),
            Relationship::fixed(...$this->rel('vennapoeg', 'vennapoja'))->brother()->son(),
            Relationship::fixed(...$this->rel('õetütar', 'õetütre'))->sister()->daughter(),
            Relationship::fixed(...$this->rel('õepoeg', 'õepoja'))->sister()->son(),
            Relationship::fixed(...$this->rel('vennatütar', 'vennatütre'))->sibling()->daughter(),
            Relationship::fixed(...$this->rel('vennapoeg', 'vennapoja'))->sibling()->son(),
            // Cousins
            Relationship::fixed(...$this->rel('nõbu', 'nõbu'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(fn (int $n) => $this->vana($n - 1, 'ema', 'ema'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->vana($n - 1, 'isa', 'isa'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->vana($n - 1, 'vanem', 'vanema'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(fn (int $n) => [
                str_repeat('lapse', $n - 1) . 'laps',
                '%s ' . str_repeat('lapse', $n - 1) . 'lapse',
            ])->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->vana($n - 1, 'tädi', 'tädi'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->vana($n - 1, 'onu', 'onu'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->vana($n - 1, 'vennatütar', 'vennatütre'))->brother()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->vana($n - 1, 'vennapoeg', 'vennapoja'))->brother()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->vana($n - 1, 'õetütar', 'õetütre'))->sister()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->vana($n - 1, 'õepoeg', 'õepoja'))->sister()->descendant()->male(),
        ];
    }
}
