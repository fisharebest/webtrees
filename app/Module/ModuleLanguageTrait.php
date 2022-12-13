<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Relationship;
use Normalizer;

use function mb_substr;
use function normalizer_normalize;

/**
 * Trait ModuleLanguageEventsTrait - default implementation of ModuleLanguageInterface.
 */
trait ModuleLanguageTrait
{
    /** @var array<string,string> */
    private array $combining_diacritics = [
        "\u{0300}" => '',
        "\u{0301}" => '',
        "\u{0302}" => '',
        "\u{0303}" => '',
        "\u{0304}" => '',
        "\u{0305}" => '',
        "\u{0306}" => '',
        "\u{0307}" => '',
        "\u{0308}" => '',
        "\u{0309}" => '',
        "\u{030A}" => '',
        "\u{030B}" => '',
        "\u{030C}" => '',
        "\u{030D}" => '',
        "\u{030E}" => '',
        "\u{030F}" => '',
        "\u{0310}" => '',
        "\u{0311}" => '',
        "\u{0312}" => '',
        "\u{0313}" => '',
        "\u{0314}" => '',
        "\u{0315}" => '',
        "\u{0316}" => '',
        "\u{0317}" => '',
        "\u{0318}" => '',
        "\u{0319}" => '',
        "\u{031A}" => '',
        "\u{031B}" => '',
        "\u{031C}" => '',
        "\u{031D}" => '',
        "\u{031E}" => '',
        "\u{031F}" => '',
        "\u{0320}" => '',
        "\u{0321}" => '',
        "\u{0322}" => '',
        "\u{0323}" => '',
        "\u{0324}" => '',
        "\u{0325}" => '',
        "\u{0326}" => '',
        "\u{0327}" => '',
        "\u{0328}" => '',
        "\u{0329}" => '',
        "\u{032A}" => '',
        "\u{032B}" => '',
        "\u{032C}" => '',
        "\u{032D}" => '',
        "\u{032E}" => '',
        "\u{032F}" => '',
        "\u{0330}" => '',
        "\u{0331}" => '',
        "\u{0332}" => '',
        "\u{0333}" => '',
        "\u{0334}" => '',
        "\u{0335}" => '',
        "\u{0336}" => '',
        "\u{0337}" => '',
        "\u{0338}" => '',
        "\u{0339}" => '',
        "\u{033A}" => '',
        "\u{033B}" => '',
        "\u{033C}" => '',
        "\u{033D}" => '',
        "\u{033E}" => '',
        "\u{033F}" => '',
        "\u{0340}" => '',
        "\u{0341}" => '',
        "\u{0342}" => '',
        "\u{0343}" => '',
        "\u{0344}" => '',
        "\u{0345}" => '',
        "\u{0346}" => '',
        "\u{0347}" => '',
        "\u{0348}" => '',
        "\u{0349}" => '',
        "\u{034A}" => '',
        "\u{034B}" => '',
        "\u{034C}" => '',
        "\u{034D}" => '',
        "\u{034E}" => '',
        "\u{034F}" => '',
        "\u{0350}" => '',
        "\u{0351}" => '',
        "\u{0352}" => '',
        "\u{0353}" => '',
        "\u{0354}" => '',
        "\u{0355}" => '',
        "\u{0356}" => '',
        "\u{0357}" => '',
        "\u{0358}" => '',
        "\u{0359}" => '',
        "\u{035A}" => '',
        "\u{035B}" => '',
        "\u{035C}" => '',
        "\u{035D}" => '',
        "\u{035E}" => '',
        "\u{035F}" => '',
        "\u{0360}" => '',
        "\u{0361}" => '',
        "\u{0362}" => '',
        "\u{0363}" => '',
        "\u{0364}" => '',
        "\u{0365}" => '',
        "\u{0366}" => '',
        "\u{0367}" => '',
        "\u{0368}" => '',
        "\u{0369}" => '',
        "\u{036A}" => '',
        "\u{036B}" => '',
        "\u{036C}" => '',
        "\u{036D}" => '',
        "\u{036E}" => '',
        "\u{036F}" => '',
    ];

    /**
     * Phone-book ordering of letters.
     *
     * @return array<int,string>
     */
    public function alphabet(): array
    {
        return ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    }

    /**
     * Default calendar used by this language.
     *
     * @return CalendarInterface
     */
    public function calendar(): CalendarInterface
    {
        return new GregorianCalendar();
    }

    /**
     * One of: 'DMY', 'MDY', 'YMD'.
     *
     * @return string
     */
    public function dateOrder(): string
    {
        return 'DMY';
    }

    /**
     * Some languages use digraphs and trigraphs.
     *
     * @param string $string
     *
     * @return string
     */
    public function initialLetter(string $string): string
    {
        return mb_substr($string, 0, 1);
    }

    /**
     * Ignore diacritics on letters - unless the language considers them a different letter.
     *
     * @param string $text
     *
     * @return string
     */
    public function normalize(string $text): string
    {
        // Decompose any combined characters.
        $text = normalizer_normalize($text, Normalizer::FORM_KD);

        // Keep any significant diacritics.
        $text = strtr($text, $this->normalizeExceptions());

        // Remove other diacritics.
        return strtr($text, $this->combining_diacritics);
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [];
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return  $this->locale()->endonym();
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Language') . ' — ' . $this->title() . ' — ' . $this->locale()->languageTag();
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleEnUs();
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [];
    }
}
