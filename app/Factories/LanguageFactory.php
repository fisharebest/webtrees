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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Localization\Locale;
use Fisharebest\Webtrees\Contracts\LanguageInterface;
use Fisharebest\Webtrees\I18N\Languages\Afrikaans;
use Fisharebest\Webtrees\I18N\Languages\Albanian;
use Fisharebest\Webtrees\I18N\Languages\Arabic;
use Fisharebest\Webtrees\I18N\Languages\Armenian;
use Fisharebest\Webtrees\I18N\Languages\Basque;
use Fisharebest\Webtrees\I18N\Languages\Bosnian;
use Fisharebest\Webtrees\I18N\Languages\Bulgarian;
use Fisharebest\Webtrees\I18N\Languages\Catalan;
use Fisharebest\Webtrees\I18N\Languages\ChineseSimplified;
use Fisharebest\Webtrees\I18N\Languages\ChineseTraditional;
use Fisharebest\Webtrees\I18N\Languages\Croatian;
use Fisharebest\Webtrees\I18N\Languages\Czech;
use Fisharebest\Webtrees\I18N\Languages\Danish;
use Fisharebest\Webtrees\I18N\Languages\Divehi;
use Fisharebest\Webtrees\I18N\Languages\Dutch;
use Fisharebest\Webtrees\I18N\Languages\EnglishAustralia;
use Fisharebest\Webtrees\I18N\Languages\EnglishGreatBritain;
use Fisharebest\Webtrees\I18N\Languages\EnglishUnitedStates;
use Fisharebest\Webtrees\I18N\Languages\Estonian;
use Fisharebest\Webtrees\I18N\Languages\Faroese;
use Fisharebest\Webtrees\I18N\Languages\Farsi;
use Fisharebest\Webtrees\I18N\Languages\Finnish;
use Fisharebest\Webtrees\I18N\Languages\French;
use Fisharebest\Webtrees\I18N\Languages\FrenchCanada;
use Fisharebest\Webtrees\I18N\Languages\Galician;
use Fisharebest\Webtrees\I18N\Languages\Georgian;
use Fisharebest\Webtrees\I18N\Languages\German;
use Fisharebest\Webtrees\I18N\Languages\Greek;
use Fisharebest\Webtrees\I18N\Languages\Hebrew;
use Fisharebest\Webtrees\I18N\Languages\Hindi;
use Fisharebest\Webtrees\I18N\Languages\Hungarian;
use Fisharebest\Webtrees\I18N\Languages\Icelandic;
use Fisharebest\Webtrees\I18N\Languages\Indonesian;
use Fisharebest\Webtrees\I18N\Languages\Italian;
use Fisharebest\Webtrees\I18N\Languages\Japanese;
use Fisharebest\Webtrees\I18N\Languages\Javanese;
use Fisharebest\Webtrees\I18N\Languages\Kazhak;
use Fisharebest\Webtrees\I18N\Languages\Korean;
use Fisharebest\Webtrees\I18N\Languages\Kurdish;
use Fisharebest\Webtrees\I18N\Languages\Latvian;
use Fisharebest\Webtrees\I18N\Languages\Lingala;
use Fisharebest\Webtrees\I18N\Languages\Lithuanian;
use Fisharebest\Webtrees\I18N\Languages\Macedonian;
use Fisharebest\Webtrees\I18N\Languages\Malay;
use Fisharebest\Webtrees\I18N\Languages\Maori;
use Fisharebest\Webtrees\I18N\Languages\Marathi;
use Fisharebest\Webtrees\I18N\Languages\Nepalese;
use Fisharebest\Webtrees\I18N\Languages\NorwegianBokmal;
use Fisharebest\Webtrees\I18N\Languages\NorwegianNynorsk;
use Fisharebest\Webtrees\I18N\Languages\Occitan;
use Fisharebest\Webtrees\I18N\Languages\Polish;
use Fisharebest\Webtrees\I18N\Languages\Portuguese;
use Fisharebest\Webtrees\I18N\Languages\PortugueseBrazil;
use Fisharebest\Webtrees\I18N\Languages\Romanian;
use Fisharebest\Webtrees\I18N\Languages\Russian;
use Fisharebest\Webtrees\I18N\Languages\Serbian;
use Fisharebest\Webtrees\I18N\Languages\SerbianLatin;
use Fisharebest\Webtrees\I18N\Languages\Slovakian;
use Fisharebest\Webtrees\I18N\Languages\Slovenian;
use Fisharebest\Webtrees\I18N\Languages\Spanish;
use Fisharebest\Webtrees\I18N\Languages\Sundanese;
use Fisharebest\Webtrees\I18N\Languages\Swahili;
use Fisharebest\Webtrees\I18N\Languages\Swedish;
use Fisharebest\Webtrees\I18N\Languages\Tagalog;
use Fisharebest\Webtrees\I18N\Languages\Tamil;
use Fisharebest\Webtrees\I18N\Languages\Tatar;
use Fisharebest\Webtrees\I18N\Languages\Thai;
use Fisharebest\Webtrees\I18N\Languages\Turkish;
use Fisharebest\Webtrees\I18N\Languages\Ukranian;
use Fisharebest\Webtrees\I18N\Languages\Urdu;
use Fisharebest\Webtrees\I18N\Languages\Uzbek;
use Fisharebest\Webtrees\I18N\Languages\Vietnamese;
use Fisharebest\Webtrees\I18N\Languages\Welsh;
use Fisharebest\Webtrees\I18N\Languages\Yiddish;
use Psr\Http\Message\ServerRequestInterface;

final readonly class LanguageFactory
{
    /**
     * These are the languages that the application supports (i.e. has translations)
     * Other languages may be under development.
     *
     * @var list<string>
     */
    private const array LANGUAGE_TAGS = [
        'af', // Afrikaans (Afrikaans)
        'su', // Basa Sunda (Sundanese)
        'bs', // Bosanski (Bosnian)
        'ca', // Catala (Catalan)
        'cs', // Cestina (Czech)
        'cy', // Cymraeg (Welsh)
        'da', // Dansk (Danish)
        'de', // Deutsch (German)
        'et', // Eesti (Estonian)
        'en-US', // English, American (English, United States)
        'en-AU', // English, Australian (English, Australia)
        'en-GB', // English, British (English, United Kingdom)
        'es', // Espanol (Spanish)
        'eu', // Euskara (Basque)
        'fo', // Foroyskt (Faroese)
        'fr', // Francais (French)
        'fr-CA', // Francais Canadien (French, Canadian)
        'gl', // Galego (Galician)
        'hr', // Hrvatski (Croatian)
        'id', // Indonesia (Indonesian)
        'is', // Islenska (Icelandic)
        'it', // Italiano (Italian)
        'jv', // Jawa (Javanese)
        'sw', // Kiswahili (Swahili)
        'ku', // Kurdi (Kurdish)
        'lv', // Latviesu (Latvian)
        'lt', // Lietuviu (Lithuanian)
        'ln', // Lingala (Lingala)
        'hu', // Magyar (Hungarian)
        'ms', // Melayu (Malay)
        'mi', // Māori (Māori)
        'nl', // Nederlands (Dutch)
        'nb', // Norsk Bokmal (Norwegian, Bokmal)
        'nn', // Norsk Nynorsk (Norwegian, Nynorsk)
        'oc', // Occitan (Occitan)
        'uz', // Ozbek (Uzbek)
        'pl', // Polski (Polish)
        'pt', // Portugues (Portuguese)
        'pt-BR', // Portugues Do Brasil (Brazilian, Portugal)
        'ro', // Romana (Romanian)
        'sq', // Shqip (Albanian)
        'sk', // Slovencina (Slovak)
        'sl', // Slovenscina (Slovenian)
        'sr-Latn', // Srpski (Serbian, Latin)
        'fi', // Suomi (Finnish)
        'sv', // Svenska (Swedish)
        'tl', // Tagalog (Tagalog)
        'vi', // Tieng Viet (Vietnamese)
        'tr', // Turkce (Turkish)
        'el', // Ελληνικά (Greek)
        'bg', // Български (Bulgarian)
        'mk', // Македонски (Macedonian)
        'ru', // Русский (Russian)
        'sr', // Српски (Serbian)
        'tt', // Татар (Tatar)
        'uk', // Українська (Ukrainian)
        'kk', // Қазақ тілі (Kazakh)
        'hy', // Հայերեն (Armenian)
        'yi', // ייִדיש (Yiddish)
        'he', // עברית (Hebrew)
        'ur', // اردو (Urdu)
        'ar', // العربية (Arabic)
        'fa', // فارسی (Persian)
        'dv', // ތާނަ (Divehi)
        'ne', // नेपाली (Nepali)
        'mr', // मराठी (Marathi)
        'hi', // हिन्दी (Hindi)
        'ta', // தமிழ் (Tamil)
        'th', // ไทย (Thai)
        'ka', // ქართული (Georgian)
        'ja', // 日本語 (Japanese)
        'zh-Hans', // 简体中文 (Chinese, Simplified)
        'zh-Hant', // 繁體中文 (Chinese, Traditional)
        'ko', // 한국어 (Korean)
    ];

    public function fromLanguageTag(string $language_tag): LanguageInterface
    {
        return match ($language_tag) {
            'af' => new Afrikaans(),
            'sq' => new Albanian(),
            'ar' => new Arabic(),
            'hy' => new Armenian(),
            'eu' => new Basque(),
            'bs' => new Bosnian(),
            'bg' => new Bulgarian(),
            'ca' => new Catalan(),
            'zh-Hans' => new ChineseSimplified(),
            'zh-Hant' => new ChineseTraditional(),
            'hr' => new Croatian(),
            'cs' => new Czech(),
            'da' => new Danish(),
            'dv' => new Divehi(),
            'nl' => new Dutch(),
            'en-AU' => new EnglishAustralia(),
            'en-GB' => new EnglishGreatBritain(),
            'en-US' => new EnglishUnitedStates(),
            'et' => new Estonian(),
            'fo' => new Faroese(),
            'fa' => new Farsi(),
            'fi' => new Finnish(),
            'fr' => new French(),
            'fr-CA' => new FrenchCanada(),
            'gl' => new Galician(),
            'ka' => new Georgian(),
            'de' => new German(),
            'el' => new Greek(),
            'he' => new Hebrew(),
            'hi' => new Hindi(),
            'hu' => new Hungarian(),
            'is' => new Icelandic(),
            'id' => new Indonesian(),
            'it' => new Italian(),
            'ja' => new Japanese(),
            'jv' => new Javanese(),
            'kk' => new Kazhak(),
            'ko' => new Korean(),
            'ku' => new Kurdish(),
            'lv' => new Latvian(),
            'ln' => new Lingala(),
            'lt' => new Lithuanian(),
            'mk' => new Macedonian(),
            'ms' => new Malay(),
            'mi' => new Maori(),
            'mr' => new Marathi(),
            'ne' => new Nepalese(),
            'nb' => new NorwegianBokmal(),
            'nn' => new NorwegianNynorsk(),
            'oc' => new Occitan(),
            'pl' => new Polish(),
            'pt' => new Portuguese(),
            'pt-BR' => new PortugueseBrazil(),
            'ro' => new Romanian(),
            'ru' => new Russian(),
            'sr' => new Serbian(),
            'sr-Latn' => new SerbianLatin(),
            'sk' => new Slovakian(),
            'sl' => new Slovenian(),
            'es' => new Spanish(),
            'su' => new Sundanese(),
            'sw' => new Swahili(),
            'sv' => new Swedish(),
            'tl' => new Tagalog(),
            'ta' => new Tamil(),
            'tt' => new Tatar(),
            'th' => new Thai(),
            'tr' => new Turkish(),
            'uk' => new Ukranian(),
            'ur' => new Urdu(),
            'uz' => new Uzbek(),
            'vi' => new Vietnamese(),
            'cy' => new Welsh(),
            'yi' => new Yiddish(),
            default => new EnglishUnitedStates(),
        };
    }

    public function fromRequest(ServerRequestInterface $request): LanguageInterface
    {
        $locales = array_map(Locale::create(...), $this->allLanguageTags());
        $default = (new EnglishUnitedStates())->locale();
        $locale  = Locale::httpAcceptLanguage($request->getServerParams(), $locales, $default);

        return $this->fromLanguageTag($locale->languageTag());
    }

    /**
     * @return list<string>
     */
    public function allLanguageTags(): array
    {
        return self::LANGUAGE_TAGS;
    }

    /**
     * @return list<LanguageInterface>
     */
    public function allLanguages(): array
    {
        return array_map(
            fn(string $language_tag): LanguageInterface => $this->fromLanguageTag($language_tag),
            self::LANGUAGE_TAGS
        );
    }
}
