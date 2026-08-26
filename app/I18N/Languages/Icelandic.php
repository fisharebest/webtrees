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

final readonly class Icelandic extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'slenska';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'is';
    protected const string    LOCALE_CODE        = 'is_IS@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'um %s';
    protected const string    DATE_AFTER         = 'eftir %s';
    protected const string    DATE_BEFORE        = 'fyrir %s';
    protected const string    DATE_BETWEEN_AND   = 'á milli %s og %s';
    protected const string    DATE_CALCULATED    = 'reiknað %s';
    protected const string    DATE_ESTIMATED     = 'áætlað %s';
    protected const string    DATE_FROM          = 'frá %s';
    protected const string    DATE_FROM_TO       = 'frá %s til %s';
    protected const string    DATE_INTERPRETED   = 'túlkað %s';
    protected const string    DATE_TO            = 'til %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'fyrir krist';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'eftir okkar tímatali';
    protected const string    LIST_SEPARATOR_AND = ' og ';
    protected const string    LIST_SEPARATOR_OR  = ' eða ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'janúar',
        'febrúar',
        'mars',
        'apríl',
        'maí',
        'júní',
        'júlí',
        'ágúst',
        'september',
        'október',
        'nóvember',
        'desember',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'tishrei',
        'heshvan',
        'kislev',
        'tevet',
        'shevat',
        'Adar I',
        'Adar II',
        'Adar',
        'nissan',
        'Iyar',
        'sivan',
        'tamuz',
        'Av',
        'elul',
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
        'Dhul-Hijjah',
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
     * amma → langamma → langlangamma
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
            Relationship::fixed(...$this->rel('systkini', 'systkina'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->rel('hálfsystir', 'hálfsystur'))->parent()->daughter(),
            Relationship::fixed(...$this->rel('hálfbróðir', 'hálfbróður'))->parent()->son(),
            Relationship::fixed(...$this->rel('hálfsystkini', 'hálfsystkina'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->rel('stjúpmóðir', 'stjúpmóður'))->parent()->wife(),
            Relationship::fixed(...$this->rel('stjúpfaðir', 'stjúpföður'))->parent()->husband(),
            Relationship::fixed(...$this->rel('stjúpforeldri', 'stjúpforeldris'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->rel('stjúpdóttir', 'stjúpdóttur'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('stjúpsonur', 'stjúpsonar'))->married()->spouse()->son(),
            Relationship::fixed(...$this->rel('stjúpbarn', 'stjúpbarns'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->rel('fyrrverandi eiginkona', 'fyrrverandi eiginkonu'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->rel('fyrrverandi eiginmaður', 'fyrrverandi eiginmanns'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->rel('fyrrverandi maki', 'fyrrverandi maka'))->divorced()->partner(),
            Relationship::fixed(...$this->rel('unnusta', 'unnustu'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->rel('unnusti', 'unnusta'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->rel('eiginkona', 'eiginkonu'))->wife(),
            Relationship::fixed(...$this->rel('eiginmaður', 'eiginmanns'))->husband(),
            Relationship::fixed(...$this->rel('maki', 'maka'))->spouse(),
            Relationship::fixed(...$this->rel('maki', 'maka'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$this->rel('tengdamóðir', 'tengdamóður'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->rel('tengdafaðir', 'tengdaföður'))->married()->spouse()->father(),
            Relationship::fixed(...$this->rel('tengdaforeldri', 'tengdaforeldris'))->married()->spouse()->parent(),
            // Children-in-law
            Relationship::fixed(...$this->rel('tengdadóttir', 'tengdadóttur'))->child()->wife(),
            Relationship::fixed(...$this->rel('tengdasonur', 'tengdasonar'))->child()->husband(),
            // Siblings-in-law
            Relationship::fixed(...$this->rel('mágkona', 'mágkonu'))->spouse()->sister(),
            Relationship::fixed(...$this->rel('mágur', 'mágs'))->spouse()->brother(),
            Relationship::fixed(...$this->rel('mágkona', 'mágkonu'))->sibling()->wife(),
            Relationship::fixed(...$this->rel('mágur', 'mágs'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->rel('amma', 'ömmu'))->parent()->mother(),
            Relationship::fixed(...$this->rel('afi', 'afa'))->parent()->father(),
            Relationship::fixed(...$this->rel('amma/afi', 'ömmu/afa'))->parent()->parent(),
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
            Relationship::dynamic(fn (int $n) => $this->lang($n - 2, 'amma', 'ömmu'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->lang($n - 2, 'afi', 'afa'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->lang($n - 2, 'afi/amma', 'afa/ömmu'))->ancestor(),
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
