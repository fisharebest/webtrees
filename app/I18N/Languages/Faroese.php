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

final readonly class Faroese extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'froyskt';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'fo';
    protected const string    LOCALE_CODE        = 'fo_FO@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    NEGATIVE_SYMBOL    = UTF8::MINUS_SIGN;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    LIST_SEPARATOR_AND = ' og ';
    protected const string    LIST_SEPARATOR_OR  = ' ella ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'januar',
        'februar',
        'mars',
        'apríl',
        'mai',
        'juni',
        'juli',
        'august',
        'september',
        'oktober',
        'november',
        'desember',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

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

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Faroese genitive helper: [nominative, '%s ' . genitive]
        $rel = static fn (string $nom, string $gen): array => [$nom, '%s ' . $gen];

        // Dynamic "lang" prefix for great-grandparents
        $lang = static fn (int $n, string $nom, string $gen): array => [
            str_repeat('lang', $n) . $nom,
            '%s ' . str_repeat('lang', $n) . $gen,
        ];

        return [
            // Adopted / foster
            Relationship::fixed(...$rel('fósturmóðir', 'fósturmóður'))->adoptive()->mother(),
            Relationship::fixed(...$rel('fósturfaðir', 'fósturföður'))->adoptive()->father(),
            Relationship::fixed(...$rel('fósturforeldri', 'fósturforeldris'))->adoptive()->parent(),
            Relationship::fixed(...$rel('fósturdóttir', 'fósturdóttur'))->adopted()->daughter(),
            Relationship::fixed(...$rel('fóstursonur', 'fóstursonar'))->adopted()->son(),
            Relationship::fixed(...$rel('fósturbarn', 'fósturbarns'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$rel('móðir', 'móður'))->mother(),
            Relationship::fixed(...$rel('faðir', 'föður'))->father(),
            Relationship::fixed(...$rel('foreldri', 'foreldris'))->parent(),
            // Children
            Relationship::fixed(...$rel('dóttir', 'dóttur'))->daughter(),
            Relationship::fixed(...$rel('sonur', 'sonar'))->son(),
            Relationship::fixed(...$rel('barn', 'barns'))->child(),
            // Siblings
            Relationship::fixed(...$rel('tvíburasystir', 'tvíburasystur'))->twin()->sister(),
            Relationship::fixed(...$rel('tvíburabróðir', 'tvíburabróður'))->twin()->brother(),
            Relationship::fixed(...$rel('tvíburi', 'tvíbura'))->twin()->sibling(),
            Relationship::fixed(...$rel('eldri systir', 'eldri systur'))->older()->sister(),
            Relationship::fixed(...$rel('eldri bróðir', 'eldri bróður'))->older()->brother(),
            Relationship::fixed(...$rel('yngri systir', 'yngri systur'))->younger()->sister(),
            Relationship::fixed(...$rel('yngri bróðir', 'yngri bróður'))->younger()->brother(),
            Relationship::fixed(...$rel('systir', 'systur'))->sister(),
            Relationship::fixed(...$rel('bróðir', 'bróður'))->brother(),
            Relationship::fixed(...$rel('systkin', 'systkina'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$rel('hálvsystir', 'hálvsystur'))->parent()->daughter(),
            Relationship::fixed(...$rel('hálvbróðir', 'hálvbróður'))->parent()->son(),
            Relationship::fixed(...$rel('hálvsystkin', 'hálvsystkina'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$rel('stjúkmóðir', 'stjúkmóður'))->parent()->wife(),
            Relationship::fixed(...$rel('stjúkfaðir', 'stjúkföður'))->parent()->husband(),
            Relationship::fixed(...$rel('stjúkforeldri', 'stjúkforeldris'))->parent()->married()->spouse(),
            Relationship::fixed(...$rel('stjúkdóttir', 'stjúkdóttur'))->married()->spouse()->daughter(),
            Relationship::fixed(...$rel('stjúksonur', 'stjúksonar'))->married()->spouse()->son(),
            Relationship::fixed(...$rel('stjúkbarn', 'stjúkbarns'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$rel('fyrrverandi kona', 'fyrrverandi konu'))->divorced()->partner()->female(),
            Relationship::fixed(...$rel('fyrrverandi maður', 'fyrrverandi mans'))->divorced()->partner()->male(),
            Relationship::fixed(...$rel('fyrrverandi maki', 'fyrrverandi maka'))->divorced()->partner(),
            Relationship::fixed(...$rel('trúloynd', 'trúloyndar'))->engaged()->partner()->female(),
            Relationship::fixed(...$rel('trúloyndi', 'trúloyndis'))->engaged()->partner()->male(),
            Relationship::fixed(...$rel('kona', 'konu'))->wife(),
            Relationship::fixed(...$rel('maður', 'mans'))->husband(),
            Relationship::fixed(...$rel('maki', 'maka'))->spouse(),
            Relationship::fixed(...$rel('maki', 'maka'))->partner(),
            // In-laws (spouse's parents) — Faroese uses "ver-" prefix
            Relationship::fixed(...$rel('vermóðir', 'vermóður'))->married()->spouse()->mother(),
            Relationship::fixed(...$rel('verfaðir', 'verföður'))->married()->spouse()->father(),
            Relationship::fixed(...$rel('verforeldri', 'verforeldris'))->married()->spouse()->parent(),
            // Children-in-law
            Relationship::fixed(...$rel('verdóttir', 'verdóttur'))->child()->wife(),
            Relationship::fixed(...$rel('versonur', 'versonar'))->child()->husband(),
            // Siblings-in-law
            Relationship::fixed(...$rel('mágkona', 'mágkonu'))->spouse()->sister(),
            Relationship::fixed(...$rel('mágur', 'mágs'))->spouse()->brother(),
            Relationship::fixed(...$rel('mágkona', 'mágkonu'))->sibling()->wife(),
            Relationship::fixed(...$rel('mágur', 'mágs'))->sibling()->husband(),
            // Grandparents — Faroese uses omma/abbi
            Relationship::fixed(...$rel('omma', 'ommu'))->parent()->mother(),
            Relationship::fixed(...$rel('abbi', 'abba'))->parent()->father(),
            Relationship::fixed(...$rel('omma/abbi', 'ommu/abba'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$rel('sonarsonur', 'sonarsonar'))->son()->son(),
            Relationship::fixed(...$rel('sonardóttir', 'sonardóttur'))->son()->daughter(),
            Relationship::fixed(...$rel('dóttursonur', 'dóttursonar'))->daughter()->son(),
            Relationship::fixed(...$rel('dótturdóttir', 'dótturdóttur'))->daughter()->daughter(),
            Relationship::fixed(...$rel('barnabarn', 'barnabarns'))->child()->child(),
            // Aunts and uncles (paternal / maternal)
            Relationship::fixed(...$rel('föðursystir', 'föðursystur'))->father()->sister(),
            Relationship::fixed(...$rel('móðursystir', 'móðursystur'))->mother()->sister(),
            Relationship::fixed(...$rel('föðurbróðir', 'föðurbróður'))->father()->brother(),
            Relationship::fixed(...$rel('móðurbróðir', 'móðurbróður'))->mother()->brother(),
            Relationship::fixed(...$rel('föðursystir', 'föðursystur'))->parent()->sister(),
            Relationship::fixed(...$rel('föðurbróðir', 'föðurbróður'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$rel('bróðurdóttir', 'bróðurdóttur'))->brother()->daughter(),
            Relationship::fixed(...$rel('systurdóttir', 'systurdóttur'))->sister()->daughter(),
            Relationship::fixed(...$rel('bróðursonur', 'bróðursonar'))->brother()->son(),
            Relationship::fixed(...$rel('systursonur', 'systursonar'))->sister()->son(),
            Relationship::fixed(...$rel('bróðurdóttir', 'bróðurdóttur'))->sibling()->daughter(),
            Relationship::fixed(...$rel('bróðursonur', 'bróðursonar'))->sibling()->son(),
            Relationship::fixed(...$rel('systkinabarn', 'systkinabarns'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$rel('frænka', 'frænku'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$rel('frændi', 'frænda'))->parent()->sibling()->son(),
            Relationship::fixed(...$rel('frændi/frænka', 'frænda/frænku'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents
            Relationship::dynamic(static fn (int $n) => $lang($n - 2, 'omma', 'ommu'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $lang($n - 2, 'abbi', 'abba'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $lang($n - 2, 'abbi/omma', 'abba/ommu'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(static fn (int $n) => $lang($n - 2, 'barnabarn', 'barnabarns'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $lang($n - 1, 'föðursystir', 'föðursystur'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $lang($n - 1, 'föðurbróðir', 'föðurbróður'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $lang($n - 1, 'bróðurdóttir', 'bróðurdóttur'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $lang($n - 1, 'bróðursonur', 'bróðursonar'))->sibling()->descendant()->male(),
        ];
    }
}
