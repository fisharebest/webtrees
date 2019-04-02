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

use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\JavascriptRenderer;
use DebugBar\StandardDebugBar;
use Fisharebest\Webtrees\DebugBar\ViewCollector;
use PDO;

/**
 * A static wrapper for maximebf/php-debugbar.
 *
 * @see https://github.com/maximebf/php-debugbar
 */
class DebugBar
{
    /** @var StandardDebugbar|null  */
    private static $debugbar = null;

    /** @var JavascriptRenderer */
    private static $renderer;

    /**
     * Initialize the Debugbar.
     *
     * @param bool $enable
     *
     * @return void
     */
    public static function init(bool $enable = true): void
    {
        if ($enable) {
            self::$debugbar = new StandardDebugBar();
            self::$debugbar->addCollector(new ViewCollector());

            self::$renderer = self::$debugbar->getJavascriptRenderer('./vendor/maximebf/debugbar/src/DebugBar/Resources/');
        }
    }

    /**
     * Initialize the PDO collector.
     *
     * @param PDO $pdo
     *
     * @return PDO
     */
    public static function initPDO(PDO $pdo): PDO
    {
        if (self::$debugbar instanceof StandardDebugBar) {
            $traceable_pdo = new TraceablePDO($pdo);
            self::$debugbar->addCollector(new PDOCollector($traceable_pdo));
        }

        return $pdo;
    }

    /**
     * Render the body content.
     *
     * @return string
     */
    public static function render(): string
    {
        if (self::$debugbar instanceof StandardDebugBar) {
            return self::$renderer->render();
        }

        return '';
    }

    /**
     * Render the head content.
     *
     * @return string
     */
    public static function renderHead(): string
    {
        if (self::$debugbar instanceof StandardDebugBar) {
            return self::$renderer->renderHead();
        }

        return '';
    }

    /**
     * For POST/redirect responses, we "stack" the data onto the next GET request.
     *
     * @return void
     */
    public static function stackData(): void
    {
        if (self::$debugbar instanceof StandardDebugBar) {
            self::$debugbar->stackData();
        }
    }

    /**
     * For JSON responses, we send the data in HTTP headers.
     *
     * @return void
     */
    public static function sendDataInHeaders(): void
    {
        if (self::$debugbar instanceof StandardDebugBar) {
            self::$debugbar->sendDataInHeaders();
        }
    }

    /**
     * Add a message.
     *
     * @param string $message
     * @param string $label
     * @param bool   $isString
     *
     * @return void
     */
    public static function addMessage($message, $label = 'info', $isString = true): void
    {
        if (self::$debugbar instanceof StandardDebugBar) {
            $collector = self::$debugbar->getCollector('messages');

            if ($collector instanceof MessagesCollector) {
                $collector->addMessage($message, $label, $isString);
            }
        }
    }

    /**
     * Start a timer.
     *
     * @param string      $name
     *
     * @return void
     */
    public static function startMeasure($name): void
    {
        if (self::$debugbar instanceof StandardDebugBar) {
            $collector = self::$debugbar->getCollector('time');

            if ($collector instanceof TimeDataCollector) {
                $collector->startMeasure($name);
            }
        }
    }

    /**
     * Stop a timer.
     *
     * @param string $name
     *
     * @return void
     */
    public static function stopMeasure($name): void
    {
        if (self::$debugbar instanceof StandardDebugBar) {
            $collector = self::$debugbar->getCollector('time');

            if ($collector instanceof TimeDataCollector) {
                $collector->stopMeasure($name);
            }
        }
    }

    /**
     * Log an exception/throwable
     *
     * @param string  $view
     * @param mixed[] $data
     *
     * @return void
     */
    public static function addView(string $view, array $data): void
    {
        if (self::$debugbar instanceof StandardDebugBar) {
            $collector = self::$debugbar->getCollector('views');

            if ($collector instanceof ViewCollector) {
                $collector->addView($view, $data);
            }
        }
    }
}
