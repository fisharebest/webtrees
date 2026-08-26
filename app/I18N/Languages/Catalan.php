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

final readonly class Catalan extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'catal';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ca';
    protected const string    LOCALE_CODE        = 'ca_ES@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'sobre %s';
    protected const string    DATE_AFTER         = 'després de %s';
    protected const string    DATE_BEFORE        = 'abans de %s';
    protected const string    DATE_BETWEEN_AND   = 'entre %s i %s';
    protected const string    DATE_CALCULATED    = 'calculat %s';
    protected const string    DATE_ESTIMATED     = 'estimat %s';
    protected const string    DATE_FROM          = 'des de %s';
    protected const string    DATE_FROM_TO       = 'de %s a %s';
    protected const string    DATE_INTERPRETED   = 'interpretat %s';
    protected const string    DATE_TO            = 'a %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'AEC';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'EC';
    protected const string    LIST_SEPARATOR_AND = ' i ';
    protected const string    LIST_SEPARATOR_OR  = ' o ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'gener',
        'febrer',
        'març',
        'abril',
        'maig',
        'juny',
        'juliol',
        'agost',
        'setembre',
        'octubre',
        'novembre',
        'desembre',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'tixrí',
        'heixvan',
        'quisleu',
        'tevet',
        'xevat',
        'adar I',
        'adar II',
        'adar',
        'nisan',
        'iar',
        'sivan',
        'tammuz',
        'av',
        'elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'vendemiari',
        'bromari',
        'rufolari',
        'nivós',
        'pluviós',
        'ventós',
        'germinal',
        'floral',
        'pradal',
        'messidor',
        'termidor',
        'fructidor',
        'dies complementaris',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'muhàrram',
        'sàfar',
        'rabi’ al-awwal',
        'rabi’ al-thani',
        'jumada al-ula',
        'jumada al-àkhira',
        'ràjab',
        'xaban',
        'ramadà',
        'xawwal',
        'dhu-l-qada',
        'dhu-l-hijja',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'farvardín',
        'ordibehesht',
        'khordad',
        'tir',
        'mordad',
        'shahrivar',
        'mehr',
        'aban',
        'azar',
        'dey',
        'bahman',
        'esfand',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    protected function formatAfterDate(string $date): string
    {
        if ($this->startsWithVowel($date)) {
            return 'després d’' . $date;
        }

        return 'després de ' . $date;
    }

    protected function formatBeforeDate(string $date): string
    {
        if ($this->startsWithVowel($date)) {
            return 'abans d’' . $date;
        }

        return 'abans de ' . $date;
    }

    protected function formatFromDate(string $date): string
    {
        if ($this->startsWithVowel($date)) {
            return 'des d’' . $date;
        }

        return 'des de ' . $date;
    }

    protected function formatFromToDate(string $date1, string $date2): string
    {
        if ($this->startsWithVowel($date1)) {
            return 'd’' . $date1 . ' a ' . $date2;
        }

        return 'de ' . $date1 . ' a ' . $date2;
    }

    /** @return array{string, string} */
    private function deLa(string $nominative): array
    {
        return [$nominative, '%s de la ' . $nominative];
    }

    /** @return array{string, string} */
    private function del1(string $nominative): array
    {
        return [$nominative, '%s del ' . $nominative];
    }

    /** @return array{string, string} */
    private function del2(string $nominative): array
    {
        return [$nominative, "%s de l'" . $nominative];
    }

    /**
     * Generate nominative and genitive forms for a dynamic relationship
     * using the Catalan "bes/rebes" prefix pattern.
     *
     * avi → besavi → rebesavi → rebesrebesavi
     *
     * @return array{string, string}
     */
    private function bes(int $n, string $suffix, string $article): array
    {
        $nominative = ($n === 1 ? 'bes' : ($n > 3 ? $n . 'è ' : str_repeat('rebes', $n - 1))) . $suffix;

        return [$nominative, '%s ' . $article . $nominative];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {

        return [
            // Adopted
            Relationship::fixed(...$this->deLa('mare adoptiva'))->adoptive()->mother(),
            Relationship::fixed(...$this->del1('pare adoptiu'))->adoptive()->father(),
            Relationship::fixed(...$this->del1('pare/mare adoptiu/iva'))->adoptive()->parent(),
            Relationship::fixed(...$this->deLa('filla adoptiva'))->adopted()->daughter(),
            Relationship::fixed(...$this->del1('fill adoptiu'))->adopted()->son(),
            Relationship::fixed(...$this->del1('fill/a adoptiu/iva'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->deLa("mare d'acollida"))->fostering()->mother(),
            Relationship::fixed(...$this->del1("pare d'acollida"))->fostering()->father(),
            Relationship::fixed(...$this->del1("pare/mare d'acollida"))->fostering()->parent(),
            Relationship::fixed(...$this->deLa("filla d'acollida"))->fostered()->daughter(),
            Relationship::fixed(...$this->del1("fill d'acollida"))->fostered()->son(),
            Relationship::fixed(...$this->del1("fill/a d'acollida"))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->deLa('mare'))->mother(),
            Relationship::fixed(...$this->del1('pare'))->father(),
            Relationship::fixed(...$this->del1('pare/mare'))->parent(),
            // Children
            Relationship::fixed(...$this->deLa('filla'))->daughter(),
            Relationship::fixed(...$this->del1('fill'))->son(),
            Relationship::fixed(...$this->del1('fill/a'))->child(),
            // Siblings
            Relationship::fixed(...$this->deLa('germana bessona'))->multiple()->sister(),
            Relationship::fixed(...$this->del1('germà bessó'))->multiple()->brother(),
            Relationship::fixed(...$this->del1('bessó/bessona'))->multiple()->sibling(),
            Relationship::fixed(...$this->deLa('germana gran'))->older()->sister(),
            Relationship::fixed(...$this->del1('germà gran'))->older()->brother(),
            Relationship::fixed(...$this->del1('germà/germana gran'))->older()->sibling(),
            Relationship::fixed(...$this->deLa('germana petita'))->younger()->sister(),
            Relationship::fixed(...$this->del1('germà petit'))->younger()->brother(),
            Relationship::fixed(...$this->del1('germà/germana petit/a'))->younger()->sibling(),
            Relationship::fixed(...$this->deLa('germana'))->sister(),
            Relationship::fixed(...$this->del1('germà'))->brother(),
            Relationship::fixed(...$this->del1('germà/germana'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->deLa('germanastra'))->parent()->daughter(),
            Relationship::fixed(...$this->del1('germanastre'))->parent()->son(),
            Relationship::fixed(...$this->del1('germanastre/a'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->deLa('madrastra'))->parent()->wife(),
            Relationship::fixed(...$this->del1('padrastre'))->parent()->husband(),
            Relationship::fixed(...$this->del1('padrastre/madrastra'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->deLa('fillastra'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->del1('fillastre'))->married()->spouse()->son(),
            Relationship::fixed(...$this->del1('fillastre/a'))->married()->spouse()->child(),
            Relationship::fixed(...$this->deLa('germanastra'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$this->del1('germanastre'))->parent()->spouse()->son(),
            Relationship::fixed(...$this->del1('germanastre/a'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->del2('ex-esposa'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->del2('ex-espòs'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->del2('ex-cònjuge'))->divorced()->partner(),
            Relationship::fixed(...$this->deLa('promesa'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->del1('promès'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->del2('esposa'))->wife(),
            Relationship::fixed(...$this->del2('espòs'))->husband(),
            Relationship::fixed(...$this->del1('cònjuge'))->spouse(),
            Relationship::fixed(...$this->deLa('parella'))->partner(),
            // In-laws
            Relationship::fixed(...$this->deLa('sogra'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->del1('sogre'))->married()->spouse()->father(),
            Relationship::fixed(...$this->del1('sogre/a'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->deLa('nora'))->child()->wife(),
            Relationship::fixed(...$this->del1('gendre'))->child()->husband(),
            Relationship::fixed(...$this->del1('gendre/nora'))->child()->married()->spouse(),
            Relationship::fixed(...$this->deLa('cunyada'))->spouse()->sister(),
            Relationship::fixed(...$this->del1('cunyat'))->spouse()->brother(),
            Relationship::fixed(...$this->deLa('cunyada'))->sibling()->wife(),
            Relationship::fixed(...$this->del1('cunyat'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->del2('àvia'))->parent()->mother(),
            Relationship::fixed(...$this->del2('avi'))->parent()->father(),
            Relationship::fixed(...$this->del2('avi/àvia'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->deLa('néta'))->child()->daughter(),
            Relationship::fixed(...$this->del1('nét'))->child()->son(),
            Relationship::fixed(...$this->del1('nét/néta'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->deLa('tia'))->parent()->sister(),
            Relationship::fixed(...$this->del2('oncle'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->deLa('neboda'))->sibling()->daughter(),
            Relationship::fixed(...$this->del1('nebot'))->sibling()->son(),
            Relationship::fixed(...$this->deLa('neboda'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$this->del1('nebot'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$this->deLa('cosina'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->del1('cosí'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'àvia', "de l'"))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'avi', "de l'"))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'avi/àvia', "de l'"))->ancestor(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'néta', 'de la '))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'nét', 'del '))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'nét/néta', 'del '))->descendant(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'tia', 'de la '))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'oncle', "de l'"))->ancestor()->brother(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'neboda', 'de la '))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'neboda', 'de la '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'nebot', 'del '))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'nebot', 'del '))->married()->spouse()->sibling()->descendant()->male(),
        ];
    }
}
