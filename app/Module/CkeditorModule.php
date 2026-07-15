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

class CkeditorModule extends AbstractModule implements ModuleExternalUrlInterface, ModuleGlobalInterface
{
    use ModuleExternalUrlTrait;
    use ModuleGlobalTrait;

    private const array CKEDITOR_LANGUAGES = [
        'af', 'ar', 'az', 'bg', 'bn', 'bs', 'ca', 'cs', 'cy', 'da', 'de', 'de-ch', 'el', 'en', 'en-au',
        'en-ca', 'en-gb', 'eo', 'es', 'es-mx', 'et', 'eu', 'fa', 'fi', 'fo', 'fr', 'fr-ca', 'gl', 'gu',
        'he', 'hi', 'hr', 'hu', 'id', 'is', 'it', 'ja', 'ka', 'km', 'ko', 'ku', 'lt', 'lv', 'mk', 'mn',
        'ms', 'nb', 'nl', 'no', 'oc', 'pl', 'pt', 'pt-br', 'ro', 'ru', 'si', 'sk', 'sl', 'sq', 'sr',
        'sr-latn', 'sv', 'th', 'tr', 'tt', 'ug', 'uk', 'vi', 'zh', 'zh-cn',
    ];

    private const array CKEDITOR_LANGUAGE_ALIASES = [
        'en-us'   => 'en',
        'nn'      => 'no',
        'zh-hans' => 'zh-cn',
        'zh-hant' => 'zh',
    ];

    // Location of our installation of CK editor.
    public const string CKEDITOR_PATH = 'ckeditor-4.15.1-custom/';

    public function title(): string
    {
        /* I18N: Name of a module. CKEditor is a trademark. Do not translate it? https://ckeditor.com */
        return I18N::translate('CKEditor™');
    }

    public function description(): string
    {
        /* I18N: Description of the “CKEditor” module. WYSIWYG = “what you see is what you get” */
        return I18N::translate('Allow other modules to edit text using a “WYSIWYG” editor, instead of using HTML codes.');
    }

    /**
     * Home page for the service.
     */
    public function externalUrl(): string
    {
        return 'https://ckeditor.com';
    }

    /**
     * Raw content, to be added at the end of the <body> element.
     * Typically, this will be <script> elements.
     */
    public function bodyContent(): string
    {
        return view('modules/ckeditor/ckeditor-js', [
            'ckeditor_path' => asset(self::CKEDITOR_PATH),
            'language'      => $this->ckeditorLanguage(I18N::languageTag()),
        ]);
    }

    private function ckeditorLanguage(string $language_tag): string
    {
        $language_tag = strtolower($language_tag);
        $language_tag = self::CKEDITOR_LANGUAGE_ALIASES[$language_tag] ?? $language_tag;

        if (in_array($language_tag, self::CKEDITOR_LANGUAGES, true)) {
            return $language_tag;
        }

        $language = explode('-', $language_tag)[0];

        if (in_array($language, self::CKEDITOR_LANGUAGES, true)) {
            return $language;
        }

        return 'en';
    }
}
