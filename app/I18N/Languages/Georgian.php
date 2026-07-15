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
use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;

final readonly class Georgian extends AbstractLanguage
{
    protected const string    ENDONYM            = 'ქართული';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ka';
    protected const string    LOCALE_CODE        = 'ka_GE@collation=phonebook';
    protected const int       MINIMUM_GROUPING_DIGITS = 2;
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Geor;
    protected const string    DATE_ABOUT         = 'მიახლოებით %s';
    protected const string    DATE_AFTER         = '%s შემდეგ';
    protected const string    DATE_BEFORE        = 'перед %s';
    protected const string    DATE_BETWEEN_AND   = '%s და %s შორის';
    protected const string    DATE_CALCULATED    = 'გამოთვლილია %s';
    protected const string    DATE_ESTIMATED     = 'სავარაუდოდ %s';
    protected const string    DATE_FROM          = 'დან %s';
    protected const string    DATE_FROM_TO       = 'დან %s ადრე %s';
    protected const string    DATE_INTERPRETED   = 'ამოცნობილია როგორც %s';
    protected const string    DATE_TO            = '%s მდე';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . ' до н.э.';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'н. э.';
    protected const string    LIST_SEPARATOR_AND = ' და ';
    protected const string    LIST_SEPARATOR_OR  = ' ან ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'იანვარი',
        'თებერვალი',
        'მარტი',
        'აპრილი',
        'მაი',
        'ივნისი',
        'ივლისი',
        'აგვისტო',
        'სექტემბერი',
        'ოქტომბერი',
        'ნოემბერი',
        'დეკემბერი',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'თიშრეი',
        'ხეშვანი',
        'ქისლევი',
        'ტევეთი',
        'შვატი',
        'ადარ I',
        'ადარ II',
        'ადარი',
        'ნისანი',
        'იარი',
        'სივანი',
        'თამუზი',
        'ავი',
        'ელული',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'ვანდემიერი',
        'ბრიუმერი',
        'ფრიმერი',
        'ნივოზი',
        'პლიუვიოზი',
        'ვანტოზი',
        'ჟერმინალი',
        'ფლორეალი',
        'პრერიალი',
        'მესიდორი',
        'თერმიდორი',
        'ფრიუქტიდორი',
        'დამატებითი დღეები',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'მუჰარამი',
        'საფარი',
        'რაბი ალ-ავალი',
        'რაბი ას-სანი',
        'ჯუმადა ალ-ულა',
        'ჯუმადა ას-სანი',
        'რაჯაბი',
        'შააბანი',
        'რამადანი',
        'შავალი',
        'ზულ-ქაადა',
        'ზულ-ჰიჯა',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'ფარვარდინი',
        'ორდიბეჰეშთი',
        'ხორდადი',
        'თირი',
        'მორდადი',
        'შაჰრივარი',
        'მეჰრი',
        'აბანი',
        'აზარი',
        'დეი',
        'ბაჰმანი',
        'ესფანდი',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Georgian genitive: "-ის" suffix, e.g. "დედის %s" (mother's %s)
        // "პაპის პაპა" pattern for great-grandparents
        $great = static function (int $n, string $nom, string $gen): array {
            $prefix = str_repeat('დიდი ', $n);

            return [$prefix . $nom, $prefix . $gen . ' %s'];
        };

        return [
            // Parents
            Relationship::fixed('დედა', 'დედის %s')->mother(),
            Relationship::fixed('მამა', 'მამის %s')->father(),
            Relationship::fixed('მშობელი', 'მშობლის %s')->parent(),
            // Children
            Relationship::fixed('ქალიშვილი', 'ქალიშვილის %s')->daughter(),
            Relationship::fixed('ვაჟიშვილი', 'ვაჟიშვილის %s')->son(),
            Relationship::fixed('შვილი', 'შვილის %s')->child(),
            // Siblings
            Relationship::fixed('და', 'დის %s')->sister(),
            Relationship::fixed('ძმა', 'ძმის %s')->brother(),
            Relationship::fixed('და-ძმა', 'და-ძმის %s')->sibling(),
            // Half-siblings
            Relationship::fixed('ნახევარ და', 'ნახევარ დის %s')->parent()->daughter(),
            Relationship::fixed('ნახევარ ძმა', 'ნახევარ ძმის %s')->parent()->son(),
            Relationship::fixed('ნახევარ და-ძმა', 'ნახევარ და-ძმის %s')->parent()->child(),
            // Stepfamily
            Relationship::fixed('დედინაცვალი', 'დედინაცვლის %s')->parent()->wife(),
            Relationship::fixed('მამინაცვალი', 'მამინაცვლის %s')->parent()->husband(),
            Relationship::fixed('მშობელინაცვალი', 'მშობელინაცვლის %s')->parent()->married()->spouse(),
            Relationship::fixed('გერი ქალიშვილი', 'გერი ქალიშვილის %s')->married()->spouse()->daughter(),
            Relationship::fixed('გერი ვაჟიშვილი', 'გერი ვაჟიშვილის %s')->married()->spouse()->son(),
            Relationship::fixed('გერი შვილი', 'გერი შვილის %s')->married()->spouse()->child(),
            Relationship::fixed('გერი და', 'გერი დის %s')->parent()->spouse()->daughter(),
            Relationship::fixed('გერი ძმა', 'გერი ძმის %s')->parent()->spouse()->son(),
            Relationship::fixed('გერი და-ძმა', 'გერი და-ძმის %s')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('ყოფილი მეუღლე', 'ყოფილი მეუღლის %s')->divorced()->partner()->female(),
            Relationship::fixed('ყოფილი მეუღლე', 'ყოფილი მეუღლის %s')->divorced()->partner()->male(),
            Relationship::fixed('ყოფილი მეუღლე', 'ყოფილი მეუღლის %s')->divorced()->partner(),
            Relationship::fixed('ნიშანდებული', 'ნიშანდებულის %s')->engaged()->partner()->female(),
            Relationship::fixed('ნიშანდებული', 'ნიშანდებულის %s')->engaged()->partner()->male(),
            Relationship::fixed('ცოლი', 'ცოლის %s')->wife(),
            Relationship::fixed('ქმარი', 'ქმრის %s')->husband(),
            Relationship::fixed('მეუღლე', 'მეუღლის %s')->spouse(),
            Relationship::fixed('პარტნიორი', 'პარტნიორის %s')->partner(),
            // In-laws
            Relationship::fixed('სიდედრი', 'სიდედრის %s')->married()->spouse()->mother(),
            Relationship::fixed('სიმამრი', 'სიმამრის %s')->married()->spouse()->father(),
            Relationship::fixed('სიმამრ-სიდედრი', 'სიმამრ-სიდედრის %s')->married()->spouse()->parent(),
            Relationship::fixed('რძალი', 'რძლის %s')->child()->wife(),
            Relationship::fixed('სიძე', 'სიძის %s')->child()->husband(),
            Relationship::fixed('მული', 'მულის %s')->spouse()->sister(),
            Relationship::fixed('ცოლის ძმა', 'ცოლის ძმის %s')->spouse()->brother(),
            Relationship::fixed('რძალი', 'რძლის %s')->sibling()->wife(),
            Relationship::fixed('ძმის ქმარი', 'ძმის ქმრის %s')->sibling()->husband(),
            // Grandparents
            Relationship::fixed('ბებია', 'ბებიის %s')->parent()->mother(),
            Relationship::fixed('ბაბუა', 'ბაბუის %s')->parent()->father(),
            Relationship::fixed('ბებია-ბაბუა', 'ბებია-ბაბუის %s')->parent()->parent(),
            // Grandchildren
            Relationship::fixed('შვილიშვილი', 'შვილიშვილის %s')->child()->daughter(),
            Relationship::fixed('შვილიშვილი', 'შვილიშვილის %s')->child()->son(),
            Relationship::fixed('შვილიშვილი', 'შვილიშვილის %s')->child()->child(),
            // Aunts and uncles
            Relationship::fixed('დეიდა', 'დეიდის %s')->mother()->sister(),
            Relationship::fixed('ბიძა', 'ბიძის %s')->mother()->brother(),
            Relationship::fixed('დეიდა', 'დეიდის %s')->father()->sister(),
            Relationship::fixed('ბიძა', 'ბიძის %s')->father()->brother(),
            Relationship::fixed('დეიდა', 'დეიდის %s')->parent()->sister(),
            Relationship::fixed('ბიძა', 'ბიძის %s')->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed('ძმისწული', 'ძმისწულის %s')->sibling()->daughter(),
            Relationship::fixed('ძმისწული', 'ძმისწულის %s')->sibling()->son(),
            Relationship::fixed('ძმისწული', 'ძმისწულის %s')->sibling()->child(),
            // Cousins
            Relationship::fixed('ბიძაშვილი', 'ბიძაშვილის %s')->parent()->sibling()->daughter(),
            Relationship::fixed('ბიძაშვილი', 'ბიძაშვილის %s')->parent()->sibling()->son(),
            Relationship::fixed('ბიძაშვილი', 'ბიძაშვილის %s')->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'ბებია', 'ბებიის'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'ბაბუა', 'ბაბუის'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'ბებია-ბაბუა', 'ბებია-ბაბუის'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'შვილიშვილი', 'შვილიშვილის'))->descendant(),
        ];
    }
}
