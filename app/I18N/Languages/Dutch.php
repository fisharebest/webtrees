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
use function str_starts_with;

final readonly class Dutch extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'Nederlands';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'nl';
    protected const string    LOCALE_CODE        = 'nl_NL@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'rond %s';
    protected const string    DATE_AFTER         = 'na %s';
    protected const string    DATE_BEFORE        = 'voor %s';
    protected const string    DATE_BETWEEN_AND   = 'tussen %s en %s';
    protected const string    DATE_CALCULATED    = '%s (berekend)';
    protected const string    DATE_ESTIMATED     = '%s (geschat)';
    protected const string    DATE_FROM          = 'vanaf %s';
    protected const string    DATE_FROM_TO       = 'vanaf %s tot %s';
    protected const string    DATE_INTERPRETED   = 'vertaald %s';
    protected const string    DATE_TO            = 'tot %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'v.C.';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'n.C.';
    protected const string    LIST_SEPARATOR_AND = ' en ';
    protected const string    LIST_SEPARATOR_OR  = ' of ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'januari',
        'februari',
        'maart',
        'april',
        'mei',
        'juni',
        'juli',
        'augustus',
        'september',
        'oktober',
        'november',
        'december',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tisjri',
        'Chesjwan',
        'Kislev',
        'Tevet',
        'Sjewat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nisan',
        'Ijar',
        'Siwan',
        'Tammoez',
        'Av',
        'Eloel',
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
        'IJ',
    ];

    public function initialLetter(string $string): string
    {
        if (str_starts_with($string, 'IJ')) {
            return 'IJ';
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
            'IJ' => UTF8::LATIN_CAPITAL_LIGATURE_IJ,
            'Ij' => UTF8::LATIN_CAPITAL_LIGATURE_IJ,
            'ij' => UTF8::LATIN_SMALL_LIGATURE_IJ,
            'iJ' => UTF8::LATIN_SMALL_LIGATURE_IJ,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Dutch genitive: "van de" (common gender) or "van het" (neuter)
        $van_de  = static fn (string $s): array => [$s, '%s van de ' . $s];
        $van_het = static fn (string $s): array => [$s, '%s van het ' . $s];

        $great = static fn (int $n, string $prefix, string $suffix): array => [
            $prefix . ($n > 3 ? 'over×' . $n . '-' : str_repeat('over', $n)) . $suffix,
            '%s van de ' . $prefix . ($n > 3 ? 'over×' . $n . '-' : str_repeat('over', $n)) . $suffix,
        ];

        return [
            // Adopted
            Relationship::fixed(...$van_de('adoptiemoeder'))->adoptive()->mother(),
            Relationship::fixed(...$van_de('adoptievader'))->adoptive()->father(),
            Relationship::fixed(...$van_de('adoptieouder'))->adoptive()->parent(),
            Relationship::fixed(...$van_de('adoptiedochter'))->adopted()->daughter(),
            Relationship::fixed(...$van_de('adoptiezoon'))->adopted()->son(),
            Relationship::fixed(...$van_het('adoptiekind'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$van_de('pleegmoeder'))->fostering()->mother(),
            Relationship::fixed(...$van_de('pleegvader'))->fostering()->father(),
            Relationship::fixed(...$van_de('pleegouder'))->fostering()->parent(),
            Relationship::fixed(...$van_de('pleegdochter'))->fostered()->daughter(),
            Relationship::fixed(...$van_de('pleegzoon'))->fostered()->son(),
            Relationship::fixed(...$van_het('pleegkind'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$van_de('moeder'))->mother(),
            Relationship::fixed(...$van_de('vader'))->father(),
            Relationship::fixed(...$van_de('ouder'))->parent(),
            // Children
            Relationship::fixed(...$van_de('dochter'))->daughter(),
            Relationship::fixed(...$van_de('zoon'))->son(),
            Relationship::fixed(...$van_het('kind'))->child(),
            // Siblings
            Relationship::fixed(...$van_de('tweelingzus'))->twin()->sister(),
            Relationship::fixed(...$van_de('tweelingbroer'))->twin()->brother(),
            Relationship::fixed(...$van_de('tweeling'))->twin()->sibling(),
            Relationship::fixed(...$van_de('oudere zus'))->older()->sister(),
            Relationship::fixed(...$van_de('oudere broer'))->older()->brother(),
            Relationship::fixed(...$van_de('ouder broer/zus'))->older()->sibling(),
            Relationship::fixed(...$van_de('jongere zus'))->younger()->sister(),
            Relationship::fixed(...$van_de('jongere broer'))->younger()->brother(),
            Relationship::fixed(...$van_de('jonger broer/zus'))->younger()->sibling(),
            Relationship::fixed(...$van_de('zus'))->sister(),
            Relationship::fixed(...$van_de('broer'))->brother(),
            Relationship::fixed(...$van_de('broer/zus'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$van_de('halfzus'))->parent()->daughter(),
            Relationship::fixed(...$van_de('halfbroer'))->parent()->son(),
            Relationship::fixed(...$van_de('halfbroer/halfzus'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$van_de('stiefmoeder'))->parent()->wife(),
            Relationship::fixed(...$van_de('stiefvader'))->parent()->husband(),
            Relationship::fixed(...$van_de('stiefouder'))->parent()->married()->spouse(),
            Relationship::fixed(...$van_de('stiefdochter'))->married()->spouse()->daughter(),
            Relationship::fixed(...$van_de('stiefzoon'))->married()->spouse()->son(),
            Relationship::fixed(...$van_het('stiefkind'))->married()->spouse()->child(),
            Relationship::fixed(...$van_de('stiefzus'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$van_de('stiefbroer'))->parent()->spouse()->son(),
            Relationship::fixed(...$van_de('stiefbroer/stiefzus'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$van_de('ex-vrouw'))->divorced()->partner()->female(),
            Relationship::fixed(...$van_de('ex-man'))->divorced()->partner()->male(),
            Relationship::fixed(...$van_de('ex-partner'))->divorced()->partner(),
            Relationship::fixed(...$van_de('verloofde'))->engaged()->partner()->female(),
            Relationship::fixed(...$van_de('verloofde'))->engaged()->partner()->male(),
            Relationship::fixed(...$van_de('echtgenote'))->wife(),
            Relationship::fixed(...$van_de('echtgenoot'))->husband(),
            Relationship::fixed(...$van_de('echtgeno(o)t(e)'))->spouse(),
            Relationship::fixed(...$van_de('partner'))->partner(),
            // In-laws
            Relationship::fixed(...$van_de('schoonmoeder'))->married()->spouse()->mother(),
            Relationship::fixed(...$van_de('schoonvader'))->married()->spouse()->father(),
            Relationship::fixed(...$van_de('schoonouder'))->married()->spouse()->parent(),
            Relationship::fixed(...$van_de('schoondochter'))->child()->wife(),
            Relationship::fixed(...$van_de('schoonzoon'))->child()->husband(),
            Relationship::fixed(...$van_het('schoonkind'))->child()->married()->spouse(),
            Relationship::fixed(...$van_de('schoonzus'))->spouse()->sister(),
            Relationship::fixed(...$van_de('zwager'))->spouse()->brother(),
            Relationship::fixed(...$van_de('schoonzus'))->sibling()->wife(),
            Relationship::fixed(...$van_de('zwager'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$van_de('grootmoeder'))->parent()->mother(),
            Relationship::fixed(...$van_de('grootvader'))->parent()->father(),
            Relationship::fixed(...$van_de('grootouder'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$van_de('kleindochter'))->child()->daughter(),
            Relationship::fixed(...$van_de('kleinzoon'))->child()->son(),
            Relationship::fixed(...$van_het('kleinkind'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$van_de('tante'))->parent()->sister(),
            Relationship::fixed(...$van_de('oom'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$van_de('nicht'))->sibling()->daughter(),
            Relationship::fixed(...$van_de('neef'))->sibling()->son(),
            Relationship::fixed(...$van_de('nicht'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$van_de('neef'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$van_de('nicht'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$van_de('neef'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'tante'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'oom'))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'nicht'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'nicht'))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'neef'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'neef'))->married()->spouse()->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'grootmoeder'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'grootvader'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'grootouder'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, '', 'kleindochter'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, '', 'kleinzoon'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, '', 'kleinkind'))->descendant(),
        ];
    }
}
