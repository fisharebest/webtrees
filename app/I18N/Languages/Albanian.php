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

use function str_repeat;

final readonly class Albanian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'shqip';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'sq';
    protected const string    LOCALE_CODE        = 'sq_AL@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NARROW_NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'rreth %s';
    protected const string    DATE_AFTER         = 'pas %s';
    protected const string    DATE_BEFORE        = 'para %s';
    protected const string    DATE_BETWEEN_AND   = 'ndërmjet %s dhe %s';
    protected const string    DATE_CALCULATED    = 'kalkuluar %s';
    protected const string    DATE_ESTIMATED     = 'vlerësuar %s';
    protected const string    DATE_FROM          = 'nga %s';
    protected const string    DATE_FROM_TO       = 'nga %s deri në %s';
    protected const string    DATE_INTERPRETED   = 'interpretuar %s';
    protected const string    DATE_TO            = 'deri te %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'PER';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'ER';
    protected const string    LIST_SEPARATOR_AND = ' dhe ';
    protected const string    LIST_SEPARATOR_OR  = ' ose ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Janar',
        'Shkurti',
        'Mars',
        'Prilli',
        'Maj',
        'Qershor',
        'Korrik',
        'Gushti',
        'Shtatori',
        'Tetor',
        'Nëntor',
        'Dhjetori',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'Janar',
        'Shkurt',
        'Mars',
        'Prill',
        'Majit',
        'Qershor',
        'Korrik',
        'Gusht',
        'Shtator',
        'Tetor',
        'Nëntor',
        'Dhjetori',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'Janar',
        'Shkurti',
        'Mars',
        'Prilli',
        'Maj',
        'Qershor',
        'Korrik',
        'Gushti',
        'Shtator',
        'Tetor',
        'Nëntor',
        'Dhjetori',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'Janar',
        'Shkurt',
        'Mars',
        'Prill',
        'Maj',
        'Qershor',
        'Korrik',
        'Gusht',
        'Shtatorin',
        'Tetor',
        'Nëntor',
        'Dhjetor',
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
        'lyar',
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
        'Ramazanit',
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
    private function ie(string $nom, string $gen): array
    {
        return [$nom, '%s i ' . $gen, '%s e ' . $gen];
    }

    /** @return array{string, string} */
    private function ster(int $n, string $suffix, string $genSuffix): array
    {
        return [
            ($n > 3 ? 'stër×' . $n . '-' : str_repeat('stër', $n)) . $suffix,
            '%s i ' . ($n > 3 ? 'stër×' . $n . '-' : str_repeat('stër', $n)) . $genSuffix,
            '%s e ' . ($n > 3 ? 'stër×' . $n . '-' : str_repeat('stër', $n)) . $genSuffix,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->ie('nënë birësuese', 'nënës birësuese'))->adoptive()->mother(),
            Relationship::fixed(...$this->ie('baba birësues', 'babait birësues'))->adoptive()->father(),
            Relationship::fixed(...$this->ie('prind birësues', 'prindit birësues'))->adoptive()->parent(),
            Relationship::fixed(...$this->ie('vajzë e birësuar', 'vajzës së birësuar'))->adopted()->daughter(),
            Relationship::fixed(...$this->ie('djalë i birësuar', 'djalit të birësuar'))->adopted()->son(),
            Relationship::fixed(...$this->ie('fëmijë i birësuar', 'fëmijës së birësuar'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->ie('nënë kujdestare', 'nënës kujdestare'))->fostering()->mother(),
            Relationship::fixed(...$this->ie('baba kujdestar', 'babait kujdestar'))->fostering()->father(),
            Relationship::fixed(...$this->ie('prind kujdestar', 'prindit kujdestar'))->fostering()->parent(),
            Relationship::fixed(...$this->ie('vajzë në kujdestari', 'vajzës në kujdestari'))->fostered()->daughter(),
            Relationship::fixed(...$this->ie('djalë në kujdestari', 'djalit në kujdestari'))->fostered()->son(),
            Relationship::fixed(...$this->ie('fëmijë në kujdestari', 'fëmijës në kujdestari'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->ie('nënë', 'nënës'))->mother(),
            Relationship::fixed(...$this->ie('baba', 'babait'))->father(),
            Relationship::fixed(...$this->ie('prind', 'prindit'))->parent(),
            // Children
            Relationship::fixed(...$this->ie('vajzë', 'vajzës'))->daughter(),
            Relationship::fixed(...$this->ie('djalë', 'djalit'))->son(),
            Relationship::fixed(...$this->ie('fëmijë', 'fëmijës'))->child(),
            // Siblings
            Relationship::fixed(...$this->ie('motër binjake', 'motrës binjake'))->multiple()->sister(),
            Relationship::fixed(...$this->ie('vëlla binjak', 'vëllait binjak'))->multiple()->brother(),
            Relationship::fixed(...$this->ie('binjak/e', 'binjakut/es'))->multiple()->sibling(),
            Relationship::fixed(...$this->ie('motër e madhe', 'motrës së madhe'))->older()->sister(),
            Relationship::fixed(...$this->ie('vëlla i madh', 'vëllait të madh'))->older()->brother(),
            Relationship::fixed(...$this->ie('motër e vogël', 'motrës së vogël'))->younger()->sister(),
            Relationship::fixed(...$this->ie('vëlla i vogël', 'vëllait të vogël'))->younger()->brother(),
            Relationship::fixed(...$this->ie('motër', 'motrës'))->sister(),
            Relationship::fixed(...$this->ie('vëlla', 'vëllait'))->brother(),
            Relationship::fixed(...$this->ie('vëlla/motër', 'vëllait/motrës'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->ie('gjysmëmotër', 'gjysmëmotrës'))->parent()->daughter(),
            Relationship::fixed(...$this->ie('gjysmëvëlla', 'gjysmëvëllait'))->parent()->son(),
            Relationship::fixed(...$this->ie('gjysmëvëlla/motër', 'gjysmëvëllait/motrës'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->ie('njerkë', 'njerkës'))->parent()->wife(),
            Relationship::fixed(...$this->ie('njerk', 'njerkut'))->parent()->husband(),
            Relationship::fixed(...$this->ie('prind vitreg', 'prindit vitreg'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->ie('vajzë vitregë', 'vajzës vitregë'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->ie('djalë vitreg', 'djalit vitreg'))->married()->spouse()->son(),
            Relationship::fixed(...$this->ie('fëmijë vitreg', 'fëmijës vitreg'))->married()->spouse()->child(),
            Relationship::fixed(...$this->ie('motër vitregë', 'motrës vitregë'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$this->ie('vëlla vitreg', 'vëllait vitreg'))->parent()->spouse()->son(),
            Relationship::fixed(...$this->ie('vëlla/motër vitreg', 'vëllait/motrës vitreg'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->ie('ish-grua', 'ish-gruas'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->ie('ish-burrë', 'ish-burrit'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->ie('ish-bashkëshort', 'ish-bashkëshortit'))->divorced()->partner(),
            Relationship::fixed(...$this->ie('e fejuar', 'së fejuarës'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->ie('i fejuar', 'të fejuarit'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->ie('grua', 'gruas'))->wife(),
            Relationship::fixed(...$this->ie('burrë', 'burrit'))->husband(),
            Relationship::fixed(...$this->ie('bashkëshort/e', 'bashkëshortit/es'))->spouse(),
            Relationship::fixed(...$this->ie('partner/e', 'partnerit/es'))->partner(),
            // In-laws (via spouse)
            Relationship::fixed(...$this->ie('vjehrrë', 'vjehrrës'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->ie('vjehërr', 'vjehërrit'))->married()->spouse()->father(),
            Relationship::fixed(...$this->ie('prind vjehërr', 'prindit vjehërr'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->ie('nuse', 'nuses'))->child()->wife(),
            Relationship::fixed(...$this->ie('dhëndër', 'dhëndrit'))->child()->husband(),
            Relationship::fixed(...$this->ie('kunatë', 'kunatës'))->spouse()->sister(),
            Relationship::fixed(...$this->ie('kunat', 'kunatit'))->spouse()->brother(),
            Relationship::fixed(...$this->ie('kunatë', 'kunatës'))->sibling()->wife(),
            Relationship::fixed(...$this->ie('kunat', 'kunatit'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->ie('gjyshe', 'gjyshes'))->parent()->mother(),
            Relationship::fixed(...$this->ie('gjysh', 'gjyshit'))->parent()->father(),
            Relationship::fixed(...$this->ie('gjysh/gjyshe', 'gjyshit/gjyshes'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->ie('mbesë', 'mbesës'))->child()->daughter(),
            Relationship::fixed(...$this->ie('nip', 'nipit'))->child()->son(),
            Relationship::fixed(...$this->ie('nip/mbesë', 'nipit/mbesës'))->child()->child(),
            // Aunts and uncles (Albanian distinguishes maternal/paternal)
            Relationship::fixed(...$this->ie('teze', 'tezes'))->mother()->sister(),
            Relationship::fixed(...$this->ie('hallë', 'hallës'))->father()->sister(),
            Relationship::fixed(...$this->ie('dajë', 'dajës'))->mother()->brother(),
            Relationship::fixed(...$this->ie('xhaxha', 'xhaxhait'))->father()->brother(),
            Relationship::fixed(...$this->ie('teze/hallë', 'tezes/hallës'))->parent()->sister(),
            Relationship::fixed(...$this->ie('dajë/xhaxha', 'dajës/xhaxhait'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->ie('mbesë', 'mbesës'))->sibling()->daughter(),
            Relationship::fixed(...$this->ie('nip', 'nipit'))->sibling()->son(),
            Relationship::fixed(...$this->ie('mbesë', 'mbesës'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$this->ie('nip', 'nipit'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$this->ie('kushërirë', 'kushërirës'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->ie('kushëri', 'kushërit'))->parent()->sibling()->son(),
            // Dynamic relationships
            // Great-aunts/uncles: ancestor(n>=2)->sister/brother
            Relationship::dynamic(fn (int $n) => $this->ster($n - 1, 'teze', 'tezes'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->ster($n - 1, 'xhaxha', 'xhaxhait'))->ancestor()->brother(),
            // Great-nieces/nephews: sibling->descendant(n>=2)
            Relationship::dynamic(fn (int $n) => $this->ster($n - 1, 'mbesë', 'mbesës'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->ster($n - 1, 'mbesë', 'mbesës'))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->ster($n - 1, 'nip', 'nipit'))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->ster($n - 1, 'nip', 'nipit'))->married()->spouse()->sibling()->descendant()->male(),
            // Great-grandparents: ancestor(n>=3)
            Relationship::dynamic(fn (int $n) => $this->ster($n - 2, 'gjyshe', 'gjyshes'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->ster($n - 2, 'gjysh', 'gjyshit'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->ster($n - 2, 'gjysh/gjyshe', 'gjyshit/gjyshes'))->ancestor(),
            // Great-grandchildren: descendant(n>=3)
            Relationship::dynamic(fn (int $n) => $this->ster($n - 2, 'mbesë', 'mbesës'))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->ster($n - 2, 'nip', 'nipit'))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->ster($n - 2, 'nip/mbesë', 'nipit/mbesës'))->descendant(),
        ];
    }
}
