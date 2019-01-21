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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractModule - common functions for blocks
 */
abstract class AbstractModule implements ModuleInterface
{
    /** @var string A unique internal name for this module (based on the installation folder). */
    private $name = '';

    /** @var int The default access level for this module.  It can be changed in the control panel. */
    protected $access_level = Auth::PRIV_PRIVATE;

    /** @var bool The default status for this module.  It can be changed in the control panel. */
    private $enabled = true;

    /** @var string For custom modules - optional (recommended) version number */
    public const CUSTOM_VERSION = '';

    /** @var string For custom modules - link for support, upgrades, etc. */
    public const CUSTOM_WEBSITE = '';

    /** @var string How to render view responses */
    protected $layout = 'layouts/default';

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'Module name goes here';
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return $this->title();
    }

    /**
     * Get a block setting.
     *
     * Originally, this was just used for the home-page blocks.  Now, it is used by any
     * module that has repeated blocks of content on the same page.
     *
     * @param int    $block_id
     * @param string $setting_name
     * @param string $default
     *
     * @return string
     */
    final protected function getBlockSetting(int $block_id, string $setting_name, string $default = ''): string
    {
        $settings = app('cache.array')->rememberForever('block_setting' . $block_id, function () use ($block_id): array {
            return DB::table('block_setting')
                ->where('block_id', '=', $block_id)
                ->pluck('setting_value', 'setting_name')
                ->all();
        });

        return $settings[$setting_name] ?? $default;
    }

    /**
     * Set a block setting.
     *
     * @param int    $block_id
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return $this
     */
    final protected function setBlockSetting(int $block_id, string $setting_name, string $setting_value): self
    {
        DB::table('block_setting')->updateOrInsert([
            'block_id'      => $block_id,
            'setting_name'  => $setting_name,
        ], [
            'setting_value' => $setting_value,
        ]);

        return $this;
    }

    /**
     * A unique internal name for this module (based on the installation folder).
     *
     * @param string $name
     *
     * @return ModuleInterface
     */
    final public function setName(string $name): ModuleInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * A unique internal name for this module (based on the installation folder).
     *
     * @return string
     */
    final public function getName(): string
    {
        return $this->name;
    }

    /**
     * Modules are either enabled or disabled.
     *
     * @param bool $enabled
     *
     * @return ModuleInterface
     */
    final public function setEnabled(bool $enabled): ModuleInterface
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Modules are either enabled or disabled.
     *
     * @return bool
     */
    final public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get a module setting. Return a default if the setting is not set.
     *
     * @param string $setting_name
     * @param string $default
     *
     * @return string
     */
    final public function getPreference($setting_name, $default = ''): string
    {
        return DB::table('module_setting')
            ->where('module_name', '=', $this->getName())
            ->where('setting_name', '=', $setting_name)
            ->value('setting_value') ?? $default;
    }

    /**
     * Set a module setting.
     *
     * Since module settings are NOT NULL, setting a value to NULL will cause
     * it to be deleted.
     *
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return $this
     */
    final public function setPreference($setting_name, $setting_value): self
    {
        DB::table('module_setting')->updateOrInsert([
            'module_name'  => $this->getName(),
            'setting_name' => $setting_name,
        ], [
            'setting_value' => $setting_value,
        ]);

        return $this;
    }

    /**
     * Get a the current access level for a module
     *
     * @param Tree   $tree
     * @param string $component tab, block, menu, etc
     *
     * @return int
     */
    final public function accessLevel(Tree $tree, string $component): int
    {
        $access_levels = app('cache.array')
            ->rememberForever('module_privacy' . $tree->id(), function () use ($tree): Collection {
                return DB::table('module_privacy')
                    ->where('gedcom_id', '=', $tree->id())
                    ->get();
            });

        $row = $access_levels->filter(function (stdClass $row) use ($component): bool {
            return $row->component === $component && $row->module_name === $this->getName();
        })->first();

        return $row ? (int) $row->access_level : $this->access_level;
    }

    /**
     * Create a response object from a view.
     *
     * @param string  $view_name
     * @param mixed[] $view_data
     * @param int     $status
     *
     * @return Response
     */
    final protected function viewResponse($view_name, $view_data, $status = Response::HTTP_OK): Response
    {
        // Make the view's data available to the layout.
        $layout_data = $view_data;

        // Render the view
        $layout_data['content'] = view($view_name, $view_data);

        // Insert the view into the layout
        $html = view($this->layout, $layout_data);

        return new Response($html, $status);
    }
}
