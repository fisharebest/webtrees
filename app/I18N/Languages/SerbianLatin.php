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
        /** @return array{string, string} */
    private function rel(string $nom, string $gen): array
    {
        return [$nom, '%s ' . $gen];
    }

    /** @return array{string, string} */
    private function pra(int $n, string $nom, string $gen): array
    {
        return [
            str_repeat('pra', $n) . $nom,
            '%s ' . str_repeat('pra', $n) . $gen,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->rel('usvojiteljka', 'usvojiteljke'))->adoptive()->mother(),
            Relationship::fixed(...$this->rel('usvojitelj', 'usvojitelja'))->adoptive()->father(),
            Relationship::fixed(...$this->rel('usvojitelj', 'usvojitelja'))->adoptive()->parent(),
            Relationship::fixed(...$this->rel('usvojena ćerka', 'usvojene ćerke'))->adopted()->daughter(),
            Relationship::fixed(...$this->rel('usvojeni sin', 'usvojenog sina'))->adopted()->son(),
            Relationship::fixed(...$this->rel('usvojeno dete', 'usvojenog deteta'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$this->rel('majka', 'majke'))->mother(),
            Relationship::fixed(...$this->rel('otac', 'oca'))->father(),
            Relationship::fixed(...$this->rel('roditelj', 'roditelja'))->parent(),
            // Children
            Relationship::fixed(...$this->rel('ćerka', 'ćerke'))->daughter(),
            Relationship::fixed(...$this->rel('sin', 'sina'))->son(),
            Relationship::fixed(...$this->rel('dete', 'deteta'))->child(),
            // Siblings
            Relationship::fixed(...$this->rel('sestra bliznakinja', 'sestre bliznakinje'))->multiple()->sister(),
            Relationship::fixed(...$this->rel('brat blizanac', 'brata blizanca'))->multiple()->brother(),
            Relationship::fixed(...$this->rel('blizanac', 'blizanca'))->multiple()->sibling(),
            Relationship::fixed(...$this->rel('starija sestra', 'starije sestre'))->older()->sister(),
            Relationship::fixed(...$this->rel('stariji brat', 'starijeg brata'))->older()->brother(),
            Relationship::fixed(...$this->rel('mlađa sestra', 'mlađe sestre'))->younger()->sister(),
            Relationship::fixed(...$this->rel('mlađi brat', 'mlađeg brata'))->younger()->brother(),
            Relationship::fixed(...$this->rel('sestra', 'sestre'))->sister(),
            Relationship::fixed(...$this->rel('brat', 'brata'))->brother(),
            Relationship::fixed(...$this->rel('brat/sestra', 'brata/sestre'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->rel('polusestra', 'polusestre'))->parent()->daughter(),
            Relationship::fixed(...$this->rel('polubrat', 'polubrata'))->parent()->son(),
            Relationship::fixed(...$this->rel('polubrat/polusestra', 'polubrata/polusestre'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->rel('maćeha', 'maćehe'))->parent()->wife(),
            Relationship::fixed(...$this->rel('očuh', 'očuha'))->parent()->husband(),
            Relationship::fixed(...$this->rel('poočim', 'poočima'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->rel('pastorka', 'pastorke'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('pastorak', 'pastorka'))->married()->spouse()->son(),
            Relationship::fixed(...$this->rel('pastorče', 'pastorčeta'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->rel('bivša supruga', 'bivše supruge'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->rel('bivši suprug', 'bivšeg supruga'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->rel('bivši partner', 'bivšeg partnera'))->divorced()->partner(),
            Relationship::fixed(...$this->rel('verenica', 'verenice'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->rel('verenik', 'verenika'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->rel('supruga', 'supruge'))->wife(),
            Relationship::fixed(...$this->rel('suprug', 'supruga'))->husband(),
            Relationship::fixed(...$this->rel('supružnik', 'supružnika'))->spouse(),
            Relationship::fixed(...$this->rel('partner', 'partnera'))->partner(),
            // In-laws (wife's parents — tašta/tast)
            Relationship::fixed(...$this->rel('tašta', 'tašte'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->rel('tast', 'tasta'))->married()->spouse()->father(),
            // In-laws (husband's parents — svekrva/svekar)
            Relationship::fixed(...$this->rel('svekrva', 'svekrve'))->spouse()->mother(),
            Relationship::fixed(...$this->rel('svekar', 'svekra'))->spouse()->father(),
            // Children-in-law
            Relationship::fixed(...$this->rel('snaha', 'snahe'))->child()->wife(),
            Relationship::fixed(...$this->rel('zet', 'zeta'))->child()->husband(),
            Relationship::fixed(...$this->rel('zet/snaha', 'zeta/snahe'))->child()->married()->spouse(),
            // Siblings-in-law
            Relationship::fixed(...$this->rel('zaova', 'zaove'))->spouse()->sister(),
            Relationship::fixed(...$this->rel('dever', 'devera'))->spouse()->brother(),
            Relationship::fixed(...$this->rel('svastika', 'svastike'))->sibling()->wife(),
            Relationship::fixed(...$this->rel('šurak', 'šuraka'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->rel('baka', 'bake'))->parent()->mother(),
            Relationship::fixed(...$this->rel('deda', 'dede'))->parent()->father(),
            Relationship::fixed(...$this->rel('baka/deda', 'bake/dede'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->rel('unuka', 'unuke'))->child()->daughter(),
            Relationship::fixed(...$this->rel('unuk', 'unuka'))->child()->son(),
            Relationship::fixed(...$this->rel('unuk/unuka', 'unuka/unuke'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->rel('tetka', 'tetke'))->parent()->sister(),
            Relationship::fixed(...$this->rel('ujak', 'ujaka'))->mother()->brother(),
            Relationship::fixed(...$this->rel('stric', 'strica'))->father()->brother(),
            Relationship::fixed(...$this->rel('stric', 'strica'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->rel('nećakinja', 'nećakinje'))->sibling()->daughter(),
            Relationship::fixed(...$this->rel('nećak', 'nećaka'))->sibling()->son(),
            Relationship::fixed(...$this->rel('nećak/nećakinja', 'nećaka/nećakinje'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$this->rel('sestrična', 'sestrične'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->rel('bratić', 'bratića'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->rel('bratić/sestrična', 'bratića/sestrične'))->parent()->sibling()->child(),

            // Dynamic relationships — great-grandparents and beyond
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'baka', 'bake'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'deda', 'dede'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'baka/deda', 'bake/dede'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'unuka', 'unuke'))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'unuk', 'unuka'))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'unuk/unuka', 'unuka/unuke'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'tetka', 'tetke'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'stric', 'strica'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'nećakinja', 'nećakinje'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'nećak', 'nećaka'))->sibling()->descendant()->male(),
        ];
    }
}
