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

final readonly class Basque extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'euskara';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'eu';
    protected const string    LOCALE_CODE        = 'eu_ES@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    NEGATIVE_SYMBOL    = UTF8::MINUS_SIGN;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = '%saren inguruan';
    protected const string    DATE_AFTER         = '%sren ondotik';
    protected const string    DATE_BEFORE        = '%saren aitzinetik';
    protected const string    DATE_BETWEEN_AND   = '%s eta %sren artean';
    protected const string    DATE_CALCULATED    = '%s kalkulatuak';
    protected const string    DATE_ESTIMATED     = '%s guti gora behera';
    protected const string    DATE_FROM          = '%stik hasita';
    protected const string    DATE_FROM_TO       = '%stik hasita %s(e)ra';
    protected const string    DATE_INTERPRETED   = '%s interpretatuak';
    protected const string    DATE_TO            = '%s arte';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'AEC';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'EC';
    protected const string    LIST_SEPARATOR_AND = ' eta ';
    protected const string    LIST_SEPARATOR_OR  = ' edo ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Urtarrila',
        'Otsaila',
        'Martxoa',
        'Apirila',
        'Maiatza',
        'Ekaina',
        'Uztaila',
        'Abuztua',
        'Iraila',
        'Urria',
        'Azaroa',
        'Abendua',
    ];
    protected const string    PERCENT_FORMAT     = '%%' . UTF8::NO_BREAK_SPACE . '%s';

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
        'Termidor',
        'Fructidor',
        'egun osagarriak',
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


    protected function assembleDate(string $day, string $month, string $year): string
    {
        return parent::assembleDate($year, $month, $day);
    }

    public function relationships(): array
    {
        // Basque genitive: nominative + explicit genitive with -(r)en suffix
        $eu = static fn (string $nom, string $gen): array => [$nom, $gen . ' %s'];

        return [
            // Adopted
            Relationship::fixed(...$eu('ama adoptatzaile', 'ama adoptatzailearen'))->adoptive()->mother(),
            Relationship::fixed(...$eu('aita adoptatzaile', 'aita adoptatzailearen'))->adoptive()->father(),
            Relationship::fixed(...$eu('guraso adoptatzaile', 'guraso adoptatzailearen'))->adoptive()->parent(),
            Relationship::fixed(...$eu('alaba adoptatua', 'alaba adoptatuaren'))->adopted()->daughter(),
            Relationship::fixed(...$eu('seme adoptatua', 'seme adoptatuaren'))->adopted()->son(),
            Relationship::fixed(...$eu('ume adoptatua', 'ume adoptatuaren'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$eu('harrera-ama', 'harrera-amaren'))->fostering()->mother(),
            Relationship::fixed(...$eu('harrera-aita', 'harrera-aitaren'))->fostering()->father(),
            Relationship::fixed(...$eu('harrera-guraso', 'harrera-gurasoren'))->fostering()->parent(),
            Relationship::fixed(...$eu('harrera-alaba', 'harrera-alabaren'))->fostered()->daughter(),
            Relationship::fixed(...$eu('harrera-seme', 'harrera-semeren'))->fostered()->son(),
            Relationship::fixed(...$eu('harrera-ume', 'harrera-umeren'))->fostered()->child(),
            // Step
            Relationship::fixed(...$eu('amaordeko', 'amaordekoren'))->parent()->wife(),
            Relationship::fixed(...$eu('aitaordeko', 'aitaordekoren'))->parent()->husband(),
            Relationship::fixed(...$eu('alabaordeko', 'alabaordekoren'))->married()->spouse()->daughter(),
            Relationship::fixed(...$eu('semeordeko', 'semeordekoren'))->married()->spouse()->son(),
            Relationship::fixed(...$eu('umeordeko', 'umeordekoren'))->married()->spouse()->child(),
            // Parents
            Relationship::fixed(...$eu('ama', 'amaren'))->mother(),
            Relationship::fixed(...$eu('aita', 'aitaren'))->father(),
            Relationship::fixed(...$eu('guraso', 'gurasoren'))->parent(),
            // Children
            Relationship::fixed(...$eu('alaba', 'alabaren'))->daughter(),
            Relationship::fixed(...$eu('seme', 'semeren'))->son(),
            Relationship::fixed(...$eu('ume', 'umeren'))->child(),
            // Siblings — ego-relative (Basque distinguishes by speaker's gender)
            Relationship::fixed(...$eu('ahizpa', 'ahizparen'))->selfFemale()->sister(),
            Relationship::fixed(...$eu('arreba', 'arrebaren'))->sister(),
            Relationship::fixed(...$eu('anaia', 'anaiaren'))->selfFemale()->brother(),
            Relationship::fixed(...$eu('neba', 'nebaren'))->brother(),
            Relationship::fixed(...$eu('senide', 'senideren'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$eu('aitaren alaba', 'aitaren alabaren'))->father()->daughter(),
            Relationship::fixed(...$eu('aitaren seme', 'aitaren semeren'))->father()->son(),
            Relationship::fixed(...$eu('amaren alaba', 'amaren alabaren'))->mother()->daughter(),
            Relationship::fixed(...$eu('amaren seme', 'amaren semeren'))->mother()->son(),
            Relationship::fixed(...$eu('erdi-senide', 'erdi-senideren'))->parent()->child(),
            // Partners
            Relationship::fixed(...$eu('senar ohia', 'senar ohiaren'))->divorced()->partner()->male(),
            Relationship::fixed(...$eu('emazte ohia', 'emazte ohiaren'))->divorced()->partner()->female(),
            Relationship::fixed(...$eu('ezkontide ohia', 'ezkontide ohiaren'))->divorced()->partner(),
            Relationship::fixed(...$eu('emazte', 'emazteren'))->wife(),
            Relationship::fixed(...$eu('senar', 'senarraren'))->husband(),
            Relationship::fixed(...$eu('ezkontide', 'ezkontidearen'))->spouse(),
            Relationship::fixed(...$eu('bikotekide', 'bikotekidearen'))->partner(),
            // In-laws — spouse's parents
            Relationship::fixed(...$eu('amaginarreba', 'amaginarrebaren'))->spouse()->mother(),
            Relationship::fixed(...$eu('aitaginarreba', 'aitaginarrebaren'))->spouse()->father(),
            // In-laws — child's spouse
            Relationship::fixed(...$eu('erraina', 'errainaren'))->child()->wife(),
            Relationship::fixed(...$eu('suhi', 'suhiaren'))->child()->husband(),
            // In-laws — spouse's siblings
            Relationship::fixed(...$eu('koinata', 'koinataren'))->spouse()->sister(),
            Relationship::fixed(...$eu('koinatu', 'koinaturen'))->spouse()->brother(),
            // In-laws — sibling's spouse
            Relationship::fixed(...$eu('koinata', 'koinataren'))->sibling()->wife(),
            Relationship::fixed(...$eu('koinatu', 'koinaturen'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$eu('amona', 'amonaren'))->parent()->mother(),
            Relationship::fixed(...$eu('aitona', 'aitonaren'))->parent()->father(),
            // Grandchildren
            Relationship::fixed(...$eu('biloba', 'bilobaren'))->child()->daughter(),
            Relationship::fixed(...$eu('biloba', 'bilobaren'))->child()->son(),
            Relationship::fixed(...$eu('biloba', 'bilobaren'))->child()->child(),
            // Great-grandparents
            Relationship::fixed(...$eu('biramona', 'biramonaren'))->parent()->parent()->mother(),
            Relationship::fixed(...$eu('biraitona', 'biraitonaren'))->parent()->parent()->father(),
            // Great-grandchildren
            Relationship::fixed(...$eu('birbiloba', 'birbilobaren'))->child()->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$eu('izeba', 'izebaren'))->parent()->sister(),
            Relationship::fixed(...$eu('osaba', 'osabaren'))->parent()->brother(),
            // Nieces and nephews (iloba is gender-neutral)
            Relationship::fixed(...$eu('iloba', 'ilobaren'))->sibling()->daughter(),
            Relationship::fixed(...$eu('iloba', 'ilobaren'))->sibling()->son(),
            Relationship::fixed(...$eu('iloba', 'ilobaren'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$eu('lehengusina', 'lehengusinaren'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$eu('lehengusu', 'lehengusuren'))->parent()->sibling()->son(),
            Relationship::fixed(...$eu('lehengusu', 'lehengusuren'))->parent()->sibling()->child(),
            // Dynamic: great-grandparents (n ≥ 3)
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $eu('biramona', 'biramonaren'),
                default => $eu($n . '. belaunaldiko amona', ($n) . '. belaunaldiko amonaren'),
            })->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $eu('biraitona', 'biraitonaren'),
                default => $eu($n . '. belaunaldiko aitona', ($n) . '. belaunaldiko aitonaren'),
            })->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $eu($n . '. belaunaldiko arbasoa', ($n) . '. belaunaldiko arbasoaren'))->ancestor(),
            // Dynamic: great-grandchildren (n ≥ 3)
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $eu('birbiloba', 'birbilobaren'),
                default => $eu($n . '. belaunaldiko biloba', ($n) . '. belaunaldiko bilobaren'),
            })->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $eu('birbiloba', 'birbilobaren'),
                default => $eu($n . '. belaunaldiko biloba', ($n) . '. belaunaldiko bilobaren'),
            })->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $eu('birbiloba', 'birbilobaren'),
                default => $eu($n . '. belaunaldiko biloba', ($n) . '. belaunaldiko bilobaren'),
            })->descendant(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $eu($n . '. belaunaldiko izeba', ($n) . '. belaunaldiko izebaren'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $eu($n . '. belaunaldiko osaba', ($n) . '. belaunaldiko osabaren'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $eu($n . '. belaunaldiko iloba', ($n) . '. belaunaldiko ilobaren'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $eu($n . '. belaunaldiko iloba', ($n) . '. belaunaldiko ilobaren'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $eu($n . '. belaunaldiko iloba', ($n) . '. belaunaldiko ilobaren'))->sibling()->descendant(),
        ];
    }
}
