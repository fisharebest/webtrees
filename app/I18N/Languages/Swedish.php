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

final readonly class Swedish extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'svenska';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'sv';
    protected const string    LOCALE_CODE        = 'sv_SE@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NARROW_NO_BREAK_SPACE;
    protected const string    NEGATIVE_SYMBOL    = UTF8::MINUS_SIGN;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'ungefär %s';
    protected const string    DATE_AFTER         = 'efter %s';
    protected const string    DATE_BEFORE        = 'före %s';
    protected const string    DATE_BETWEEN_AND   = 'mellan %s och %s';
    protected const string    DATE_CALCULATED    = 'beräknad %s';
    protected const string    DATE_ESTIMATED     = 'uppskattad %s';
    protected const string    DATE_FROM          = 'från %s';
    protected const string    DATE_FROM_TO       = 'från %s till %s';
    protected const string    DATE_INTERPRETED   = 'tolkat %s';
    protected const string    DATE_TO            = 'till %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'fvt';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'evt';
    protected const string    LIST_SEPARATOR_AND = ' och ';
    protected const string    LIST_SEPARATOR_OR  = ' eller ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'januari',
        'februari',
        'mars',
        'april',
        'maj',
        'juni',
        'juli',
        'augusti',
        'september',
        'oktober',
        'november',
        'december',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'tishrei',
        'heshvan',
        'kislev',
        'tevet',
        'shvat',
        'adar I',
        'adar II',
        'adar',
        'nisan',
        'ijar',
        'sivan',
        'tamuz',
        'av',
        'elul',
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
        'Jumada al-akhirah',
        'Rajab',
        'Sha’ban',
        'Ramadan',
        'Shawwal',
        'Dhu l-Qa’dah',
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
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
    ];

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_RING_ABOVE => UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
            'A' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
            'O' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
            'a' . UTF8::COMBINING_RING_ABOVE => UTF8::LATIN_SMALL_LETTER_A_WITH_RING_ABOVE,
            'a' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_SMALL_LETTER_A_WITH_DIAERESIS,
            'o' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
        ];
    }

    /**
     * Generate nominative and genitive forms with the Swedish "-s" suffix.
     *
     * @return array{string, string}
     */
    private function gen(string $nominative): array
    {
        return [$nominative, '%s ' . $nominative . 's'];
    }

    /**
     * Generate nominative and genitive forms for a dynamic relationship
     * using the repeated "gammel" prefix.
     *
     * mormor → gammelmormor → gammelgammelmormor
     *
     * @return array{string, string}
     */
    private function gammel(int $n, string $suffix): array
    {
        return $this->gen(($n > 3 ? 'gammel×' . $n . '-' : str_repeat('gammel', $n)) . $suffix);
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {

        return [
            // Parents
            Relationship::fixed(...$this->gen('mor'))->mother(),
            Relationship::fixed(...$this->gen('far'))->father(),
            Relationship::fixed(...$this->gen('förälder'))->parent(),
            // Children
            Relationship::fixed(...$this->gen('dotter'))->daughter(),
            Relationship::fixed(...$this->gen('son'))->son(),
            Relationship::fixed(...$this->gen('barn'))->child(),
            // Siblings
            Relationship::fixed(...$this->gen('tvillingsyster'))->multiple()->sister(),
            Relationship::fixed(...$this->gen('tvillingbror'))->multiple()->brother(),
            Relationship::fixed(...$this->gen('tvilling'))->multiple()->sibling(),
            Relationship::fixed(...$this->gen('storasyster'))->older()->sister(),
            Relationship::fixed(...$this->gen('storebror'))->older()->brother(),
            Relationship::fixed(...$this->gen('äldre syskon'))->older()->sibling(),
            Relationship::fixed(...$this->gen('lillasyster'))->younger()->sister(),
            Relationship::fixed(...$this->gen('lillebror'))->younger()->brother(),
            Relationship::fixed(...$this->gen('yngre syskon'))->younger()->sibling(),
            Relationship::fixed(...$this->gen('syster'))->sister(),
            Relationship::fixed(...$this->gen('bror'))->brother(),
            Relationship::fixed(...$this->gen('syskon'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->gen('halvsyster'))->parent()->daughter(),
            Relationship::fixed(...$this->gen('halvbror'))->parent()->son(),
            Relationship::fixed(...$this->gen('halvsyskon'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->gen('styvmor'))->parent()->wife(),
            Relationship::fixed(...$this->gen('styvfar'))->parent()->husband(),
            Relationship::fixed(...$this->gen('styvförälder'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->gen('styvdotter'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->gen('styvson'))->married()->spouse()->son(),
            Relationship::fixed(...$this->gen('styvbarn'))->married()->spouse()->child(),
            Relationship::fixed(...$this->gen('styvsyster'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$this->gen('styvbror'))->parent()->spouse()->son(),
            Relationship::fixed(...$this->gen('styvsyskon'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->gen('ex-fru'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->gen('ex-man'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->gen('ex-make/maka'))->divorced()->partner(),
            Relationship::fixed(...$this->gen('fästmö'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->gen('fästman'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->gen('hustru'))->wife(),
            Relationship::fixed(...$this->gen('make'))->husband(),
            Relationship::fixed(...$this->gen('make/maka'))->spouse(),
            Relationship::fixed(...$this->gen('partner'))->partner(),
            // In-laws
            Relationship::fixed(...$this->gen('svärmor'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->gen('svärfar'))->married()->spouse()->father(),
            Relationship::fixed(...$this->gen('svärförälder'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->gen('svärdotter'))->child()->wife(),
            Relationship::fixed(...$this->gen('svärson'))->child()->husband(),
            Relationship::fixed(...$this->gen('svägerska'))->spouse()->sister(),
            Relationship::fixed(...$this->gen('svåger'))->spouse()->brother(),
            Relationship::fixed(...$this->gen('svägerska'))->sibling()->wife(),
            Relationship::fixed(...$this->gen('svåger'))->sibling()->husband(),
            // Grandparents - maternal/paternal
            Relationship::fixed(...$this->gen('mormor'))->mother()->mother(),
            Relationship::fixed(...$this->gen('morfar'))->mother()->father(),
            Relationship::fixed(...$this->gen('farmor'))->father()->mother(),
            Relationship::fixed(...$this->gen('farfar'))->father()->father(),
            Relationship::fixed(...$this->gen('mormor/farmor'))->parent()->mother(),
            Relationship::fixed(...$this->gen('morfar/farfar'))->parent()->father(),
            Relationship::fixed(...$this->gen('mor-/farförälder'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->gen('dotterdotter'))->daughter()->daughter(),
            Relationship::fixed(...$this->gen('dotterson'))->daughter()->son(),
            Relationship::fixed(...$this->gen('sonson'))->son()->son(),
            Relationship::fixed(...$this->gen('sondotter'))->son()->daughter(),
            Relationship::fixed(...$this->gen('barnbarn'))->child()->child(),
            // Aunts and uncles - maternal/paternal
            Relationship::fixed(...$this->gen('moster'))->mother()->sister(),
            Relationship::fixed(...$this->gen('morbror'))->mother()->brother(),
            Relationship::fixed(...$this->gen('faster'))->father()->sister(),
            Relationship::fixed(...$this->gen('farbror'))->father()->brother(),
            Relationship::fixed(...$this->gen('tant'))->parent()->sister(),
            Relationship::fixed(...$this->gen('farbror/morbror'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->gen('brorsdotter'))->brother()->daughter(),
            Relationship::fixed(...$this->gen('brorson'))->brother()->son(),
            Relationship::fixed(...$this->gen('systerdotter'))->sister()->daughter(),
            Relationship::fixed(...$this->gen('systerson'))->sister()->son(),
            // Cousins
            Relationship::fixed(...$this->gen('kusin'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->gen('kusin'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(fn (int $n) => $this->gammel($n - 1, 'mormor'))->mother()->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->gammel($n - 1, 'morfar'))->mother()->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->gammel($n - 1, 'farmor'))->father()->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->gammel($n - 1, 'farfar'))->father()->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->gammel($n - 1, 'mor-/farförälder'))->ancestor(),
            Relationship::dynamic(fn (int $n) => $this->gammel($n - 2, 'barnbarn'))->descendant(),
        ];
    }
}
