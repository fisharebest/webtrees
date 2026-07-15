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
    public function relationships(): array
    {
        // Galician genitive: "da" (f), "do" (m)
        $da = static fn (string $s): array => [$s, '%s da ' . $s];
        $do = static fn (string $s): array => [$s, '%s do ' . $s];

        $great = static fn (int $n, string $suffix, string $article): array => [
            ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : ($n === 3 ? 'tetra' : $n . '°'))) . $suffix,
            '%s ' . $article . ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : ($n === 3 ? 'tetra' : $n . '°'))) . $suffix,
        ];

        return [
            // Adopted
            Relationship::fixed(...$da('nai adoptiva'))->adoptive()->mother(),
            Relationship::fixed(...$do('pai adoptivo'))->adoptive()->father(),
            Relationship::fixed(...$do('pai/nai adoptivo/a'))->adoptive()->parent(),
            Relationship::fixed(...$da('filla adoptiva'))->adopted()->daughter(),
            Relationship::fixed(...$do('fillo adoptivo'))->adopted()->son(),
            Relationship::fixed(...$do('fillo/a adoptivo/a'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$da('nai'))->mother(),
            Relationship::fixed(...$do('pai'))->father(),
            Relationship::fixed(...$do('pai/nai'))->parent(),
            // Children
            Relationship::fixed(...$da('filla'))->daughter(),
            Relationship::fixed(...$do('fillo'))->son(),
            Relationship::fixed(...$do('fillo/a'))->child(),
            // Siblings
            Relationship::fixed(...$da('irmá xemelga'))->twin()->sister(),
            Relationship::fixed(...$do('irmán xemelgo'))->twin()->brother(),
            Relationship::fixed(...$do('xemelgo/a'))->twin()->sibling(),
            Relationship::fixed(...$da('irmá maior'))->older()->sister(),
            Relationship::fixed(...$do('irmán maior'))->older()->brother(),
            Relationship::fixed(...$do('irmán/á maior'))->older()->sibling(),
            Relationship::fixed(...$da('irmá menor'))->younger()->sister(),
            Relationship::fixed(...$do('irmán menor'))->younger()->brother(),
            Relationship::fixed(...$do('irmán/á menor'))->younger()->sibling(),
            Relationship::fixed(...$da('irmá'))->sister(),
            Relationship::fixed(...$do('irmán'))->brother(),
            Relationship::fixed(...$do('irmán/á'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$da('media irmá'))->parent()->daughter(),
            Relationship::fixed(...$do('medio irmán'))->parent()->son(),
            Relationship::fixed(...$do('medio/a irmán/á'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$da('madrasta'))->parent()->wife(),
            Relationship::fixed(...$do('padrastro'))->parent()->husband(),
            Relationship::fixed(...$do('padrastro/madrasta'))->parent()->married()->spouse(),
            Relationship::fixed(...$da('enteada'))->married()->spouse()->daughter(),
            Relationship::fixed(...$do('enteado'))->married()->spouse()->son(),
            Relationship::fixed(...$do('enteado/a'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$da('ex-esposa'))->divorced()->partner()->female(),
            Relationship::fixed(...$do('ex-marido'))->divorced()->partner()->male(),
            Relationship::fixed(...$do('ex-cónxuxe'))->divorced()->partner(),
            Relationship::fixed(...$da('noiva'))->engaged()->partner()->female(),
            Relationship::fixed(...$do('noivo'))->engaged()->partner()->male(),
            Relationship::fixed(...$da('esposa'))->wife(),
            Relationship::fixed(...$do('marido'))->husband(),
            Relationship::fixed(...$do('cónxuxe'))->spouse(),
            Relationship::fixed(...$do('compañeiro/a'))->partner(),
            // In-laws
            Relationship::fixed(...$da('sogra'))->married()->spouse()->mother(),
            Relationship::fixed(...$do('sogro'))->married()->spouse()->father(),
            Relationship::fixed(...$do('sogro/a'))->married()->spouse()->parent(),
            Relationship::fixed(...$da('nora'))->child()->wife(),
            Relationship::fixed(...$do('xenro'))->child()->husband(),
            Relationship::fixed(...$do('xenro/nora'))->child()->married()->spouse(),
            Relationship::fixed(...$da('cuñada'))->spouse()->sister(),
            Relationship::fixed(...$do('cuñado'))->spouse()->brother(),
            Relationship::fixed(...$da('cuñada'))->sibling()->wife(),
            Relationship::fixed(...$do('cuñado'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$da('avoa'))->parent()->mother(),
            Relationship::fixed(...$do('avó'))->parent()->father(),
            Relationship::fixed(...$do('avó/avoa'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$da('neta'))->child()->daughter(),
            Relationship::fixed(...$do('neto'))->child()->son(),
            Relationship::fixed(...$do('neto/a'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$da('tía'))->parent()->sister(),
            Relationship::fixed(...$do('tío'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$da('sobriña'))->sibling()->daughter(),
            Relationship::fixed(...$do('sobriño'))->sibling()->son(),
            Relationship::fixed(...$da('sobriña'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$do('sobriño'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$da('curmá'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$do('curmán'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'avoa', 'da '))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'avó', 'do '))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'avó/avoa', 'do '))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'neta', 'da '))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'neto', 'do '))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'neto/a', 'do '))->descendant(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'tía', 'da '))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'tío', 'do '))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobriña', 'da '))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobriña', 'da '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobriño', 'do '))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobriño', 'do '))->married()->spouse()->sibling()->descendant()->male(),
        ];
    }
}
