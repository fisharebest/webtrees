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

use function mb_substr;

final readonly class NorwegianNynorsk extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'norsk nynorsk';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'nn';
    protected const string    LOCALE_CODE        = 'nn_NO@collation=phonebook';
    protected const string    DATE_ABOUT         = 'omlag %s';
    protected const string    DATE_AFTER         = 'etter %s';
    protected const string    DATE_BEFORE        = 'før %s';
    protected const string    DATE_BETWEEN_AND   = 'mellom %s og %s';
    protected const string    DATE_CALCULATED    = 'berekna %s';
    protected const string    DATE_ESTIMATED     = 'estimert %s';
    protected const string    DATE_FROM          = 'frå %s';
    protected const string    DATE_FROM_TO       = 'frå %s til %s';
    protected const string    DATE_INTERPRETED   = 'tolka %s';
    protected const string    DATE_TO            = 'til %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'fvt. Juliansk kalender';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'evt. Juliansk kalender';
    protected const string    LIST_SEPARATOR_AND = ' og ';
    protected const string    LIST_SEPARATOR_OR  = ' eller ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'januar',
        'februar',
        'mars',
        'april',
        'mai',
        'juni',
        'juli',
        'august',
        'september',
        'oktober',
        'november',
        'desember',
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
        'Nisan',
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
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        UTF8::LATIN_CAPITAL_LETTER_AE,
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_STROKE,
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
    ];

    protected function assembleDate(string $day, string $month, string $year): string
    {
        return $this->assembleDateDdotMY($day, $month, $year);
    }

    public function initialLetter(string $string): string
    {
        if (str_starts_with($string, 'AA')) {
            return 'Å';
        }

        return mb_substr($string, 0, 1);
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'O' . UTF8::COMBINING_LONG_SOLIDUS_OVERLAY => UTF8::LATIN_CAPITAL_LETTER_O_WITH_STROKE,
            'A' . UTF8::COMBINING_RING_ABOVE           => UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
            'AA'                                       => UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
            'Aa'                                       => UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
            'o' . UTF8::COMBINING_LONG_SOLIDUS_OVERLAY => UTF8::LATIN_SMALL_LETTER_O_WITH_STROKE,
            'a' . UTF8::COMBINING_RING_ABOVE           => UTF8::LATIN_SMALL_LETTER_A_WITH_RING_ABOVE,
            'aa'                                       => UTF8::LATIN_SMALL_LETTER_A_WITH_RING_ABOVE,
            'aA'                                       => UTF8::LATIN_SMALL_LETTER_A_WITH_RING_ABOVE,
        ];
    }

    public function relationships(): array
    {
        // Norwegian Nynorsk genitive: "-s" suffix
        $gen = static fn (string $s): array => [$s, '%s ' . $s . 's'];

        $great = static fn (int $n, string $prefix, string $suffix): array => [
            $prefix . ($n > 3 ? 'tipp×' . $n . '-olde' : ($n === 1 ? 'olde' : str_repeat('tipp', $n - 1) . 'olde')) . $suffix,
            '%s ' . $prefix . ($n > 3 ? 'tipp×' . $n . '-olde' : ($n === 1 ? 'olde' : str_repeat('tipp', $n - 1) . 'olde')) . $suffix . 's',
        ];

        return [
            // Parents
            Relationship::fixed(...$gen('mor'))->mother(),
            Relationship::fixed(...$gen('far'))->father(),
            Relationship::fixed(...$gen('forelder'))->parent(),
            // Children
            Relationship::fixed(...$gen('dotter'))->daughter(),
            Relationship::fixed(...$gen('son'))->son(),
            Relationship::fixed(...$gen('barn'))->child(),
            // Siblings
            Relationship::fixed(...$gen('tvillingsyster'))->twin()->sister(),
            Relationship::fixed(...$gen('tvillingbror'))->twin()->brother(),
            Relationship::fixed(...$gen('tvilling'))->twin()->sibling(),
            Relationship::fixed(...$gen('storesyster'))->older()->sister(),
            Relationship::fixed(...$gen('storebror'))->older()->brother(),
            Relationship::fixed(...$gen('eldre sysken'))->older()->sibling(),
            Relationship::fixed(...$gen('lillesyster'))->younger()->sister(),
            Relationship::fixed(...$gen('lillebror'))->younger()->brother(),
            Relationship::fixed(...$gen('yngre sysken'))->younger()->sibling(),
            Relationship::fixed(...$gen('syster'))->sister(),
            Relationship::fixed(...$gen('bror'))->brother(),
            Relationship::fixed(...$gen('sysken'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$gen('halvsyster'))->parent()->daughter(),
            Relationship::fixed(...$gen('halvbror'))->parent()->son(),
            Relationship::fixed(...$gen('halvsysken'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$gen('stemor'))->parent()->wife(),
            Relationship::fixed(...$gen('stefar'))->parent()->husband(),
            Relationship::fixed(...$gen('steforelder'))->parent()->married()->spouse(),
            Relationship::fixed(...$gen('stedotter'))->married()->spouse()->daughter(),
            Relationship::fixed(...$gen('steson'))->married()->spouse()->son(),
            Relationship::fixed(...$gen('stebarn'))->married()->spouse()->child(),
            Relationship::fixed(...$gen('stesyster'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$gen('stebror'))->parent()->spouse()->son(),
            Relationship::fixed(...$gen('stesysken'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$gen('ekskone'))->divorced()->partner()->female(),
            Relationship::fixed(...$gen('eksmann'))->divorced()->partner()->male(),
            Relationship::fixed(...$gen('ekspartner'))->divorced()->partner(),
            Relationship::fixed(...$gen('forlova'))->engaged()->partner()->female(),
            Relationship::fixed(...$gen('forlova'))->engaged()->partner()->male(),
            Relationship::fixed(...$gen('hustru'))->wife(),
            Relationship::fixed(...$gen('mann'))->husband(),
            Relationship::fixed(...$gen('ektefelle'))->spouse(),
            Relationship::fixed(...$gen('partner'))->partner(),
            // In-laws
            Relationship::fixed(...$gen('svigermor'))->married()->spouse()->mother(),
            Relationship::fixed(...$gen('svigerfar'))->married()->spouse()->father(),
            Relationship::fixed(...$gen('svigerforelder'))->married()->spouse()->parent(),
            Relationship::fixed(...$gen('svigerdotter'))->child()->wife(),
            Relationship::fixed(...$gen('svigerson'))->child()->husband(),
            Relationship::fixed(...$gen('svigerinne'))->spouse()->sister(),
            Relationship::fixed(...$gen('svoger'))->spouse()->brother(),
            Relationship::fixed(...$gen('svigerinne'))->sibling()->wife(),
            Relationship::fixed(...$gen('svoger'))->sibling()->husband(),
            // Grandparents - maternal/paternal
            Relationship::fixed(...$gen('mormor'))->mother()->mother(),
            Relationship::fixed(...$gen('morfar'))->mother()->father(),
            Relationship::fixed(...$gen('farmor'))->father()->mother(),
            Relationship::fixed(...$gen('farfar'))->father()->father(),
            Relationship::fixed(...$gen('bestemor'))->parent()->mother(),
            Relationship::fixed(...$gen('bestefar'))->parent()->father(),
            Relationship::fixed(...$gen('besteforelder'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$gen('barnebarn'))->child()->child(),
            // Aunts and uncles - maternal/paternal
            Relationship::fixed(...$gen('moster'))->mother()->sister(),
            Relationship::fixed(...$gen('morbror'))->mother()->brother(),
            Relationship::fixed(...$gen('faster'))->father()->sister(),
            Relationship::fixed(...$gen('farbror'))->father()->brother(),
            Relationship::fixed(...$gen('tante'))->parent()->sister(),
            Relationship::fixed(...$gen('onkel'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$gen('niese'))->sibling()->daughter(),
            Relationship::fixed(...$gen('nevø'))->sibling()->son(),
            // Cousins
            Relationship::fixed(...$gen('kusine'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$gen('fetter'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'mor'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'far'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'forelder'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, '', 'barn'))->descendant(),
        ];
    }
}
