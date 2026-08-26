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

final readonly class Latvian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::ThreeFormsLatvian;

    protected const string    ENDONYM            = 'latviešu';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'lv';
    protected const string    LOCALE_CODE        = 'lv_LV@collation=phonebook';
    protected const int       MINIMUM_GROUPING_DIGITS = 3;
    protected const string    DIGITS_SEPARATOR   = UTF8::NARROW_NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    LIST_SEPARATOR_AND = ' un ';
    protected const string    LIST_SEPARATOR_OR  = ' vai ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'janvāris',
        'februāris',
        'marts',
        'aprīlis',
        'maijs',
        'jūnijs',
        'jūlijs',
        'augusts',
        'septembris',
        'oktobris',
        'novembris',
        'decembris',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'janvāra',
        'februāra',
        'marta',
        'aprīļa',
        'maija',
        'jūnija',
        'jūlija',
        'augusta',
        'septembra',
        'oktobra',
        'novembra',
        'decembra',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'janvārī',
        'februārī',
        'martā',
        'aprīlī',
        'maijā',
        'jūnijā',
        'jūlijā',
        'augustā',
        'septembrī',
        'oktobrī',
        'novembrī',
        'decembrī',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'janvāri',
        'februāri',
        'martu',
        'aprīli',
        'maiju',
        'jūniju',
        'jūliju',
        'augustu',
        'septembri',
        'oktobri',
        'novembri',
        'decembri',
    ];

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

    /**
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function rel(string $nom, string $gen): array
    {
        return [$nom, '%s ' . $gen];
    }

    /** @return array{string, string} */
    private function vec(int $n, string $nom, string $gen): array
    {
        return [
            str_repeat('vec', $n) . $nom,
            '%s ' . str_repeat('vec', $n) . $gen,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->rel('adoptētā meita', 'adoptētās meitas'))->adopted()->daughter(),
            Relationship::fixed(...$this->rel('adoptētais dēls', 'adoptētā dēla'))->adopted()->son(),
            Relationship::fixed(...$this->rel('adoptētais bērns', 'adoptētā bērna'))->adopted()->child(),
            Relationship::fixed(...$this->rel('adoptētāja māte', 'adoptētājas mātes'))->adoptive()->mother(),
            Relationship::fixed(...$this->rel('adoptētājs tēvs', 'adoptētāja tēva'))->adoptive()->father(),
            Relationship::fixed(...$this->rel('adoptētājs vecāks', 'adoptētāja vecāka'))->adoptive()->parent(),
            // Foster
            Relationship::fixed(...$this->rel('audžumeita', 'audžumeitas'))->fostered()->daughter(),
            Relationship::fixed(...$this->rel('audžudēls', 'audžudēla'))->fostered()->son(),
            Relationship::fixed(...$this->rel('audžubērns', 'audžubērna'))->fostered()->child(),
            Relationship::fixed(...$this->rel('audžumāte', 'audžumātes'))->fostering()->mother(),
            Relationship::fixed(...$this->rel('audžutēvs', 'audžutēva'))->fostering()->father(),
            Relationship::fixed(...$this->rel('audžuvecāks', 'audžuvecāka'))->fostering()->parent(),
            // Parents
            Relationship::fixed(...$this->rel('māte', 'mātes'))->mother(),
            Relationship::fixed(...$this->rel('tēvs', 'tēva'))->father(),
            Relationship::fixed(...$this->rel('vecāks', 'vecāka'))->parent(),
            // Children
            Relationship::fixed(...$this->rel('meita', 'meitas'))->daughter(),
            Relationship::fixed(...$this->rel('dēls', 'dēla'))->son(),
            Relationship::fixed(...$this->rel('bērns', 'bērna'))->child(),
            // Siblings
            Relationship::fixed(...$this->rel('māsa', 'māsas'))->sister(),
            Relationship::fixed(...$this->rel('brālis', 'brāļa'))->brother(),
            Relationship::fixed(...$this->rel('brālis/māsa', 'brāļa/māsas'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->rel('pusmāsa', 'pusmāsas'))->parent()->daughter(),
            Relationship::fixed(...$this->rel('pusbrālis', 'pusbrāļa'))->parent()->son(),
            // Stepfamily
            Relationship::fixed(...$this->rel('pamāte', 'pamātes'))->parent()->wife(),
            Relationship::fixed(...$this->rel('patēvs', 'patēva'))->parent()->husband(),
            Relationship::fixed(...$this->rel('pameita', 'pameitas'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('padēls', 'padēla'))->married()->spouse()->son(),
            Relationship::fixed(...$this->rel('pabērns', 'pabērna'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->rel('bijusī sieva', 'bijušās sievas'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->rel('bijušais vīrs', 'bijušā vīra'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->rel('bijušais laulātais', 'bijušā laulātā'))->divorced()->partner(),
            Relationship::fixed(...$this->rel('līgava', 'līgavas'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->rel('līgavainis', 'līgavaiņa'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->rel('sieva', 'sievas'))->wife(),
            Relationship::fixed(...$this->rel('vīrs', 'vīra'))->husband(),
            Relationship::fixed(...$this->rel('laulātais draugs', 'laulātā drauga'))->spouse(),
            Relationship::fixed(...$this->rel('partneris', 'partnera'))->partner(),
            // In-laws — wife's parents
            Relationship::fixed(...$this->rel('sievasmāte', 'sievasmātes'))->wife()->mother(),
            Relationship::fixed(...$this->rel('sievastēvs', 'sievastēva'))->wife()->father(),
            // In-laws — husband's parents
            Relationship::fixed(...$this->rel('vīramāte', 'vīramātes'))->husband()->mother(),
            Relationship::fixed(...$this->rel('vīratēvs', 'vīratēva'))->husband()->father(),
            // In-laws — spouse's parents (generic)
            Relationship::fixed(...$this->rel('vīramāte', 'vīramātes'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->rel('vīratēvs', 'vīratēva'))->married()->spouse()->father(),
            Relationship::fixed(...$this->rel('vīramāte', 'vīramātes'))->spouse()->mother(),
            Relationship::fixed(...$this->rel('vīratēvs', 'vīratēva'))->spouse()->father(),
            // Children-in-law
            Relationship::fixed(...$this->rel('vedekla', 'vedeklas'))->child()->wife(),
            Relationship::fixed(...$this->rel('znots', 'znota'))->child()->husband(),
            // Siblings-in-law (spouse's siblings)
            Relationship::fixed(...$this->rel('svaine', 'svaines'))->spouse()->sister(),
            Relationship::fixed(...$this->rel('svainis', 'svaiņa'))->spouse()->brother(),
            // Siblings-in-law (sibling's spouses)
            Relationship::fixed(...$this->rel('brāļasieva', 'brāļasievas'))->brother()->wife(),
            Relationship::fixed(...$this->rel('māsasvīrs', 'māsasvīra'))->sister()->husband(),
            Relationship::fixed(...$this->rel('svaine', 'svaines'))->sibling()->wife(),
            Relationship::fixed(...$this->rel('svainis', 'svaiņa'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->rel('vecāmāte', 'vecāmātes'))->parent()->mother(),
            Relationship::fixed(...$this->rel('vectēvs', 'vectēva'))->parent()->father(),
            Relationship::fixed(...$this->rel('vecvecāks', 'vecvecāka'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->rel('mazmeita', 'mazmeitas'))->child()->daughter(),
            Relationship::fixed(...$this->rel('mazdēls', 'mazdēla'))->child()->son(),
            Relationship::fixed(...$this->rel('mazbērns', 'mazbērna'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->rel('tante', 'tantes'))->parent()->sister(),
            Relationship::fixed(...$this->rel('tēvocis', 'tēvoča'))->parent()->brother(),
            // Nieces and nephews (from brother)
            Relationship::fixed(...$this->rel('brāļameita', 'brāļameitas'))->brother()->daughter(),
            Relationship::fixed(...$this->rel('brāļadēls', 'brāļadēla'))->brother()->son(),
            // Nieces and nephews (from sister)
            Relationship::fixed(...$this->rel('māsasmeita', 'māsasmeitas'))->sister()->daughter(),
            Relationship::fixed(...$this->rel('māsasdēls', 'māsasdēla'))->sister()->son(),
            // Nieces and nephews (generic)
            Relationship::fixed(...$this->rel('brāļameita', 'brāļameitas'))->sibling()->daughter(),
            Relationship::fixed(...$this->rel('brāļadēls', 'brāļadēla'))->sibling()->son(),
            // Cousins
            Relationship::fixed(...$this->rel('māsīca', 'māsīcas'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->rel('brālēns', 'brālēna'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->rel('brālēns', 'brālēna'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond (vec- prefix)
            Relationship::dynamic(fn (int $n) => $this->vec($n - 2, 'vecāmāte', 'vecāmātes'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->vec($n - 2, 'vectēvs', 'vectēva'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->vec($n - 2, 'vectēvs', 'vectēva'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(fn (int $n) => $this->vec($n - 2, 'mazmeita', 'mazmeitas'))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->vec($n - 2, 'mazdēls', 'mazdēla'))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->vec($n - 2, 'mazdēls', 'mazdēla'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->vec($n - 1, 'tante', 'tantes'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->vec($n - 1, 'tēvocis', 'tēvoča'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->vec($n - 1, 'brāļameita', 'brāļameitas'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->vec($n - 1, 'brāļadēls', 'brāļadēla'))->sibling()->descendant()->male(),
        ];
    }

    /**
     * Gregorian/Julian month names — case-inflected.
     *
     * @return array<int,string>
     */
}
