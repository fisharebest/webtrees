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

final readonly class Occitan extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsPluralForMoreThanOne;

    protected const string    ENDONYM            = 'occitan';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'oc';
    protected const string    LOCALE_CODE        = 'oc_FR@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_AFTER         = 'après %s';
    protected const string    DATE_FROM          = 'de %s';
    protected const string    DATE_FROM_TO       = 'de %s a %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'BC';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'AC';
    protected const string    LIST_SEPARATOR_AND = ' e ';
    protected const string    LIST_SEPARATOR_OR  = ' o ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'genièr',
        'febrièr',
        'març',
        'abril',
        'mai',
        'junh',
        'julhet',
        'agost',
        'setembre',
        'octobre',
        'novembre',
        'decembre',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

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
        'Nissan',
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
        'Rabi al-awwal',
        'Rabi al-thani',
        'Jumada-al-awwal',
        'Jumada-al-thani',
        'Rajab',
        'Sha’aban',
        'Ramadan',
        'Shawwal',
        'Dhu-al-Qi’dah',
        'Dhu-al-Hijjah',
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
        // Occitan genitive: "de la" (f), "del" (m), "de l'" (before vowel)
        $de_la = static fn (string $s): array => [$s, '%s de la ' . $s];
        $del   = static fn (string $s): array => [$s, '%s del ' . $s];
        $de_l  = static fn (string $s): array => [$s, "%s de l'" . $s];

        $great = static fn (int $n, string $suffix, string $article): array => [
            ($n === 1 ? 'bes' : ($n > 3 ? $n . 'au ' : str_repeat('rebes', $n - 1))) . $suffix,
            '%s ' . $article . ($n === 1 ? 'bes' : ($n > 3 ? $n . 'au ' : str_repeat('rebes', $n - 1))) . $suffix,
        ];

        return [
            // Adopted
            Relationship::fixed(...$de_la('maire adoptiva'))->adoptive()->mother(),
            Relationship::fixed(...$del('paire adoptiu'))->adoptive()->father(),
            Relationship::fixed(...$del('paire/maire adoptiu/iva'))->adoptive()->parent(),
            Relationship::fixed(...$de_la('filha adoptiva'))->adopted()->daughter(),
            Relationship::fixed(...$del('filh adoptiu'))->adopted()->son(),
            Relationship::fixed(...$del('filh/a adoptiu/iva'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$de_la("maire d'acòlhiment"))->fostering()->mother(),
            Relationship::fixed(...$del("paire d'acòlhiment"))->fostering()->father(),
            Relationship::fixed(...$del("paire/maire d'acòlhiment"))->fostering()->parent(),
            Relationship::fixed(...$de_la("filha d'acòlhiment"))->fostered()->daughter(),
            Relationship::fixed(...$del("filh d'acòlhiment"))->fostered()->son(),
            Relationship::fixed(...$del("filh/a d'acòlhiment"))->fostered()->child(),
            // Parents
            Relationship::fixed(...$de_la('maire'))->mother(),
            Relationship::fixed(...$del('paire'))->father(),
            Relationship::fixed(...$del('paire/maire'))->parent(),
            // Children
            Relationship::fixed(...$de_la('filha'))->daughter(),
            Relationship::fixed(...$del('filh'))->son(),
            Relationship::fixed(...$del('filh/a'))->child(),
            // Siblings
            Relationship::fixed(...$de_la('sòrre jumèla'))->twin()->sister(),
            Relationship::fixed(...$del('frair jumèl'))->twin()->brother(),
            Relationship::fixed(...$del('jumèl/a'))->twin()->sibling(),
            Relationship::fixed(...$de_la('sòrre granda'))->older()->sister(),
            Relationship::fixed(...$del('frair grand'))->older()->brother(),
            Relationship::fixed(...$del('frair/sòrre grand/a'))->older()->sibling(),
            Relationship::fixed(...$de_la('sòrre pichòta'))->younger()->sister(),
            Relationship::fixed(...$del('frair pichòt'))->younger()->brother(),
            Relationship::fixed(...$del('frair/sòrre pichòt/a'))->younger()->sibling(),
            Relationship::fixed(...$de_la('sòrre'))->sister(),
            Relationship::fixed(...$del('frair'))->brother(),
            Relationship::fixed(...$del('frair/sòrre'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$de_la('mièja-sòrre'))->parent()->daughter(),
            Relationship::fixed(...$del('mièg-frair'))->parent()->son(),
            Relationship::fixed(...$del('mièg-frair/sòrre'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$de_la('mairastra'))->parent()->wife(),
            Relationship::fixed(...$del('pairastra'))->parent()->husband(),
            Relationship::fixed(...$del('pairastra/mairastra'))->parent()->married()->spouse(),
            Relationship::fixed(...$de_la('filhastra'))->married()->spouse()->daughter(),
            Relationship::fixed(...$del('filhastre'))->married()->spouse()->son(),
            Relationship::fixed(...$del('filhastre/a'))->married()->spouse()->child(),
            Relationship::fixed(...$de_la('mièja-sòrre'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$del('mièg-frair'))->parent()->spouse()->son(),
            Relationship::fixed(...$del('mièg-frair/sòrre'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$de_l('ex-espòsa'))->divorced()->partner()->female(),
            Relationship::fixed(...$de_l('ex-espòs'))->divorced()->partner()->male(),
            Relationship::fixed(...$de_l('ex-cònjuge'))->divorced()->partner(),
            Relationship::fixed(...$de_la('promesa'))->engaged()->partner()->female(),
            Relationship::fixed(...$del('promès'))->engaged()->partner()->male(),
            Relationship::fixed(...$de_l('espòsa'))->wife(),
            Relationship::fixed(...$de_l('espòs'))->husband(),
            Relationship::fixed(...$del('cònjuge'))->spouse(),
            Relationship::fixed(...$de_la('parèlha'))->partner(),
            // In-laws
            Relationship::fixed(...$de_la('sògra'))->married()->spouse()->mother(),
            Relationship::fixed(...$del('sògre'))->married()->spouse()->father(),
            Relationship::fixed(...$del('sògre/a'))->married()->spouse()->parent(),
            Relationship::fixed(...$de_la('nòra'))->child()->wife(),
            Relationship::fixed(...$del('gendre'))->child()->husband(),
            Relationship::fixed(...$del('gendre/nòra'))->child()->married()->spouse(),
            Relationship::fixed(...$de_la('cònha'))->spouse()->sister(),
            Relationship::fixed(...$del('conhat'))->spouse()->brother(),
            Relationship::fixed(...$de_la('cònha'))->sibling()->wife(),
            Relationship::fixed(...$del('conhat'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$de_l('aviòla'))->parent()->mother(),
            Relationship::fixed(...$de_l('aviol'))->parent()->father(),
            Relationship::fixed(...$de_l('aviol/a'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$de_la('petita-filha'))->child()->daughter(),
            Relationship::fixed(...$del('petit-filh'))->child()->son(),
            Relationship::fixed(...$del('petit-filh/a'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$de_la('tanta'))->parent()->sister(),
            Relationship::fixed(...$de_l('òncle'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$de_la('nebòda'))->sibling()->daughter(),
            Relationship::fixed(...$del('nebot'))->sibling()->son(),
            Relationship::fixed(...$de_la('nebòda'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$del('nebot'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$de_la('cosina'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$del('cosin'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'aviòla', "de l'"))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'aviol', "de l'"))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'aviol/a', "de l'"))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'petita-filha', 'de la '))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'petit-filh', 'del '))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'petit-filh/a', 'del '))->descendant(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'tanta', 'de la '))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'òncle', "de l'"))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'nebòda', 'de la '))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'nebòda', 'de la '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'nebot', 'del '))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'nebot', 'del '))->married()->spouse()->sibling()->descendant()->male(),
        ];
    }
}
