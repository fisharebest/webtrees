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
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
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
    public function relationships(): array
    {
        // Albanian genitive uses linking article "i" (masculine %s) / "e" (feminine %s)
        // followed by the noun in genitive/dative case.
        $ie = static fn (string $nom, string $gen): array => [$nom, '%s i ' . $gen, '%s e ' . $gen];

        // Dynamic with "stër" prefix for great-generations
        $ster = static fn (int $n, string $suffix, string $genSuffix): array => [
            ($n > 3 ? 'stër×' . $n . '-' : str_repeat('stër', $n)) . $suffix,
            '%s i ' . ($n > 3 ? 'stër×' . $n . '-' : str_repeat('stër', $n)) . $genSuffix,
            '%s e ' . ($n > 3 ? 'stër×' . $n . '-' : str_repeat('stër', $n)) . $genSuffix,
        ];

        return [
            // Adopted
            Relationship::fixed(...$ie('nënë birësuese', 'nënës birësuese'))->adoptive()->mother(),
            Relationship::fixed(...$ie('baba birësues', 'babait birësues'))->adoptive()->father(),
            Relationship::fixed(...$ie('prind birësues', 'prindit birësues'))->adoptive()->parent(),
            Relationship::fixed(...$ie('vajzë e birësuar', 'vajzës së birësuar'))->adopted()->daughter(),
            Relationship::fixed(...$ie('djalë i birësuar', 'djalit të birësuar'))->adopted()->son(),
            Relationship::fixed(...$ie('fëmijë i birësuar', 'fëmijës së birësuar'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$ie('nënë kujdestare', 'nënës kujdestare'))->fostering()->mother(),
            Relationship::fixed(...$ie('baba kujdestar', 'babait kujdestar'))->fostering()->father(),
            Relationship::fixed(...$ie('prind kujdestar', 'prindit kujdestar'))->fostering()->parent(),
            Relationship::fixed(...$ie('vajzë në kujdestari', 'vajzës në kujdestari'))->fostered()->daughter(),
            Relationship::fixed(...$ie('djalë në kujdestari', 'djalit në kujdestari'))->fostered()->son(),
            Relationship::fixed(...$ie('fëmijë në kujdestari', 'fëmijës në kujdestari'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$ie('nënë', 'nënës'))->mother(),
            Relationship::fixed(...$ie('baba', 'babait'))->father(),
            Relationship::fixed(...$ie('prind', 'prindit'))->parent(),
            // Children
            Relationship::fixed(...$ie('vajzë', 'vajzës'))->daughter(),
            Relationship::fixed(...$ie('djalë', 'djalit'))->son(),
            Relationship::fixed(...$ie('fëmijë', 'fëmijës'))->child(),
            // Siblings
            Relationship::fixed(...$ie('motër binjake', 'motrës binjake'))->twin()->sister(),
            Relationship::fixed(...$ie('vëlla binjak', 'vëllait binjak'))->twin()->brother(),
            Relationship::fixed(...$ie('binjak/e', 'binjakut/es'))->twin()->sibling(),
            Relationship::fixed(...$ie('motër e madhe', 'motrës së madhe'))->older()->sister(),
            Relationship::fixed(...$ie('vëlla i madh', 'vëllait të madh'))->older()->brother(),
            Relationship::fixed(...$ie('motër e vogël', 'motrës së vogël'))->younger()->sister(),
            Relationship::fixed(...$ie('vëlla i vogël', 'vëllait të vogël'))->younger()->brother(),
            Relationship::fixed(...$ie('motër', 'motrës'))->sister(),
            Relationship::fixed(...$ie('vëlla', 'vëllait'))->brother(),
            Relationship::fixed(...$ie('vëlla/motër', 'vëllait/motrës'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$ie('gjysmëmotër', 'gjysmëmotrës'))->parent()->daughter(),
            Relationship::fixed(...$ie('gjysmëvëlla', 'gjysmëvëllait'))->parent()->son(),
            Relationship::fixed(...$ie('gjysmëvëlla/motër', 'gjysmëvëllait/motrës'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$ie('njerkë', 'njerkës'))->parent()->wife(),
            Relationship::fixed(...$ie('njerk', 'njerkut'))->parent()->husband(),
            Relationship::fixed(...$ie('prind vitreg', 'prindit vitreg'))->parent()->married()->spouse(),
            Relationship::fixed(...$ie('vajzë vitregë', 'vajzës vitregë'))->married()->spouse()->daughter(),
            Relationship::fixed(...$ie('djalë vitreg', 'djalit vitreg'))->married()->spouse()->son(),
            Relationship::fixed(...$ie('fëmijë vitreg', 'fëmijës vitreg'))->married()->spouse()->child(),
            Relationship::fixed(...$ie('motër vitregë', 'motrës vitregë'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$ie('vëlla vitreg', 'vëllait vitreg'))->parent()->spouse()->son(),
            Relationship::fixed(...$ie('vëlla/motër vitreg', 'vëllait/motrës vitreg'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$ie('ish-grua', 'ish-gruas'))->divorced()->partner()->female(),
            Relationship::fixed(...$ie('ish-burrë', 'ish-burrit'))->divorced()->partner()->male(),
            Relationship::fixed(...$ie('ish-bashkëshort', 'ish-bashkëshortit'))->divorced()->partner(),
            Relationship::fixed(...$ie('e fejuar', 'së fejuarës'))->engaged()->partner()->female(),
            Relationship::fixed(...$ie('i fejuar', 'të fejuarit'))->engaged()->partner()->male(),
            Relationship::fixed(...$ie('grua', 'gruas'))->wife(),
            Relationship::fixed(...$ie('burrë', 'burrit'))->husband(),
            Relationship::fixed(...$ie('bashkëshort/e', 'bashkëshortit/es'))->spouse(),
            Relationship::fixed(...$ie('partner/e', 'partnerit/es'))->partner(),
            // In-laws (via spouse)
            Relationship::fixed(...$ie('vjehrrë', 'vjehrrës'))->married()->spouse()->mother(),
            Relationship::fixed(...$ie('vjehërr', 'vjehërrit'))->married()->spouse()->father(),
            Relationship::fixed(...$ie('prind vjehërr', 'prindit vjehërr'))->married()->spouse()->parent(),
            Relationship::fixed(...$ie('nuse', 'nuses'))->child()->wife(),
            Relationship::fixed(...$ie('dhëndër', 'dhëndrit'))->child()->husband(),
            Relationship::fixed(...$ie('kunatë', 'kunatës'))->spouse()->sister(),
            Relationship::fixed(...$ie('kunat', 'kunatit'))->spouse()->brother(),
            Relationship::fixed(...$ie('kunatë', 'kunatës'))->sibling()->wife(),
            Relationship::fixed(...$ie('kunat', 'kunatit'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$ie('gjyshe', 'gjyshes'))->parent()->mother(),
            Relationship::fixed(...$ie('gjysh', 'gjyshit'))->parent()->father(),
            Relationship::fixed(...$ie('gjysh/gjyshe', 'gjyshit/gjyshes'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$ie('mbesë', 'mbesës'))->child()->daughter(),
            Relationship::fixed(...$ie('nip', 'nipit'))->child()->son(),
            Relationship::fixed(...$ie('nip/mbesë', 'nipit/mbesës'))->child()->child(),
            // Aunts and uncles (Albanian distinguishes maternal/paternal)
            Relationship::fixed(...$ie('teze', 'tezes'))->mother()->sister(),
            Relationship::fixed(...$ie('hallë', 'hallës'))->father()->sister(),
            Relationship::fixed(...$ie('dajë', 'dajës'))->mother()->brother(),
            Relationship::fixed(...$ie('xhaxha', 'xhaxhait'))->father()->brother(),
            Relationship::fixed(...$ie('teze/hallë', 'tezes/hallës'))->parent()->sister(),
            Relationship::fixed(...$ie('dajë/xhaxha', 'dajës/xhaxhait'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$ie('mbesë', 'mbesës'))->sibling()->daughter(),
            Relationship::fixed(...$ie('nip', 'nipit'))->sibling()->son(),
            Relationship::fixed(...$ie('mbesë', 'mbesës'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$ie('nip', 'nipit'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$ie('kushërirë', 'kushërirës'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$ie('kushëri', 'kushërit'))->parent()->sibling()->son(),
            // Dynamic relationships
            // Great-aunts/uncles: ancestor(n>=2)->sister/brother
            Relationship::dynamic(static fn (int $n) => $ster($n - 1, 'teze', 'tezes'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $ster($n - 1, 'xhaxha', 'xhaxhait'))->ancestor()->brother(),
            // Great-nieces/nephews: sibling->descendant(n>=2)
            Relationship::dynamic(static fn (int $n) => $ster($n - 1, 'mbesë', 'mbesës'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $ster($n - 1, 'mbesë', 'mbesës'))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $ster($n - 1, 'nip', 'nipit'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $ster($n - 1, 'nip', 'nipit'))->married()->spouse()->sibling()->descendant()->male(),
            // Great-grandparents: ancestor(n>=3)
            Relationship::dynamic(static fn (int $n) => $ster($n - 2, 'gjyshe', 'gjyshes'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $ster($n - 2, 'gjysh', 'gjyshit'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $ster($n - 2, 'gjysh/gjyshe', 'gjyshit/gjyshes'))->ancestor(),
            // Great-grandchildren: descendant(n>=3)
            Relationship::dynamic(static fn (int $n) => $ster($n - 2, 'mbesë', 'mbesës'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $ster($n - 2, 'nip', 'nipit'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $ster($n - 2, 'nip/mbesë', 'nipit/mbesës'))->descendant(),
        ];
    }
}
