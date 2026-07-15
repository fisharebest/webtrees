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

final readonly class Kurdish extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'kurd';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ku';
    protected const string    LOCALE_CODE        = 'ku_TR@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'Der dorê %s';
    protected const string    DATE_AFTER         = 'paşê %s';
    protected const string    DATE_BEFORE        = 'Berya/berê %s';
    protected const string    DATE_BETWEEN_AND   = 'Navbera %s û %s';
    protected const string    DATE_CALCULATED    = 'Çortik %s';
    protected const string    DATE_ESTIMATED     = 'Texmînî %s';
    protected const string    DATE_FROM          = 'ji %s';
    protected const string    DATE_FROM_TO       = 'Ji %s heya %s';
    protected const string    DATE_INTERPRETED   = 'Şirovekirinî %s';
    protected const string    DATE_TO            = 'heya/ ta %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'BZ';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'PZ';
    protected const string    LIST_SEPARATOR_AND = ' û ';
    protected const string    LIST_SEPARATOR_OR  = ' an ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Rêbendan',
        'Reşemî',
        'Adar',
        'Avrêl',
        'Gulan',
        'Pûşper',
        'Tîrmeh',
        'Gelawêj',
        'Rezber',
        'Kewçêr',
        'Sermawez',
        'Berfanbar',
    ];
    protected const string    PERCENT_FORMAT     = '%%%s';

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'Çile',
        'Sibat',
        'Adar',
        'Nîsan',
        'Gulan',
        'Hezîran',
        'Tîrmeh',
        'Gelawêj',
        'Îlon',
        'Cotmeh',
        'Sermawez',
        'Berfanbar',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'Rêbendan',
        'Reşemî',
        'Adar',
        'Nîsan',
        'Gulan',
        'Pûşper',
        'Tîrmeh',
        'Gelawêj',
        'Îlon',
        'Cotmeh',
        'Sermawez',
        'Berfanbar',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

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
        'sertayê rojan',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Muherrem',
        'Sefer',
        'Rebîûl-ewwel',
        'Rebîûl-axir',
        'Cemazîyel-ewwel',
        'Cemazîyel-axir',
        'Receb',
        'Şeban',
        'Ramazan',
        'Şewwal',
        'Zîlqade',
        'Zîlhicce',
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
        // Kurmanji genitive: ezafe construction — "yê" linking
        $ku = static fn (string $s): array => [$s, $s . ' yê %s'];

        // "kal" prefix for great-grandparents, repeating for each generation
        $great = static function (int $n, string $nom): array {
            $prefix = str_repeat('kal', $n);

            return [$prefix . $nom, $prefix . $nom . ' yê %s'];
        };

        return [
            // Parents
            Relationship::fixed(...$ku('dayik'))->mother(),
            Relationship::fixed(...$ku('bav'))->father(),
            Relationship::fixed(...$ku('dêûbav'))->parent(),
            // Children
            Relationship::fixed(...$ku('keç'))->daughter(),
            Relationship::fixed(...$ku('kur'))->son(),
            Relationship::fixed(...$ku('zarok'))->child(),
            // Siblings
            Relationship::fixed(...$ku('xwişk'))->sister(),
            Relationship::fixed(...$ku('bira'))->brother(),
            Relationship::fixed(...$ku('xwişkûbira'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$ku('nîvxwişk'))->parent()->daughter(),
            Relationship::fixed(...$ku('nîvbira'))->parent()->son(),
            Relationship::fixed(...$ku('nîvxwişkûbira'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$ku('diya'))->parent()->wife(),
            Relationship::fixed(...$ku('bavê'))->parent()->husband(),
            Relationship::fixed(...$ku('dêûbavê zincîrî'))->parent()->married()->spouse(),
            Relationship::fixed(...$ku('keça zincîrî'))->married()->spouse()->daughter(),
            Relationship::fixed(...$ku('kurê zincîrî'))->married()->spouse()->son(),
            Relationship::fixed(...$ku('zarokê zincîrî'))->married()->spouse()->child(),
            Relationship::fixed(...$ku('xwişka zincîrî'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$ku('birayê zincîrî'))->parent()->spouse()->son(),
            Relationship::fixed(...$ku('xwişkûbirayê zincîrî'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$ku('hevjîna berê'))->divorced()->partner()->female(),
            Relationship::fixed(...$ku('hevjînê berê'))->divorced()->partner()->male(),
            Relationship::fixed(...$ku('hevjînê berê'))->divorced()->partner(),
            Relationship::fixed(...$ku('destgirtî'))->engaged()->partner()->female(),
            Relationship::fixed(...$ku('destgirtî'))->engaged()->partner()->male(),
            Relationship::fixed(...$ku('jin'))->wife(),
            Relationship::fixed(...$ku('mêr'))->husband(),
            Relationship::fixed(...$ku('hevjîn'))->spouse(),
            Relationship::fixed(...$ku('hevkar'))->partner(),
            // In-laws
            Relationship::fixed(...$ku('xesû'))->married()->spouse()->mother(),
            Relationship::fixed(...$ku('xezûr'))->married()->spouse()->father(),
            Relationship::fixed(...$ku('xesûxezûr'))->married()->spouse()->parent(),
            Relationship::fixed(...$ku('bûk'))->child()->wife(),
            Relationship::fixed(...$ku('zava'))->child()->husband(),
            Relationship::fixed(...$ku('jinbira'))->spouse()->sister(),
            Relationship::fixed(...$ku('hêvir'))->spouse()->brother(),
            Relationship::fixed(...$ku('bûk'))->sibling()->wife(),
            Relationship::fixed(...$ku('hêvir'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$ku('dapîr'))->parent()->mother(),
            Relationship::fixed(...$ku('bapîr'))->parent()->father(),
            Relationship::fixed(...$ku('dapîr û bapîr'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$ku('nevîça'))->child()->daughter(),
            Relationship::fixed(...$ku('nevî'))->child()->son(),
            Relationship::fixed(...$ku('nevî'))->child()->child(),
            // Aunts and uncles — maternal/paternal
            Relationship::fixed(...$ku('xaltî'))->mother()->sister(),
            Relationship::fixed(...$ku('xal'))->mother()->brother(),
            Relationship::fixed(...$ku('met'))->father()->sister(),
            Relationship::fixed(...$ku('ap'))->father()->brother(),
            Relationship::fixed(...$ku('xaltî'))->parent()->sister(),
            Relationship::fixed(...$ku('ap'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$ku('keça xwişk/birayî'))->sibling()->daughter(),
            Relationship::fixed(...$ku('kurê xwişk/birayî'))->sibling()->son(),
            Relationship::fixed(...$ku('zarokê xwişk/birayî'))->sibling()->child(),
            // Cousins — flat
            Relationship::fixed(...$ku('pismam'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$ku('pismam'))->parent()->sibling()->son(),
            Relationship::fixed(...$ku('pismam'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'dapîr'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'bapîr'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'bapîr'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nevî'))->descendant(),
        ];
    }
}
