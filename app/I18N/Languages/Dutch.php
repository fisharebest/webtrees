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

use Closure;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

use function mb_substr;
use function str_repeat;
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
     * Generate nominative and genitive forms for a common gender noun ("de").
     *
     * @return array{string, string}
     */
    private function vanDe(string $nominative): array
    {
        return [$nominative, '%s van de ' . $nominative];
    }

    /**
     * Generate nominative and genitive forms for a neuter gender noun ("het").
     *
     * @return array{string, string}
     */
    private function vanHet(string $nominative): array
    {
        return [$nominative, '%s van het ' . $nominative];
    }

    /**
     * Generate nominative and genitive forms for a dynamic relationship
     * by prepending a repeated prefix to a base suffix.
     *
     * @return array{string, string}
     */
    private function repeat(int $n, string $suffix, string $repeat, Closure $genitive): array
    {
        return $genitive(($n > 3 ? $repeat . '×' . $n . '-' : str_repeat($repeat, $n)) . $suffix);
    }

    /**
     * Dynamic ancestor relationship using the "bet" prefix.
     *
     * overgrootmoeder → betovergrootmoeder → betbetovergrootmoeder
     *
     * @return array{string, string}
     */
    private function bet(int $n, string $suffix, Closure $genitive): array
    {
        return $this->repeat($n, $suffix, 'bet', $genitive);
    }

    /**
     * Dynamic aunt/uncle/niece/nephew relationship using the "oud" prefix.
     *
     * tante → oudtante → oudoudtante
     *
     * @return array{string, string}
     */
    private function oud(int $n, string $suffix, Closure $genitive): array
    {
        return $this->repeat($n, $suffix, 'oud', $genitive);
    }

    /**
     * Dynamic descendant relationship using the "over" prefix.
     *
     * kleinkind → overkleinkind → overoverkleinkind
     *
     * @return array{string, string}
     */
    private function over(int $n, string $suffix, Closure $genitive): array
    {
        return $this->repeat($n, $suffix, 'over', $genitive);
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->vanDe('adoptiemoeder'))->adoptive()->mother(),
            Relationship::fixed(...$this->vanDe('adoptievader'))->adoptive()->father(),
            Relationship::fixed(...$this->vanDe('adoptieouder'))->adoptive()->parent(),
            Relationship::fixed(...$this->vanDe('adoptiedochter'))->adopted()->daughter(),
            Relationship::fixed(...$this->vanDe('adoptiezoon'))->adopted()->son(),
            Relationship::fixed(...$this->vanHet('adoptiekind'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->vanDe('pleegmoeder'))->fostering()->mother(),
            Relationship::fixed(...$this->vanDe('pleegvader'))->fostering()->father(),
            Relationship::fixed(...$this->vanDe('pleegouder'))->fostering()->parent(),
            Relationship::fixed(...$this->vanDe('pleegdochter'))->fostered()->daughter(),
            Relationship::fixed(...$this->vanDe('pleegzoon'))->fostered()->son(),
            Relationship::fixed(...$this->vanHet('pleegkind'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->vanDe('moeder'))->mother(),
            Relationship::fixed(...$this->vanDe('vader'))->father(),
            Relationship::fixed(...$this->vanDe('ouder'))->parent(),
            // Children
            Relationship::fixed(...$this->vanDe('dochter'))->daughter(),
            Relationship::fixed(...$this->vanDe('zoon'))->son(),
            Relationship::fixed(...$this->vanHet('kind'))->child(),
            // Siblings
            Relationship::fixed(...$this->vanDe('tweelingzus'))->multiple()->sister(),
            Relationship::fixed(...$this->vanDe('tweelingbroer'))->multiple()->brother(),
            Relationship::fixed(...$this->vanDe('tweeling'))->multiple()->sibling(),
            Relationship::fixed(...$this->vanDe('oudere zus'))->older()->sister(),
            Relationship::fixed(...$this->vanDe('oudere broer'))->older()->brother(),
            Relationship::fixed(...$this->vanDe('oudere broer/zus'))->older()->sibling(),
            Relationship::fixed(...$this->vanDe('jongere zus'))->younger()->sister(),
            Relationship::fixed(...$this->vanDe('jongere broer'))->younger()->brother(),
            Relationship::fixed(...$this->vanDe('jongere broer/zus'))->younger()->sibling(),
            Relationship::fixed(...$this->vanDe('zus'))->sister(),
            Relationship::fixed(...$this->vanDe('broer'))->brother(),
            Relationship::fixed(...$this->vanDe('broer/zus'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->vanDe('halfzus'))->parent()->daughter(),
            Relationship::fixed(...$this->vanDe('halfbroer'))->parent()->son(),
            Relationship::fixed(...$this->vanDe('halfbroer/halfzus'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->vanDe('stiefmoeder'))->parent()->wife(),
            Relationship::fixed(...$this->vanDe('stiefvader'))->parent()->husband(),
            Relationship::fixed(...$this->vanDe('stiefouder'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->vanDe('stiefdochter'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->vanDe('stiefzoon'))->married()->spouse()->son(),
            Relationship::fixed(...$this->vanHet('stiefkind'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->vanDe('ex-vrouw'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->vanDe('ex-man'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->vanDe('ex-partner'))->divorced()->partner(),
            Relationship::fixed(...$this->vanDe('verloofde'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->vanDe('verloofde'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->vanDe('echtgenote'))->wife(),
            Relationship::fixed(...$this->vanDe('echtgenoot'))->husband(),
            Relationship::fixed(...$this->vanDe('echtgeno(o)t(e)'))->spouse(),
            Relationship::fixed(...$this->vanDe('partner'))->partner(),
            // In-laws
            Relationship::fixed(...$this->vanDe('schoonmoeder'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->vanDe('schoonvader'))->married()->spouse()->father(),
            Relationship::fixed(...$this->vanDe('schoonouder'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->vanDe('schoondochter'))->child()->wife(),
            Relationship::fixed(...$this->vanDe('schoonzoon'))->child()->husband(),
            Relationship::fixed(...$this->vanHet('schoonkind'))->child()->married()->spouse(),
            Relationship::fixed(...$this->vanDe('schoonzus'))->spouse()->sister(),
            Relationship::fixed(...$this->vanDe('zwager'))->spouse()->brother(),
            Relationship::fixed(...$this->vanDe('schoonzus'))->sibling()->wife(),
            Relationship::fixed(...$this->vanDe('zwager'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->vanDe('grootmoeder'))->parent()->mother(),
            Relationship::fixed(...$this->vanDe('grootvader'))->parent()->father(),
            Relationship::fixed(...$this->vanDe('grootouder'))->parent()->parent(),
            // Great-grandparents
            Relationship::fixed(...$this->vanDe('overgrootmoeder'))->parent()->parent()->mother(),
            Relationship::fixed(...$this->vanDe('overgrootvader'))->parent()->parent()->father(),
            Relationship::fixed(...$this->vanDe('overgrootouder'))->parent()->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->vanDe('kleindochter'))->child()->daughter(),
            Relationship::fixed(...$this->vanDe('kleinzoon'))->child()->son(),
            Relationship::fixed(...$this->vanHet('kleinkind'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->vanDe('tante'))->parent()->sister(),
            Relationship::fixed(...$this->vanDe('oom'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->vanDe('nicht'))->sibling()->daughter(),
            Relationship::fixed(...$this->vanDe('neef'))->sibling()->son(),
            Relationship::fixed(...$this->vanDe('nicht'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$this->vanDe('neef'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$this->vanDe('nicht'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->vanDe('neef'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(fn (int $n) => $this->oud($n - 1, 'tante', $this->vanDe(...)))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->oud($n - 1, 'oom', $this->vanDe(...)))->ancestor()->brother(),
            Relationship::dynamic(fn (int $n) => $this->oud($n - 1, 'nicht', $this->vanDe(...)))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->oud($n - 1, 'nicht', $this->vanDe(...)))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->oud($n - 1, 'neef', $this->vanDe(...)))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->oud($n - 1, 'neef', $this->vanDe(...)))->married()->spouse()->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->bet($n - 3, 'overgrootmoeder', $this->vanDe(...)))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->bet($n - 3, 'overgrootvader', $this->vanDe(...)))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->bet($n - 3, 'overgrootouder', $this->vanDe(...)))->ancestor(),
            Relationship::dynamic(fn (int $n) => $this->over($n - 2, 'kleindochter', $this->vanDe(...)))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->over($n - 2, 'kleinzoon', $this->vanDe(...)))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->over($n - 2, 'kleinkind', $this->vanHet(...)))->descendant(),
        ];
    }
}
