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
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
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
    public function relationships(): array
    {
        // Latvian genitive: nominative + genitive form
        $rel = static fn (string $nom, string $gen): array => [$nom, '%s ' . $gen];

        // Dynamic prefix for great- generations: vec- repeats
        $vec = static fn (int $n, string $nom, string $gen): array => [
            str_repeat('vec', $n) . $nom,
            '%s ' . str_repeat('vec', $n) . $gen,
        ];

        return [
            // Adopted
            Relationship::fixed(...$rel('adoptētā meita', 'adoptētās meitas'))->adopted()->daughter(),
            Relationship::fixed(...$rel('adoptētais dēls', 'adoptētā dēla'))->adopted()->son(),
            Relationship::fixed(...$rel('adoptētais bērns', 'adoptētā bērna'))->adopted()->child(),
            Relationship::fixed(...$rel('adoptētāja māte', 'adoptētājas mātes'))->adoptive()->mother(),
            Relationship::fixed(...$rel('adoptētājs tēvs', 'adoptētāja tēva'))->adoptive()->father(),
            Relationship::fixed(...$rel('adoptētājs vecāks', 'adoptētāja vecāka'))->adoptive()->parent(),
            // Foster
            Relationship::fixed(...$rel('audžumeita', 'audžumeitas'))->fostered()->daughter(),
            Relationship::fixed(...$rel('audžudēls', 'audžudēla'))->fostered()->son(),
            Relationship::fixed(...$rel('audžubērns', 'audžubērna'))->fostered()->child(),
            Relationship::fixed(...$rel('audžumāte', 'audžumātes'))->fostering()->mother(),
            Relationship::fixed(...$rel('audžutēvs', 'audžutēva'))->fostering()->father(),
            Relationship::fixed(...$rel('audžuvecāks', 'audžuvecāka'))->fostering()->parent(),
            // Parents
            Relationship::fixed(...$rel('māte', 'mātes'))->mother(),
            Relationship::fixed(...$rel('tēvs', 'tēva'))->father(),
            Relationship::fixed(...$rel('vecāks', 'vecāka'))->parent(),
            // Children
            Relationship::fixed(...$rel('meita', 'meitas'))->daughter(),
            Relationship::fixed(...$rel('dēls', 'dēla'))->son(),
            Relationship::fixed(...$rel('bērns', 'bērna'))->child(),
            // Siblings
            Relationship::fixed(...$rel('māsa', 'māsas'))->sister(),
            Relationship::fixed(...$rel('brālis', 'brāļa'))->brother(),
            Relationship::fixed(...$rel('brālis/māsa', 'brāļa/māsas'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$rel('pusmāsa', 'pusmāsas'))->parent()->daughter(),
            Relationship::fixed(...$rel('pusbrālis', 'pusbrāļa'))->parent()->son(),
            // Stepfamily
            Relationship::fixed(...$rel('pamāte', 'pamātes'))->parent()->wife(),
            Relationship::fixed(...$rel('patēvs', 'patēva'))->parent()->husband(),
            Relationship::fixed(...$rel('pameita', 'pameitas'))->married()->spouse()->daughter(),
            Relationship::fixed(...$rel('padēls', 'padēla'))->married()->spouse()->son(),
            Relationship::fixed(...$rel('pabērns', 'pabērna'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$rel('bijusī sieva', 'bijušās sievas'))->divorced()->partner()->female(),
            Relationship::fixed(...$rel('bijušais vīrs', 'bijušā vīra'))->divorced()->partner()->male(),
            Relationship::fixed(...$rel('bijušais laulātais', 'bijušā laulātā'))->divorced()->partner(),
            Relationship::fixed(...$rel('līgava', 'līgavas'))->engaged()->partner()->female(),
            Relationship::fixed(...$rel('līgavainis', 'līgavaiņa'))->engaged()->partner()->male(),
            Relationship::fixed(...$rel('sieva', 'sievas'))->wife(),
            Relationship::fixed(...$rel('vīrs', 'vīra'))->husband(),
            Relationship::fixed(...$rel('laulātais draugs', 'laulātā drauga'))->spouse(),
            Relationship::fixed(...$rel('partneris', 'partnera'))->partner(),
            // In-laws — wife's parents
            Relationship::fixed(...$rel('sievasmāte', 'sievasmātes'))->wife()->mother(),
            Relationship::fixed(...$rel('sievastēvs', 'sievastēva'))->wife()->father(),
            // In-laws — husband's parents
            Relationship::fixed(...$rel('vīramāte', 'vīramātes'))->husband()->mother(),
            Relationship::fixed(...$rel('vīratēvs', 'vīratēva'))->husband()->father(),
            // In-laws — spouse's parents (generic)
            Relationship::fixed(...$rel('vīramāte', 'vīramātes'))->married()->spouse()->mother(),
            Relationship::fixed(...$rel('vīratēvs', 'vīratēva'))->married()->spouse()->father(),
            Relationship::fixed(...$rel('vīramāte', 'vīramātes'))->spouse()->mother(),
            Relationship::fixed(...$rel('vīratēvs', 'vīratēva'))->spouse()->father(),
            // Children-in-law
            Relationship::fixed(...$rel('vedekla', 'vedeklas'))->child()->wife(),
            Relationship::fixed(...$rel('znots', 'znota'))->child()->husband(),
            // Siblings-in-law (spouse's siblings)
            Relationship::fixed(...$rel('svaine', 'svaines'))->spouse()->sister(),
            Relationship::fixed(...$rel('svainis', 'svaiņa'))->spouse()->brother(),
            // Siblings-in-law (sibling's spouses)
            Relationship::fixed(...$rel('brāļasieva', 'brāļasievas'))->brother()->wife(),
            Relationship::fixed(...$rel('māsasvīrs', 'māsasvīra'))->sister()->husband(),
            Relationship::fixed(...$rel('svaine', 'svaines'))->sibling()->wife(),
            Relationship::fixed(...$rel('svainis', 'svaiņa'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$rel('vecāmāte', 'vecāmātes'))->parent()->mother(),
            Relationship::fixed(...$rel('vectēvs', 'vectēva'))->parent()->father(),
            Relationship::fixed(...$rel('vecvecāks', 'vecvecāka'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$rel('mazmeita', 'mazmeitas'))->child()->daughter(),
            Relationship::fixed(...$rel('mazdēls', 'mazdēla'))->child()->son(),
            Relationship::fixed(...$rel('mazbērns', 'mazbērna'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$rel('tante', 'tantes'))->parent()->sister(),
            Relationship::fixed(...$rel('tēvocis', 'tēvoča'))->parent()->brother(),
            // Nieces and nephews (from brother)
            Relationship::fixed(...$rel('brāļameita', 'brāļameitas'))->brother()->daughter(),
            Relationship::fixed(...$rel('brāļadēls', 'brāļadēla'))->brother()->son(),
            // Nieces and nephews (from sister)
            Relationship::fixed(...$rel('māsasmeita', 'māsasmeitas'))->sister()->daughter(),
            Relationship::fixed(...$rel('māsasdēls', 'māsasdēla'))->sister()->son(),
            // Nieces and nephews (generic)
            Relationship::fixed(...$rel('brāļameita', 'brāļameitas'))->sibling()->daughter(),
            Relationship::fixed(...$rel('brāļadēls', 'brāļadēla'))->sibling()->son(),
            // Cousins
            Relationship::fixed(...$rel('māsīca', 'māsīcas'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$rel('brālēns', 'brālēna'))->parent()->sibling()->son(),
            Relationship::fixed(...$rel('brālēns', 'brālēna'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond (vec- prefix)
            Relationship::dynamic(static fn (int $n) => $vec($n - 2, 'vecāmāte', 'vecāmātes'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $vec($n - 2, 'vectēvs', 'vectēva'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $vec($n - 2, 'vectēvs', 'vectēva'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(static fn (int $n) => $vec($n - 2, 'mazmeita', 'mazmeitas'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $vec($n - 2, 'mazdēls', 'mazdēla'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $vec($n - 2, 'mazdēls', 'mazdēla'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $vec($n - 1, 'tante', 'tantes'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $vec($n - 1, 'tēvocis', 'tēvoča'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $vec($n - 1, 'brāļameita', 'brāļameitas'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $vec($n - 1, 'brāļadēls', 'brāļadēla'))->sibling()->descendant()->male(),
        ];
    }

    /**
     * Gregorian/Julian month names — case-inflected.
     *
     * @return array<int,string>
     */
}
