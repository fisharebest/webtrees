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

final readonly class Marathi extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'मराठी';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'mr';
    protected const string    LOCALE_CODE        = 'mr_IN@collation=phonebook';
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
    protected const string    LIST_SEPARATOR_AND = ' आणि ';
    protected const string    LIST_SEPARATOR_OR  = ' किंवा ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'जानेवारी',
        'फेब्रुवारी',
        'मार्च',
        'एप्रिल',
        'मे',
        'जून',
        'जुलै',
        'ऑगस्ट',
        'सप्टेंबर',
        'ओक्टोबर',
        'नोव्हेंबर',
        'डिसेंबर',
    ];


    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'तिश्री',
        'हेश्वान',
        'किस्लेव',
        'तेवेत',
        'शेवत',
        'अदार १',
        'अदार २',
        'अदार',
        'निसान',
        'इयार',
        'सिवान',
        'तमुझ',
        'आव',
        'एलुल',
    ];


    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'वेन्डेमियेर',
        'ब्रुमेयर',
        'फ्रिमेयर',
        'निव्होस',
        'प्लुव्हिओस',
        'व्हेन्टोस',
        'जर्मिनल',
        'फ्लोरियल',
        'प्रेरियल',
        'मेसीडोर',
        'थर्मिडोर',
        'फ्रुक्टिडोर',
        'पूरक दिवस',
    ];


    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'मुहर्रम',
        'सफर',
        'रबी उल-अव्वल',
        'रबी उल-आखिर',
        'जमादिल अव्वल',
        'जमादिल सानी',
        'रजब',
        'शाबान',
        'रमजान',
        'शव्वाल',
        'जिल्कद',
        'जिल्हिज्ज',
    ];


    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'फरवर्दिन',
        'ऑर्डिबेहेश्त',
        'खोरदाद',
        'तीर',
        'मोरदाद',
        'शहरीवर',
        'मेहर',
        'आबान',
        'आझर',
        'दे',
        'बहमन',
        'इस्फंद',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Marathi genitive: postposition चा (cha, masculine possessed) / ची (chi, feminine possessed)
        $mr = static fn (string $s): array => [$s, $s . ' चा %s', $s . ' ची %s'];

        return [
            // Adopted
            Relationship::fixed(...$mr('दत्तक आई'))->adoptive()->mother(),
            Relationship::fixed(...$mr('दत्तक वडील'))->adoptive()->father(),
            Relationship::fixed(...$mr('दत्तक पालक'))->adoptive()->parent(),
            Relationship::fixed(...$mr('दत्तक मुलगी'))->adopted()->daughter(),
            Relationship::fixed(...$mr('दत्तक मुलगा'))->adopted()->son(),
            Relationship::fixed(...$mr('दत्तक मूल'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$mr('पोषक आई'))->fostering()->mother(),
            Relationship::fixed(...$mr('पोषक वडील'))->fostering()->father(),
            Relationship::fixed(...$mr('पोषक पालक'))->fostering()->parent(),
            Relationship::fixed(...$mr('पोषित मुलगी'))->fostered()->daughter(),
            Relationship::fixed(...$mr('पोषित मुलगा'))->fostered()->son(),
            Relationship::fixed(...$mr('पोषित मूल'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$mr('आई'))->mother(),
            Relationship::fixed(...$mr('वडील'))->father(),
            Relationship::fixed(...$mr('पालक'))->parent(),
            // Children
            Relationship::fixed(...$mr('मुलगी'))->daughter(),
            Relationship::fixed(...$mr('मुलगा'))->son(),
            Relationship::fixed(...$mr('मूल'))->child(),
            // Siblings — twins
            Relationship::fixed(...$mr('जुळी बहीण'))->twin()->sister(),
            Relationship::fixed(...$mr('जुळा भाऊ'))->twin()->brother(),
            Relationship::fixed(...$mr('जुळे भावंड'))->twin()->sibling(),
            // Siblings — elder/younger
            Relationship::fixed(...$mr('मोठी बहीण'))->older()->sister(),
            Relationship::fixed(...$mr('लहान बहीण'))->younger()->sister(),
            Relationship::fixed(...$mr('मोठा भाऊ'))->older()->brother(),
            Relationship::fixed(...$mr('लहान भाऊ'))->younger()->brother(),
            Relationship::fixed(...$mr('बहीण'))->sister(),
            Relationship::fixed(...$mr('भाऊ'))->brother(),
            Relationship::fixed(...$mr('भावंड'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$mr('सावत्र बहीण'))->father()->daughter(),
            Relationship::fixed(...$mr('सावत्र भाऊ'))->father()->son(),
            Relationship::fixed(...$mr('सावत्र बहीण'))->mother()->daughter(),
            Relationship::fixed(...$mr('सावत्र भाऊ'))->mother()->son(),
            Relationship::fixed(...$mr('सावत्र बहीण'))->parent()->daughter(),
            Relationship::fixed(...$mr('सावत्र भाऊ'))->parent()->son(),
            Relationship::fixed(...$mr('भावंड'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$mr('सावत्र आई'))->parent()->wife(),
            Relationship::fixed(...$mr('सावत्र वडील'))->parent()->husband(),
            Relationship::fixed(...$mr('सावत्र मुलगी'))->married()->spouse()->daughter(),
            Relationship::fixed(...$mr('सावत्र मुलगा'))->married()->spouse()->son(),
            Relationship::fixed(...$mr('सावत्र मूल'))->married()->spouse()->child(),
            Relationship::fixed(...$mr('सावत्र मुलगी'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$mr('सावत्र मुलगा'))->parent()->spouse()->son(),
            Relationship::fixed(...$mr('सावत्र मूल'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$mr('माजी पत्नी'))->divorced()->partner()->female(),
            Relationship::fixed(...$mr('माजी पती'))->divorced()->partner()->male(),
            Relationship::fixed(...$mr('माजी जोडीदार'))->divorced()->partner(),
            Relationship::fixed(...$mr('मंगेतर'))->engaged()->partner()->female(),
            Relationship::fixed(...$mr('मंगेतर'))->engaged()->partner()->male(),
            Relationship::fixed(...$mr('पत्नी'))->wife(),
            Relationship::fixed(...$mr('पती'))->husband(),
            Relationship::fixed(...$mr('जोडीदार'))->spouse(),
            Relationship::fixed(...$mr('साथीदार'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$mr('सासू'))->married()->spouse()->mother(),
            Relationship::fixed(...$mr('सासरे'))->married()->spouse()->father(),
            Relationship::fixed(...$mr('सासू/सासरे'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$mr('सून'))->child()->wife(),
            Relationship::fixed(...$mr('जावई'))->child()->husband(),
            Relationship::fixed(...$mr('सून/जावई'))->child()->married()->spouse(),
            // In-laws (husband's siblings)
            Relationship::fixed(...$mr('नणंद'))->husband()->sister(),
            Relationship::fixed(...$mr('दीर'))->husband()->brother(),
            // In-laws (wife's siblings)
            Relationship::fixed(...$mr('मेहुणी'))->wife()->sister(),
            Relationship::fixed(...$mr('मेहुणा'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$mr('वहिनी'))->brother()->wife(),
            Relationship::fixed(...$mr('भावोजी'))->sister()->husband(),
            // Grandparents (Marathi does not distinguish paternal/maternal)
            Relationship::fixed(...$mr('आजी'))->parent()->mother(),
            Relationship::fixed(...$mr('आजोबा'))->parent()->father(),
            Relationship::fixed(...$mr('आजी-आजोबा'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$mr('नात'))->child()->daughter(),
            Relationship::fixed(...$mr('नातू'))->child()->son(),
            Relationship::fixed(...$mr('नातवंड'))->child()->child(),
            // Aunts — paternal
            Relationship::fixed(...$mr('आत्या'))->father()->sister(),
            // Aunts — maternal
            Relationship::fixed(...$mr('मावशी'))->mother()->sister(),
            // Aunts — generic
            Relationship::fixed(...$mr('आत्या/मावशी'))->parent()->sister(),
            // Uncles — paternal
            Relationship::fixed(...$mr('काका'))->father()->brother(),
            // Uncles — maternal
            Relationship::fixed(...$mr('मामा'))->mother()->brother(),
            // Uncles — generic
            Relationship::fixed(...$mr('काका/मामा'))->parent()->brother(),
            // Uncle/aunt spouses
            Relationship::fixed(...$mr('काकू'))->father()->brother()->wife(),
            Relationship::fixed(...$mr('मामी'))->mother()->brother()->wife(),
            Relationship::fixed(...$mr('मावसा'))->mother()->sister()->husband(),
            // Nieces/Nephews — through brother
            Relationship::fixed(...$mr('पुतणी'))->brother()->daughter(),
            Relationship::fixed(...$mr('पुतण्या'))->brother()->son(),
            // Nieces/Nephews — through sister
            Relationship::fixed(...$mr('भाची'))->sister()->daughter(),
            Relationship::fixed(...$mr('भाचा'))->sister()->son(),
            // Generic niece/nephew
            Relationship::fixed(...$mr('पुतणी/भाची'))->sibling()->daughter(),
            Relationship::fixed(...$mr('पुतण्या/भाचा'))->sibling()->son(),
            // Cousins — paternal uncle's children (चुलत)
            Relationship::fixed(...$mr('चुलत बहीण'))->father()->brother()->daughter(),
            Relationship::fixed(...$mr('चुलत भाऊ'))->father()->brother()->son(),
            // Cousins — paternal aunt's children (आत्ये)
            Relationship::fixed(...$mr('आत्ये बहीण'))->father()->sister()->daughter(),
            Relationship::fixed(...$mr('आत्ये भाऊ'))->father()->sister()->son(),
            // Cousins — maternal uncle's children (मामे)
            Relationship::fixed(...$mr('मामे बहीण'))->mother()->brother()->daughter(),
            Relationship::fixed(...$mr('मामे भाऊ'))->mother()->brother()->son(),
            // Cousins — maternal aunt's children (मावस)
            Relationship::fixed(...$mr('मावस बहीण'))->mother()->sister()->daughter(),
            Relationship::fixed(...$mr('मावस भाऊ'))->mother()->sister()->son(),
            // Generic cousin fallback
            Relationship::fixed(...$mr('चुलत/मामे बहीण'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$mr('चुलत/मामे भाऊ'))->parent()->sibling()->son(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $mr('आत्या/मावशी' . ($n > 2 ? ' — पिढी ' . ($n - 1) : ' मोठी')))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $mr('काका/मामा' . ($n > 2 ? ' — पिढी ' . ($n - 1) : ' मोठे')))->ancestor()->brother(),
            // Dynamic: grand-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $mr('पुतणी/भाची' . ($n > 2 ? ' — पिढी ' . ($n - 1) : ' मोठी')))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $mr('पुतण्या/भाचा' . ($n > 2 ? ' — पिढी ' . ($n - 1) : ' मोठा')))->sibling()->descendant()->male(),
            // Dynamic: ancestors (पणजी/पणजोबा for great-grandparents, खापर- prefix for deeper)
            Relationship::dynamic(static fn (int $n) => $mr(str_repeat('खापर-', $n - 3) . 'पणजी'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $mr(str_repeat('खापर-', $n - 3) . 'पणजोबा'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $mr(str_repeat('खापर-', $n - 3) . 'पणजी/पणजोबा'))->ancestor(),
            // Dynamic: descendants (पणती/पणतू for great-grandchildren, खापर- prefix for deeper)
            Relationship::dynamic(static fn (int $n) => $mr(str_repeat('खापर-', $n - 3) . 'पणती'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $mr(str_repeat('खापर-', $n - 3) . 'पणतू'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $mr(str_repeat('खापर-', $n - 3) . 'पणतवंड'))->descendant(),
        ];
    }
}
