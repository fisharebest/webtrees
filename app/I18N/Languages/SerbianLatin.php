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

final readonly class SerbianLatin extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::ThreeFormsSlavic;

    protected const string    ENDONYM            = 'srpski';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'sr-Latn';
    protected const string    LOCALE_CODE        = 'sr_RS@latin;collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'oko %s';
    protected const string    DATE_AFTER         = 'posle %s';
    protected const string    DATE_BEFORE        = 'pre %s';
    protected const string    DATE_BETWEEN_AND   = 'između %s i %s';
    protected const string    DATE_CALCULATED    = 'izračunato %s';
    protected const string    DATE_ESTIMATED     = 'procenjeno %s';
    protected const string    DATE_FROM          = 'od %s';
    protected const string    DATE_FROM_TO       = 'od %s do %s';
    protected const string    DATE_INTERPRETED   = 'protumačeno %s';
    protected const string    DATE_TO            = 'do %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'p.n.e';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'n.e';
    protected const string    LIST_SEPARATOR_AND = ' i ';
    protected const string    LIST_SEPARATOR_OR  = ' ili ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Januar',
        'Februar',
        'Mart',
        'April',
        'Maj',
        'Jun',
        'Jul',
        'Avgust',
        'Septembar',
        'Oktobar',
        'Novembar',
        'Decembar',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'januara',
        'februara',
        'marta',
        'aprila',
        'maja',
        'juna',
        'jula',
        'avgusta',
        'septembra',
        'oktobra',
        'novembra',
        'decembra',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'januaru',
        'februaru',
        'martu',
        'aprilu',
        'maju',
        'junu',
        'julu',
        'avgustu',
        'septembru',
        'oktobru',
        'novembru',
        'decembru',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'januara',
        'februara',
        'marta',
        'aprila',
        'majem',
        'juna',
        'jula',
        'avgusta',
        'septembra',
        'oktobra',
        'novembra',
        'decembra',
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
        'Muharrem',
        'Safer',
        'Rabi’ al-awwal',
        'Rabi’ al-thani',
        'Džumade-l-ula',
        'Džumade-l-uhra',
        'Redžeb',
        'Ša’ban',
        'Ramazan',
        'Ševval',
        'Zu-l-ka’de',
        'Zu-l-hidždže',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = [
        '',
        'Muharrem',
        'Safera',
        'Rabi’ al-awwal',
        'Rabi’ al-thani',
        'Džumade-l-ula',
        'Džumade-l-uhra',
        'Redžeba',
        'Ša’bana',
        'Ramazana',
        'Ševvala',
        'Zu-l-ka’dea',
        'Zu-l-hidždžea',
    ];

    protected const array HIJRI_MONTHS_LOCATIVE = [
        '',
        'Muharrem',
        'Saferu',
        'Rabi’ al-awwal',
        'Rabi’ al-thani',
        'Džumade-l-ulau',
        'Džumade-l-uhrau',
        'Redžebu',
        'Ša’banu',
        'Ramazanu',
        'Ševvalu',
        'Zu-l-ka’deu',
        'Zu-l-hidždžeu',
    ];

    protected const array HIJRI_MONTHS_INSTRUMENTAL = [
        '',
        'Muharrem',
        'Saferom',
        'Rabi’ al-awwal',
        'Rabi’ al-thani',
        'Džumade-l-ulaom',
        'Džumade-l-uhraom',
        'Redžebom',
        'Ša’banom',
        'Ramazanom',
        'Ševvalom',
        'Zu-l-ka’deom',
        'Zu-l-hidždžeom',
    ];

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
        'D' . UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
        UTF8::LATIN_CAPITAL_LETTER_D_WITH_STROKE,
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'LJ',
        'M',
        'N',
        'NJ',
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
        // Serbian Latin genitive helper: [nominative, '%s ' . genitive]
        $rel = static fn (string $nom, string $gen): array => [$nom, '%s ' . $gen];

        // Dynamic "pra" prefix for great-grandparents
        $pra = static fn (int $n, string $nom, string $gen): array => [
            str_repeat('pra', $n) . $nom,
            '%s ' . str_repeat('pra', $n) . $gen,
        ];

        return [
            // Adopted
            Relationship::fixed(...$rel('usvojiteljka', 'usvojiteljke'))->adoptive()->mother(),
            Relationship::fixed(...$rel('usvojitelj', 'usvojitelja'))->adoptive()->father(),
            Relationship::fixed(...$rel('usvojitelj', 'usvojitelja'))->adoptive()->parent(),
            Relationship::fixed(...$rel('usvojena ćerka', 'usvojene ćerke'))->adopted()->daughter(),
            Relationship::fixed(...$rel('usvojeni sin', 'usvojenog sina'))->adopted()->son(),
            Relationship::fixed(...$rel('usvojeno dete', 'usvojenog deteta'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$rel('majka', 'majke'))->mother(),
            Relationship::fixed(...$rel('otac', 'oca'))->father(),
            Relationship::fixed(...$rel('roditelj', 'roditelja'))->parent(),
            // Children
            Relationship::fixed(...$rel('ćerka', 'ćerke'))->daughter(),
            Relationship::fixed(...$rel('sin', 'sina'))->son(),
            Relationship::fixed(...$rel('dete', 'deteta'))->child(),
            // Siblings
            Relationship::fixed(...$rel('sestra bliznakinja', 'sestre bliznakinje'))->twin()->sister(),
            Relationship::fixed(...$rel('brat blizanac', 'brata blizanca'))->twin()->brother(),
            Relationship::fixed(...$rel('blizanac', 'blizanca'))->twin()->sibling(),
            Relationship::fixed(...$rel('starija sestra', 'starije sestre'))->older()->sister(),
            Relationship::fixed(...$rel('stariji brat', 'starijeg brata'))->older()->brother(),
            Relationship::fixed(...$rel('mlađa sestra', 'mlađe sestre'))->younger()->sister(),
            Relationship::fixed(...$rel('mlađi brat', 'mlađeg brata'))->younger()->brother(),
            Relationship::fixed(...$rel('sestra', 'sestre'))->sister(),
            Relationship::fixed(...$rel('brat', 'brata'))->brother(),
            Relationship::fixed(...$rel('brat/sestra', 'brata/sestre'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$rel('polusestra', 'polusestre'))->parent()->daughter(),
            Relationship::fixed(...$rel('polubrat', 'polubrata'))->parent()->son(),
            Relationship::fixed(...$rel('polubrat/polusestra', 'polubrata/polusestre'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$rel('maćeha', 'maćehe'))->parent()->wife(),
            Relationship::fixed(...$rel('očuh', 'očuha'))->parent()->husband(),
            Relationship::fixed(...$rel('poočim', 'poočima'))->parent()->married()->spouse(),
            Relationship::fixed(...$rel('pastorka', 'pastorke'))->married()->spouse()->daughter(),
            Relationship::fixed(...$rel('pastorak', 'pastorka'))->married()->spouse()->son(),
            Relationship::fixed(...$rel('pastorče', 'pastorčeta'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$rel('bivša supruga', 'bivše supruge'))->divorced()->partner()->female(),
            Relationship::fixed(...$rel('bivši suprug', 'bivšeg supruga'))->divorced()->partner()->male(),
            Relationship::fixed(...$rel('bivši partner', 'bivšeg partnera'))->divorced()->partner(),
            Relationship::fixed(...$rel('verenica', 'verenice'))->engaged()->partner()->female(),
            Relationship::fixed(...$rel('verenik', 'verenika'))->engaged()->partner()->male(),
            Relationship::fixed(...$rel('supruga', 'supruge'))->wife(),
            Relationship::fixed(...$rel('suprug', 'supruga'))->husband(),
            Relationship::fixed(...$rel('supružnik', 'supružnika'))->spouse(),
            Relationship::fixed(...$rel('partner', 'partnera'))->partner(),
            // In-laws (wife's parents — tašta/tast)
            Relationship::fixed(...$rel('tašta', 'tašte'))->married()->spouse()->mother(),
            Relationship::fixed(...$rel('tast', 'tasta'))->married()->spouse()->father(),
            // In-laws (husband's parents — svekrva/svekar)
            Relationship::fixed(...$rel('svekrva', 'svekrve'))->spouse()->mother(),
            Relationship::fixed(...$rel('svekar', 'svekra'))->spouse()->father(),
            // Children-in-law
            Relationship::fixed(...$rel('snaha', 'snahe'))->child()->wife(),
            Relationship::fixed(...$rel('zet', 'zeta'))->child()->husband(),
            Relationship::fixed(...$rel('zet/snaha', 'zeta/snahe'))->child()->married()->spouse(),
            // Siblings-in-law
            Relationship::fixed(...$rel('zaova', 'zaove'))->spouse()->sister(),
            Relationship::fixed(...$rel('dever', 'devera'))->spouse()->brother(),
            Relationship::fixed(...$rel('svastika', 'svastike'))->sibling()->wife(),
            Relationship::fixed(...$rel('šurak', 'šuraka'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$rel('baka', 'bake'))->parent()->mother(),
            Relationship::fixed(...$rel('deda', 'dede'))->parent()->father(),
            Relationship::fixed(...$rel('baka/deda', 'bake/dede'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$rel('unuka', 'unuke'))->child()->daughter(),
            Relationship::fixed(...$rel('unuk', 'unuka'))->child()->son(),
            Relationship::fixed(...$rel('unuk/unuka', 'unuka/unuke'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$rel('tetka', 'tetke'))->parent()->sister(),
            Relationship::fixed(...$rel('ujak', 'ujaka'))->mother()->brother(),
            Relationship::fixed(...$rel('stric', 'strica'))->father()->brother(),
            Relationship::fixed(...$rel('stric', 'strica'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$rel('nećakinja', 'nećakinje'))->sibling()->daughter(),
            Relationship::fixed(...$rel('nećak', 'nećaka'))->sibling()->son(),
            Relationship::fixed(...$rel('nećak/nećakinja', 'nećaka/nećakinje'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$rel('sestrična', 'sestrične'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$rel('bratić', 'bratića'))->parent()->sibling()->son(),
            Relationship::fixed(...$rel('bratić/sestrična', 'bratića/sestrične'))->parent()->sibling()->child(),

            // Dynamic relationships — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'baka', 'bake'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'deda', 'dede'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'baka/deda', 'bake/dede'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'unuka', 'unuke'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'unuk', 'unuka'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'unuk/unuka', 'unuka/unuke'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'tetka', 'tetke'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'stric', 'strica'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'nećakinja', 'nećakinje'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'nećak', 'nećaka'))->sibling()->descendant()->male(),
        ];
    }
}
