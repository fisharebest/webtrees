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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;

use function explode;
use function in_array;
use function strtolower;

class TinymceModule extends AbstractModule implements ModuleExternalUrlInterface, ModuleGlobalInterface
{
    use ModuleExternalUrlTrait;
    use ModuleGlobalTrait;

    /**
     * These languages are bundled from tinymce-i18n/langs8 in resources/js/tinymce.js.
     */
    private const array TINYMCE_LANGUAGES = [
        'ar', 'bg-BG', 'bs', 'ca', 'cs', 'cy', 'da', 'de', 'el', 'es', 'et', 'eu', 'fa', 'fi', 'fr-FR', 'gl',
        'he-IL', 'hr', 'hu-HU', 'hy', 'id', 'is-IS', 'it', 'ja', 'ka-GE', 'kk', 'ko-KR', 'lt', 'lv', 'nb-NO',
        'ne', 'nl', 'oc', 'pl', 'pt-BR', 'pt-PT', 'ro', 'ru', 'sk', 'sl-SI', 'sr', 'sv-SE', 'ta', 'th-TH',
        'tr', 'uk', 'vi', 'zh-CN', 'zh-TW',
    ];

    private const array TINYMCE_LANGUAGE_ALIASES = [
        'bg'      => 'bg-BG',
        'en-au'   => 'en',
        'en-gb'   => 'en',
        'en-us'   => 'en',
        'fr'      => 'fr-FR',
        'fr-ca'   => 'fr-FR',
        'he'      => 'he-IL',
        'hu'      => 'hu-HU',
        'is'      => 'is-IS',
        'ka'      => 'ka-GE',
        'ko'      => 'ko-KR',
        'nb'      => 'nb-NO',
        'nn'      => 'nb-NO',
        'pt-br'   => 'pt-BR',
        'pt'      => 'pt-PT',
        'sl'      => 'sl-SI',
        'sv'      => 'sv-SE',
        'th'      => 'th-TH',
        'zh-cn'   => 'zh-CN',
        'zh-hans' => 'zh-CN',
        'zh-hant' => 'zh-TW',
        'zh-hk'   => 'zh-TW',
        'zh-mo'   => 'zh-TW',
        'zh-tw'   => 'zh-TW',
    ];

    public function isEnabledByDefault(): bool
    {
        // Existing installations keep CKEditor active until TinyMCE is explicitly enabled.
        return false;
    }

    public function title(): string
    {
        /* I18N: Name of a module. TinyMCE is a trademark. Do not translate it? https://www.tiny.cloud */
        return I18N::translate('TinyMCE™');
    }

    public function description(): string
    {
        /* I18N: Description of the “TinyMCE” module. WYSIWYG = “what you see is what you get” */
        return I18N::translate('Allow other modules to edit text using a “WYSIWYG” editor, instead of using HTML codes.');
    }

    /**
     * Home page for the service.
     */
    public function externalUrl(): string
    {
        return 'https://www.tiny.cloud';
    }

    /**
     * Raw content, to be added at the end of the <body> element.
     * Typically, this will be <script> elements.
     */
    public function bodyContent(): string
    {
        return view('modules/tinymce/tinymce-js', [
            // TinyMCE is bundled separately and loaded only on pages that use html-edit textareas.
            'tinymce_script' => asset('js/tinymce.min.js'),
            'language'       => $this->tinymceLanguage(I18N::languageTag()),
        ]);
    }

    private function tinymceLanguage(string $language_tag): string
    {
        $language_tag = strtolower($language_tag);
        $language_tag = self::TINYMCE_LANGUAGE_ALIASES[$language_tag] ?? $language_tag;

        if (in_array($language_tag, self::TINYMCE_LANGUAGES, true)) {
            return $language_tag;
        }

        $language = explode('-', $language_tag)[0];
        $language = self::TINYMCE_LANGUAGE_ALIASES[$language] ?? $language;

        if (in_array($language, self::TINYMCE_LANGUAGES, true)) {
            return $language;
        }

        return 'en';
    }
}
