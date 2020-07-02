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

namespace Fisharebest\Webtrees\DebugBar;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * A data collector for maximebf/php-debugbar.
 * Gathers information about views.
 */
class ViewCollector extends DataCollector implements Renderable
{
    /** @var string[] */
    protected $views = [];

    /**
     * Add details about a view
     *
     * @param string              $view
     * @param array<string,mixed> $data
     *
     * @return void
     */
    public function addView(string $view, array $data): void
    {
        $num = count($this->views) + 1;
        $key = '#' . $num . ' ' . $view;

        $this->views[$key] = $this->getDataFormatter()->formatVar($data);
    }

    /**
     * Called by the DebugBar when data needs to be collected
     *
     * @return array<string,mixed> Collected data
     */
    public function collect(): array
    {
        $views = $this->views;

        return [
            'count' => count($views),
            'views' => $views,
        ];
    }

    /**
     * Returns the unique name of the collector
     *
     * @return string
     */
    public function getName(): string
    {
        return 'views';
    }

    /**
     * Returns a hash where keys are control names and their values
     * an array of options as defined in {@see \DebugBar\JavascriptRenderer::addControl()}
     *
     * @return array<string,mixed>
     */
    public function getWidgets(): array
    {
        $name = $this->getName();

        return [
            $name            => [
                'icon'    => 'list-alt',
                'widget'  => 'PhpDebugBar.Widgets.VariableListWidget',
                'map'     => $name . '.views',
                'default' => '[]',
            ],
            $name . ':badge' => [
                'map'     => $name . '.count',
                'default' => 0,
            ],
        ];
    }
}
