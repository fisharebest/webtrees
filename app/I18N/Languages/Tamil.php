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

final readonly class Tamil extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'தமிழ்';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ta';
    protected const string    LOCALE_CODE        = 'ta_IN@collation=phonebook';
    protected const int       DIGITS_GROUP = 2;
    protected const array     DIGITS       = [
        0 => UTF8::TAMIL_DIGIT_ZERO,
        1 => UTF8::TAMIL_DIGIT_ONE,
        2 => UTF8::TAMIL_DIGIT_TWO,
        3 => UTF8::TAMIL_DIGIT_THREE,
        4 => UTF8::TAMIL_DIGIT_FOUR,
        5 => UTF8::TAMIL_DIGIT_FIVE,
        6 => UTF8::TAMIL_DIGIT_SIX,
        7 => UTF8::TAMIL_DIGIT_SEVEN,
        8 => UTF8::TAMIL_DIGIT_EIGHT,
        9 => UTF8::TAMIL_DIGIT_NINE,
    ];
    protected const Script    SCRIPT             = Script::Taml;
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    DATE_FROM          = '%s இலிருந்து';
    protected const string    LIST_SEPARATOR_AND = ' மற்றும் ';
    protected const string    LIST_SEPARATOR_OR  = ' அல்லது ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'ஜனவரி',
        'பிப்ரவரி',
        'மார்ச்',
        'ஏப்ரல்',
        'மே',
        'ஜூன்',
        'ஜூலை',
        'ஆகஸ்ட்',
        'செப்டம்பர்',
        'அக்டோபர்',
        'நவம்பர்',
        'டிசம்பர்',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'திஷ்ரி',
        'ஹெஷ்வான்',
        'கிஸ்லேவ்',
        'தேவேத்',
        'ஷெவாத்',
        'ஆதார் ௧',
        'ஆதார் ௨',
        'ஆதார்',
        'நிசான்',
        'இயார்',
        'சிவான்',
        'தமூஸ்',
        'ஆவ்',
        'எலுல்',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'வாண்டெமியேர்',
        'ப்ரூமேர்',
        'ஃப்ரிமேர்',
        'நிவோஸ்',
        'ப்ளூவியோஸ்',
        'வாண்டோஸ்',
        'ஜெர்மினல்',
        'ஃப்ளோரியல்',
        'ப்ரேரியல்',
        'மெசிடோர்',
        'தெர்மிடோர்',
        'ஃப்ரக்டிடோர்',
        'நிரப்பு நாட்கள்',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'முஹர்ரம்',
        'சஃபர்',
        'ரபீஉல் அவ்வல்',
        'ரபீஉல் ஆகிர்',
        'ஜுமாதல் அவ்வல்',
        'ஜுமாதல் ஆகிர்',
        'ரஜப்',
        'ஷஃபான்',
        'ரமலான்',
        'ஷவ்வால்',
        'துல்கஃதா',
        'துல்ஹிஜ்ஜா',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'ஃபர்வர்தின்',
        'ஆர்திபஹெஷ்த்',
        'கோர்தாத்',
        'தீர்',
        'மோர்தாத்',
        'ஷஹ்ரிவர்',
        'மெஹ்ர்',
        'அபான்',
        'ஆசர்',
        'தே',
        'பஹ்மன்',
        'எஸ்ஃபண்ட்',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Tamil genitive: "-இன்" / "-உடைய" suffix or "…யின் %s"
        // Tamil distinguishes elder/younger siblings and has elaborate kinship
        $great = static function (int $n, string $nom, string $gen): array {
            $prefix = str_repeat('கொள்ளு', $n);

            return [$prefix . $nom, $prefix . $gen . ' %s'];
        };

        return [
            // Parents
            Relationship::fixed('அம்மா', 'அம்மாவின் %s')->mother(),
            Relationship::fixed('அப்பா', 'அப்பாவின் %s')->father(),
            Relationship::fixed('பெற்றோர்', 'பெற்றோரின் %s')->parent(),
            // Children
            Relationship::fixed('மகள்', 'மகளின் %s')->daughter(),
            Relationship::fixed('மகன்', 'மகனின் %s')->son(),
            Relationship::fixed('குழந்தை', 'குழந்தையின் %s')->child(),
            // Siblings — elder/younger
            Relationship::fixed('அக்கா', 'அக்காவின் %s')->older()->sister(),
            Relationship::fixed('அண்ணன்', 'அண்ணனின் %s')->older()->brother(),
            Relationship::fixed('தங்கை', 'தங்கையின் %s')->younger()->sister(),
            Relationship::fixed('தம்பி', 'தம்பியின் %s')->younger()->brother(),
            Relationship::fixed('அக்கா', 'அக்காவின் %s')->sister(),
            Relationship::fixed('அண்ணன்', 'அண்ணனின் %s')->brother(),
            Relationship::fixed('உடன்பிறப்பு', 'உடன்பிறப்பின் %s')->sibling(),
            // Half-siblings
            Relationship::fixed('ஒன்றுவிட்ட அக்கா', 'ஒன்றுவிட்ட அக்காவின் %s')->parent()->daughter(),
            Relationship::fixed('ஒன்றுவிட்ட அண்ணன்', 'ஒன்றுவிட்ட அண்ணனின் %s')->parent()->son(),
            Relationship::fixed('ஒன்றுவிட்ட உடன்பிறப்பு', 'ஒன்றுவிட்ட உடன்பிறப்பின் %s')->parent()->child(),
            // Stepfamily
            Relationship::fixed('சித்தி', 'சித்தியின் %s')->parent()->wife(),
            Relationship::fixed('மாற்றான் அப்பா', 'மாற்றான் அப்பாவின் %s')->parent()->husband(),
            Relationship::fixed('வளர்ப்புப் பெற்றோர்', 'வளர்ப்புப் பெற்றோரின் %s')->parent()->married()->spouse(),
            Relationship::fixed('வளர்ப்பு மகள்', 'வளர்ப்பு மகளின் %s')->married()->spouse()->daughter(),
            Relationship::fixed('வளர்ப்பு மகன்', 'வளர்ப்பு மகனின் %s')->married()->spouse()->son(),
            Relationship::fixed('வளர்ப்பு குழந்தை', 'வளர்ப்பு குழந்தையின் %s')->married()->spouse()->child(),
            Relationship::fixed('வளர்ப்பு அக்கா', 'வளர்ப்பு அக்காவின் %s')->parent()->spouse()->daughter(),
            Relationship::fixed('வளர்ப்பு அண்ணன்', 'வளர்ப்பு அண்ணனின் %s')->parent()->spouse()->son(),
            Relationship::fixed('வளர்ப்பு உடன்பிறப்பு', 'வளர்ப்பு உடன்பிறப்பின் %s')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('முன்னாள் துணை', 'முன்னாள் துணையின் %s')->divorced()->partner()->female(),
            Relationship::fixed('முன்னாள் துணை', 'முன்னாள் துணையின் %s')->divorced()->partner()->male(),
            Relationship::fixed('முன்னாள் துணை', 'முன்னாள் துணையின் %s')->divorced()->partner(),
            Relationship::fixed('நிச்சயதார்த்தம்', 'நிச்சயதார்த்தத்தின் %s')->engaged()->partner()->female(),
            Relationship::fixed('நிச்சயதார்த்தம்', 'நிச்சயதார்த்தத்தின் %s')->engaged()->partner()->male(),
            Relationship::fixed('மனைவி', 'மனைவியின் %s')->wife(),
            Relationship::fixed('கணவன்', 'கணவனின் %s')->husband(),
            Relationship::fixed('துணை', 'துணையின் %s')->spouse(),
            Relationship::fixed('பங்குதாரர்', 'பங்குதாரரின் %s')->partner(),
            // In-laws
            Relationship::fixed('மாமியார்', 'மாமியாரின் %s')->married()->spouse()->mother(),
            Relationship::fixed('மாமனார்', 'மாமனாரின் %s')->married()->spouse()->father(),
            Relationship::fixed('மாமன்-மாமி', 'மாமன்-மாமியின் %s')->married()->spouse()->parent(),
            Relationship::fixed('மருமகள்', 'மருமகளின் %s')->child()->wife(),
            Relationship::fixed('மருமகன்', 'மருமகனின் %s')->child()->husband(),
            Relationship::fixed('கொழுந்தி', 'கொழுந்தியின் %s')->spouse()->sister(),
            Relationship::fixed('மைத்துனன்', 'மைத்துனனின் %s')->spouse()->brother(),
            Relationship::fixed('அண்ணி', 'அண்ணியின் %s')->sibling()->wife(),
            Relationship::fixed('மைத்துனன்', 'மைத்துனனின் %s')->sibling()->husband(),
            // Grandparents
            Relationship::fixed('பாட்டி', 'பாட்டியின் %s')->parent()->mother(),
            Relationship::fixed('தாத்தா', 'தாத்தாவின் %s')->parent()->father(),
            Relationship::fixed('தாத்தா-பாட்டி', 'தாத்தா-பாட்டியின் %s')->parent()->parent(),
            // Grandchildren
            Relationship::fixed('பேத்தி', 'பேத்தியின் %s')->child()->daughter(),
            Relationship::fixed('பேரன்', 'பேரனின் %s')->child()->son(),
            Relationship::fixed('பேரக்குழந்தை', 'பேரக்குழந்தையின் %s')->child()->child(),
            // Aunts and uncles — maternal/paternal
            Relationship::fixed('அத்தை', 'அத்தையின் %s')->mother()->sister(),
            Relationship::fixed('மாமா', 'மாமாவின் %s')->mother()->brother(),
            Relationship::fixed('அத்தை', 'அத்தையின் %s')->father()->sister(),
            Relationship::fixed('சித்தப்பா', 'சித்தப்பாவின் %s')->father()->brother(),
            Relationship::fixed('அத்தை', 'அத்தையின் %s')->parent()->sister(),
            Relationship::fixed('மாமா', 'மாமாவின் %s')->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed('மருமகள்', 'மருமகளின் %s')->sibling()->daughter(),
            Relationship::fixed('மருமகன்', 'மருமகனின் %s')->sibling()->son(),
            Relationship::fixed('உடன்பிறப்பின் குழந்தை', 'உடன்பிறப்பின் குழந்தையின் %s')->sibling()->child(),
            // Cousins
            Relationship::fixed('உறவினர்', 'உறவினரின் %s')->parent()->sibling()->daughter(),
            Relationship::fixed('உறவினர்', 'உறவினரின் %s')->parent()->sibling()->son(),
            Relationship::fixed('உறவினர்', 'உறவினரின் %s')->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'பாட்டி', 'பாட்டியின்'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'தாத்தா', 'தாத்தாவின்'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'தாத்தா-பாட்டி', 'தாத்தா-பாட்டியின்'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'பேரக்குழந்தை', 'பேரக்குழந்தையின்'))->descendant(),
        ];
    }
}
