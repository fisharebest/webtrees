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
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Hindi extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsPluralForMoreThanOne;

    protected const string    ENDONYM            = 'हिन्दी';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'hi';
    protected const string    LOCALE_CODE        = 'hi_IN@collation=phonebook';
    protected const int       DIGITS_GROUP = 2;
    protected const array     DIGITS       = [
        0 => UTF8::DEVANAGARI_DIGIT_ZERO,
        1 => UTF8::DEVANAGARI_DIGIT_ONE,
        2 => UTF8::DEVANAGARI_DIGIT_TWO,
        3 => UTF8::DEVANAGARI_DIGIT_THREE,
        4 => UTF8::DEVANAGARI_DIGIT_FOUR,
        5 => UTF8::DEVANAGARI_DIGIT_FIVE,
        6 => UTF8::DEVANAGARI_DIGIT_SIX,
        7 => UTF8::DEVANAGARI_DIGIT_SEVEN,
        8 => UTF8::DEVANAGARI_DIGIT_EIGHT,
        9 => UTF8::DEVANAGARI_DIGIT_NINE,
    ];
    protected const Script    SCRIPT             = Script::Deva;
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    DATE_ABOUT         = 'लगभग %s';
    protected const string    DATE_AFTER         = '%s के बाद';
    protected const string    DATE_BEFORE        = '%s से पहले';
    protected const string    DATE_BETWEEN_AND   = '%s और %s के बीच';
    protected const string    DATE_CALCULATED    = 'परिकलित %s';
    protected const string    DATE_ESTIMATED     = 'अनुमानित %s';
    protected const string    DATE_FROM          = '%s से';
    protected const string    DATE_FROM_TO       = '%s से %s तक';
    protected const string    DATE_INTERPRETED   = 'व्याख्यायित %s';
    protected const string    DATE_TO            = '%s तक';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'ईसा पूर्व';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'सीई';
    protected const string    LIST_SEPARATOR_AND = ' और ';
    protected const string    LIST_SEPARATOR_OR  = ' या ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'जनवरी',
        'फरवरी',
        'मार्च',
        'अप्रैल',
        'मई',
        'जून',
        'जुलाई',
        'अगस्त',
        'सितंबर',
        'अक्टूबर',
        'नवंबर',
        'दिसंबर',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'तिशरी',
        'चेशवन',
        'किसलेव',
        'तेवत',
        'शेवत',
        'अदार अव्वल',
        'अदार दुवम',
        'अदार',
        'निसान',
        'यार',
        'सीवान',
        'तामुज़',
        'आव',
        'एलूल',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'वेनडेमियर',
        'ब्रूमेयर',
        'फ्रीमैर',
        'निवोस',
        'प्लूविओस',
        'वेन्टोस',
        'जर्मिनल',
        'फ्लोरिअल',
        'प्रेरिअल',
        'मेसीडोर',
        'थर्मिडोर',
        'फ्रुक्टिडोर',
        'जोर्स कम्प्लीमेंटरेस',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'मुहर्रम',
        'सफर',
        'रबी उल-अव्वल',
        'रबी उल-आख़िर',
        'जमादिल अव्वल',
        'जमादिल सानी',
        'रज्जब',
        'शाबान',
        'रमजान',
        'शव्वाल',
        'ज़िल क़दा',
        'ज़िल हज',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'फरवरदिन',
        'ओर्दिबेहेश्त',
        'खोरदाद',
        'तीर',
        'मोरदाद',
        'शहरीवर',
        'मेहर',
        'आबान',
        'आज़र',
        'दे',
        'बेहमन',
        'इसफंद',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Hindi genitive: postposition का (ka, male possessed) / की (ki, female possessed)
        // %s is the possessed noun — "माँ का %s" = "mother's [male]", "माँ की %s" = "mother's [female]"
        $hi = static fn (string $s): array => [$s, $s . ' का %s', $s . ' की %s'];

        return [
            // Adopted
            Relationship::fixed(...$hi('सौतेली माँ'))->adoptive()->mother(),
            Relationship::fixed(...$hi('सौतेला पिता'))->adoptive()->father(),
            Relationship::fixed(...$hi('सौतेला अभिभावक'))->adoptive()->parent(),
            Relationship::fixed(...$hi('गोद ली बेटी'))->adopted()->daughter(),
            Relationship::fixed(...$hi('गोद लिया बेटा'))->adopted()->son(),
            Relationship::fixed(...$hi('गोद लिया बच्चा'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$hi('पालक माँ'))->fostering()->mother(),
            Relationship::fixed(...$hi('पालक पिता'))->fostering()->father(),
            Relationship::fixed(...$hi('पालक अभिभावक'))->fostering()->parent(),
            Relationship::fixed(...$hi('पालित बेटी'))->fostered()->daughter(),
            Relationship::fixed(...$hi('पालित बेटा'))->fostered()->son(),
            Relationship::fixed(...$hi('पालित बच्चा'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$hi('माँ'))->mother(),
            Relationship::fixed(...$hi('पिता'))->father(),
            Relationship::fixed(...$hi('अभिभावक'))->parent(),
            // Children
            Relationship::fixed(...$hi('बेटी'))->daughter(),
            Relationship::fixed(...$hi('बेटा'))->son(),
            Relationship::fixed(...$hi('बच्चा'))->child(),
            // Siblings — twins
            Relationship::fixed(...$hi('जुड़वाँ बहन'))->twin()->sister(),
            Relationship::fixed(...$hi('जुड़वाँ भाई'))->twin()->brother(),
            Relationship::fixed(...$hi('जुड़वाँ'))->twin()->sibling(),
            // Siblings — elder/younger
            Relationship::fixed(...$hi('बड़ी बहन'))->older()->sister(),
            Relationship::fixed(...$hi('छोटी बहन'))->younger()->sister(),
            Relationship::fixed(...$hi('बड़ा भाई'))->older()->brother(),
            Relationship::fixed(...$hi('छोटा भाई'))->younger()->brother(),
            Relationship::fixed(...$hi('बहन'))->sister(),
            Relationship::fixed(...$hi('भाई'))->brother(),
            Relationship::fixed(...$hi('भाई-बहन'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$hi('सौतेली बहन'))->father()->daughter(),
            Relationship::fixed(...$hi('सौतेला भाई'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$hi('सौतेली बहन'))->mother()->daughter(),
            Relationship::fixed(...$hi('सौतेला भाई'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$hi('सौतेली बहन'))->parent()->daughter(),
            Relationship::fixed(...$hi('सौतेला भाई'))->parent()->son(),
            Relationship::fixed(...$hi('भाई-बहन'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$hi('सौतेली माँ'))->parent()->wife(),
            Relationship::fixed(...$hi('सौतेला पिता'))->parent()->husband(),
            Relationship::fixed(...$hi('सौतेली बेटी'))->married()->spouse()->daughter(),
            Relationship::fixed(...$hi('सौतेला बेटा'))->married()->spouse()->son(),
            Relationship::fixed(...$hi('सौतेला बच्चा'))->married()->spouse()->child(),
            Relationship::fixed(...$hi('सौतेली बेटी'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$hi('सौतेला बेटा'))->parent()->spouse()->son(),
            Relationship::fixed(...$hi('सौतेला बच्चा'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$hi('पूर्व पत्नी'))->divorced()->partner()->female(),
            Relationship::fixed(...$hi('पूर्व पति'))->divorced()->partner()->male(),
            Relationship::fixed(...$hi('पूर्व जीवनसाथी'))->divorced()->partner(),
            Relationship::fixed(...$hi('मंगेतर'))->engaged()->partner()->female(),
            Relationship::fixed(...$hi('मंगेतर'))->engaged()->partner()->male(),
            Relationship::fixed(...$hi('पत्नी'))->wife(),
            Relationship::fixed(...$hi('पति'))->husband(),
            Relationship::fixed(...$hi('जीवनसाथी'))->spouse(),
            Relationship::fixed(...$hi('साथी'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$hi('सास'))->married()->spouse()->mother(),
            Relationship::fixed(...$hi('ससुर'))->married()->spouse()->father(),
            Relationship::fixed(...$hi('ससुराल'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$hi('बहू'))->child()->wife(),
            Relationship::fixed(...$hi('दामाद'))->child()->husband(),
            Relationship::fixed(...$hi('बहू/दामाद'))->child()->married()->spouse(),
            // In-laws (husband's siblings)
            Relationship::fixed(...$hi('ननद'))->husband()->sister(),
            Relationship::fixed(...$hi('जेठ'))->husband()->older()->brother(),
            Relationship::fixed(...$hi('देवर'))->husband()->younger()->brother(),
            Relationship::fixed(...$hi('देवर/जेठ'))->husband()->brother(),
            // In-laws (wife's siblings)
            Relationship::fixed(...$hi('साली'))->wife()->sister(),
            Relationship::fixed(...$hi('साला'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$hi('भाभी'))->brother()->wife(),
            Relationship::fixed(...$hi('बहनोई'))->older()->sister()->husband(),
            Relationship::fixed(...$hi('बहनोई'))->younger()->sister()->husband(),
            Relationship::fixed(...$hi('बहनोई'))->sister()->husband(),
            // Grandparents — paternal
            Relationship::fixed(...$hi('दादी'))->father()->mother(),
            Relationship::fixed(...$hi('दादा'))->father()->father(),
            // Grandparents — maternal
            Relationship::fixed(...$hi('नानी'))->mother()->mother(),
            Relationship::fixed(...$hi('नाना'))->mother()->father(),
            // Grandparents — generic fallback
            Relationship::fixed(...$hi('दादी/नानी'))->parent()->mother(),
            Relationship::fixed(...$hi('दादा/नाना'))->parent()->father(),
            Relationship::fixed(...$hi('दादा-दादी/नाना-नानी'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$hi('पोती'))->son()->daughter(),
            Relationship::fixed(...$hi('पोता'))->son()->son(),
            Relationship::fixed(...$hi('नतिनी'))->daughter()->daughter(),
            Relationship::fixed(...$hi('नाती'))->daughter()->son(),
            Relationship::fixed(...$hi('पोती/नतिनी'))->child()->daughter(),
            Relationship::fixed(...$hi('पोता/नाती'))->child()->son(),
            Relationship::fixed(...$hi('पोता-पोती'))->child()->child(),
            // Aunts — paternal
            Relationship::fixed(...$hi('बुआ'))->father()->sister(),
            // Aunts — maternal
            Relationship::fixed(...$hi('मौसी'))->mother()->sister(),
            // Aunts — generic
            Relationship::fixed(...$hi('बुआ/मौसी'))->parent()->sister(),
            // Uncles — paternal
            Relationship::fixed(...$hi('चाचा'))->father()->brother(),
            // Uncles — maternal
            Relationship::fixed(...$hi('मामा'))->mother()->brother(),
            // Uncles — generic
            Relationship::fixed(...$hi('चाचा/मामा'))->parent()->brother(),
            // Uncle's/aunt's spouse
            Relationship::fixed(...$hi('चाची'))->father()->brother()->wife(),
            Relationship::fixed(...$hi('फूफा'))->father()->sister()->husband(),
            Relationship::fixed(...$hi('मामी'))->mother()->brother()->wife(),
            Relationship::fixed(...$hi('मौसा'))->mother()->sister()->husband(),
            // Nieces/Nephews — through brother
            Relationship::fixed(...$hi('भतीजी'))->brother()->daughter(),
            Relationship::fixed(...$hi('भतीजा'))->brother()->son(),
            // Nieces/Nephews — through sister
            Relationship::fixed(...$hi('भांजी'))->sister()->daughter(),
            Relationship::fixed(...$hi('भांजा'))->sister()->son(),
            // Generic niece/nephew
            Relationship::fixed(...$hi('भतीजी/भांजी'))->sibling()->daughter(),
            Relationship::fixed(...$hi('भतीजा/भांजा'))->sibling()->son(),
            // Cousins — paternal uncle's children (चचेरा)
            Relationship::fixed(...$hi('चचेरी बहन'))->father()->brother()->daughter(),
            Relationship::fixed(...$hi('चचेरा भाई'))->father()->brother()->son(),
            // Cousins — paternal aunt's children (फुफेरा)
            Relationship::fixed(...$hi('फुफेरी बहन'))->father()->sister()->daughter(),
            Relationship::fixed(...$hi('फुफेरा भाई'))->father()->sister()->son(),
            // Cousins — maternal uncle's children (ममेरा)
            Relationship::fixed(...$hi('ममेरी बहन'))->mother()->brother()->daughter(),
            Relationship::fixed(...$hi('ममेरा भाई'))->mother()->brother()->son(),
            // Cousins — maternal aunt's children (मौसेरा)
            Relationship::fixed(...$hi('मौसेरी बहन'))->mother()->sister()->daughter(),
            Relationship::fixed(...$hi('मौसेरा भाई'))->mother()->sister()->son(),
            // Generic cousin fallback
            Relationship::fixed(...$hi('चचेरी/ममेरी बहन'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$hi('चचेरा/ममेरा भाई'))->parent()->sibling()->son(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $hi('बुआ/मौसी' . ($n > 2 ? ' — पीढ़ी ' . ($n - 1) : ' बड़ी')))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $hi('चाचा/मामा' . ($n > 2 ? ' — पीढ़ी ' . ($n - 1) : ' बड़े')))->ancestor()->brother(),
            // Dynamic: grand-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $hi('भतीजी/भांजी' . ($n > 2 ? ' — पीढ़ी ' . ($n - 1) : ' बड़ी')))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $hi('भतीजा/भांजा' . ($n > 2 ? ' — पीढ़ी ' . ($n - 1) : ' बड़े')))->sibling()->descendant()->male(),
            // Dynamic: ancestors — paternal great-grandparents (पर- prefix)
            Relationship::dynamic(static fn (int $n) => $hi(str_repeat('पर', $n - 2) . 'दादी'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $hi(str_repeat('पर', $n - 2) . 'दादा'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $hi(str_repeat('पर', $n - 2) . 'दादा/दादी'))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(static fn (int $n) => $hi(str_repeat('पर', $n - 2) . 'पोती'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $hi(str_repeat('पर', $n - 2) . 'पोता'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $hi(str_repeat('पर', $n - 2) . 'पोता-पोती'))->descendant(),
        ];
    }
}
