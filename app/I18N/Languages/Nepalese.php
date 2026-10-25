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

final readonly class Nepalese extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'नेपाली';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ne';
    protected const string    LOCALE_CODE        = 'ne_NP@collation=phonebook';
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
    protected const string    DATE_ABOUT         = '%s बारेमा';
    protected const string    DATE_AFTER         = '%s पछि';
    protected const string    DATE_BEFORE        = 'अगाडि %s';
    protected const string    DATE_BETWEEN_AND   = '%s र %s को बीचमा';
    protected const string    DATE_CALCULATED    = '%s गणना गरियो';
    protected const string    DATE_ESTIMATED     = 'अनुमानित %s';
    protected const string    DATE_FROM          = '%s बाट';
    protected const string    DATE_FROM_TO       = '%s देखि %s सम्म';
    protected const string    DATE_TO            = '%s लाई';
    protected const string    LIST_SEPARATOR_AND = ' र ';
    protected const string    LIST_SEPARATOR_OR  = ' वा ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'जनवरी',
        'फेब्रुवरी',
        'मार्च',
        'अप्रिल',
        'मई',
        'जुन',
        'जुलाई',
        'अगस्ट',
        'सेप्टेम्बर',
        'अक्टोबर',
        'नोभेम्बर',
        'डिसेम्बर',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'तिश्री',
        'हेस्भान',
        'किस्लेभ',
        'टेभेट',
        'शेवत',
        'अडार १',
        'अडार २',
        'अडार',
        'निसान',
        'इयार',
        'सिभान',
        'टामुज',
        'एभ',
        'एलउल',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'भेन्डेमाइर',
        'ब्रुमेयर',
        'फ्रिमेयर',
        'निभोस',
        'प्लुभिओस्',
        'भेन्टोज',
        'कीटाणुजन्य',
        'फ्लोरियल',
        'प्रेयरियल',
        'मेसीडोर',
        'थर्मिडर',
        'फ्रुक्टिडोर',
        'पूरक दिनहरू',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'मुहाराम',
        'सफार',
        'रबी अल-अव्वल',
        'रबी अस-सानी',
        'जुमाद अल अव्वल',
        'जुमादा अल थानी',
        'राजब',
        'शाबान',
        'रमादान',
        'साववाल',
        'धु अल-किदाह',
        'धु अल हिज्जा',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'फार्भार्डिन',
        'ओर्डिबेहेश',
        'खोर्डाद',
        'टिर',
        'मोर्डाद',
        'शाह्रिभर',
        'मेहर',
        'अबान',
        'अजार',
        'डेइ',
        'बाहमन',
        'इस्फान्ड',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    protected const array ALPHABET = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
    ];

    /**
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function ne(string $s): array
    {
        return [$s, $s . ' को %s'];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->ne('सौतेली आमा'))->adoptive()->mother(),
            Relationship::fixed(...$this->ne('सौतेला बुबा'))->adoptive()->father(),
            Relationship::fixed(...$this->ne('सौतेला अभिभावक'))->adoptive()->parent(),
            Relationship::fixed(...$this->ne('गोद लिइएकी छोरी'))->adopted()->daughter(),
            Relationship::fixed(...$this->ne('गोद लिइएको छोरा'))->adopted()->son(),
            Relationship::fixed(...$this->ne('गोद लिइएको बच्चा'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->ne('पालक आमा'))->fostering()->mother(),
            Relationship::fixed(...$this->ne('पालक बुबा'))->fostering()->father(),
            Relationship::fixed(...$this->ne('पालक अभिभावक'))->fostering()->parent(),
            Relationship::fixed(...$this->ne('पालित छोरी'))->fostered()->daughter(),
            Relationship::fixed(...$this->ne('पालित छोरा'))->fostered()->son(),
            Relationship::fixed(...$this->ne('पालित बच्चा'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->ne('आमा'))->mother(),
            Relationship::fixed(...$this->ne('बुबा'))->father(),
            Relationship::fixed(...$this->ne('अभिभावक'))->parent(),
            // Children
            Relationship::fixed(...$this->ne('छोरी'))->daughter(),
            Relationship::fixed(...$this->ne('छोरा'))->son(),
            Relationship::fixed(...$this->ne('सन्तान'))->child(),
            // Siblings — twins
            Relationship::fixed(...$this->ne('जुम्ल्याहा दिदी/बहिनी'))->multiple()->sister(),
            Relationship::fixed(...$this->ne('जुम्ल्याहा दाइ/भाइ'))->multiple()->brother(),
            Relationship::fixed(...$this->ne('जुम्ल्याहा'))->multiple()->sibling(),
            // Siblings — elder/younger
            Relationship::fixed(...$this->ne('दिदी'))->older()->sister(),
            Relationship::fixed(...$this->ne('बहिनी'))->younger()->sister(),
            Relationship::fixed(...$this->ne('दाइ'))->older()->brother(),
            Relationship::fixed(...$this->ne('भाइ'))->younger()->brother(),
            Relationship::fixed(...$this->ne('बहिनी'))->sister(),
            Relationship::fixed(...$this->ne('भाइ'))->brother(),
            Relationship::fixed(...$this->ne('दाजुभाइ/दिदीबहिनी'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$this->ne('सौतेनी बहिनी'))->father()->daughter(),
            Relationship::fixed(...$this->ne('सौतेला भाइ'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$this->ne('सौतेनी बहिनी'))->mother()->daughter(),
            Relationship::fixed(...$this->ne('सौतेला भाइ'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$this->ne('सौतेनी बहिनी'))->parent()->daughter(),
            Relationship::fixed(...$this->ne('सौतेला भाइ'))->parent()->son(),
            Relationship::fixed(...$this->ne('दाजुभाइ/दिदीबहिनी'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->ne('सौतेली आमा'))->parent()->wife(),
            Relationship::fixed(...$this->ne('सौतेला बुबा'))->parent()->husband(),
            Relationship::fixed(...$this->ne('सौतेनी छोरी'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->ne('सौतेला छोरा'))->married()->spouse()->son(),
            Relationship::fixed(...$this->ne('सौतेला बच्चा'))->married()->spouse()->child(),
            Relationship::fixed(...$this->ne('सौतेनी छोरी'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$this->ne('सौतेला छोरा'))->parent()->spouse()->son(),
            Relationship::fixed(...$this->ne('सौतेला बच्चा'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->ne('पूर्व पत्नी'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->ne('पूर्व पति'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->ne('पूर्व जीवनसाथी'))->divorced()->partner(),
            Relationship::fixed(...$this->ne('मङ्गेतर'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->ne('मङ्गेतर'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->ne('पत्नी'))->wife(),
            Relationship::fixed(...$this->ne('पति'))->husband(),
            Relationship::fixed(...$this->ne('जीवनसाथी'))->spouse(),
            Relationship::fixed(...$this->ne('साथी'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$this->ne('सासू'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->ne('ससुरा'))->married()->spouse()->father(),
            Relationship::fixed(...$this->ne('ससुराली'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$this->ne('बुहारी'))->child()->wife(),
            Relationship::fixed(...$this->ne('ज्वाइँ'))->child()->husband(),
            Relationship::fixed(...$this->ne('बुहारी/ज्वाइँ'))->child()->married()->spouse(),
            // In-laws (husband's siblings)
            Relationship::fixed(...$this->ne('ननन्द'))->husband()->sister(),
            Relationship::fixed(...$this->ne('जेठाजु'))->husband()->older()->brother(),
            Relationship::fixed(...$this->ne('देवर'))->husband()->younger()->brother(),
            Relationship::fixed(...$this->ne('जेठाजु/देवर'))->husband()->brother(),
            // In-laws (wife's siblings)
            Relationship::fixed(...$this->ne('साली'))->wife()->sister(),
            Relationship::fixed(...$this->ne('साला'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$this->ne('भाउजू'))->brother()->wife(),
            Relationship::fixed(...$this->ne('बहिनी ज्वाइँ'))->sister()->husband(),
            // Grandparents — paternal
            Relationship::fixed(...$this->ne('बज्यै'))->father()->mother(),
            Relationship::fixed(...$this->ne('बाजे'))->father()->father(),
            // Grandparents — maternal
            Relationship::fixed(...$this->ne('नानी'))->mother()->mother(),
            Relationship::fixed(...$this->ne('नाना'))->mother()->father(),
            // Grandparents — generic fallback
            Relationship::fixed(...$this->ne('हजुरआमा'))->parent()->mother(),
            Relationship::fixed(...$this->ne('हजुरबुबा'))->parent()->father(),
            Relationship::fixed(...$this->ne('हजुरआमा/हजुरबुबा'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->ne('नातिनी'))->child()->daughter(),
            Relationship::fixed(...$this->ne('नाति'))->child()->son(),
            Relationship::fixed(...$this->ne('नाति-नातिनी'))->child()->child(),
            // Aunts — paternal
            Relationship::fixed(...$this->ne('फुपू'))->father()->sister(),
            // Aunts — maternal
            Relationship::fixed(...$this->ne('माइजू'))->mother()->sister(),
            // Aunts — generic
            Relationship::fixed(...$this->ne('फुपू/माइजू'))->parent()->sister(),
            // Uncles — paternal
            Relationship::fixed(...$this->ne('काका'))->father()->brother(),
            // Uncles — maternal
            Relationship::fixed(...$this->ne('मामा'))->mother()->brother(),
            // Uncles — generic
            Relationship::fixed(...$this->ne('काका/मामा'))->parent()->brother(),
            // Uncle/aunt spouses
            Relationship::fixed(...$this->ne('काकी'))->father()->brother()->wife(),
            Relationship::fixed(...$this->ne('फुपाजु'))->father()->sister()->husband(),
            Relationship::fixed(...$this->ne('मामी'))->mother()->brother()->wife(),
            Relationship::fixed(...$this->ne('मौसा'))->mother()->sister()->husband(),
            // Nieces/Nephews — through brother
            Relationship::fixed(...$this->ne('भतिजी'))->brother()->daughter(),
            Relationship::fixed(...$this->ne('भतिजा'))->brother()->son(),
            // Nieces/Nephews — through sister
            Relationship::fixed(...$this->ne('भान्जी'))->sister()->daughter(),
            Relationship::fixed(...$this->ne('भान्जा'))->sister()->son(),
            // Generic niece/nephew
            Relationship::fixed(...$this->ne('भतिजी/भान्जी'))->sibling()->daughter(),
            Relationship::fixed(...$this->ne('भतिजा/भान्जा'))->sibling()->son(),
            // Cousins — paternal uncle's children (चचेरा)
            Relationship::fixed(...$this->ne('चचेरी बहिनी'))->father()->brother()->daughter(),
            Relationship::fixed(...$this->ne('चचेरा भाइ'))->father()->brother()->son(),
            // Cousins — paternal aunt's children (फुपुवा)
            Relationship::fixed(...$this->ne('फुपुवा बहिनी'))->father()->sister()->daughter(),
            Relationship::fixed(...$this->ne('फुपुवा भाइ'))->father()->sister()->son(),
            // Cousins — maternal uncle's children (ममेरा)
            Relationship::fixed(...$this->ne('ममेरी बहिनी'))->mother()->brother()->daughter(),
            Relationship::fixed(...$this->ne('ममेरा भाइ'))->mother()->brother()->son(),
            // Cousins — maternal aunt's children (मौसेरा)
            Relationship::fixed(...$this->ne('मौसेरी बहिनी'))->mother()->sister()->daughter(),
            Relationship::fixed(...$this->ne('मौसेरा भाइ'))->mother()->sister()->son(),
            // Generic cousin fallback
            Relationship::fixed(...$this->ne('चचेरी/ममेरी बहिनी'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->ne('चचेरा/ममेरा भाइ'))->parent()->sibling()->son(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->ne('फुपू/माइजू' . ($n > 2 ? ' — पुस्ता ' . ($n - 1) : ' ठूली')))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->ne('काका/मामा' . ($n > 2 ? ' — पुस्ता ' . ($n - 1) : ' ठूला')))->ancestor()->brother(),
            // Dynamic: grand-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->ne('भतिजी/भान्जी' . ($n > 2 ? ' — पुस्ता ' . ($n - 1) : ' ठूली')))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->ne('भतिजा/भान्जा' . ($n > 2 ? ' — पुस्ता ' . ($n - 1) : ' ठूला')))->sibling()->descendant()->male(),
            // Dynamic: ancestors
            Relationship::dynamic(fn (int $n) => $this->ne(str_repeat('पर', $n - 2) . 'बज्यै'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->ne(str_repeat('पर', $n - 2) . 'बाजे'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->ne(str_repeat('पर', $n - 2) . 'हजुरआमा/हजुरबुबा'))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(fn (int $n) => $this->ne(str_repeat('पर', $n - 2) . 'नातिनी'))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->ne(str_repeat('पर', $n - 2) . 'नाति'))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->ne(str_repeat('पर', $n - 2) . 'नाति-नातिनी'))->descendant(),
        ];
    }
}
