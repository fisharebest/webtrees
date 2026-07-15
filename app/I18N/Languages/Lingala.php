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

final readonly class Lingala extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsPluralForMoreThanOne;

    protected const string    ENDONYM            = 'lingla';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ln';
    protected const string    LOCALE_CODE        = 'ln_CD@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'likoló na %s';
    protected const string    DATE_AFTER         = 'nsima ya %s';
    protected const string    DATE_BEFORE        = 'libosó ya %s';
    protected const string    DATE_BETWEEN_AND   = 'káti na %s mpé %s';
    protected const string    DATE_CALCULATED    = '%s etángámí';
    protected const string    DATE_FROM          = 'útá %s';
    protected const string    DATE_FROM_TO       = 'útá %s kín’o %s';
    protected const string    DATE_TO            = 'na %s';
    protected const string    LIST_SEPARATOR_AND = ' na ';
    protected const string    LIST_SEPARATOR_OR  = ' to ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Yanwáli',
        'Febwáli',
        'Mársi',
        'Apríli',
        'Máyí',
        'Yuni',
        'Yúli',
        'Augústo',
        'Sɛtɛ́mbɛ',
        'Ɔkɔtɔbɛ',
        'Novɛ́mbɛ',
        'Dɛsɛ́mbɛ',
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
        'mikɔlɔ mya kobakisa',
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
        // Lingala genitive: "ya" (of) — e.g. "mama ya %s"
        // Bantu noun-class language; kinship terms are relatively flat
        $ln = static fn (string $s): array => [$s, $s . ' ya %s'];

        return [
            // Parents
            Relationship::fixed(...$ln('mama'))->mother(),
            Relationship::fixed(...$ln('tata'))->father(),
            Relationship::fixed(...$ln('moboti'))->parent(),
            // Children
            Relationship::fixed(...$ln('mwana mwasi'))->daughter(),
            Relationship::fixed(...$ln('mwana mobali'))->son(),
            Relationship::fixed(...$ln('mwana'))->child(),
            // Siblings
            Relationship::fixed(...$ln('ndeko mwasi'))->sister(),
            Relationship::fixed(...$ln('ndeko mobali'))->brother(),
            Relationship::fixed(...$ln('ndeko'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$ln('ndeko mwasi ya ndámbo'))->parent()->daughter(),
            Relationship::fixed(...$ln('ndeko mobali ya ndámbo'))->parent()->son(),
            Relationship::fixed(...$ln('ndeko ya ndámbo'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$ln('mama ya kobɔkɔla'))->parent()->wife(),
            Relationship::fixed(...$ln('tata ya kobɔkɔla'))->parent()->husband(),
            Relationship::fixed(...$ln('moboti ya kobɔkɔla'))->parent()->married()->spouse(),
            Relationship::fixed(...$ln('mwana mwasi ya kobɔkɔla'))->married()->spouse()->daughter(),
            Relationship::fixed(...$ln('mwana mobali ya kobɔkɔla'))->married()->spouse()->son(),
            Relationship::fixed(...$ln('mwana ya kobɔkɔla'))->married()->spouse()->child(),
            Relationship::fixed(...$ln('ndeko mwasi ya kobɔkɔla'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$ln('ndeko mobali ya kobɔkɔla'))->parent()->spouse()->son(),
            Relationship::fixed(...$ln('ndeko ya kobɔkɔla'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$ln('molongani ya kala'))->divorced()->partner()->female(),
            Relationship::fixed(...$ln('molongani ya kala'))->divorced()->partner()->male(),
            Relationship::fixed(...$ln('molongani ya kala'))->divorced()->partner(),
            Relationship::fixed(...$ln('mobalani'))->engaged()->partner()->female(),
            Relationship::fixed(...$ln('mobalani'))->engaged()->partner()->male(),
            Relationship::fixed(...$ln('mwasi'))->wife(),
            Relationship::fixed(...$ln('mobali'))->husband(),
            Relationship::fixed(...$ln('molongani'))->spouse(),
            Relationship::fixed(...$ln('moninga'))->partner(),
            // In-laws
            Relationship::fixed(...$ln('bokilo mwasi'))->married()->spouse()->mother(),
            Relationship::fixed(...$ln('bokilo mobali'))->married()->spouse()->father(),
            Relationship::fixed(...$ln('bokilo'))->married()->spouse()->parent(),
            Relationship::fixed(...$ln('bɔkɛli mwasi'))->child()->wife(),
            Relationship::fixed(...$ln('bɔkɛli mobali'))->child()->husband(),
            Relationship::fixed(...$ln('ndeko mwasi ya molongani'))->spouse()->sister(),
            Relationship::fixed(...$ln('ndeko mobali ya molongani'))->spouse()->brother(),
            Relationship::fixed(...$ln('mwasi ya ndeko'))->sibling()->wife(),
            Relationship::fixed(...$ln('mobali ya ndeko'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$ln('nkɔkɔ mwasi'))->parent()->mother(),
            Relationship::fixed(...$ln('nkɔkɔ mobali'))->parent()->father(),
            Relationship::fixed(...$ln('nkɔkɔ'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$ln('nkɔkɔ mwasi'))->child()->daughter(),
            Relationship::fixed(...$ln('nkɔkɔ mobali'))->child()->son(),
            Relationship::fixed(...$ln('nkɔkɔ'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$ln('tántí'))->mother()->sister(),
            Relationship::fixed(...$ln('tɔ́ngɔ'))->mother()->brother(),
            Relationship::fixed(...$ln('tántí'))->father()->sister(),
            Relationship::fixed(...$ln('nkɔ́kɔ'))->father()->brother(),
            Relationship::fixed(...$ln('tántí'))->parent()->sister(),
            Relationship::fixed(...$ln('nkɔ́kɔ'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$ln('mwana ya ndeko mwasi'))->sibling()->daughter(),
            Relationship::fixed(...$ln('mwana ya ndeko mobali'))->sibling()->son(),
            Relationship::fixed(...$ln('mwana ya ndeko'))->sibling()->child(),
            // Cousins — flat system (one term for all)
            Relationship::fixed(...$ln('ndeko ya mbɔ́ka'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$ln('ndeko ya mbɔ́ka'))->parent()->sibling()->son(),
            Relationship::fixed(...$ln('ndeko ya mbɔ́ka'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => [
                'nkɔkɔ mwasi ya molɔ́ngɔ́ ya ' . ($n - 1),
                'nkɔkɔ mwasi ya molɔ́ngɔ́ ya ' . ($n - 1) . ' ya %s',
            ])->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => [
                'nkɔkɔ mobali ya molɔ́ngɔ́ ya ' . ($n - 1),
                'nkɔkɔ mobali ya molɔ́ngɔ́ ya ' . ($n - 1) . ' ya %s',
            ])->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => [
                'nkɔkɔ ya molɔ́ngɔ́ ya ' . ($n - 1),
                'nkɔkɔ ya molɔ́ngɔ́ ya ' . ($n - 1) . ' ya %s',
            ])->ancestor(),
            Relationship::dynamic(static fn (int $n) => [
                'nkɔkɔ ya molɔ́ngɔ́ ya ' . ($n - 1),
                'nkɔkɔ ya molɔ́ngɔ́ ya ' . ($n - 1) . ' ya %s',
            ])->descendant(),
        ];
    }
}
