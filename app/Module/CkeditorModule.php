<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

/**
 * Class CkeditorModule
 */
class CkeditorModule extends AbstractModule implements ModuleExternalUrlInterface, ModuleGlobalInterface
{
    use ModuleExternalUrlTrait;
    use ModuleGlobalTrait;

    // Location of our installation of CK editor.
    public const CKEDITOR_PATH = 'ckeditor-4.15.1-custom/';

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module. CKEditor is a trademark. Do not translate it? http://ckeditor.com */
        return I18N::translate('CKEditor™');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “CKEditor” module. WYSIWYG = “what you see is what you get” */
        return I18N::translate('Allow other modules to edit text using a “WYSIWYG” editor, instead of using HTML codes.');
    }

    /**
     * Home page for the service.
     *
     * @return string
     */
    public function externalUrl(): string
    {
        return 'https://ckeditor.com';
    }

    /**
     * Raw content, to be added at the end of the <body> element.
     * Typically, this will be <script> elements.
     *
     * @return string
     */
    public function bodyContent(): string
    {
        return view('modules/ckeditor/ckeditor-js', [
            'ckeditor_path' => asset(self::CKEDITOR_PATH),
            'language'      => I18N::locale()->language()->code(),
        ]);
    }
}
