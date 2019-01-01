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

namespace Fisharebest\Webtrees;

use Exception;
use function ob_end_clean;
use Throwable;

/**
 * Simple view/template class.
 */
class View
{
    // Where do our templates live
    const TEMPLATE_PATH = 'resources/views/';

    // File extension for our template files.
    const TEMPLATE_EXTENSION = '.phtml';

    /**
     * @var string The (file) name of the view.
     */
    private $name;

    /**
     * @var mixed[] Data to be inserted into the view.
     */
    private $data;

    /**
     * @var mixed[] Data to be inserted into all views.
     */
    private static $shared_data = [];

    /**
     * @var string Implementation of Blade "stacks".
     */
    private static $stack;

    /**
     * @var array[] Implementation of Blade "stacks".
     */
    private static $stacks = [];

    /**
     * Createa view from a template name and optional data.
     *
     * @param string $name
     * @param array  $data
     */
    public function __construct(string $name, $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * Shared data that is available to all views.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public static function share(string $key, $value)
    {
        self::$shared_data[$key] = $value;
    }

    /**
     * Implementation of Blade "stacks".
     *
     * @see https://laravel.com/docs/5.5/blade#stacks
     *
     * @param string $stack
     *
     * @return void
     */
    public static function push(string $stack)
    {
        self::$stack = $stack;
        ob_start();
    }

    /**
     * Implementation of Blade "stacks".
     *
     * @return void
     */
    public static function endpush()
    {
        self::$stacks[self::$stack][] = ob_get_clean();
    }

    /**
     * Implementation of Blade "stacks".
     *
     * @param string $stack
     *
     * @return string
     */
    public static function stack(string $stack): string
    {
        $content = implode('', self::$stacks[$stack] ?? []);

        self::$stacks[$stack] = [];

        return $content;
    }

    /**
     * Render a view.
     *
     * @return string
     * @throws Throwable
     */
    public function render(): string
    {
        $variables_for_view = $this->data + self::$shared_data;
        extract($variables_for_view);

        try {
            ob_start();
            // Do not use require, so we can catch errors for missing files
            include $this->getFilenameForView($this->name);

            return ob_get_clean();
        } catch (Throwable $ex) {
            ob_end_clean();
            throw $ex;
        }
    }

    /**
     * Allow a theme to override the default views.
     *
     * @param string $view_name
     *
     * @return string
     * @throws Exception
     */
    public function getFilenameForView($view_name): string
    {
        foreach ($this->paths() as $path) {
            $view_file = $path . $view_name . self::TEMPLATE_EXTENSION;

            if (is_file($view_file)) {
                return $view_file;
            }
        }

        throw new Exception('View not found: ' . e($view_name));
    }

    /**
     * Cerate and render a view in a single operation.
     *
     * @param string  $name
     * @param mixed[] $data
     *
     * @return string
     */
    public static function make($name, $data = []): string
    {
        $view = new static($name, $data);

        DebugBar::addView($name, $data);

        return $view->render();
    }

    /**
     * @return string[]
     */
    private function paths(): array
    {
        static $paths = [];

        if (empty($paths)) {
            // Module views
            // @TODO - this includes disabled modules.
            $paths = glob(WT_ROOT . Webtrees::MODULES_PATH . '*/' . self::TEMPLATE_PATH);
            // Theme views
            $paths[] = WT_ROOT . Webtrees::THEMES_PATH . Theme::theme()->themeId() . '/' . self::TEMPLATE_PATH;
            // Core views
            $paths[] = WT_ROOT . self::TEMPLATE_PATH;

            $paths = array_filter($paths, function (string $path): bool {
                return is_dir($path);
            });
        }

        return $paths;
    }
}
