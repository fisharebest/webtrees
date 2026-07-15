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

final readonly class Italian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'italiano';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'it';
    protected const string    LOCALE_CODE        = 'it_IT@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'circa %s';
    protected const string    DATE_AFTER         = 'dopo il %s';
    protected const string    DATE_BEFORE        = 'prima del %s';
    protected const string    DATE_BETWEEN_AND   = 'tra il %s e il %s';
    protected const string    DATE_CALCULATED    = '%s (calcolata)';
    protected const string    DATE_ESTIMATED     = '%s (stimata)';
    protected const string    DATE_FROM          = 'dal %s';
    protected const string    DATE_FROM_TO       = 'dal %s al %s';
    protected const string    DATE_INTERPRETED   = 'interpretato %s';
    protected const string    DATE_TO            = 'fino al %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'a.C.';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'd.C.';
    protected const string    LIST_SEPARATOR_AND = ' e ';
    protected const string    LIST_SEPARATOR_OR  = ' o ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'gennaio',
        'febbraio',
        'marzo',
        'aprile',
        'maggio',
        'giugno',
        'luglio',
        'agosto',
        'settembre',
        'ottobre',
        'novembre',
        'dicembre',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tishrì',
        'Cheshvàn',
        'Kislèv',
        'Tevèt',
        'Shevàt',
        'Adàr I',
        'Adr II',
        'Adàr',
        'Nisàn',
        'Iyàr',
        'Sivàn',
        'Tamùz',
        'Av',
        'Elùl',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Vendemmiaio',
        'Brumaio',
        'Frimaio',
        'Nevoso',
        'Piovoso',
        'Ventoso',
        'Germinale',
        'Floreale',
        'Pratile',
        'Messidoro',
        'Termidoro',
        'Fruttidoro',
        'giorni complementari',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Muharram',
        'Safar',
        'Rabi al-Awwal',
        'Rabi al-Thani',
        'Jumada al-Awwal',
        'Jumada al-Thani',
        'Rajab',
        'Shaaban',
        'Ramadan',
        'Shawwal',
        'Dhu al-Qida',
        'Dhu al-Hijja',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'farvardin',
        'ordibehesht',
        'khordad',
        'tir',
        'mordad',
        'shahrivar',
        'mehr',
        'aban',
        'azar',
        'dey',
        'bahman',
        'esfand',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Italian genitive: "della" (f), "del" (m), "dello" (m before s+cons/z), "dell'" (before vowel)
        $della = static fn (string $s): array => [$s, '%s della ' . $s];
        $del   = static fn (string $s): array => [$s, '%s del ' . $s];
        $dello = static fn (string $s): array => [$s, '%s dello ' . $s];
        $dell  = static fn (string $s): array => [$s, "%s dell'" . $s];

        $great = static fn (int $n, string $suffix, string $article): array => [
            ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : $n . '°')) . $suffix,
            '%s ' . $article . ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : $n . '°')) . $suffix,
        ];

        return [
            // Adopted
            Relationship::fixed(...$della('madre adottiva'))->adoptive()->mother(),
            Relationship::fixed(...$del('padre adottivo'))->adoptive()->father(),
            Relationship::fixed(...$del('genitore adottivo'))->adoptive()->parent(),
            Relationship::fixed(...$della('figlia adottiva'))->adopted()->daughter(),
            Relationship::fixed(...$del('figlio adottivo'))->adopted()->son(),
            Relationship::fixed(...$del('figlio/a adottivo/a'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$della('madre'))->mother(),
            Relationship::fixed(...$del('padre'))->father(),
            Relationship::fixed(...$del('genitore'))->parent(),
            // Children
            Relationship::fixed(...$della('figlia'))->daughter(),
            Relationship::fixed(...$del('figlio'))->son(),
            Relationship::fixed(...$del('figlio/a'))->child(),
            // Siblings
            Relationship::fixed(...$della('sorella gemella'))->twin()->sister(),
            Relationship::fixed(...$del('fratello gemello'))->twin()->brother(),
            Relationship::fixed(...$del('gemello/a'))->twin()->sibling(),
            Relationship::fixed(...$della('sorella maggiore'))->older()->sister(),
            Relationship::fixed(...$del('fratello maggiore'))->older()->brother(),
            Relationship::fixed(...$del('fratello/sorella maggiore'))->older()->sibling(),
            Relationship::fixed(...$della('sorella minore'))->younger()->sister(),
            Relationship::fixed(...$del('fratello minore'))->younger()->brother(),
            Relationship::fixed(...$del('fratello/sorella minore'))->younger()->sibling(),
            Relationship::fixed(...$della('sorella'))->sister(),
            Relationship::fixed(...$del('fratello'))->brother(),
            Relationship::fixed(...$del('fratello/sorella'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$della('sorellastra'))->parent()->daughter(),
            Relationship::fixed(...$del('fratellastro'))->parent()->son(),
            Relationship::fixed(...$del('fratellastro/sorellastra'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$della('matrigna'))->parent()->wife(),
            Relationship::fixed(...$del('patrigno'))->parent()->husband(),
            Relationship::fixed(...$del('patrigno/matrigna'))->parent()->married()->spouse(),
            Relationship::fixed(...$della('figliastra'))->married()->spouse()->daughter(),
            Relationship::fixed(...$del('figliastro'))->married()->spouse()->son(),
            Relationship::fixed(...$del('figliastro/a'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$dell('ex-moglie'))->divorced()->partner()->female(),
            Relationship::fixed(...$dell('ex-marito'))->divorced()->partner()->male(),
            Relationship::fixed(...$dell('ex-coniuge'))->divorced()->partner(),
            Relationship::fixed(...$della('fidanzata'))->engaged()->partner()->female(),
            Relationship::fixed(...$del('fidanzato'))->engaged()->partner()->male(),
            Relationship::fixed(...$della('moglie'))->wife(),
            Relationship::fixed(...$del('marito'))->husband(),
            Relationship::fixed(...$del('coniuge'))->spouse(),
            Relationship::fixed(...$del('partner'))->partner(),
            // In-laws
            Relationship::fixed(...$della('suocera'))->married()->spouse()->mother(),
            Relationship::fixed(...$del('suocero'))->married()->spouse()->father(),
            Relationship::fixed(...$del('suocero/a'))->married()->spouse()->parent(),
            Relationship::fixed(...$della('nuora'))->child()->wife(),
            Relationship::fixed(...$del('genero'))->child()->husband(),
            Relationship::fixed(...$del('genero/nuora'))->child()->married()->spouse(),
            Relationship::fixed(...$della('cognata'))->spouse()->sister(),
            Relationship::fixed(...$del('cognato'))->spouse()->brother(),
            Relationship::fixed(...$della('cognata'))->sibling()->wife(),
            Relationship::fixed(...$del('cognato'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$della('nonna'))->parent()->mother(),
            Relationship::fixed(...$del('nonno'))->parent()->father(),
            Relationship::fixed(...$del('nonno/a'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$della('nipote'))->child()->daughter(),
            Relationship::fixed(...$del('nipote'))->child()->son(),
            Relationship::fixed(...$del('nipote'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$della('zia'))->parent()->sister(),
            Relationship::fixed(...$dello('zio'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$della('nipote'))->sibling()->daughter(),
            Relationship::fixed(...$del('nipote'))->sibling()->son(),
            // Cousins
            Relationship::fixed(...$della('cugina'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$del('cugino'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'nonna', 'della '))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'nonno', 'del '))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'nonno/a', 'del '))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nipote', 'della '))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nipote', 'del '))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nipote', 'del '))->descendant(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'zia', 'della '))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'zio', 'dello '))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'nipote', 'della '))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'nipote', 'del '))->sibling()->descendant()->male(),
        ];
    }
}
