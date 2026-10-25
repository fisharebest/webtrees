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

final readonly class Italian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'italiano';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'it';
    protected const string    LOCALE_CODE        = 'it_IT@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'circa %s';
    protected const string    DATE_AFTER         = 'dopo il %s';
    protected const string    DATE_BEFORE        = 'prima del %s';
    protected const string    DATE_BETWEEN_AND   = 'tra il %s e il %s';
    protected const string    DATE_CALCULATED    = '%s (calcolata)';
    protected const string    DATE_ESTIMATED     = '%s (stimata)';
    protected const string    DATE_FROM          = 'dal %s';
    protected const string    DATE_FROM_TO       = 'dal %s al %s';
    protected const string    DATE_INTERPRETED   = 'interpretato %s';
    protected const string    DATE_TO            = 'fino al %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'a.C.';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'd.C.';
    protected const string    LIST_SEPARATOR_AND = ' e ';
    protected const string    LIST_SEPARATOR_OR  = ' o ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'gennaio',
        'febbraio',
        'marzo',
        'aprile',
        'maggio',
        'giugno',
        'luglio',
        'agosto',
        'settembre',
        'ottobre',
        'novembre',
        'dicembre',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'tishrì',
        'cheshvàn',
        'kislèv',
        'tevèt',
        'shevàt',
        'adàr I',
        'adr II',
        'adàr',
        'nisàn',
        'iyàr',
        'sivàn',
        'tamùz',
        'av',
        'elùl',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'vendemmiaio',
        'brumaio',
        'frimaio',
        'nevoso',
        'piovoso',
        'ventoso',
        'germinale',
        'floreale',
        'pratile',
        'messidoro',
        'termidoro',
        'fruttidoro',
        'giorni complementari',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'muharram',
        'safar',
        'rabi al-awwal',
        'rabi al-thani',
        'jumada al-awwal',
        'jumada al-thani',
        'rajab',
        'shaaban',
        'ramadan',
        'shawwal',
        'dhu al-qida',
        'dhu al-hijja',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'farvardin',
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
            return 'dopo l’' . $date;
        }

        return 'dopo il ' . $date;
    }

    protected function formatBeforeDate(string $date): string
    {
        if ($this->startsWithVowel($date)) {
            return 'prima dell’' . $date;
        }

        return 'prima del ' . $date;
    }

    protected function formatBetweenDate(string $date1, string $date2): string
    {
        if ($this->startsWithVowel($date1)) {
            $first = 'l’' . $date1;
        } else {
            $first = 'il ' . $date1;
        }

        if ($this->startsWithVowel($date2)) {
            $second = 'l’' . $date2;
        } else {
            $second = 'il ' . $date2;
        }

        return 'tra ' . $first . ' e ' . $second;
    }

    protected function formatFromDate(string $date): string
    {
        if ($this->startsWithVowel($date)) {
            return 'dall’' . $date;
        }

        return 'dal ' . $date;
    }

    protected function formatFromToDate(string $date1, string $date2): string
    {
        if ($this->startsWithVowel($date1)) {
            $first = 'dall’' . $date1;
        } else {
            $first = 'dal ' . $date1;
        }

        if ($this->startsWithVowel($date2)) {
            $second = 'all’' . $date2;
        } else {
            $second = 'al ' . $date2;
        }

        return $first . ' ' . $second;
    }

    protected function formatToDate(string $date): string
    {
        if ($this->startsWithVowel($date)) {
            return 'fino all’' . $date;
        }

        return 'fino al ' . $date;
    }

    /**
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function della(string $s): array
    {
        return [$s, '%s della ' . $s];
    }

    /** @return array{string, string} */
    private function del(string $s): array
    {
        return [$s, '%s del ' . $s];
    }

    /** @return array{string, string} */
    private function dello(string $s): array
    {
        return [$s, '%s dello ' . $s];
    }

    /** @return array{string, string} */
    private function dell(string $s): array
    {
        return [$s, "%s dell'" . $s];
    }

    /** @return array{string, string} */
    private function great(int $n, string $suffix, string $article): array
    {
        return [
            ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : $n . '°')) . $suffix,
            '%s ' . $article . ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : $n . '°')) . $suffix,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->della('madre adottiva'))->adoptive()->mother(),
            Relationship::fixed(...$this->del('padre adottivo'))->adoptive()->father(),
            Relationship::fixed(...$this->del('genitore adottivo'))->adoptive()->parent(),
            Relationship::fixed(...$this->della('figlia adottiva'))->adopted()->daughter(),
            Relationship::fixed(...$this->del('figlio adottivo'))->adopted()->son(),
            Relationship::fixed(...$this->del('figlio/a adottivo/a'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$this->della('madre'))->mother(),
            Relationship::fixed(...$this->del('padre'))->father(),
            Relationship::fixed(...$this->del('genitore'))->parent(),
            // Children
            Relationship::fixed(...$this->della('figlia'))->daughter(),
            Relationship::fixed(...$this->del('figlio'))->son(),
            Relationship::fixed(...$this->del('figlio/a'))->child(),
            // Siblings
            Relationship::fixed(...$this->della('sorella gemella'))->multiple()->sister(),
            Relationship::fixed(...$this->del('fratello gemello'))->multiple()->brother(),
            Relationship::fixed(...$this->del('gemello/a'))->multiple()->sibling(),
            Relationship::fixed(...$this->della('sorella maggiore'))->older()->sister(),
            Relationship::fixed(...$this->del('fratello maggiore'))->older()->brother(),
            Relationship::fixed(...$this->del('fratello/sorella maggiore'))->older()->sibling(),
            Relationship::fixed(...$this->della('sorella minore'))->younger()->sister(),
            Relationship::fixed(...$this->del('fratello minore'))->younger()->brother(),
            Relationship::fixed(...$this->del('fratello/sorella minore'))->younger()->sibling(),
            Relationship::fixed(...$this->della('sorella'))->sister(),
            Relationship::fixed(...$this->del('fratello'))->brother(),
            Relationship::fixed(...$this->del('fratello/sorella'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->della('sorellastra'))->parent()->daughter(),
            Relationship::fixed(...$this->del('fratellastro'))->parent()->son(),
            Relationship::fixed(...$this->del('fratellastro/sorellastra'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->della('matrigna'))->parent()->wife(),
            Relationship::fixed(...$this->del('patrigno'))->parent()->husband(),
            Relationship::fixed(...$this->del('patrigno/matrigna'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->della('figliastra'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->del('figliastro'))->married()->spouse()->son(),
            Relationship::fixed(...$this->del('figliastro/a'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->dell('ex-moglie'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->dell('ex-marito'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->dell('ex-coniuge'))->divorced()->partner(),
            Relationship::fixed(...$this->della('fidanzata'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->del('fidanzato'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->della('moglie'))->wife(),
            Relationship::fixed(...$this->del('marito'))->husband(),
            Relationship::fixed(...$this->del('coniuge'))->spouse(),
            Relationship::fixed(...$this->del('partner'))->partner(),
            // In-laws
            Relationship::fixed(...$this->della('suocera'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->del('suocero'))->married()->spouse()->father(),
            Relationship::fixed(...$this->del('suocero/a'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->della('nuora'))->child()->wife(),
            Relationship::fixed(...$this->del('genero'))->child()->husband(),
            Relationship::fixed(...$this->del('genero/nuora'))->child()->married()->spouse(),
            Relationship::fixed(...$this->della('cognata'))->spouse()->sister(),
            Relationship::fixed(...$this->del('cognato'))->spouse()->brother(),
            Relationship::fixed(...$this->della('cognata'))->sibling()->wife(),
            Relationship::fixed(...$this->del('cognato'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->della('nonna'))->parent()->mother(),
            Relationship::fixed(...$this->del('nonno'))->parent()->father(),
            Relationship::fixed(...$this->del('nonno/a'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->della('nipote'))->child()->daughter(),
            Relationship::fixed(...$this->del('nipote'))->child()->son(),
            Relationship::fixed(...$this->del('nipote'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->della('zia'))->parent()->sister(),
            Relationship::fixed(...$this->dello('zio'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->della('nipote'))->sibling()->daughter(),
            Relationship::fixed(...$this->del('nipote'))->sibling()->son(),
            // Cousins
            Relationship::fixed(...$this->della('cugina'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->del('cugino'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'nonna', 'della '))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'nonno', 'del '))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'nonno/a', 'del '))->ancestor(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'nipote', 'della '))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'nipote', 'del '))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'nipote', 'del '))->descendant(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'zia', 'della '))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'zio', 'dello '))->ancestor()->brother(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'nipote', 'della '))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'nipote', 'del '))->sibling()->descendant()->male(),
        ];
    }
}
