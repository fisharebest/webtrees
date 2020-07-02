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
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Throwable;

use function array_key_exists;
use function explode;
use function extract;
use function implode;
use function is_file;
use function ob_end_clean;
use function ob_get_level;
use function ob_start;
use function sha1;

use const EXTR_OVERWRITE;

/**
 * Simple view/template class.
 */
class View
{
    public const NAMESPACE_SEPARATOR = '::';

    // File extension for our template files.
    private const TEMPLATE_EXTENSION = '.phtml';

    /**
     * @var string The (file) name of the view.
     */
    private $name;

    /**
     * @var mixed[] Data to be inserted into the view.
     */
    private $data;

    /**
     * @var string[] Where do the templates live, for each namespace.
     */
    private static $namespaces = [
        '' => Webtrees::ROOT_DIR . 'resources/views/',
    ];

    /**
     * @var string[] Modules can replace core views with their own.
     */
    private static $replacements = [];

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
     * @param string       $name
     * @param array<mixed> $data
     */
    public function __construct(string $name, $data = [])
    {
        $this->name = $name;
        $this->data = $data;
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
    public static function push(string $stack): void
    {
        self::$stack = $stack;

        ob_start();
    }

    /**
     * Implementation of Blade "stacks".
     *
     * @return void
     */
    public static function endpush(): void
    {
        $content = ob_get_clean();

        if ($content === false) {
            throw new LogicException('found endpush(), but did not find push()');
        }

        self::$stacks[self::$stack][] = $content;
    }

    /**
     * Variant of push that will only add one copy of each item.
     *
     * @param string $stack
     *
     * @return void
     */
    public static function pushunique(string $stack): void
    {
        self::$stack = $stack;

        ob_start();
    }

    /**
     * Variant of push that will only add one copy of each item.
     *
     * @return void
     */
    public static function endpushunique(): void
    {
        $content = ob_get_clean();

        if ($content === false) {
            throw new LogicException('found endpushunique(), but did not find pushunique()');
        }

        self::$stacks[self::$stack][sha1($content)] = $content;
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
        extract($this->data, EXTR_OVERWRITE);

        try {
            ob_start();
            // Do not use require, so we can catch errors for missing files
            include $this->getFilenameForView($this->name);

            return ob_get_clean();
        } catch (Throwable $ex) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            throw $ex;
        }
    }

    /**
     * @param string $namespace
     * @param string $path
     *
     * @throws InvalidArgumentException
     */
    public static function registerNamespace(string $namespace, string $path): void
    {
        if ($namespace === '') {
            throw new InvalidArgumentException('Cannot register the default namespace');
        }

        if (!Str::endsWith($path, '/')) {
            throw new InvalidArgumentException('Paths must end with a directory separator');
        }

        self::$namespaces[$namespace] = $path;
    }

    /**
     * @param string $old
     * @param string $new
     *
     * @throws InvalidArgumentException
     */
    public static function registerCustomView(string $old, string $new): void
    {
        if (Str::contains($old, self::NAMESPACE_SEPARATOR) && Str::contains($new, self::NAMESPACE_SEPARATOR)) {
            self::$replacements[$old] = $new;
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * Find the file for a view.
     *
     * @param string $view_name
     *
     * @return string
     * @throws Exception
     */
    public function getFilenameForView(string $view_name): string
    {
        // If we request "::view", then use it explicityly.  Don't allow replacements.
        $explicit = Str::startsWith($view_name, self::NAMESPACE_SEPARATOR);

        if (!Str::contains($view_name, self::NAMESPACE_SEPARATOR)) {
            $view_name = self::NAMESPACE_SEPARATOR . $view_name;
        }

        // Apply replacements / customisations
        while (!$explicit && array_key_exists($view_name, self::$replacements)) {
            $view_name = self::$replacements[$view_name];
        }

        [$namespace, $view_name] = explode(self::NAMESPACE_SEPARATOR, $view_name, 2);

        if ((self::$namespaces[$namespace] ?? null) === null) {
            throw new RuntimeException('Namespace "' . e($namespace) .  '" not found.');
        }

        $view_file = self::$namespaces[$namespace] . $view_name . self::TEMPLATE_EXTENSION;

        if (!is_file($view_file)) {
            throw new RuntimeException('View file not found: ' . e($view_file));
        }

        return $view_file;
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
        $view = new self($name, $data);

        DebugBar::addView($name, $data);

        return $view->render();
    }
}
