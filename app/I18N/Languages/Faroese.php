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
     * Generate nominative and genitive forms from explicit noun forms.
     *
     * @return array{string, string}
     */
    private function rel(string $nominative, string $genitive): array
    {
        return [$nominative, '%s ' . $genitive];
    }

    /**
     * Generate nominative and genitive forms for a dynamic relationship
     * using the repeated "lang" prefix.
     *
     * omma → langomma → langlangomma
     *
     * @return array{string, string}
     */
    private function lang(int $n, string $nominative, string $genitive): array
    {
        return [
            str_repeat('lang', $n) . $nominative,
            '%s ' . str_repeat('lang', $n) . $genitive,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {

        return [
            // Adopted / foster
            Relationship::fixed(...$this->rel('fósturmóðir', 'fósturmóður'))->adoptive()->mother(),
            Relationship::fixed(...$this->rel('fósturfaðir', 'fósturföður'))->adoptive()->father(),
            Relationship::fixed(...$this->rel('fósturforeldri', 'fósturforeldris'))->adoptive()->parent(),
            Relationship::fixed(...$this->rel('fósturdóttir', 'fósturdóttur'))->adopted()->daughter(),
            Relationship::fixed(...$this->rel('fóstursonur', 'fóstursonar'))->adopted()->son(),
            Relationship::fixed(...$this->rel('fósturbarn', 'fósturbarns'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$this->rel('móðir', 'móður'))->mother(),
            Relationship::fixed(...$this->rel('faðir', 'föður'))->father(),
            Relationship::fixed(...$this->rel('foreldri', 'foreldris'))->parent(),
            // Children
            Relationship::fixed(...$this->rel('dóttir', 'dóttur'))->daughter(),
            Relationship::fixed(...$this->rel('sonur', 'sonar'))->son(),
            Relationship::fixed(...$this->rel('barn', 'barns'))->child(),
            // Siblings
            Relationship::fixed(...$this->rel('tvíburasystir', 'tvíburasystur'))->multiple()->sister(),
            Relationship::fixed(...$this->rel('tvíburabróðir', 'tvíburabróður'))->multiple()->brother(),
            Relationship::fixed(...$this->rel('tvíburi', 'tvíbura'))->multiple()->sibling(),
            Relationship::fixed(...$this->rel('eldri systir', 'eldri systur'))->older()->sister(),
            Relationship::fixed(...$this->rel('eldri bróðir', 'eldri bróður'))->older()->brother(),
            Relationship::fixed(...$this->rel('yngri systir', 'yngri systur'))->younger()->sister(),
            Relationship::fixed(...$this->rel('yngri bróðir', 'yngri bróður'))->younger()->brother(),
            Relationship::fixed(...$this->rel('systir', 'systur'))->sister(),
            Relationship::fixed(...$this->rel('bróðir', 'bróður'))->brother(),
            Relationship::fixed(...$this->rel('systkin', 'systkina'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->rel('hálvsystir', 'hálvsystur'))->parent()->daughter(),
            Relationship::fixed(...$this->rel('hálvbróðir', 'hálvbróður'))->parent()->son(),
            Relationship::fixed(...$this->rel('hálvsystkin', 'hálvsystkina'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->rel('stjúkmóðir', 'stjúkmóður'))->parent()->wife(),
            Relationship::fixed(...$this->rel('stjúkfaðir', 'stjúkföður'))->parent()->husband(),
            Relationship::fixed(...$this->rel('stjúkforeldri', 'stjúkforeldris'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->rel('stjúkdóttir', 'stjúkdóttur'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('stjúksonur', 'stjúksonar'))->married()->spouse()->son(),
            Relationship::fixed(...$this->rel('stjúkbarn', 'stjúkbarns'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->rel('fyrrverandi kona', 'fyrrverandi konu'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->rel('fyrrverandi maður', 'fyrrverandi mans'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->rel('fyrrverandi maki', 'fyrrverandi maka'))->divorced()->partner(),
            Relationship::fixed(...$this->rel('trúloynd', 'trúloyndar'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->rel('trúloyndi', 'trúloyndis'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->rel('kona', 'konu'))->wife(),
            Relationship::fixed(...$this->rel('maður', 'mans'))->husband(),
            Relationship::fixed(...$this->rel('maki', 'maka'))->spouse(),
            Relationship::fixed(...$this->rel('maki', 'maka'))->partner(),
            // In-laws (spouse's parents) — Faroese uses "ver-" prefix
            Relationship::fixed(...$this->rel('vermóðir', 'vermóður'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->rel('verfaðir', 'verföður'))->married()->spouse()->father(),
            Relationship::fixed(...$this->rel('verforeldri', 'verforeldris'))->married()->spouse()->parent(),
            // Children-in-law
            Relationship::fixed(...$this->rel('verdóttir', 'verdóttur'))->child()->wife(),
            Relationship::fixed(...$this->rel('versonur', 'versonar'))->child()->husband(),
            // Siblings-in-law
            Relationship::fixed(...$this->rel('mágkona', 'mágkonu'))->spouse()->sister(),
            Relationship::fixed(...$this->rel('mágur', 'mágs'))->spouse()->brother(),
            Relationship::fixed(...$this->rel('mágkona', 'mágkonu'))->sibling()->wife(),
            Relationship::fixed(...$this->rel('mágur', 'mágs'))->sibling()->husband(),
            // Grandparents — Faroese uses omma/abbi
            Relationship::fixed(...$this->rel('omma', 'ommu'))->parent()->mother(),
            Relationship::fixed(...$this->rel('abbi', 'abba'))->parent()->father(),
            Relationship::fixed(...$this->rel('omma/abbi', 'ommu/abba'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->rel('sonarsonur', 'sonarsonar'))->son()->son(),
            Relationship::fixed(...$this->rel('sonardóttir', 'sonardóttur'))->son()->daughter(),
            Relationship::fixed(...$this->rel('dóttursonur', 'dóttursonar'))->daughter()->son(),
            Relationship::fixed(...$this->rel('dótturdóttir', 'dótturdóttur'))->daughter()->daughter(),
            Relationship::fixed(...$this->rel('barnabarn', 'barnabarns'))->child()->child(),
            // Aunts and uncles (paternal / maternal)
            Relationship::fixed(...$this->rel('föðursystir', 'föðursystur'))->father()->sister(),
            Relationship::fixed(...$this->rel('móðursystir', 'móðursystur'))->mother()->sister(),
            Relationship::fixed(...$this->rel('föðurbróðir', 'föðurbróður'))->father()->brother(),
            Relationship::fixed(...$this->rel('móðurbróðir', 'móðurbróður'))->mother()->brother(),
            Relationship::fixed(...$this->rel('föðursystir', 'föðursystur'))->parent()->sister(),
            Relationship::fixed(...$this->rel('föðurbróðir', 'föðurbróður'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->rel('bróðurdóttir', 'bróðurdóttur'))->brother()->daughter(),
            Relationship::fixed(...$this->rel('systurdóttir', 'systurdóttur'))->sister()->daughter(),
            Relationship::fixed(...$this->rel('bróðursonur', 'bróðursonar'))->brother()->son(),
            Relationship::fixed(...$this->rel('systursonur', 'systursonar'))->sister()->son(),
            Relationship::fixed(...$this->rel('bróðurdóttir', 'bróðurdóttur'))->sibling()->daughter(),
            Relationship::fixed(...$this->rel('bróðursonur', 'bróðursonar'))->sibling()->son(),
            Relationship::fixed(...$this->rel('systkinabarn', 'systkinabarns'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$this->rel('frænka', 'frænku'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->rel('frændi', 'frænda'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->rel('frændi/frænka', 'frænda/frænku'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents
            Relationship::dynamic(fn (int $n) => $this->lang($n - 2, 'omma', 'ommu'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->lang($n - 2, 'abbi', 'abba'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->lang($n - 2, 'abbi/omma', 'abba/ommu'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(fn (int $n) => $this->lang($n - 2, 'barnabarn', 'barnabarns'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->lang($n - 1, 'föðursystir', 'föðursystur'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->lang($n - 1, 'föðurbróðir', 'föðurbróður'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->lang($n - 1, 'bróðurdóttir', 'bróðurdóttur'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->lang($n - 1, 'bróðursonur', 'bróðursonar'))->sibling()->descendant()->male(),
        ];
    }
}
