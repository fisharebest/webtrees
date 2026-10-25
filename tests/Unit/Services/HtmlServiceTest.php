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

namespace Fisharebest\Webtrees\Tests\Unit\Services;

use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Services\HtmlService;

#[CoversClass(HtmlService::class)]
class HtmlServiceTest extends TestCase
{
    private HtmlService $html_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->html_service = new HtmlService();
    }

    public function testAllowedHtml(): void
    {
        $dirty = '<div class="foo">bar</div>';
        $clean = $this->html_service->sanitize($dirty);

        self::assertSame($dirty, $clean);
    }

    public function testDisallowedHtml(): void
    {
        $dirty = '<div class="foo" onclick="alert(123)">bar</div>';
        $clean = $this->html_service->sanitize($dirty);

        self::assertSame('<div class="foo">bar</div>', $clean);
    }

    // --- Custom attribute: class on all elements ---

    public function testClassAttributeOnDiv(): void
    {
        $html = '<div class="container">content</div>';
        self::assertSame($html, $this->html_service->sanitize($html));
    }

    public function testClassAttributeOnSpan(): void
    {
        $html = '<span class="highlight">text</span>';
        self::assertSame($html, $this->html_service->sanitize($html));
    }

    // --- Custom attribute: id on all elements ---

    public function testIdAttributeOnDiv(): void
    {
        $html = '<div id="main-content">content</div>';
        self::assertSame($html, $this->html_service->sanitize($html));
    }

    public function testIdAttributeOnHeading(): void
    {
        $html = '<h2 id="section-title">Title</h2>';
        self::assertSame($html, $this->html_service->sanitize($html));
    }

    // --- Custom attribute: target on <a> ---

    public function testTargetAttributeOnLink(): void
    {
        $html = '<a href="https://example.com" target="_blank">link</a>';
        self::assertSame($html, $this->html_service->sanitize($html));
    }

    public function testTargetAttributeSelf(): void
    {
        $html = '<a href="https://example.com" target="_self">link</a>';
        self::assertSame($html, $this->html_service->sanitize($html));
    }

    // --- Custom attribute: usemap on <img> ---

    public function testUsemapAttributeOnImg(): void
    {
        $html = '<img src="https://example.com/image.png" usemap="#mymap">';
        self::assertSame('<img src="https://example.com/image.png" usemap="#mymap" />', $this->html_service->sanitize($html));
    }

    // --- Custom element: <map> ---

    public function testMapElement(): void
    {
        $html = '<map name="mymap" id="mymap" title="My Map"></map>';
        self::assertSame($html, $this->html_service->sanitize($html));
    }

    // --- Custom element: <area> ---

    public function testAreaElement(): void
    {
        $html = '<map name="mymap"><area shape="rect" coords="0,0,100,100" href="https://example.com" alt="Example"></map>';
        self::assertSame('<map name="mymap"><area shape="rect" coords="0,0,100,100" href="https://example.com" alt="Example" /></map>', $this->html_service->sanitize($html));
    }

    public function testAreaElementAllAttributes(): void
    {
        $html = '<map name="mymap"><area name="region1" id="area1" alt="Region" coords="0,0,50,50" accesskey="r" href="https://example.com" shape="circle" tabindex="1"></map>';
        self::assertSame('<map name="mymap"><area name="region1" id="area1" alt="Region" coords="0,0,50,50" accesskey="r" href="https://example.com" shape="circle" tabindex="1" /></map>', $this->html_service->sanitize($html));
    }

    // --- Custom element: <audio> ---

    public function testAudioElement(): void
    {
        $html = '<audio src="https://example.com/song.mp3" controls></audio>';
        self::assertSame($html, $this->html_service->sanitize($html));
    }

    // --- Custom element: <video> ---

    public function testVideoElement(): void
    {
        $html = '<video src="https://example.com/video.mp4" controls width="640" height="480"></video>';
        self::assertSame($html, $this->html_service->sanitize($html));
    }

    public function testVideoElementWithPoster(): void
    {
        $html = '<video src="https://example.com/video.mp4" poster="https://example.com/thumb.jpg" controls></video>';
        self::assertSame($html, $this->html_service->sanitize($html));
    }

    // --- Custom attribute: style on all elements ---

    public function testStyleAttributeOnDiv(): void
    {
        $html = '<div style="width: 50%; margin: auto;">content</div>';
        self::assertSame($html, $this->html_service->sanitize($html));
    }

    public function testStyleAttributeOnImg(): void
    {
        $html = '<img src="https://example.com/img.png" style="max-width: 100%;">';
        self::assertSame('<img src="https://example.com/img.png" style="max-width: 100%;" />', $this->html_service->sanitize($html));
    }

    // --- Link schemes: mailto and tel ---

    public function testMailtoLinkScheme(): void
    {
        $html = '<a href="mailto:user@example.com">email</a>';
        $clean = $this->html_service->sanitize($html);

        // The sanitizer may entity-encode special characters in URLs.
        self::assertStringContainsString('mailto:', $clean);
        self::assertStringContainsString('example.com', $clean);
        self::assertStringContainsString('>email</a>', $clean);
    }

    public function testTelLinkScheme(): void
    {
        $html = '<a href="tel:+1234567890">call</a>';
        $clean = $this->html_service->sanitize($html);

        // The sanitizer may entity-encode special characters in URLs.
        self::assertStringContainsString('tel:', $clean);
        self::assertStringContainsString('1234567890', $clean);
        self::assertStringContainsString('>call</a>', $clean);
    }

    // --- Disallowed: script, event handlers, javascript: scheme ---

    public function testScriptElementRemoved(): void
    {
        $html = '<script>alert("xss")</script>';
        self::assertSame('', $this->html_service->sanitize($html));
    }

    public function testJavascriptSchemeRemoved(): void
    {
        $dirty = '<a href="javascript:alert(1)">click</a>';
        $clean = $this->html_service->sanitize($dirty);

        self::assertStringNotContainsString('javascript:', $clean);
    }

    public function testOnEventAttributeRemoved(): void
    {
        $dirty = '<img src="https://example.com/img.png" onerror="alert(1)">';
        $clean = $this->html_service->sanitize($dirty);

        self::assertSame('<img src="https://example.com/img.png" />', $clean);
    }
}
