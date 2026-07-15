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
use Fisharebest\Webtrees\Enums\PluralRule;

use function str_repeat;

final readonly class Greek extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'Ελληνικά';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'el';
    protected const string    LOCALE_CODE        = 'el_GR@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Grek;
    protected const string    DATE_ABOUT         = 'σχετικά με %s';
    protected const string    DATE_AFTER         = 'μετά %s';
    protected const string    DATE_BEFORE        = 'πριν %s';
    protected const string    DATE_BETWEEN_AND   = 'μεταξύ %s και %s';
    protected const string    DATE_CALCULATED    = 'υπολογίστηκε %s';
    protected const string    DATE_ESTIMATED     = 'εκτιμώμενη %s';
    protected const string    DATE_FROM          = 'από %s';
    protected const string    DATE_FROM_TO       = 'Από %s εώς %s';
    protected const string    DATE_INTERPRETED   = 'ερμηνεύεται %s';
    protected const string    DATE_TO            = 'έως %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'ΠΚΧ';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'ΜΚΧ';
    protected const string    LIST_SEPARATOR_AND = ' και ';
    protected const string    LIST_SEPARATOR_OR  = ' ή ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Ιανουάριος',
        'Φεβρουάριος',
        'Μάρτιος',
        'Απρίλιος',
        'Μάιος',
        'Ιούνιος',
        'Ιούλιος',
        'Αύγουστος',
        'Σεπτέμβριος',
        'Οκτώβριος',
        'Νοέμβριος',
        'Δεκέμβριος',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'Ιανουαρίου',
        'Φεβρουαρίου',
        'Μαρτίου',
        'Απριλίου',
        'Μαΐου',
        'Ιουνίου',
        'Ιουλίου',
        'Αυγούστου',
        'Σεπτεμβρίου',
        'Οκτωβρίου',
        'Νοεμβρίου',
        'Δεκεμβρίου',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Τισρί',
        'Χεσβάν',
        'Κισλέβ',
        'Τεβέτ',
        'Σεβάτ',
        'Αδάρ Αʹ',
        'Αδάρ Βʹ',
        'Αδάρ',
        'Νισάν',
        'Ιγιάρ',
        'Σιβάν',
        'Ταμούζ',
        'Αβ',
        'Ελούλ',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Βαντεμιαίρ',
        'Μπρυμαίρ',
        'Φριμαίρ',
        'Νιβόζ',
        'Πλυβιόζ',
        'Βαντόζ',
        'Ζερμινάλ',
        'Φλοραίαλ',
        'Πραιριάλ',
        'Μεσιδόρ',
        'Θερμιδόρ',
        'Φρυκτιδόρ',
        'συμπληρωματικές ημέρες',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Μουχάραμ',
        'Σαφάρ',
        'Ραμπί αλ-Αουάλ',
        'Ραμπί αλ-Θάνι',
        'Τζουμάντα αλ-Αουάλ',
        'Τζουμάντα αλ-Θάνι',
        'Ρατζάμπ',
        'Σααμπάν',
        'Ραμαζάνι',
        'Σαουάλ',
        'Ντου αλ-Καντά',
        'Ντου αλ-Χιτζά',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Φαρβαρντίν',
        'Ορντιμπεχέστ',
        'Χορντάντ',
        'Τιρ',
        'Μορντάντ',
        'Σαχριβάρ',
        'Μεχρ',
        'Αμπάν',
        'Αζάρ',
        'Ντέι',
        'Μπάχμαν',
        'Εσφάντ',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;
    /**
     * @return array<int,string>
     */
    /** @var array<int,string> */
    protected const array ALPHABET = ['Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω'];

    /**
     * Gregorian/Julian month names — case-inflected.
     *
     * @return array<Relationship>
     */

    public function relationships(): array
    {
        // Greek: "προ-" prefix for great-grandparents
        // n=1: προ (great-grand), n=2: προπρο (great-great-grand), etc.
        $great = static function (int $n, string $nom, string $article, string $genNoun): array {
            $prefix = str_repeat('προ', $n);

            return [$prefix . $nom, '%s ' . $article . ' ' . $prefix . $genNoun];
        };

        return [
            // Parents
            Relationship::fixed('μητέρα', '%s της μητέρας')->mother(),
            Relationship::fixed('πατέρας', '%s του πατέρα')->father(),
            Relationship::fixed('γονέας', '%s του γονέα')->parent(),
            // Children
            Relationship::fixed('κόρη', '%s της κόρης')->daughter(),
            Relationship::fixed('γιος', '%s του γιου')->son(),
            Relationship::fixed('παιδί', '%s του παιδιού')->child(),
            // Siblings
            Relationship::fixed('αδελφή', '%s της αδελφής')->sister(),
            Relationship::fixed('αδελφός', '%s του αδελφού')->brother(),
            Relationship::fixed('αδελφός/ή', '%s του αδελφού/ής')->sibling(),
            // Half-siblings
            Relationship::fixed('ετεροθαλής αδελφή', '%s της ετεροθαλούς αδελφής')->parent()->daughter(),
            Relationship::fixed('ετεροθαλής αδελφός', '%s του ετεροθαλούς αδελφού')->parent()->son(),
            Relationship::fixed('ετεροθαλές αδέλφι', '%s του ετεροθαλούς αδελφού/ής')->parent()->child(),
            // Stepfamily
            Relationship::fixed('μητριά', '%s της μητριάς')->parent()->wife(),
            Relationship::fixed('πατριός', '%s του πατριού')->parent()->husband(),
            Relationship::fixed('θετός γονέας', '%s του θετού γονέα')->parent()->married()->spouse(),
            Relationship::fixed('θετή κόρη', '%s της θετής κόρης')->married()->spouse()->daughter(),
            Relationship::fixed('θετός γιος', '%s του θετού γιου')->married()->spouse()->son(),
            Relationship::fixed('θετό παιδί', '%s του θετού παιδιού')->married()->spouse()->child(),
            Relationship::fixed('ετεροθαλής αδελφή', '%s της ετεροθαλούς αδελφής')->parent()->spouse()->daughter(),
            Relationship::fixed('ετεροθαλής αδελφός', '%s του ετεροθαλούς αδελφού')->parent()->spouse()->son(),
            Relationship::fixed('ετεροθαλές αδέλφι', '%s του ετεροθαλούς αδελφού/ής')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('πρώην σύζυγος', '%s της πρώην συζύγου')->divorced()->partner()->female(),
            Relationship::fixed('πρώην σύζυγος', '%s του πρώην συζύγου')->divorced()->partner()->male(),
            Relationship::fixed('πρώην σύζυγος', '%s του/της πρώην συζύγου')->divorced()->partner(),
            Relationship::fixed('αρραβωνιαστικιά', '%s της αρραβωνιαστικιάς')->engaged()->partner()->female(),
            Relationship::fixed('αρραβωνιαστικός', '%s του αρραβωνιαστικού')->engaged()->partner()->male(),
            Relationship::fixed('σύζυγος', '%s της συζύγου')->wife(),
            Relationship::fixed('σύζυγος', '%s του συζύγου')->husband(),
            Relationship::fixed('σύζυγος', '%s του/της συζύγου')->spouse(),
            Relationship::fixed('σύντροφος', '%s του/της συντρόφου')->partner(),
            // In-laws
            Relationship::fixed('πεθερά', '%s της πεθεράς')->married()->spouse()->mother(),
            Relationship::fixed('πεθερός', '%s του πεθερού')->married()->spouse()->father(),
            Relationship::fixed('πεθερικά', '%s των πεθερικών')->married()->spouse()->parent(),
            Relationship::fixed('νύφη', '%s της νύφης')->child()->wife(),
            Relationship::fixed('γαμπρός', '%s του γαμπρού')->child()->husband(),
            Relationship::fixed('κουνιάδα', '%s της κουνιάδας')->spouse()->sister(),
            Relationship::fixed('κουνιάδος', '%s του κουνιάδου')->spouse()->brother(),
            Relationship::fixed('κουνιάδα', '%s της κουνιάδας')->sibling()->wife(),
            Relationship::fixed('γαμπρός', '%s του γαμπρού')->sibling()->husband(),
            // Grandparents
            Relationship::fixed('γιαγιά', '%s της γιαγιάς')->parent()->mother(),
            Relationship::fixed('παππούς', '%s του παππού')->parent()->father(),
            Relationship::fixed('παππούς/γιαγιά', '%s του παππού/της γιαγιάς')->parent()->parent(),
            // Grandchildren
            Relationship::fixed('εγγονή', '%s της εγγονής')->child()->daughter(),
            Relationship::fixed('εγγονός', '%s του εγγονού')->child()->son(),
            Relationship::fixed('εγγόνι', '%s του εγγονιού')->child()->child(),
            // Aunts and uncles
            Relationship::fixed('θεία', '%s της θείας')->parent()->sister(),
            Relationship::fixed('θείος', '%s του θείου')->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed('ανιψιά', '%s της ανιψιάς')->sibling()->daughter(),
            Relationship::fixed('ανιψιός', '%s του ανιψιού')->sibling()->son(),
            // Cousins
            Relationship::fixed('ξαδέλφη', '%s της ξαδέλφης')->parent()->sibling()->daughter(),
            Relationship::fixed('ξάδελφος', '%s του ξαδέλφου')->parent()->sibling()->son(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'γιαγιά', 'της', 'γιαγιάς'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'παππούς', 'του', 'παππού'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'γονέας', 'του', 'γονέα'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'εγγόνι', 'του', 'εγγονιού'))->descendant(),
        ];
    }
}
