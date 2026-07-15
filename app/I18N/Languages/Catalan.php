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
        'Gener',
        'Febrer',
        'Març',
        'Abril',
        'Maig',
        'Juny',
        'Juliol',
        'Agost',
        'Setembre',
        'Octubre',
        'Novembre',
        'Desembre',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tixrí',
        'Heixvan',
        'Quisleu',
        'Tevet',
        'Xevat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nisan',
        'Iar',
        'Sivan',
        'Tammuz',
        'Av',
        'Elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Vendemiari',
        'Bromari',
        'Rufolari',
        'Nivós',
        'Pluviós',
        'Ventós',
        'Germinal',
        'Floral',
        'Pradal',
        'Messidor',
        'Termidor',
        'Fructidor',
        'dies complementaris',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Muhàrram',
        'Sàfar',
        'Rabi’ al-awwal',
        'Rabi’ al-thani',
        'Jumada al-ula',
        'Jumada al-àkhira',
        'Ràjab',
        'Xaban',
        'Ramadà',
        'Xawwal',
        'Dhu-l-qada',
        'Dhu-l-hijja',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Farvardín',
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
        // Catalan genitive: "de la" (f), "del" (m), "de l'" (before vowel)
        $de_la = static fn (string $s): array => [$s, '%s de la ' . $s];
        $del   = static fn (string $s): array => [$s, '%s del ' . $s];
        $de_l  = static fn (string $s): array => [$s, "%s de l'" . $s];

        $great = static fn (int $n, string $suffix, string $article): array => [
            ($n === 1 ? 'bes' : ($n > 3 ? $n . 'è ' : str_repeat('rebes', $n - 1))) . $suffix,
            '%s ' . $article . ($n === 1 ? 'bes' : ($n > 3 ? $n . 'è ' : str_repeat('rebes', $n - 1))) . $suffix,
        ];

        return [
            // Adopted
            Relationship::fixed(...$de_la('mare adoptiva'))->adoptive()->mother(),
            Relationship::fixed(...$del('pare adoptiu'))->adoptive()->father(),
            Relationship::fixed(...$del('pare/mare adoptiu/iva'))->adoptive()->parent(),
            Relationship::fixed(...$de_la('filla adoptiva'))->adopted()->daughter(),
            Relationship::fixed(...$del('fill adoptiu'))->adopted()->son(),
            Relationship::fixed(...$del('fill/a adoptiu/iva'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$de_la("mare d'acollida"))->fostering()->mother(),
            Relationship::fixed(...$del("pare d'acollida"))->fostering()->father(),
            Relationship::fixed(...$del("pare/mare d'acollida"))->fostering()->parent(),
            Relationship::fixed(...$de_la("filla d'acollida"))->fostered()->daughter(),
            Relationship::fixed(...$del("fill d'acollida"))->fostered()->son(),
            Relationship::fixed(...$del("fill/a d'acollida"))->fostered()->child(),
            // Parents
            Relationship::fixed(...$de_la('mare'))->mother(),
            Relationship::fixed(...$del('pare'))->father(),
            Relationship::fixed(...$del('pare/mare'))->parent(),
            // Children
            Relationship::fixed(...$de_la('filla'))->daughter(),
            Relationship::fixed(...$del('fill'))->son(),
            Relationship::fixed(...$del('fill/a'))->child(),
            // Siblings
            Relationship::fixed(...$de_la('germana bessona'))->twin()->sister(),
            Relationship::fixed(...$del('germà bessó'))->twin()->brother(),
            Relationship::fixed(...$del('bessó/bessona'))->twin()->sibling(),
            Relationship::fixed(...$de_la('germana gran'))->older()->sister(),
            Relationship::fixed(...$del('germà gran'))->older()->brother(),
            Relationship::fixed(...$del('germà/germana gran'))->older()->sibling(),
            Relationship::fixed(...$de_la('germana petita'))->younger()->sister(),
            Relationship::fixed(...$del('germà petit'))->younger()->brother(),
            Relationship::fixed(...$del('germà/germana petit/a'))->younger()->sibling(),
            Relationship::fixed(...$de_la('germana'))->sister(),
            Relationship::fixed(...$del('germà'))->brother(),
            Relationship::fixed(...$del('germà/germana'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$de_la('germanastra'))->parent()->daughter(),
            Relationship::fixed(...$del('germanastre'))->parent()->son(),
            Relationship::fixed(...$del('germanastre/a'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$de_la('madrastra'))->parent()->wife(),
            Relationship::fixed(...$del('padrastre'))->parent()->husband(),
            Relationship::fixed(...$del('padrastre/madrastra'))->parent()->married()->spouse(),
            Relationship::fixed(...$de_la('fillastra'))->married()->spouse()->daughter(),
            Relationship::fixed(...$del('fillastre'))->married()->spouse()->son(),
            Relationship::fixed(...$del('fillastre/a'))->married()->spouse()->child(),
            Relationship::fixed(...$de_la('germanastra'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$del('germanastre'))->parent()->spouse()->son(),
            Relationship::fixed(...$del('germanastre/a'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$de_l('ex-esposa'))->divorced()->partner()->female(),
            Relationship::fixed(...$de_l('ex-espòs'))->divorced()->partner()->male(),
            Relationship::fixed(...$de_l('ex-cònjuge'))->divorced()->partner(),
            Relationship::fixed(...$de_la('promesa'))->engaged()->partner()->female(),
            Relationship::fixed(...$del('promès'))->engaged()->partner()->male(),
            Relationship::fixed(...$de_l('esposa'))->wife(),
            Relationship::fixed(...$de_l('espòs'))->husband(),
            Relationship::fixed(...$del('cònjuge'))->spouse(),
            Relationship::fixed(...$de_la('parella'))->partner(),
            // In-laws
            Relationship::fixed(...$de_la('sogra'))->married()->spouse()->mother(),
            Relationship::fixed(...$del('sogre'))->married()->spouse()->father(),
            Relationship::fixed(...$del('sogre/a'))->married()->spouse()->parent(),
            Relationship::fixed(...$de_la('nora'))->child()->wife(),
            Relationship::fixed(...$del('gendre'))->child()->husband(),
            Relationship::fixed(...$del('gendre/nora'))->child()->married()->spouse(),
            Relationship::fixed(...$de_la('cunyada'))->spouse()->sister(),
            Relationship::fixed(...$del('cunyat'))->spouse()->brother(),
            Relationship::fixed(...$de_la('cunyada'))->sibling()->wife(),
            Relationship::fixed(...$del('cunyat'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$de_l('àvia'))->parent()->mother(),
            Relationship::fixed(...$de_l('avi'))->parent()->father(),
            Relationship::fixed(...$de_l('avi/àvia'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$de_la('néta'))->child()->daughter(),
            Relationship::fixed(...$del('nét'))->child()->son(),
            Relationship::fixed(...$del('nét/néta'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$de_la('tia'))->parent()->sister(),
            Relationship::fixed(...$de_l('oncle'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$de_la('neboda'))->sibling()->daughter(),
            Relationship::fixed(...$del('nebot'))->sibling()->son(),
            Relationship::fixed(...$de_la('neboda'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$del('nebot'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$de_la('cosina'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$del('cosí'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'àvia', "de l'"))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'avi', "de l'"))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'avi/àvia', "de l'"))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'néta', 'de la '))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nét', 'del '))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nét/néta', 'del '))->descendant(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'tia', 'de la '))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'oncle', "de l'"))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'neboda', 'de la '))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'neboda', 'de la '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'nebot', 'del '))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'nebot', 'del '))->married()->spouse()->sibling()->descendant()->male(),
        ];
    }
}
