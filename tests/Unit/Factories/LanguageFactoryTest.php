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

namespace Fisharebest\Webtrees\Tests\Unit\Factories;

use Fisharebest\Webtrees\Factories\LanguageFactory;
use Fisharebest\Webtrees\I18N\Languages\ChineseSimplified;
use Fisharebest\Webtrees\I18N\Languages\ChineseTraditional;
use Fisharebest\Webtrees\I18N\Languages\EnglishAustralia;
use Fisharebest\Webtrees\I18N\Languages\EnglishGreatBritain;
use Fisharebest\Webtrees\I18N\Languages\EnglishUnitedStates;
use Fisharebest\Webtrees\I18N\Languages\French;
use Fisharebest\Webtrees\I18N\Languages\FrenchCanada;
use Fisharebest\Webtrees\I18N\Languages\German;
use Fisharebest\Webtrees\I18N\Languages\PortugueseBrazil;
use Fisharebest\Webtrees\I18N\Languages\SerbianLatin;
use Fisharebest\Webtrees\Tests\TestCase;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LanguageFactory::class)]
class LanguageFactoryTest extends TestCase
{
    private LanguageFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new LanguageFactory();
    }

    public function testFromLanguageTagReturnsCorrectLanguage(): void
    {
        $language = $this->factory->fromLanguageTag('de');

        self::assertInstanceOf(German::class, $language);
    }

    public function testFromLanguageTagReturnsFallbackForUnknownTag(): void
    {
        $language = $this->factory->fromLanguageTag('xx');

        self::assertInstanceOf(EnglishUnitedStates::class, $language);
    }

    public function testAllLanguageTagsReturnsNonEmptyList(): void
    {
        $tags = $this->factory->allLanguageTags();

        self::assertNotEmpty($tags);
        self::assertContains('en-US', $tags);
        self::assertContains('de', $tags);
        self::assertContains('zh-Hans', $tags);
        self::assertContains('zh-Hant', $tags);
    }

    public function testAllLanguagesReturnsSameLengthAsAllTags(): void
    {
        $languages = $this->factory->allLanguages();
        $tags      = $this->factory->allLanguageTags();

        self::assertSameSize($tags, $languages);
    }

    public function testFromRequestWithEmptyHeaderReturnsFallback(): void
    {
        $request  = new ServerRequest('GET', '/');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(EnglishUnitedStates::class, $language);
    }

    public function testFromRequestExactMatchLowerCase(): void
    {
        $request  = $this->requestWithAcceptLanguage('de');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(German::class, $language);
    }

    public function testFromRequestExactMatchWithRegion(): void
    {
        $request  = $this->requestWithAcceptLanguage('en-AU');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(EnglishAustralia::class, $language);
    }

    public function testFromRequestCaseInsensitiveRegion(): void
    {
        // Browsers may send "en-au" instead of "en-AU"
        $request  = $this->requestWithAcceptLanguage('en-au');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(EnglishAustralia::class, $language);
    }

    public function testFromRequestCaseInsensitiveLanguage(): void
    {
        // Upper case language code
        $request  = $this->requestWithAcceptLanguage('DE');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(German::class, $language);
    }

    public function testFromRequestChineseTraditionalFromZhTw(): void
    {
        // zh-TW is an alias for zh-Hant
        $request  = $this->requestWithAcceptLanguage('zh-TW');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(ChineseTraditional::class, $language);
    }

    public function testFromRequestChineseTraditionalFromZhTwLowerCase(): void
    {
        $request  = $this->requestWithAcceptLanguage('zh-tw');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(ChineseTraditional::class, $language);
    }

    public function testFromRequestChineseTraditionalFromZhHk(): void
    {
        $request  = $this->requestWithAcceptLanguage('zh-HK');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(ChineseTraditional::class, $language);
    }

    public function testFromRequestChineseSimplifiedFromZhCn(): void
    {
        // zh-CN is an alias for zh-Hans
        $request  = $this->requestWithAcceptLanguage('zh-CN');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(ChineseSimplified::class, $language);
    }

    public function testFromRequestChineseSimplifiedFromZhSg(): void
    {
        $request  = $this->requestWithAcceptLanguage('zh-SG');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(ChineseSimplified::class, $language);
    }

    public function testFromRequestChineseTraditionalFromZhHant(): void
    {
        // zh-Hant with mixed case
        $request  = $this->requestWithAcceptLanguage('zh-hant');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(ChineseTraditional::class, $language);
    }

    public function testFromRequestBaseLanguageMatchesRegionalVariant(): void
    {
        // Bare "en" should match the first English variant in LANGUAGE_TAGS
        $request  = $this->requestWithAcceptLanguage('en');
        $language = $this->factory->fromRequest($request);

        // Should be one of the English variants
        self::assertContains($language->languageTag(), ['en-US', 'en-AU', 'en-GB']);
    }

    public function testFromRequestRegionalVariantMatchesBaseLanguage(): void
    {
        // "de-DE" is not supported, but "de" is — should fall back to "de"
        $request  = $this->requestWithAcceptLanguage('de-DE');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(German::class, $language);
    }

    public function testFromRequestRegionalVariantMatchesBaseLanguageCaseInsensitive(): void
    {
        $request  = $this->requestWithAcceptLanguage('de-de');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(German::class, $language);
    }

    public function testFromRequestPrefersExactMatchOverBaseMatch(): void
    {
        // "en-NZ" is not supported but "en-AU" is — exact match for en-AU should win
        $request  = $this->requestWithAcceptLanguage('en-NZ, en-AU');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(EnglishAustralia::class, $language);
    }

    public function testFromRequestRespectsQualityWeighting(): void
    {
        // German preferred over French via quality value
        $request  = $this->requestWithAcceptLanguage('fr;q=0.8, de;q=1.0');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(German::class, $language);
    }

    public function testFromRequestIgnoresQualityZero(): void
    {
        // German is explicitly rejected, French should be chosen
        $request  = $this->requestWithAcceptLanguage('de;q=0, fr');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(French::class, $language);
    }

    public function testFromRequestIgnoresWildcard(): void
    {
        // Wildcard alone should fall back
        $request  = $this->requestWithAcceptLanguage('*');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(EnglishUnitedStates::class, $language);
    }

    public function testFromRequestWithNoMatchReturnsFallback(): void
    {
        // Completely unsupported language
        $request  = $this->requestWithAcceptLanguage('xx');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(EnglishUnitedStates::class, $language);
    }

    public function testFromRequestComplexHeader(): void
    {
        // Realistic browser header: prefer zh-TW, fallback to en-US
        $request  = $this->requestWithAcceptLanguage('zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(ChineseTraditional::class, $language);
    }

    public function testFromRequestPortugueseBrazil(): void
    {
        $request  = $this->requestWithAcceptLanguage('pt-BR');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(PortugueseBrazil::class, $language);
    }

    public function testFromRequestPortugueseBrazilLowerCase(): void
    {
        $request  = $this->requestWithAcceptLanguage('pt-br');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(PortugueseBrazil::class, $language);
    }

    public function testFromRequestFrenchCanada(): void
    {
        $request  = $this->requestWithAcceptLanguage('fr-CA');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(FrenchCanada::class, $language);
    }

    public function testFromRequestSerbianLatin(): void
    {
        $request  = $this->requestWithAcceptLanguage('sr-Latn');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(SerbianLatin::class, $language);
    }

    public function testFromRequestSerbianLatinLowerCase(): void
    {
        $request  = $this->requestWithAcceptLanguage('sr-latn');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(SerbianLatin::class, $language);
    }

    public function testFromRequestEnglishGbLowerCase(): void
    {
        $request  = $this->requestWithAcceptLanguage('en-gb');
        $language = $this->factory->fromRequest($request);

        self::assertInstanceOf(EnglishGreatBritain::class, $language);
    }

    /**
     * Every supported language tag should produce a matching language object.
     */
    public function testFromLanguageTagCoversAllSupportedTags(): void
    {
        foreach ($this->factory->allLanguageTags() as $tag) {
            $language = $this->factory->fromLanguageTag($tag);

            self::assertSame($tag, $language->languageTag());
        }
    }

    private function requestWithAcceptLanguage(string $header): ServerRequest
    {
        return (new ServerRequest('GET', '/'))->withHeader('Accept-Language', $header);
    }
}
