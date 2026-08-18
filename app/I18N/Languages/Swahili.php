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

use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Swahili extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'Kiswahili';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'sw';
    protected const string    LOCALE_CODE        = 'sw_TZ@collation=phonebook';
    protected const string    LIST_SEPARATOR_AND = ' na ';
    protected const string    LIST_SEPARATOR_OR  = ' au ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Januari',
        'Februari',
        'Machi',
        'Aprili',
        'Mei',
        'Juni',
        'Julai',
        'Agosti',
        'Septemba',
        'Oktoba',
        'Novemba',
        'Desemba',
    ];

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

        /** @return array{string, string} */
    private function sw(string $s): array
    {
        return [$s, '%s wa ' . $s];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->sw('mama mlezi'))->adoptive()->mother(),
            Relationship::fixed(...$this->sw('baba mlezi'))->adoptive()->father(),
            Relationship::fixed(...$this->sw('mzazi mlezi'))->adoptive()->parent(),
            Relationship::fixed(...$this->sw('binti wa kuasili'))->adopted()->daughter(),
            Relationship::fixed(...$this->sw('mwana wa kuasili'))->adopted()->son(),
            Relationship::fixed(...$this->sw('mtoto wa kuasili'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->sw('mama mlea'))->fostering()->mother(),
            Relationship::fixed(...$this->sw('baba mlea'))->fostering()->father(),
            Relationship::fixed(...$this->sw('mzazi mlea'))->fostering()->parent(),
            Relationship::fixed(...$this->sw('binti mlea'))->fostered()->daughter(),
            Relationship::fixed(...$this->sw('mwana mlea'))->fostered()->son(),
            Relationship::fixed(...$this->sw('mtoto mlea'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->sw('mama'))->mother(),
            Relationship::fixed(...$this->sw('baba'))->father(),
            Relationship::fixed(...$this->sw('mzazi'))->parent(),
            // Children
            Relationship::fixed(...$this->sw('binti'))->daughter(),
            Relationship::fixed(...$this->sw('mwana'))->son(),
            Relationship::fixed(...$this->sw('mtoto'))->child(),
            // Siblings — twin first
            Relationship::fixed(...$this->sw('dada pacha'))->multiple()->sister(),
            Relationship::fixed(...$this->sw('kaka pacha'))->multiple()->brother(),
            Relationship::fixed(...$this->sw('ndugu pacha'))->multiple()->sibling(),
            Relationship::fixed(...$this->sw('dada'))->sister(),
            Relationship::fixed(...$this->sw('kaka'))->brother(),
            Relationship::fixed(...$this->sw('ndugu'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$this->sw('dada wa baba'))->father()->daughter(),
            Relationship::fixed(...$this->sw('kaka wa baba'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$this->sw('dada wa mama'))->mother()->daughter(),
            Relationship::fixed(...$this->sw('kaka wa mama'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$this->sw('dada wa kambo'))->parent()->daughter(),
            Relationship::fixed(...$this->sw('kaka wa kambo'))->parent()->son(),
            Relationship::fixed(...$this->sw('ndugu wa kambo'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->sw('mama wa kambo'))->parent()->wife(),
            Relationship::fixed(...$this->sw('baba wa kambo'))->parent()->husband(),
            Relationship::fixed(...$this->sw('binti wa kambo'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->sw('mwana wa kambo'))->married()->spouse()->son(),
            Relationship::fixed(...$this->sw('mtoto wa kambo'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->sw('mke wa zamani'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->sw('mume wa zamani'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->sw('mwenzi wa zamani'))->divorced()->partner(),
            Relationship::fixed(...$this->sw('mchumba wa kike'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->sw('mchumba wa kiume'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->sw('mke'))->wife(),
            Relationship::fixed(...$this->sw('mume'))->husband(),
            Relationship::fixed(...$this->sw('mwenzi'))->spouse(),
            Relationship::fixed(...$this->sw('mwenzi'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$this->sw('mama mkwe'))->husband()->mother(),
            Relationship::fixed(...$this->sw('baba mkwe'))->husband()->father(),
            Relationship::fixed(...$this->sw('mama mkwe'))->wife()->mother(),
            Relationship::fixed(...$this->sw('baba mkwe'))->wife()->father(),
            Relationship::fixed(...$this->sw('mkwe'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$this->sw('mkwe wa kike'))->child()->wife(),
            Relationship::fixed(...$this->sw('mkwe wa kiume'))->child()->husband(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$this->sw('wifi'))->husband()->sister(),
            Relationship::fixed(...$this->sw('shemeji'))->husband()->brother(),
            Relationship::fixed(...$this->sw('wifi'))->wife()->sister(),
            Relationship::fixed(...$this->sw('shemeji'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$this->sw('wifi'))->brother()->wife(),
            Relationship::fixed(...$this->sw('shemeji'))->sister()->husband(),
            // Grandparents
            Relationship::fixed(...$this->sw('bibi'))->parent()->mother(),
            Relationship::fixed(...$this->sw('babu'))->parent()->father(),
            Relationship::fixed(...$this->sw('babu/bibi'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->sw('mjukuu wa kike'))->child()->daughter(),
            Relationship::fixed(...$this->sw('mjukuu wa kiume'))->child()->son(),
            Relationship::fixed(...$this->sw('mjukuu'))->child()->child(),
            // Aunts/Uncles
            Relationship::fixed(...$this->sw('shangazi'))->father()->sister(),
            Relationship::fixed(...$this->sw('mama mdogo'))->mother()->younger()->sister(),
            Relationship::fixed(...$this->sw('mama mkubwa'))->mother()->older()->sister(),
            Relationship::fixed(...$this->sw('mama mdogo'))->mother()->sister(),
            Relationship::fixed(...$this->sw('mjomba'))->mother()->brother(),
            Relationship::fixed(...$this->sw('baba mdogo'))->father()->younger()->brother(),
            Relationship::fixed(...$this->sw('baba mkubwa'))->father()->older()->brother(),
            Relationship::fixed(...$this->sw('baba mdogo'))->father()->brother(),
            Relationship::fixed(...$this->sw('mjomba/shangazi'))->parent()->sibling(),
            // Nieces/Nephews
            Relationship::fixed(...$this->sw('mpwa wa kike'))->sibling()->daughter(),
            Relationship::fixed(...$this->sw('mpwa wa kiume'))->sibling()->son(),
            Relationship::fixed(...$this->sw('mpwa'))->sibling()->child(),
            // Cousins — flat (one term for all degrees)
            Relationship::fixed(...$this->sw('binamu wa kike'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->sw('binamu wa kiume'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->sw('binamu'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->sw($n > 2 ? 'shangazi wa kizazi cha ' . $n : 'shangazi'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->sw($n > 2 ? 'mjomba wa kizazi cha ' . $n : 'mjomba'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->sw($n > 2 ? 'mpwa wa kike wa kizazi cha ' . $n : 'mpwa wa kike'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->sw($n > 2 ? 'mpwa wa kiume wa kizazi cha ' . $n : 'mpwa wa kiume'))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->sw($n > 2 ? 'mpwa wa kizazi cha ' . $n : 'mpwa'))->sibling()->descendant(),
            // Dynamic: ancestors — bibi/babu mkubwa (great-grand), then kizazi cha N
            Relationship::dynamic(fn (int $n) => $this->sw(match (true) {
                $n === 3 => 'bibi mkubwa',
                default  => 'bibi wa kizazi cha ' . $n,
            }))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->sw(match (true) {
                $n === 3 => 'babu mkubwa',
                default  => 'babu wa kizazi cha ' . $n,
            }))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->sw(match (true) {
                $n === 3 => 'mzee mkubwa',
                default  => 'mzee wa kizazi cha ' . $n,
            }))->ancestor(),
            // Dynamic: descendants — kitukuu (great-grandchild), then kizazi cha N
            Relationship::dynamic(fn (int $n) => $this->sw(match (true) {
                $n === 3 => 'kitukuu wa kike',
                default  => 'kizazi cha ' . $n . ' wa kike',
            }))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->sw(match (true) {
                $n === 3 => 'kitukuu wa kiume',
                default  => 'kizazi cha ' . $n . ' wa kiume',
            }))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->sw(match (true) {
                $n === 3 => 'kitukuu',
                default  => 'kizazi cha ' . $n,
            }))->descendant(),
        ];
    }
}
