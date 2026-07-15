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
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

use function str_repeat;

final readonly class Afrikaans extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'Afrikaans';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'af';
    protected const string    LOCALE_CODE        = 'af_ZA@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    DATE_ABOUT         = 'op ongeveer %s';
    protected const string    DATE_AFTER         = 'na %s';
    protected const string    DATE_BEFORE        = 'voor %s';
    protected const string    DATE_BETWEEN_AND   = 'tussen %s en %s';
    protected const string    DATE_CALCULATED    = 'bereken as %s';
    protected const string    DATE_ESTIMATED     = 'beraam op %s';
    protected const string    DATE_EXACT         = '%s';
    protected const string    DATE_FROM          = 'vanaf %s';
    protected const string    DATE_FROM_TO       = 'vanaf %s tot %s';
    protected const string    DATE_INTERPRETED   = 'geïnterpreteer %s';
    protected const string    DATE_TO            = 'tot %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'v.C.';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'n.C.';
    protected const string    LIST_SEPARATOR_AND = ' en ';
    protected const string    LIST_SEPARATOR_OR  = ' of ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Januarie',
        'Februarie',
        'Maart',
        'April',
        'Mei',
        'Junie',
        'Julie',
        'Augustus',
        'September',
        'Oktober',
        'November',
        'Desember',
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
        'Farvadin',
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

    protected const array ALPHABET = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
    ];

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Afrikaans genitive: "se" (possessive particle)
        $se = static fn (string $s): array => [$s, '%s se ' . $s];

        $groot = static fn (int $n, string $prefix, string $suffix): array => [
            $prefix . ($n > 3 ? 'groot×' . $n . '-' : str_repeat('groot-', $n)) . $suffix,
            '%s se ' . $prefix . ($n > 3 ? 'groot×' . $n . '-' : str_repeat('groot-', $n)) . $suffix,
        ];

        return [
            // Adopted
            Relationship::fixed(...$se('aanneemmoeder'))->adoptive()->mother(),
            Relationship::fixed(...$se('aanneemvader'))->adoptive()->father(),
            Relationship::fixed(...$se('aanneemouer'))->adoptive()->parent(),
            Relationship::fixed(...$se('aangenome dogter'))->adopted()->daughter(),
            Relationship::fixed(...$se('aangenome seun'))->adopted()->son(),
            Relationship::fixed(...$se('aangenome kind'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$se('pleegmoeder'))->fostering()->mother(),
            Relationship::fixed(...$se('pleegvader'))->fostering()->father(),
            Relationship::fixed(...$se('pleegouer'))->fostering()->parent(),
            Relationship::fixed(...$se('pleegdogter'))->fostered()->daughter(),
            Relationship::fixed(...$se('pleegseun'))->fostered()->son(),
            Relationship::fixed(...$se('pleegkind'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$se('moeder'))->mother(),
            Relationship::fixed(...$se('vader'))->father(),
            Relationship::fixed(...$se('ouer'))->parent(),
            // Children
            Relationship::fixed(...$se('dogter'))->daughter(),
            Relationship::fixed(...$se('seun'))->son(),
            Relationship::fixed(...$se('kind'))->child(),
            // Siblings
            Relationship::fixed(...$se('tweelingbroer'))->twin()->brother(),
            Relationship::fixed(...$se('tweelingsuster'))->twin()->sister(),
            Relationship::fixed(...$se('tweeling'))->twin()->sibling(),
            Relationship::fixed(...$se('ouer broer'))->older()->brother(),
            Relationship::fixed(...$se('ouer suster'))->older()->sister(),
            Relationship::fixed(...$se('ouer broer/suster'))->older()->sibling(),
            Relationship::fixed(...$se('jonger broer'))->younger()->brother(),
            Relationship::fixed(...$se('jonger suster'))->younger()->sister(),
            Relationship::fixed(...$se('jonger broer/suster'))->younger()->sibling(),
            Relationship::fixed(...$se('suster'))->sister(),
            Relationship::fixed(...$se('broer'))->brother(),
            Relationship::fixed(...$se('broer/suster'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$se('halfsuster'))->parent()->daughter(),
            Relationship::fixed(...$se('halfbroer'))->parent()->son(),
            Relationship::fixed(...$se('halfbroer/halfsuster'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$se('stiefmoeder'))->parent()->wife(),
            Relationship::fixed(...$se('stiefvader'))->parent()->husband(),
            Relationship::fixed(...$se('stiefouer'))->parent()->married()->spouse(),
            Relationship::fixed(...$se('stiefdogter'))->married()->spouse()->daughter(),
            Relationship::fixed(...$se('stiefseun'))->married()->spouse()->son(),
            Relationship::fixed(...$se('stiefkind'))->married()->spouse()->child(),
            Relationship::fixed(...$se('stiefsuster'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$se('stiefbroer'))->parent()->spouse()->son(),
            Relationship::fixed(...$se('stiefbroer/stiefsuster'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$se('eks-vrou'))->divorced()->partner()->female(),
            Relationship::fixed(...$se('eks-man'))->divorced()->partner()->male(),
            Relationship::fixed(...$se('eks-maat'))->divorced()->partner(),
            Relationship::fixed(...$se('verloofde'))->engaged()->partner()->female(),
            Relationship::fixed(...$se('verloofde'))->engaged()->partner()->male(),
            Relationship::fixed(...$se('vrou'))->wife(),
            Relationship::fixed(...$se('man'))->husband(),
            Relationship::fixed(...$se('eggenoot'))->spouse(),
            Relationship::fixed(...$se('maat'))->partner(),
            // In-laws
            Relationship::fixed(...$se('skoonmoeder'))->married()->spouse()->mother(),
            Relationship::fixed(...$se('skoonvader'))->married()->spouse()->father(),
            Relationship::fixed(...$se('skoonouer'))->married()->spouse()->parent(),
            Relationship::fixed(...$se('skoondogter'))->child()->wife(),
            Relationship::fixed(...$se('skoonseun'))->child()->husband(),
            Relationship::fixed(...$se('skoonkind'))->child()->married()->spouse(),
            Relationship::fixed(...$se('skoonsuster'))->spouse()->sister(),
            Relationship::fixed(...$se('skoonbroer'))->spouse()->brother(),
            Relationship::fixed(...$se('skoonsuster'))->sibling()->wife(),
            Relationship::fixed(...$se('skoonbroer'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$se('ouma'))->parent()->mother(),
            Relationship::fixed(...$se('oupa'))->parent()->father(),
            Relationship::fixed(...$se('grootouers'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$se('kleindogter'))->child()->daughter(),
            Relationship::fixed(...$se('kleinseun'))->child()->son(),
            Relationship::fixed(...$se('kleinkind'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$se('tante'))->parent()->sister(),
            Relationship::fixed(...$se('oom'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$se('niggie'))->sibling()->daughter(),
            Relationship::fixed(...$se('neef'))->sibling()->son(),
            Relationship::fixed(...$se('niggie'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$se('neef'))->married()->spouse()->sibling()->son(),
            // Cousins (flat - same term for all levels)
            Relationship::fixed(...$se('niggie'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$se('neef'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $groot($n - 1, '', 'tante'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $groot($n - 1, '', 'oom'))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $groot($n - 1, '', 'niggie'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $groot($n - 1, '', 'niggie'))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $groot($n - 1, '', 'neef'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $groot($n - 1, '', 'neef'))->married()->spouse()->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $groot($n - 1, '', 'ouma'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $groot($n - 1, '', 'oupa'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $groot($n - 1, '', 'grootouers'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $groot($n - 2, '', 'kleindogter'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $groot($n - 2, '', 'kleinseun'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $groot($n - 2, '', 'kleinkind'))->descendant(),
        ];
    }
}
