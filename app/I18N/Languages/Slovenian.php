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

final readonly class Slovenian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::FourFormsSlovenian;

    protected const string    ENDONYM            = 'slovenščina';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'sl';
    protected const string    LOCALE_CODE        = 'sl_SI@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    NEGATIVE_SYMBOL    = UTF8::MINUS_SIGN;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'okoli %s';
    protected const string    DATE_AFTER         = 'po %s';
    protected const string    DATE_BEFORE        = 'pred %s';
    protected const string    DATE_BETWEEN_AND   = 'med %s in %s';
    protected const string    DATE_CALCULATED    = 'izračunano %s';
    protected const string    DATE_ESTIMATED     = 'ocenjeno %s';
    protected const string    DATE_FROM          = 'od %s';
    protected const string    DATE_FROM_TO       = 'od %s do %s';
    protected const string    DATE_INTERPRETED   = 'interpretirano kot %s';
    protected const string    DATE_TO            = 'do %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'pr. K.';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'po K.';
    protected const string    LIST_SEPARATOR_AND = ' in ';
    protected const string    LIST_SEPARATOR_OR  = ' ali ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'januar',
        'februar',
        'marec',
        'april',
        'maj',
        'junij',
        'julij',
        'avgust',
        'september',
        'oktober',
        'november',
        'december',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'januarja',
        'februarja',
        'marca',
        'aprila',
        'maja',
        'junija',
        'julija',
        'avgusta',
        'septembra',
        'oktobra',
        'novembra',
        'decembra',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'januarju',
        'februarju',
        'marcu',
        'aprilu',
        'maju',
        'juniju',
        'juliju',
        'avgustu',
        'septembru',
        'oktobru',
        'novembru',
        'decembru',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'januarjem',
        'februarjem',
        'marcem',
        'aprilom',
        'majem',
        'junijem',
        'julijem',
        'avgustom',
        'septembrom',
        'oktobrom',
        'novembrom',
        'decembrom',
    ];

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'tišri',
        'ešvan',
        'kislev',
        'tevet',
        'šebat',
        'adar',
        'beadar',
        'adar',
        'nisan',
        'ijar',
        'sivan',
        'tamuz',
        'av',
        'elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'vendemiaire',
        'brumaire',
        'frimaire',
        'nivose',
        'pluviôse',
        'ventose',
        'germinal',
        'floréal',
        'prairial',
        'messidor',
        'thermidor',
        'fructidor',
        'dodatni dnevi',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'muharrem',
        'safer',
        'Rabi’ al-awwal',
        'Rabi’ al-thani',
        'džumadel-ula',
        'džumadel-uhra',
        'redžeb',
        'šaban',
        'ramazan',
        'šewal',
        'zul-ka’de',
        'zul-hidže',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'farvardin',
        'ordibehešt',
        'kordad',
        'tir',
        'mordad',
        'šarivar',
        'mer',
        'aban',
        'azar',
        'dej',
        'bahman',
        'esfand',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;
    /**
     * @return array<int,string>
     */
    /** @var array<int,string> */
    protected const array ALPHABET = [
        'A',
        'B',
        'C',
        UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
        UTF8::LATIN_CAPITAL_LETTER_C_WITH_ACUTE,
        'D',
        UTF8::LATIN_CAPITAL_LETTER_D_WITH_STROKE,
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
    ];

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
            'C' . UTF8::COMBINING_CARON                => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
            'C' . UTF8::COMBINING_CEDILLA              => UTF8::LATIN_CAPITAL_LETTER_C_WITH_ACUTE,
            'D' . UTF8::COMBINING_SHORT_STROKE_OVERLAY => UTF8::LATIN_CAPITAL_LETTER_D_WITH_STROKE,
            'S' . UTF8::COMBINING_CARON                => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'Z' . UTF8::COMBINING_CARON                => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
            'c' . UTF8::COMBINING_CARON                => UTF8::LATIN_SMALL_LETTER_C_WITH_CARON,
            'c' . UTF8::COMBINING_CEDILLA              => UTF8::LATIN_SMALL_LETTER_C_WITH_ACUTE,
            'd' . UTF8::COMBINING_SHORT_STROKE_OVERLAY => UTF8::LATIN_SMALL_LETTER_D_WITH_STROKE,
            's' . UTF8::COMBINING_CARON                => UTF8::LATIN_SMALL_LETTER_S_WITH_CARON,
            'z' . UTF8::COMBINING_CARON                => UTF8::LATIN_SMALL_LETTER_Z_WITH_CARON,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Slovenian genitive helper: nominative + genitive form with "%s "
        $rel = static fn (string $nom, string $gen): array => [$nom, '%s ' . $gen];

        // Dynamic "pra-" prefix for great-grandparents/children
        $pra = static fn (int $n, string $nom, string $gen): array => [
            str_repeat('pra', $n) . $nom,
            '%s ' . str_repeat('pra', $n) . $gen,
        ];

        return [
            // Adopted
            Relationship::fixed(...$rel('posvojiteljica', 'posvojiteljice'))->adoptive()->mother(),
            Relationship::fixed(...$rel('posvojitelj', 'posvojitelja'))->adoptive()->father(),
            Relationship::fixed(...$rel('posvojitelj', 'posvojitelja'))->adoptive()->parent(),
            Relationship::fixed(...$rel('posvojena hči', 'posvojene hčere'))->adopted()->daughter(),
            Relationship::fixed(...$rel('posvojeni sin', 'posvojenega sina'))->adopted()->son(),
            Relationship::fixed(...$rel('posvojeni otrok', 'posvojenega otroka'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$rel('mati', 'matere'))->mother(),
            Relationship::fixed(...$rel('oče', 'očeta'))->father(),
            Relationship::fixed(...$rel('starš', 'starša'))->parent(),
            // Children
            Relationship::fixed(...$rel('hči', 'hčere'))->daughter(),
            Relationship::fixed(...$rel('sin', 'sina'))->son(),
            Relationship::fixed(...$rel('otrok', 'otroka'))->child(),
            // Siblings
            Relationship::fixed(...$rel('sestra dvojčica', 'sestre dvojčice'))->twin()->sister(),
            Relationship::fixed(...$rel('brat dvojček', 'brata dvojčka'))->twin()->brother(),
            Relationship::fixed(...$rel('dvojček', 'dvojčka'))->twin()->sibling(),
            Relationship::fixed(...$rel('starejša sestra', 'starejše sestre'))->older()->sister(),
            Relationship::fixed(...$rel('starejši brat', 'starejšega brata'))->older()->brother(),
            Relationship::fixed(...$rel('mlajša sestra', 'mlajše sestre'))->younger()->sister(),
            Relationship::fixed(...$rel('mlajši brat', 'mlajšega brata'))->younger()->brother(),
            Relationship::fixed(...$rel('sestra', 'sestre'))->sister(),
            Relationship::fixed(...$rel('brat', 'brata'))->brother(),
            Relationship::fixed(...$rel('brat/sestra', 'brata/sestre'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$rel('polsestra', 'polsestre'))->parent()->daughter(),
            Relationship::fixed(...$rel('polbrat', 'polbrata'))->parent()->son(),
            Relationship::fixed(...$rel('polbrat/polsestra', 'polbrata/polsestre'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$rel('mačeha', 'mačehe'))->parent()->wife(),
            Relationship::fixed(...$rel('očim', 'očima'))->parent()->husband(),
            Relationship::fixed(...$rel('očim/mačeha', 'očima/mačehe'))->parent()->married()->spouse(),
            Relationship::fixed(...$rel('pastorka', 'pastorke'))->married()->spouse()->daughter(),
            Relationship::fixed(...$rel('pastorek', 'pastorka'))->married()->spouse()->son(),
            Relationship::fixed(...$rel('pastorek/pastorka', 'pastorka/pastorke'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$rel('bivša žena', 'bivše žene'))->divorced()->partner()->female(),
            Relationship::fixed(...$rel('bivši mož', 'bivšega moža'))->divorced()->partner()->male(),
            Relationship::fixed(...$rel('bivši partner', 'bivšega partnerja'))->divorced()->partner(),
            Relationship::fixed(...$rel('zaročenka', 'zaročenke'))->engaged()->partner()->female(),
            Relationship::fixed(...$rel('zaročenec', 'zaročenca'))->engaged()->partner()->male(),
            Relationship::fixed(...$rel('žena', 'žene'))->wife(),
            Relationship::fixed(...$rel('mož', 'moža'))->husband(),
            Relationship::fixed(...$rel('zakonec', 'zakonca'))->spouse(),
            Relationship::fixed(...$rel('partner', 'partnerja'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$rel('tašča', 'tašče'))->married()->spouse()->mother(),
            Relationship::fixed(...$rel('tast', 'tasta'))->married()->spouse()->father(),
            Relationship::fixed(...$rel('tašča', 'tašče'))->spouse()->mother(),
            Relationship::fixed(...$rel('tast', 'tasta'))->spouse()->father(),
            // Children-in-law
            Relationship::fixed(...$rel('snaha', 'snahe'))->child()->wife(),
            Relationship::fixed(...$rel('zet', 'zeta'))->child()->husband(),
            Relationship::fixed(...$rel('zet/snaha', 'zeta/snahe'))->child()->married()->spouse(),
            // Siblings-in-law
            Relationship::fixed(...$rel('svakinja', 'svakinje'))->spouse()->sister(),
            Relationship::fixed(...$rel('svak', 'svaka'))->spouse()->brother(),
            Relationship::fixed(...$rel('snaha', 'snahe'))->sibling()->wife(),
            Relationship::fixed(...$rel('svak', 'svaka'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$rel('babica', 'babice'))->parent()->mother(),
            Relationship::fixed(...$rel('dedek', 'dedka'))->parent()->father(),
            Relationship::fixed(...$rel('babica/dedek', 'babice/dedka'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$rel('vnukinja', 'vnukinje'))->child()->daughter(),
            Relationship::fixed(...$rel('vnuk', 'vnuka'))->child()->son(),
            Relationship::fixed(...$rel('vnuk/vnukinja', 'vnuka/vnukinje'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$rel('teta', 'tete'))->parent()->sister(),
            Relationship::fixed(...$rel('ujec', 'ujca'))->mother()->brother(),
            Relationship::fixed(...$rel('stric', 'strica'))->father()->brother(),
            Relationship::fixed(...$rel('stric', 'strica'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$rel('nečakinja', 'nečakinje'))->sibling()->daughter(),
            Relationship::fixed(...$rel('nečak', 'nečaka'))->sibling()->son(),
            Relationship::fixed(...$rel('nečak/nečakinja', 'nečaka/nečakinje'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$rel('sestrična', 'sestrične'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$rel('bratranec', 'bratranca'))->parent()->sibling()->son(),
            Relationship::fixed(...$rel('bratranec/sestrična', 'bratranca/sestrične'))->parent()->sibling()->child(),
            // Dynamic relationships — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'babica', 'babice'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'dedek', 'dedka'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'babica/dedek', 'babice/dedka'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'vnukinja', 'vnukinje'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'vnuk', 'vnuka'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'vnuk/vnukinja', 'vnuka/vnukinje'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'teta', 'tete'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'stric', 'strica'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'nečakinja', 'nečakinje'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'nečak', 'nečaka'))->sibling()->descendant()->male(),
        ];
    }

    /**
     * Gregorian/Julian month names — case-inflected.
     *
     * @return array<int,string>
     */
}
