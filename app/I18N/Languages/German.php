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

final readonly class German extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'Deutsch';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'de';
    protected const string    LOCALE_CODE        = 'de_DE@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'um %s';
    protected const string    DATE_AFTER         = 'nach %s';
    protected const string    DATE_BEFORE        = 'vor %s';
    protected const string    DATE_BETWEEN_AND   = 'zwischen %s und %s';
    protected const string    DATE_CALCULATED    = 'berechnet %s';
    protected const string    DATE_ESTIMATED     = 'geschätzt %s';
    protected const string    DATE_FROM          = 'von %s';
    protected const string    DATE_FROM_TO       = 'von %s bis %s';
    protected const string    DATE_INTERPRETED   = 'interpretiert %s';
    protected const string    DATE_TO            = 'bis %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'v. u. Z.';
    protected const string    ERA_CE             = '%s&#7478;&#7489;&#7480;';
    protected const string    LIST_SEPARATOR_AND = ' und ';
    protected const string    LIST_SEPARATOR_OR  = ' oder ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Januar',
        'Februar',
        'März',
        'April',
        'Mai',
        'Juni',
        'Juli',
        'August',
        'September',
        'Oktober',
        'November',
        'Dezember',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tischri',
        'Cheschwan',
        'Kislew',
        'Tewet',
        'Schwat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nisan',
        'Ijar',
        'Siwan',
        'Tammus',
        'Aw',
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
        'Ergänzungungstage',
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
        'Dschumādā l-ūlā',
        'Dschumādā th-thāniya',
        'Rajab',
        'Schaʿbān',
        'Ramadan',
        'Schawwāl',
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

    protected function assembleDate(string $day, string $month, string $year): string
    {
        return $this->assembleDateDdotMY($day, $month, $year);
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_DIAERESIS    => 'AE',
            'O' . UTF8::COMBINING_DIAERESIS    => 'OE',
            'U' . UTF8::COMBINING_DIAERESIS    => 'UE',
            UTF8::LATIN_CAPITAL_LETTER_SHARP_S => 'SS',
            'a' . UTF8::COMBINING_DIAERESIS    => 'ae',
            'o' . UTF8::COMBINING_DIAERESIS    => 'oe',
            'u' . UTF8::COMBINING_DIAERESIS    => 'ue',
            UTF8::LATIN_SMALL_LETTER_SHARP_S   => 'ss',
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        $genitive = static fn (string $nominative, string $article): array => [$nominative, '%s ' . $article . $nominative];

        // "der" for feminine, "des" for masculine/neuter (with genitive -s/-es suffix on the noun)
        $der = static fn (string $s): array => $genitive($s, 'der ');
        $des = static fn (string $s, string $gen = ''): array => [$s, '%s des ' . ($gen !== '' ? $gen : $s . 's')];

        $great = static fn (int $n, string $prefix, string $suffix, string $article): array => $genitive(
            $prefix . ($n > 3 ? 'Ur×' . $n . '-' : str_repeat('Ur', $n)) . $suffix,
            $article,
        );

        return [
            // Adopted
            Relationship::fixed(...$der('Adoptivmutter'))->adoptive()->mother(),
            Relationship::fixed(...$des('Adoptivvater', 'Adoptivvaters'))->adoptive()->father(),
            Relationship::fixed(...$des('Adoptivelternteil', 'Adoptivelternteils'))->adoptive()->parent(),
            Relationship::fixed(...$der('Adoptivtochter'))->adopted()->daughter(),
            Relationship::fixed(...$des('Adoptivsohn', 'Adoptivsohnes'))->adopted()->son(),
            Relationship::fixed(...$des('Adoptivkind', 'Adoptivkindes'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$der('Pflegemutter'))->fostering()->mother(),
            Relationship::fixed(...$des('Pflegevater', 'Pflegevaters'))->fostering()->father(),
            Relationship::fixed(...$des('Pflegeelternteil', 'Pflegeelternteils'))->fostering()->parent(),
            Relationship::fixed(...$der('Pflegetochter'))->fostered()->daughter(),
            Relationship::fixed(...$des('Pflegesohn', 'Pflegesohnes'))->fostered()->son(),
            Relationship::fixed(...$des('Pflegekind', 'Pflegekindes'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$der('Mutter'))->mother(),
            Relationship::fixed(...$des('Vater', 'Vaters'))->father(),
            Relationship::fixed(...$des('Elternteil', 'Elternteils'))->parent(),
            // Children
            Relationship::fixed(...$der('Tochter'))->daughter(),
            Relationship::fixed(...$des('Sohn', 'Sohnes'))->son(),
            Relationship::fixed(...$des('Kind', 'Kindes'))->child(),
            // Siblings
            Relationship::fixed(...$der('Zwillingsschwester'))->twin()->sister(),
            Relationship::fixed(...$des('Zwillingsbruder', 'Zwillingsbruders'))->twin()->brother(),
            Relationship::fixed(...$des('Zwillingsgeschwister', 'Zwillingsgeschwisters'))->twin()->sibling(),
            Relationship::fixed(...$der('große Schwester'))->older()->sister(),
            Relationship::fixed(...$des('großer Bruder', 'großen Bruders'))->older()->brother(),
            Relationship::fixed(...$des('älteres Geschwister', 'älteren Geschwisters'))->older()->sibling(),
            Relationship::fixed(...$der('kleine Schwester'))->younger()->sister(),
            Relationship::fixed(...$des('kleiner Bruder', 'kleinen Bruders'))->younger()->brother(),
            Relationship::fixed(...$des('jüngeres Geschwister', 'jüngeren Geschwisters'))->younger()->sibling(),
            Relationship::fixed(...$der('Schwester'))->sister(),
            Relationship::fixed(...$des('Bruder', 'Bruders'))->brother(),
            Relationship::fixed(...$des('Geschwister', 'Geschwisters'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$der('Halbschwester'))->parent()->daughter(),
            Relationship::fixed(...$des('Halbbruder', 'Halbbruders'))->parent()->son(),
            Relationship::fixed(...$des('Halbgeschwister', 'Halbgeschwisters'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$der('Stiefmutter'))->parent()->wife(),
            Relationship::fixed(...$des('Stiefvater', 'Stiefvaters'))->parent()->husband(),
            Relationship::fixed(...$des('Stiefelternteil', 'Stiefelternteils'))->parent()->married()->spouse(),
            Relationship::fixed(...$der('Stieftochter'))->married()->spouse()->daughter(),
            Relationship::fixed(...$des('Stiefsohn', 'Stiefsohnes'))->married()->spouse()->son(),
            Relationship::fixed(...$des('Stiefkind', 'Stiefkindes'))->married()->spouse()->child(),
            Relationship::fixed(...$der('Stiefschwester'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$des('Stiefbruder', 'Stiefbruders'))->parent()->spouse()->son(),
            Relationship::fixed(...$des('Stiefgeschwister', 'Stiefgeschwisters'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$der('Ex-Ehefrau'))->divorced()->partner()->female(),
            Relationship::fixed(...$des('Ex-Ehemann', 'Ex-Ehemannes'))->divorced()->partner()->male(),
            Relationship::fixed(...$des('Ex-Ehepartner', 'Ex-Ehepartners'))->divorced()->partner(),
            Relationship::fixed(...$der('Verlobte'))->engaged()->partner()->female(),
            Relationship::fixed(...$des('Verlobter', 'Verlobten'))->engaged()->partner()->male(),
            Relationship::fixed(...$der('Ehefrau'))->wife(),
            Relationship::fixed(...$des('Ehemann', 'Ehemannes'))->husband(),
            Relationship::fixed(...$des('Ehepartner', 'Ehepartners'))->spouse(),
            Relationship::fixed(...$des('Partner', 'Partners'))->partner(),
            // In-laws
            Relationship::fixed(...$der('Schwiegermutter'))->married()->spouse()->mother(),
            Relationship::fixed(...$des('Schwiegervater', 'Schwiegervaters'))->married()->spouse()->father(),
            Relationship::fixed(...$des('Schwiegerelternteil', 'Schwiegerelternteils'))->married()->spouse()->parent(),
            Relationship::fixed(...$der('Schwiegertochter'))->child()->wife(),
            Relationship::fixed(...$des('Schwiegersohn', 'Schwiegersohnes'))->child()->husband(),
            Relationship::fixed(...$des('Schwiegerkind', 'Schwiegerkindes'))->child()->married()->spouse(),
            Relationship::fixed(...$der('Schwägerin'))->spouse()->sister(),
            Relationship::fixed(...$des('Schwager', 'Schwagers'))->spouse()->brother(),
            Relationship::fixed(...$der('Schwägerin'))->sibling()->wife(),
            Relationship::fixed(...$des('Schwager', 'Schwagers'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$der('Großmutter'))->parent()->mother(),
            Relationship::fixed(...$des('Großvater', 'Großvaters'))->parent()->father(),
            Relationship::fixed(...$des('Großelternteil', 'Großelternteils'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$der('Enkelin'))->child()->daughter(),
            Relationship::fixed(...$des('Enkel', 'Enkels'))->child()->son(),
            Relationship::fixed(...$des('Enkelkind', 'Enkelkindes'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$der('Tante'))->parent()->sister(),
            Relationship::fixed(...$des('Onkel', 'Onkels'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$der('Nichte'))->sibling()->daughter(),
            Relationship::fixed(...$des('Neffe', 'Neffen'))->sibling()->son(),
            Relationship::fixed(...$der('Nichte'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$des('Neffe', 'Neffen'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$der('Cousine'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$des('Cousin', 'Cousins'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'Tante', 'der '))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'Onkel', 'des '))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'Nichte', 'der '))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'Nichte', 'der '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'Neffe', 'des '))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'Neffe', 'des '))->married()->spouse()->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'Großmutter', 'der '))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'Großvater', 'des '))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'Großelternteil', 'des '))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, '', 'Enkelin', 'der '))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, '', 'Enkel', 'des '))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, '', 'Enkelkind', 'des '))->descendant(),
        ];
    }
}
