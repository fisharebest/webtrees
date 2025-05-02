<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
 * Class YandexMetrica - add support for Yandex Metrica.
 */
class YandexMetrica extends AbstractModule implements ModuleAnalyticsInterface, ModuleConfigInterface, ModuleExternalUrlInterface, ModuleGlobalInterface
{
    use ModuleAnalyticsTrait;
    use ModuleConfigTrait;
    use ModuleExternalUrlTrait;
    use ModuleGlobalTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('Yandex Metrica');
    }

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * Is this a tracker, as opposed to just a site-verification.
     *
     * @return bool
     */
    public function isTracker(): bool
    {
        return false;
    }

    /**
     * Form fields to edit the parameters.
     *
     * @return string
     */
    public function analyticsFormFields(): string
    {
        return view('modules/yandex-metrica/form', $this->analyticsParameters());
    }

    /**
     * Home page for the service.
     *
     * @return string
     */
    public function externalUrl(): string
    {
        return 'https://metrika.yandex.ru';
    }

    /**
     * The parameters that need to be embedded in the snippet.
     *
     * @return array<string>
     */
    public function analyticsParameters(): array
    {
        return [
            'YANDEX_METRICA_ID' => $this->getPreference('YANDEX_METRICA_ID')
        ];
    }

    /**
     * Embed placeholders in the snippet.
     *
     * @param array<string> $parameters
     *
     * @return string
     */
    public function analyticsSnippet(array $parameters): string
    {
        return view('modules/yandex-metrica/snippet', $parameters);
    }

    /**
     * Raw content, to be added at the end of the <head> element.
     * Typically, this will be <link> and <meta> elements.
     *
     * @return string
     */
    public function headContent(): string
    {
        if ($this->analyticsCanShow()) {
            return $this->analyticsSnippet($this->analyticsParameters());
        }

        return '';
    }
}
