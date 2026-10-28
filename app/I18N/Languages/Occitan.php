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
    protected const string    DIGITS_SEPARATOR   = UTF8::NARROW_NO_BREAK_SPACE;
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
        'tishrei',
        'heshvan',
        'kislev',
        'tevet',
        'shevat',
        'adar I',
        'adar II',
        'adar',
        'nissan',
        'iyar',
        'sivan',
        'tamuz',
        'av',
        'elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'vendémiaire',
        'brumaire',
        'frimaire',
        'nivôse',
        'pluviôse',
        'ventôse',
        'germinal',
        'floréal',
        'prairial',
        'messidor',
        'thermidor',
        'fructidor',
        'jours complémentaires',
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
        'jumada-al-awwal',
        'jumada-al-thani',
        'rajab',
        'sha’aban',
        'ramadan',
        'shawwal',
        'dhu-al-qi’dah',
        'dhu-al-hijjah',
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

    protected function formatFromDate(string $date): string
    {
        if ($this->startsWithVowel($date)) {
            return 'd’' . $date;
        }

        return 'de ' . $date;
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
     * using the Occitan "bes/rebes" prefix pattern.
     *
     * aviol → besaviol → rebesaviol → rebesrebesaviol
     *
     * @return array{string, string}
     */
    private function bes(int $n, string $suffix, string $article): array
    {
        $nominative = ($n === 1 ? 'bes' : ($n > 3 ? $n . 'au ' : str_repeat('rebes', $n - 1))) . $suffix;

        return [$nominative, '%s ' . $article . $nominative];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {

        return [
            // Adopted
            Relationship::fixed(...$this->deLa('maire adoptiva'))->adoptive()->mother(),
            Relationship::fixed(...$this->del1('paire adoptiu'))->adoptive()->father(),
            Relationship::fixed(...$this->del1('paire/maire adoptiu/iva'))->adoptive()->parent(),
            Relationship::fixed(...$this->deLa('filha adoptiva'))->adopted()->daughter(),
            Relationship::fixed(...$this->del1('filh adoptiu'))->adopted()->son(),
            Relationship::fixed(...$this->del1('filh/a adoptiu/iva'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->deLa("maire d'acòlhiment"))->fostering()->mother(),
            Relationship::fixed(...$this->del1("paire d'acòlhiment"))->fostering()->father(),
            Relationship::fixed(...$this->del1("paire/maire d'acòlhiment"))->fostering()->parent(),
            Relationship::fixed(...$this->deLa("filha d'acòlhiment"))->fostered()->daughter(),
            Relationship::fixed(...$this->del1("filh d'acòlhiment"))->fostered()->son(),
            Relationship::fixed(...$this->del1("filh/a d'acòlhiment"))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->deLa('maire'))->mother(),
            Relationship::fixed(...$this->del1('paire'))->father(),
            Relationship::fixed(...$this->del1('paire/maire'))->parent(),
            // Children
            Relationship::fixed(...$this->deLa('filha'))->daughter(),
            Relationship::fixed(...$this->del1('filh'))->son(),
            Relationship::fixed(...$this->del1('filh/a'))->child(),
            // Siblings
            Relationship::fixed(...$this->deLa('sòrre jumèla'))->multiple()->sister(),
            Relationship::fixed(...$this->del1('frair jumèl'))->multiple()->brother(),
            Relationship::fixed(...$this->del1('jumèl/a'))->multiple()->sibling(),
            Relationship::fixed(...$this->deLa('sòrre granda'))->older()->sister(),
            Relationship::fixed(...$this->del1('frair grand'))->older()->brother(),
            Relationship::fixed(...$this->del1('frair/sòrre grand/a'))->older()->sibling(),
            Relationship::fixed(...$this->deLa('sòrre pichòta'))->younger()->sister(),
            Relationship::fixed(...$this->del1('frair pichòt'))->younger()->brother(),
            Relationship::fixed(...$this->del1('frair/sòrre pichòt/a'))->younger()->sibling(),
            Relationship::fixed(...$this->deLa('sòrre'))->sister(),
            Relationship::fixed(...$this->del1('frair'))->brother(),
            Relationship::fixed(...$this->del1('frair/sòrre'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->deLa('mièja-sòrre'))->parent()->daughter(),
            Relationship::fixed(...$this->del1('mièg-frair'))->parent()->son(),
            Relationship::fixed(...$this->del1('mièg-frair/sòrre'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->deLa('mairastra'))->parent()->wife(),
            Relationship::fixed(...$this->del1('pairastra'))->parent()->husband(),
            Relationship::fixed(...$this->del1('pairastra/mairastra'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->deLa('filhastra'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->del1('filhastre'))->married()->spouse()->son(),
            Relationship::fixed(...$this->del1('filhastre/a'))->married()->spouse()->child(),
            Relationship::fixed(...$this->deLa('mièja-sòrre'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$this->del1('mièg-frair'))->parent()->spouse()->son(),
            Relationship::fixed(...$this->del1('mièg-frair/sòrre'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->del2('ex-espòsa'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->del2('ex-espòs'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->del2('ex-cònjuge'))->divorced()->partner(),
            Relationship::fixed(...$this->deLa('promesa'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->del1('promès'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->del2('espòsa'))->wife(),
            Relationship::fixed(...$this->del2('espòs'))->husband(),
            Relationship::fixed(...$this->del1('cònjuge'))->spouse(),
            Relationship::fixed(...$this->deLa('parèlha'))->partner(),
            // In-laws
            Relationship::fixed(...$this->deLa('sògra'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->del1('sògre'))->married()->spouse()->father(),
            Relationship::fixed(...$this->del1('sògre/a'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->deLa('nòra'))->child()->wife(),
            Relationship::fixed(...$this->del1('gendre'))->child()->husband(),
            Relationship::fixed(...$this->del1('gendre/nòra'))->child()->married()->spouse(),
            Relationship::fixed(...$this->deLa('cònha'))->spouse()->sister(),
            Relationship::fixed(...$this->del1('conhat'))->spouse()->brother(),
            Relationship::fixed(...$this->deLa('cònha'))->sibling()->wife(),
            Relationship::fixed(...$this->del1('conhat'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->del2('aviòla'))->parent()->mother(),
            Relationship::fixed(...$this->del2('aviol'))->parent()->father(),
            Relationship::fixed(...$this->del2('aviol/a'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->deLa('petita-filha'))->child()->daughter(),
            Relationship::fixed(...$this->del1('petit-filh'))->child()->son(),
            Relationship::fixed(...$this->del1('petit-filh/a'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->deLa('tanta'))->parent()->sister(),
            Relationship::fixed(...$this->del2('òncle'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->deLa('nebòda'))->sibling()->daughter(),
            Relationship::fixed(...$this->del1('nebot'))->sibling()->son(),
            Relationship::fixed(...$this->deLa('nebòda'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$this->del1('nebot'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$this->deLa('cosina'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->del1('cosin'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'aviòla', "de l'"))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'aviol', "de l'"))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'aviol/a', "de l'"))->ancestor(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'petita-filha', 'de la '))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'petit-filh', 'del '))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 2, 'petit-filh/a', 'del '))->descendant(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'tanta', 'de la '))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'òncle', "de l'"))->ancestor()->brother(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'nebòda', 'de la '))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'nebòda', 'de la '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'nebot', 'del '))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->bes($n - 1, 'nebot', 'del '))->married()->spouse()->sibling()->descendant()->male(),
        ];
    }
}
