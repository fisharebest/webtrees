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

final readonly class Polish extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::ThreeFormsPolish;

    protected const string    ENDONYM            = 'polski';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'pl';
    protected const string    LOCALE_CODE        = 'pl_PL@collation=phonebook';
    protected const int       MINIMUM_GROUPING_DIGITS = 2;
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'ok. %s';
    protected const string    DATE_AFTER         = 'po %s';
    protected const string    DATE_BEFORE        = 'przed %s';
    protected const string    DATE_BETWEEN_AND   = 'pomiędzy %s a %s';
    protected const string    DATE_CALCULATED    = 'wyliczone na %s';
    protected const string    DATE_ESTIMATED     = 'szacowane na %s';
    protected const string    DATE_FROM          = 'od %s';
    protected const string    DATE_FROM_TO       = 'od %s do %s';
    protected const string    DATE_INTERPRETED   = 'zinterpretowane jako %s';
    protected const string    DATE_TO            = 'do %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'p.n.e.';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'n.e.';
    protected const string    LIST_SEPARATOR_AND = ' i ';
    protected const string    LIST_SEPARATOR_OR  = ' lub ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'styczeń',
        'luty',
        'marzec',
        'kwiecień',
        'maj',
        'czerwiec',
        'lipiec',
        'sierpień',
        'wrzesień',
        'październik',
        'listopad',
        'grudzień',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'stycznia',
        'lutego',
        'marca',
        'kwietnia',
        'maja',
        'czerwca',
        'lipca',
        'sierpnia',
        'września',
        'października',
        'listopada',
        'grudnia',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'styczniu',
        'lutym',
        'marcu',
        'kwietniu',
        'maju',
        'czerwcu',
        'lipcu',
        'sierpniu',
        'wrześniu',
        'październiku',
        'listopadzie',
        'grudniu',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'styczniem',
        'lutym',
        'marcem',
        'kwietniem',
        'majem',
        'czerwcem',
        'lipcem',
        'sierpniem',
        'wrześniem',
        'październikiem',
        'listopadem',
        'grudniem',
    ];

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'tiszri',
        'cheszwan',
        'kislew',
        'tewet',
        'szwat',
        'adar I',
        'adar II',
        'adar',
        'nisan',
        'ijar',
        'siwan',
        'tamuz',
        'aw',
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
        'Dni Sankiulotów',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'muharram',
        'safar',
        'rabi al-awwal',
        'rabi al-sani',
        'dżumada al-ula',
        'dżumada as-sani',
        'radżab',
        'szaban',
        'ramadan',
        'szawwal',
        'zu al-kada',
        'zu al-hidżdża',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Farwardin',
        'Ordibeheszt',
        'Chordad',
        'Tir',
        'Mordad',
        'Szahriwar',
        'Mehr',
        'Aban',
        'Asar',
        'Dei',
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
        UTF8::LATIN_CAPITAL_LETTER_C_WITH_ACUTE,
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        UTF8::LATIN_CAPITAL_LETTER_L_WITH_STROKE,
        'M',
        'N',
        'O',
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
        'P',
        'Q',
        'R',
        'S',
        UTF8::LATIN_CAPITAL_LETTER_S_WITH_ACUTE,
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        UTF8::LATIN_CAPITAL_LETTER_Z_WITH_ACUTE,
        UTF8::LATIN_CAPITAL_LETTER_Z_WITH_DOT_ABOVE,
    ];

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'C' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_CAPITAL_LETTER_C_WITH_ACUTE,
            'L' . UTF8::COMBINING_SHORT_STROKE_OVERLAY => UTF8::LATIN_CAPITAL_LETTER_L_WITH_STROKE,
            'O' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
            'S' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_CAPITAL_LETTER_S_WITH_ACUTE,
            'Z' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_ACUTE,
            'Z' . UTF8::COMBINING_DOT_ABOVE            => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_DOT_ABOVE,
            'c' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_SMALL_LETTER_C_WITH_ACUTE,
            'l' . UTF8::COMBINING_SHORT_STROKE_OVERLAY => UTF8::LATIN_SMALL_LETTER_L_WITH_STROKE,
            'o' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_SMALL_LETTER_O_WITH_ACUTE,
            's' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_SMALL_LETTER_S_WITH_ACUTE,
            'z' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_SMALL_LETTER_Z_WITH_ACUTE,
            'z' . UTF8::COMBINING_DOT_ABOVE            => UTF8::LATIN_SMALL_LETTER_Z_WITH_DOT_ABOVE,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        $pra = static fn (int $n, string $nominative, string $genitive): array => [
            ($n > 3 ? 'pra ×' . $n . ' ' : str_repeat('pra', $n)) . $nominative,
            ($n > 3 ? 'pra ×' . $n . ' ' : str_repeat('pra', $n)) . $genitive,
        ];

        return [
            // Parents
            Relationship::fixed('ojciec', '%s ojca')->father(),
            Relationship::fixed('matka', '%s matki')->mother(),
            Relationship::fixed('rodzic', '%s rodzica')->parent(),
            // Children
            Relationship::fixed('syn', '%s syna')->son(),
            Relationship::fixed('córka', '%s córki')->daughter(),
            Relationship::fixed('dziecko', '%s dziecka')->child(),
            // Siblings
            Relationship::fixed('starsza siostra', '%s starszej siostry')->older()->sister(),
            Relationship::fixed('starszy brat', '%s starszego brata')->older()->brother(),
            Relationship::fixed('starsze rodzeństwo', '%s starszego rodzeństwa')->older()->sibling(),
            Relationship::fixed('młodsza siostra', '%s młodszej siostry')->younger()->sister(),
            Relationship::fixed('młodszy brat', '%s młodszego brata')->younger()->brother(),
            Relationship::fixed('młodsze rodzeństwo', '%s młodszego rodzeństwa')->younger()->sibling(),
            Relationship::fixed('brat', '%s brata')->brother(),
            Relationship::fixed('siostra', '%s siostry')->sister(),
            Relationship::fixed('rodzeństwo', '%s rodzeństwa')->sibling(),
            // Divorced partners
            Relationship::fixed('była żona', '%s byłej żony')->divorced()->partner()->female(),
            Relationship::fixed('były mąż', '%s byłego męża')->divorced()->partner()->male(),
            Relationship::fixed('były partner/partnerka', '%s byłego partnera/partnerki')->divorced()->partner(),
            // Engaged partners
            Relationship::fixed('narzeczona', '%s narzeczonej')->engaged()->partner()->female(),
            Relationship::fixed('narzeczony', '%s narzeczonego')->engaged()->partner()->male(),
            // Married partners
            Relationship::fixed('żona', '%s żony')->wife(),
            Relationship::fixed('mąż', '%s męża')->husband(),
            Relationship::fixed('małżonek/małżonka', '%s małżonka/małżonki')->spouse(),
            // Unmarried partners
            Relationship::fixed('partnerka', '%s partnerki')->partner()->female(),
            Relationship::fixed('partner', '%s partnera')->partner(),
            // In-laws (via wife)
            Relationship::fixed('teść', '%s teścia')->wife()->father(),
            Relationship::fixed('teściowa', '%s teściowej')->wife()->mother(),
            // In-laws (via spouse)
            Relationship::fixed('teść', '%s teścia')->spouse()->father(),
            Relationship::fixed('teściowa', '%s teściowej')->spouse()->mother(),
            Relationship::fixed('zięć', '%s zięcia')->child()->husband(),
            Relationship::fixed('synowa', '%s synowej')->child()->wife(),
            Relationship::fixed('szwagier', '%s szwagra')->spouse()->brother(),
            Relationship::fixed('szwagier', '%s szwagra')->sibling()->husband(),
            Relationship::fixed('szwagierka', '%s szwagierki')->spouse()->sister(),
            Relationship::fixed('szwagierka', '%s szwagierki')->sibling()->wife(),
            // Step-parents
            Relationship::fixed('macocha', '%s macochy')->parent()->spouse()->female(),
            Relationship::fixed('ojczym', '%s ojczyma')->parent()->spouse()->male(),
            // Step-children
            Relationship::fixed('pasierbica', '%s pasierbicy')->spouse()->daughter(),
            Relationship::fixed('pasierb', '%s pasierba')->spouse()->son(),
            // Half-siblings
            Relationship::fixed('brat przyrodni', '%s brata przyrodniego')->parent()->son(),
            Relationship::fixed('siostra przyrodnia', '%s siostry przyrodniej')->parent()->daughter(),
            Relationship::fixed('rodzeństwo przyrodnie', '%s rodzeństwa przyrodniego')->parent()->child(),
            // Grandparents
            Relationship::fixed('dziadek', '%s dziadka')->parent()->father(),
            Relationship::fixed('babcia', '%s babci')->parent()->mother(),
            Relationship::fixed('dziadek/babcia', '%s dziadka/babci')->parent()->parent(),
            // Great-grandparents (fixed)
            Relationship::fixed('pradziadek', '%s pradziadka')->parent()->parent()->father(),
            Relationship::fixed('prababcia', '%s prababci')->parent()->parent()->mother(),
            Relationship::fixed('pradziadek/prababcia', '%s pradziadka/prababci')->parent()->parent()->parent(),
            // Ancestors (dynamic)
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'dziadek', '%s pradziadka'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'babcia', '%s prababci'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'dziadek/babcia', '%s pradziadka/prababci'))->ancestor(),
            // Grandchildren
            Relationship::fixed('wnuk', '%s wnuka')->child()->son(),
            Relationship::fixed('wnuczka', '%s wnuczki')->child()->daughter(),
            Relationship::fixed('wnuk/wnuczka', '%s wnuka/wnuczki')->child()->child(),
            // Great-grandchildren (fixed)
            Relationship::fixed('prawnuk', '%s prawnuka')->child()->child()->son(),
            Relationship::fixed('prawnuczka', '%s prawnuczki')->child()->child()->daughter(),
            Relationship::fixed('prawnuk/prawnuczka', '%s prawnuka/prawnuczki')->child()->child()->child(),
            // Descendants (dynamic)
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'wnuk', '%s prawnuka'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'wnuczka', '%s prawnuczki'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'wnuk/wnuczka', '%s prawnuka/prawnuczki'))->descendant(),
            // Aunts and uncles
            Relationship::fixed('wujek', '%s wujka')->mother()->brother(),
            Relationship::fixed('stryj', '%s stryja')->father()->brother(),
            Relationship::fixed('ciotka', '%s ciotki')->parent()->sister(),
            Relationship::fixed('wujek/stryj', '%s wujka/stryja')->parent()->brother(),
            // Great-aunts and great-uncles
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'wujek/stryj', '%s prawujka/prastryja'))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'ciotka', '%s praciotki'))->ancestor()->sister(),
            // Nieces and nephews (sister's children)
            Relationship::fixed('siostrzenica', '%s siostrzenicy')->sister()->daughter(),
            Relationship::fixed('siostrzeniec', '%s siostrzeńca')->sister()->son(),
            // Nieces and nephews (brother's children)
            Relationship::fixed('bratanica', '%s bratanicy')->brother()->daughter(),
            Relationship::fixed('bratanek', '%s bratanka')->brother()->son(),
            // Generic nieces and nephews
            Relationship::fixed('siostrzenica/bratanica', '%s siostrzenicy/bratanicy')->sibling()->daughter(),
            Relationship::fixed('siostrzeniec/bratanek', '%s siostrzeńca/bratanka')->sibling()->son(),
            Relationship::fixed('siostrzeniec/bratanek', '%s siostrzeńca/bratanka')->sibling()->child(),
            // Great-nieces and great-nephews
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'siostrzeniec/bratanek', '%s prasiostrzeńca/prabratanka'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'siostrzenica/bratanica', '%s prasiostrzenicy/prabratanicy'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'siostrzeniec/bratanek', '%s prasiostrzeńca/prabratanka'))->sibling()->descendant(),
            // Cousins
            Relationship::dynamic(static fn (int $n): array => ['kuzynka', '%s kuzynki'])->symmetricCousin()->female(),
            Relationship::dynamic(static fn (int $n): array => ['kuzyn', '%s kuzyna'])->symmetricCousin()->male(),
            Relationship::dynamic(static fn (int $n): array => ['kuzyn/kuzynka', '%s kuzyna/kuzynki'])->symmetricCousin(),
        ];
    }

    /**
     * Gregorian/Julian month names — case-inflected.
     *
     * @return array<int,string>
     */
}
