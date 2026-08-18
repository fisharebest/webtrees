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
    protected const string    DIGITS_SEPARATOR   = UTF8::NARROW_NO_BREAK_SPACE;
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
     * Generate nominative and genitive forms with the Afrikaans "se" particle.
     *
     * @return array{string, string}
     */
    private function se(string $nominative): array
    {
        return [$nominative, '%s se ' . $nominative];
    }

    /**
     * Generate nominative and genitive forms for a dynamic relationship
     * using the repeated "groot-" prefix.
     *
     * ouma → groot-ouma → groot-groot-ouma
     *
     * @return array{string, string}
     */
    private function groot(int $n, string $suffix): array
    {
        return $this->se(($n > 3 ? 'groot×' . $n . '-' : str_repeat('groot-', $n)) . $suffix);
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {

        return [
            // Adopted
            Relationship::fixed(...$this->se('aanneemmoeder'))->adoptive()->mother(),
            Relationship::fixed(...$this->se('aanneemvader'))->adoptive()->father(),
            Relationship::fixed(...$this->se('aanneemouer'))->adoptive()->parent(),
            Relationship::fixed(...$this->se('aangenome dogter'))->adopted()->daughter(),
            Relationship::fixed(...$this->se('aangenome seun'))->adopted()->son(),
            Relationship::fixed(...$this->se('aangenome kind'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->se('pleegmoeder'))->fostering()->mother(),
            Relationship::fixed(...$this->se('pleegvader'))->fostering()->father(),
            Relationship::fixed(...$this->se('pleegouer'))->fostering()->parent(),
            Relationship::fixed(...$this->se('pleegdogter'))->fostered()->daughter(),
            Relationship::fixed(...$this->se('pleegseun'))->fostered()->son(),
            Relationship::fixed(...$this->se('pleegkind'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->se('moeder'))->mother(),
            Relationship::fixed(...$this->se('vader'))->father(),
            Relationship::fixed(...$this->se('ouer'))->parent(),
            // Children
            Relationship::fixed(...$this->se('dogter'))->daughter(),
            Relationship::fixed(...$this->se('seun'))->son(),
            Relationship::fixed(...$this->se('kind'))->child(),
            // Siblings
            Relationship::fixed(...$this->se('tweelingbroer'))->multiple()->brother(),
            Relationship::fixed(...$this->se('tweelingsuster'))->multiple()->sister(),
            Relationship::fixed(...$this->se('tweeling'))->multiple()->sibling(),
            Relationship::fixed(...$this->se('ouer broer'))->older()->brother(),
            Relationship::fixed(...$this->se('ouer suster'))->older()->sister(),
            Relationship::fixed(...$this->se('ouer broer/suster'))->older()->sibling(),
            Relationship::fixed(...$this->se('jonger broer'))->younger()->brother(),
            Relationship::fixed(...$this->se('jonger suster'))->younger()->sister(),
            Relationship::fixed(...$this->se('jonger broer/suster'))->younger()->sibling(),
            Relationship::fixed(...$this->se('suster'))->sister(),
            Relationship::fixed(...$this->se('broer'))->brother(),
            Relationship::fixed(...$this->se('broer/suster'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->se('halfsuster'))->parent()->daughter(),
            Relationship::fixed(...$this->se('halfbroer'))->parent()->son(),
            Relationship::fixed(...$this->se('halfbroer/halfsuster'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->se('stiefmoeder'))->parent()->wife(),
            Relationship::fixed(...$this->se('stiefvader'))->parent()->husband(),
            Relationship::fixed(...$this->se('stiefouer'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->se('stiefdogter'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->se('stiefseun'))->married()->spouse()->son(),
            Relationship::fixed(...$this->se('stiefkind'))->married()->spouse()->child(),
            Relationship::fixed(...$this->se('stiefsuster'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$this->se('stiefbroer'))->parent()->spouse()->son(),
            Relationship::fixed(...$this->se('stiefbroer/stiefsuster'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->se('eks-vrou'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->se('eks-man'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->se('eks-maat'))->divorced()->partner(),
            Relationship::fixed(...$this->se('verloofde'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->se('verloofde'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->se('vrou'))->wife(),
            Relationship::fixed(...$this->se('man'))->husband(),
            Relationship::fixed(...$this->se('eggenoot'))->spouse(),
            Relationship::fixed(...$this->se('maat'))->partner(),
            // In-laws
            Relationship::fixed(...$this->se('skoonmoeder'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->se('skoonvader'))->married()->spouse()->father(),
            Relationship::fixed(...$this->se('skoonouer'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->se('skoondogter'))->child()->wife(),
            Relationship::fixed(...$this->se('skoonseun'))->child()->husband(),
            Relationship::fixed(...$this->se('skoonkind'))->child()->married()->spouse(),
            Relationship::fixed(...$this->se('skoonsuster'))->spouse()->sister(),
            Relationship::fixed(...$this->se('skoonbroer'))->spouse()->brother(),
            Relationship::fixed(...$this->se('skoonsuster'))->sibling()->wife(),
            Relationship::fixed(...$this->se('skoonbroer'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->se('ouma'))->parent()->mother(),
            Relationship::fixed(...$this->se('oupa'))->parent()->father(),
            Relationship::fixed(...$this->se('grootouers'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->se('kleindogter'))->child()->daughter(),
            Relationship::fixed(...$this->se('kleinseun'))->child()->son(),
            Relationship::fixed(...$this->se('kleinkind'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->se('tante'))->parent()->sister(),
            Relationship::fixed(...$this->se('oom'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->se('niggie'))->sibling()->daughter(),
            Relationship::fixed(...$this->se('neef'))->sibling()->son(),
            Relationship::fixed(...$this->se('niggie'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$this->se('neef'))->married()->spouse()->sibling()->son(),
            // Cousins (flat - same term for all levels)
            Relationship::fixed(...$this->se('niggie'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->se('neef'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(fn (int $n) => $this->groot($n - 1, 'tante'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->groot($n - 1, 'oom'))->ancestor()->brother(),
            Relationship::dynamic(fn (int $n) => $this->groot($n - 1, 'niggie'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->groot($n - 1, 'niggie'))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->groot($n - 1, 'neef'))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->groot($n - 1, 'neef'))->married()->spouse()->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->groot($n - 1, 'ouma'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->groot($n - 1, 'oupa'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->groot($n - 1, 'grootouers'))->ancestor(),
            Relationship::dynamic(fn (int $n) => $this->groot($n - 2, 'kleindogter'))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->groot($n - 2, 'kleinseun'))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->groot($n - 2, 'kleinkind'))->descendant(),
        ];
    }
}
