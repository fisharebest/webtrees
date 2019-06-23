<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\I18N;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function app;

/**
 * Trait ModuleAnalyticsTrait - default implementation of ModuleAnalyticsInterface
 */
trait ModuleAnalyticsTrait
{
    /**
     * Should we add this tracker?
     *
     * @return bool
     */
    public function analyticsCanShow(): bool
    {
        $request = app(ServerRequestInterface::class);

        // If the browser sets the DNT header, then we won't use analytics.
        $dnt = $request->getServerParams()['HTTP_DNT'] ?? '';

        if ($dnt === '1') {
            return false;
        }

        foreach ($this->analyticsParameters() as $parameter) {
            if ($parameter === '') {
                return false;
            }
        }

        return true;
    }

    /**
     * The parameters that need to be embedded in the snippet.
     *
     * @return string[]
     */
    public function analyticsParameters(): array
    {
        return [];
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Tracking and analytics');
    }

    /**
     * @return ResponseInterface
     */
    public function getAdminAction(): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/analytics-edit', [
            'form_fields' => $this->analyticsFormFields(),
            'preview'     => $this->analyticsSnippet($this->analyticsParameters()),
            'title'       => $this->title(),
        ]);
    }

    /**
     * @param string  $view_name
     * @param mixed[] $view_data
     * @param int     $status
     *
     * @return ResponseInterface
     */
    abstract protected function viewResponse(string $view_name, array $view_data, $status = StatusCodeInterface::STATUS_OK): ResponseInterface;

    /**
     * Form fields to edit the parameters.
     *
     * @return string
     */
    public function analyticsFormFields(): string
    {
        return '';
    }

    /**
     * Embed placeholders in the snippet.
     *
     * @param string[] $parameters
     *
     * @return string
     */
    public function analyticsSnippet(array $parameters): string
    {
        return '';
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    abstract public function title(): string;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        foreach (array_keys($this->analyticsParameters()) as $parameter) {
            $new_value = $request->getParsedBody()[$parameter];

            $this->setPreference($parameter, $new_value);
        }

        return redirect(route('analytics'));
    }

    /**
     * Set a module setting.
     * Since module settings are NOT NULL, setting a value to NULL will cause
     * it to be deleted.
     *
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return void
     */
    abstract public function setPreference(string $setting_name, string $setting_value): void;
}
