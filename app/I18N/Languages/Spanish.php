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

final readonly class Spanish extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'espaol';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'es';
    protected const string    LOCALE_CODE        = 'es_ES@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'sobre %s';
    protected const string    DATE_AFTER         = 'después de %s';
    protected const string    DATE_BEFORE        = 'antes de %s';
    protected const string    DATE_BETWEEN_AND   = 'entre %s y %s';
    protected const string    DATE_CALCULATED    = '%s calculadas';
    protected const string    DATE_ESTIMATED     = '%s estimadas';
    protected const string    DATE_FROM          = 'desde %s';
    protected const string    DATE_FROM_TO       = 'desde %s hasta %s';
    protected const string    DATE_INTERPRETED   = '%s interpretadas';
    protected const string    DATE_TO            = 'hasta %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'AEC';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'EC';
    protected const string    LIST_SEPARATOR_AND = ' y ';
    protected const string    LIST_SEPARATOR_OR  = ' o ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'enero',
        'febrero',
        'marzo',
        'abril',
        'mayo',
        'junio',
        'julio',
        'agosto',
        'septiembre',
        'octubre',
        'noviembre',
        'diciembre',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'tishrei',
        'jeshván',
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
        'vendimiario',
        'brumario',
        'frimario',
        'nivoso',
        'pluvioso',
        'ventoso',
        'germinal',
        'floreal',
        'pradial',
        'messidor',
        'termidor',
        'fructidor',
        'días complementarios',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Muharram',
        'Safar',
        'Rabi al-Awwal',
        'Rabi al-Thani',
        'Jumada I-Üla',
        'Jumada I-Akhira',
        'Rajab',
        'Shaabán',
        'Ramadán',
        'Shawwal',
        'Zu I-Qada',
        'Zu I-Hijja',
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
        UTF8::LATIN_CAPITAL_LETTER_N_WITH_TILDE,
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
    ];

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'N' . UTF8::COMBINING_TILDE => UTF8::LATIN_CAPITAL_LETTER_N_WITH_TILDE,
            'n' . UTF8::COMBINING_TILDE => UTF8::LATIN_SMALL_LETTER_N_WITH_TILDE,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Spanish genitive: "de la" (f), "del" (m)
        $de_la = static fn (string $s): array => [$s, '%s de la ' . $s];
        $del   = static fn (string $s): array => [$s, '%s del ' . $s];

        $great = static fn (int $n, string $suffix, string $article): array => [
            ($n === 1 ? 'bis' : ($n === 2 ? 'tatara' : ($n > 3 ? $n . '° ' : str_repeat('tatara', $n - 1)))) . $suffix,
            '%s ' . $article . ($n === 1 ? 'bis' : ($n === 2 ? 'tatara' : ($n > 3 ? $n . '° ' : str_repeat('tatara', $n - 1)))) . $suffix,
        ];

        return [
            // Adopted
            Relationship::fixed(...$de_la('madre adoptiva'))->adoptive()->mother(),
            Relationship::fixed(...$del('padre adoptivo'))->adoptive()->father(),
            Relationship::fixed(...$del('padre/madre adoptivo/a'))->adoptive()->parent(),
            Relationship::fixed(...$de_la('hija adoptiva'))->adopted()->daughter(),
            Relationship::fixed(...$del('hijo adoptivo'))->adopted()->son(),
            Relationship::fixed(...$del('hijo/a adoptivo/a'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$de_la('madre de acogida'))->fostering()->mother(),
            Relationship::fixed(...$del('padre de acogida'))->fostering()->father(),
            Relationship::fixed(...$del('padre/madre de acogida'))->fostering()->parent(),
            Relationship::fixed(...$de_la('hija de acogida'))->fostered()->daughter(),
            Relationship::fixed(...$del('hijo de acogida'))->fostered()->son(),
            Relationship::fixed(...$del('hijo/a de acogida'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$de_la('madre'))->mother(),
            Relationship::fixed(...$del('padre'))->father(),
            Relationship::fixed(...$del('padre/madre'))->parent(),
            // Children
            Relationship::fixed(...$de_la('hija'))->daughter(),
            Relationship::fixed(...$del('hijo'))->son(),
            Relationship::fixed(...$del('hijo/a'))->child(),
            // Siblings
            Relationship::fixed(...$de_la('hermana gemela'))->twin()->sister(),
            Relationship::fixed(...$del('hermano gemelo'))->twin()->brother(),
            Relationship::fixed(...$del('gemelo/a'))->twin()->sibling(),
            Relationship::fixed(...$de_la('hermana mayor'))->older()->sister(),
            Relationship::fixed(...$del('hermano mayor'))->older()->brother(),
            Relationship::fixed(...$del('hermano/a mayor'))->older()->sibling(),
            Relationship::fixed(...$de_la('hermana menor'))->younger()->sister(),
            Relationship::fixed(...$del('hermano menor'))->younger()->brother(),
            Relationship::fixed(...$del('hermano/a menor'))->younger()->sibling(),
            Relationship::fixed(...$de_la('hermana'))->sister(),
            Relationship::fixed(...$del('hermano'))->brother(),
            Relationship::fixed(...$del('hermano/a'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$de_la('media hermana'))->parent()->daughter(),
            Relationship::fixed(...$del('medio hermano'))->parent()->son(),
            Relationship::fixed(...$del('medio/a hermano/a'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$de_la('madrastra'))->parent()->wife(),
            Relationship::fixed(...$del('padrastro'))->parent()->husband(),
            Relationship::fixed(...$del('padrastro/madrastra'))->parent()->married()->spouse(),
            Relationship::fixed(...$de_la('hijastra'))->married()->spouse()->daughter(),
            Relationship::fixed(...$del('hijastro'))->married()->spouse()->son(),
            Relationship::fixed(...$del('hijastro/a'))->married()->spouse()->child(),
            Relationship::fixed(...$de_la('hermanastra'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$del('hermanastro'))->parent()->spouse()->son(),
            Relationship::fixed(...$del('hermanastro/a'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$de_la('ex-esposa'))->divorced()->partner()->female(),
            Relationship::fixed(...$del('ex-esposo'))->divorced()->partner()->male(),
            Relationship::fixed(...$del('ex-cónyuge'))->divorced()->partner(),
            Relationship::fixed(...$de_la('prometida'))->engaged()->partner()->female(),
            Relationship::fixed(...$del('prometido'))->engaged()->partner()->male(),
            Relationship::fixed(...$de_la('esposa'))->wife(),
            Relationship::fixed(...$del('esposo'))->husband(),
            Relationship::fixed(...$del('cónyuge'))->spouse(),
            Relationship::fixed(...$de_la('pareja'))->partner(),
            // In-laws
            Relationship::fixed(...$de_la('suegra'))->married()->spouse()->mother(),
            Relationship::fixed(...$del('suegro'))->married()->spouse()->father(),
            Relationship::fixed(...$del('suegro/a'))->married()->spouse()->parent(),
            Relationship::fixed(...$de_la('nuera'))->child()->wife(),
            Relationship::fixed(...$del('yerno'))->child()->husband(),
            Relationship::fixed(...$del('yerno/nuera'))->child()->married()->spouse(),
            Relationship::fixed(...$de_la('cuñada'))->spouse()->sister(),
            Relationship::fixed(...$del('cuñado'))->spouse()->brother(),
            Relationship::fixed(...$de_la('cuñada'))->sibling()->wife(),
            Relationship::fixed(...$del('cuñado'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$de_la('abuela'))->parent()->mother(),
            Relationship::fixed(...$del('abuelo'))->parent()->father(),
            Relationship::fixed(...$del('abuelo/a'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$de_la('nieta'))->child()->daughter(),
            Relationship::fixed(...$del('nieto'))->child()->son(),
            Relationship::fixed(...$del('nieto/a'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$de_la('tía'))->parent()->sister(),
            Relationship::fixed(...$del('tío'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$de_la('sobrina'))->sibling()->daughter(),
            Relationship::fixed(...$del('sobrino'))->sibling()->son(),
            Relationship::fixed(...$de_la('sobrina'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$del('sobrino'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$de_la('prima'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$del('primo'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'abuela', 'de la '))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'abuelo', 'del '))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'abuelo/a', 'del '))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nieta', 'de la '))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nieto', 'del '))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nieto/a', 'del '))->descendant(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'tía', 'de la '))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'tío', 'del '))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobrina', 'de la '))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobrina', 'de la '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobrino', 'del '))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobrino', 'del '))->married()->spouse()->sibling()->descendant()->male(),
        ];
    }
}
