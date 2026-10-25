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

final readonly class Galician extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'galego';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'gl';
    protected const string    LOCALE_CODE        = 'gl_ES@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    LIST_SEPARATOR_AND = ' e ';
    protected const string    LIST_SEPARATOR_OR  = ' ou ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'xaneiro',
        'febreiro',
        'marzo',
        'abril',
        'maio',
        'xuño',
        'xullo',
        'agosto',
        'setembro',
        'outubro',
        'novembro',
        'decembro',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'tishrei',
        'heshván',
        'kislev',
        'tevet',
        'shevat',
        'adar I',
        'adar II',
        'adar',
        'nisán',
        'iyar',
        'siván',
        'tamuz',
        'av',
        'elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'vendimario',
        'brumario',
        'frimario',
        'nivoso',
        'pluvioso',
        'ventoso',
        'xerminal',
        'floreal',
        'pradial',
        'mesidor',
        'termidor',
        'frutidor',
        'días complementarios',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Muharram',
        'Safar',
        'Rabiʿ al-awwal',
        'Rabiʿ al-thani',
        'Jumada al-awwal',
        'Jumada al-thani',
        'Rajab',
        'Shaʿbán',
        'Ramadán',
        'Shawwal',
        'Dhu al-Qiʿdah',
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
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function da(string $s): array
    {
        return [$s, '%s da ' . $s];
    }

    /** @return array{string, string} */
    private function do(string $s): array
    {
        return [$s, '%s do ' . $s];
    }

    /** @return array{string, string} */
    private function great(int $n, string $suffix, string $article): array
    {
        return [
            ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : ($n === 3 ? 'tetra' : $n . '°'))) . $suffix,
            '%s ' . $article . ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : ($n === 3 ? 'tetra' : $n . '°'))) . $suffix,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->da('nai adoptiva'))->adoptive()->mother(),
            Relationship::fixed(...$this->do('pai adoptivo'))->adoptive()->father(),
            Relationship::fixed(...$this->do('pai/nai adoptivo/a'))->adoptive()->parent(),
            Relationship::fixed(...$this->da('filla adoptiva'))->adopted()->daughter(),
            Relationship::fixed(...$this->do('fillo adoptivo'))->adopted()->son(),
            Relationship::fixed(...$this->do('fillo/a adoptivo/a'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$this->da('nai'))->mother(),
            Relationship::fixed(...$this->do('pai'))->father(),
            Relationship::fixed(...$this->do('pai/nai'))->parent(),
            // Children
            Relationship::fixed(...$this->da('filla'))->daughter(),
            Relationship::fixed(...$this->do('fillo'))->son(),
            Relationship::fixed(...$this->do('fillo/a'))->child(),
            // Siblings
            Relationship::fixed(...$this->da('irmá xemelga'))->multiple()->sister(),
            Relationship::fixed(...$this->do('irmán xemelgo'))->multiple()->brother(),
            Relationship::fixed(...$this->do('xemelgo/a'))->multiple()->sibling(),
            Relationship::fixed(...$this->da('irmá maior'))->older()->sister(),
            Relationship::fixed(...$this->do('irmán maior'))->older()->brother(),
            Relationship::fixed(...$this->do('irmán/á maior'))->older()->sibling(),
            Relationship::fixed(...$this->da('irmá menor'))->younger()->sister(),
            Relationship::fixed(...$this->do('irmán menor'))->younger()->brother(),
            Relationship::fixed(...$this->do('irmán/á menor'))->younger()->sibling(),
            Relationship::fixed(...$this->da('irmá'))->sister(),
            Relationship::fixed(...$this->do('irmán'))->brother(),
            Relationship::fixed(...$this->do('irmán/á'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->da('media irmá'))->parent()->daughter(),
            Relationship::fixed(...$this->do('medio irmán'))->parent()->son(),
            Relationship::fixed(...$this->do('medio/a irmán/á'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->da('madrasta'))->parent()->wife(),
            Relationship::fixed(...$this->do('padrastro'))->parent()->husband(),
            Relationship::fixed(...$this->do('padrastro/madrasta'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->da('enteada'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->do('enteado'))->married()->spouse()->son(),
            Relationship::fixed(...$this->do('enteado/a'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->da('ex-esposa'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->do('ex-marido'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->do('ex-cónxuxe'))->divorced()->partner(),
            Relationship::fixed(...$this->da('noiva'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->do('noivo'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->da('esposa'))->wife(),
            Relationship::fixed(...$this->do('marido'))->husband(),
            Relationship::fixed(...$this->do('cónxuxe'))->spouse(),
            Relationship::fixed(...$this->do('compañeiro/a'))->partner(),
            // In-laws
            Relationship::fixed(...$this->da('sogra'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->do('sogro'))->married()->spouse()->father(),
            Relationship::fixed(...$this->do('sogro/a'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->da('nora'))->child()->wife(),
            Relationship::fixed(...$this->do('xenro'))->child()->husband(),
            Relationship::fixed(...$this->do('xenro/nora'))->child()->married()->spouse(),
            Relationship::fixed(...$this->da('cuñada'))->spouse()->sister(),
            Relationship::fixed(...$this->do('cuñado'))->spouse()->brother(),
            Relationship::fixed(...$this->da('cuñada'))->sibling()->wife(),
            Relationship::fixed(...$this->do('cuñado'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->da('avoa'))->parent()->mother(),
            Relationship::fixed(...$this->do('avó'))->parent()->father(),
            Relationship::fixed(...$this->do('avó/avoa'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->da('neta'))->child()->daughter(),
            Relationship::fixed(...$this->do('neto'))->child()->son(),
            Relationship::fixed(...$this->do('neto/a'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->da('tía'))->parent()->sister(),
            Relationship::fixed(...$this->do('tío'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->da('sobriña'))->sibling()->daughter(),
            Relationship::fixed(...$this->do('sobriño'))->sibling()->son(),
            Relationship::fixed(...$this->da('sobriña'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$this->do('sobriño'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$this->da('curmá'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->do('curmán'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'avoa', 'da '))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'avó', 'do '))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'avó/avoa', 'do '))->ancestor(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'neta', 'da '))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'neto', 'do '))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'neto/a', 'do '))->descendant(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'tía', 'da '))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'tío', 'do '))->ancestor()->brother(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'sobriña', 'da '))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'sobriña', 'da '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'sobriño', 'do '))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'sobriño', 'do '))->married()->spouse()->sibling()->descendant()->male(),
        ];
    }
}
