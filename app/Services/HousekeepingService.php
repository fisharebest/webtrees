<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Illuminate\Database\Capsule\Manager as DB;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;

use function date;
use function time;

/**
 * Clean up old data, files and folders.
 */
class HousekeepingService
{
    // This is a list of old files and directories, from earlier versions of webtrees.
    // git diff 1.7.9..master --name-status | grep ^D
    private const OLD_PATHS = [
        // Removed in 1.0.3
        'themechange.php',
        // Removed in 1.1.0
        'addremotelink.php',
        'addsearchlink.php',
        'client.php',
        'dir_editor.php',
        'editconfig_gedcom.php',
        'editgedcoms.php',
        'edit_merge.php',
        'edit_news.php',
        'genservice.php',
        'logs.php',
        'manageservers.php',
        'media.php',
        'module_admin.php',
        //'modules', // Do not delete - users may have stored custom modules/data here
        'opensearch.php',
        'PEAR.php',
        'pgv_to_wt.php',
        'places',
        //'robots.txt', // Do not delete this - it may contain user data
        'serviceClientTest.php',
        'siteconfig.php',
        'SOAP',
        'uploadmedia.php',
        'useradmin.php',
        'webservice',
        'wtinfo.php',
        // Removed in 1.1.2
        'treenav.php',
        // Removed in 1.2.3
        'modules_v2',
        // Removed in 1.2.4
        'search_engine.php',
        // Removed in 1.2.5
        'sidebar.php',
        // Removed in 1.2.6
        // Removed in 1.2.7
        'login_register.php',
        // Removed in 1.3.0
        'admin_site_ipaddress.php',
        'downloadgedcom.php',
        'export_gedcom.php',
        'gedcheck.php',
        'images',
        // Removed in 1.3.1
        'imageflush.php',
        '/lightbox/js/tip_balloon_RTL.js',
        // Removed in 1.4.0
        'imageview.php',
        'media/MediaInfo.txt',
        'media/thumbs/ThumbsInfo.txt',
        // Removed in 1.5.3
        'readme.html',
        // Removed in 1.6.0
        'downloadbackup.php',
        'site-php-version.php',
        // Removed in 1.7.0
        'admin_site_other.php',
        'js',
        'library',
        'save.php',
        // Removed in 1.7.2
        'assets/js-1.7.0',
        // Removed in 1.7.4
        'assets/js-1.7.2',
        // Removed in 1.7.7
        'assets/js-1.7.4',
        // Removed in 2.0.0
        'action.php',
        'addmedia.php',
        'addmin.php',
        'admin_media.php',
        'admin_media_upload.php',
        'admin_module_blocks.php',
        'admin_module_charts.php',
        'admin_module_menus.php',
        'admin_module_reports.php',
        'admin_module_sidebar.php',
        'admin_module_tabs.php',
        'admin_modules.php',
        'admin_pgv_to_wt.php',
        'admin_site_access.php',
        'admin_site_change.php',
        'admin_site_clean.php',
        'admin_site_config.php',
        'admin_site_info.php',
        'admin_site_logs.php',
        'admin_site_merge.php',
        'admin_site_readme.php',
        'admin_site_upgrade.php',
        'admin_trees_check.php',
        'admin_trees_config.php',
        'admin_trees_download.php',
        'admin_trees_duplicates.php',
        'admin_trees_export.php',
        'admin_trees_manage.php',
        'admin_trees_merge.php',
        'admin_trees_places.php',
        'admin_trees_renumber.php',
        'admin_trees_unconnected.php',
        'admin_users.php',
        'admin_users_bulk.php',
        'ancestry.php',
        'app/Controller',
        'app/HitCounter.php',
        'app/Module/ClippingsCart/ClippingsCartController.php',
        'app/Module/FamiliesSidebarModule.php',
        'app/Module/FamilyTreeFavorites',
        'app/Module/GoogleMaps',
        'app/Module/IndividualSidebarModule.php',
        'app/Module/PageMenuModule.php',
        'app/Query',
        'app/SpecialChars',
        'assets/js-1.7.7',
        'assets/js-1.7.9',
        'autocomplete.php',
        'block_edit.php',
        'branches.php',
        'calendar.php',
        'compact.php',
        'data/html_purifier_cache',
        'descendancy.php',
        'editnews.php',
        'edituser.php',
        'edit_changes.php',
        'edit_interface.php',
        'expand_view.php',
        'familybook.php',
        'famlist.php',
        'fanchart.php',
        'find.php',
        'help_text.php',
        'hourglass.php',
        'hourglass_ajax.php',
        'import.php',
        'includes',
        'index_edit.php',
        'indilist.php',
        'inverselink.php',
        'language',
        'lifespan.php',
        'login.php',
        'logout.php',
        'mediafirewall.php',
        'medialist.php',
        'message.php',
        'module.php',
        'modules_v3',
        'notelist.php',
        'packages',
        'pedigree.php',
        'relationship.php',
        'repolist.php',
        'reportengine.php',
        'search.php',
        'search_advanced.php',
        'site-offline.php',
        'site-unavailable.php',
        'sourcelist.php',
        'statistics.php',
        'statisticsplot.php',
        'themes',
        'timeline.php',
        // Removed in 2.0.3
        'public/css/vendor.css',
        // Removed in 2.0.4
        'public/favicon-120.png',
        'public/favicon-144.png',
        'public/favicon-57.png',
        'public/favicon-76.png',
        'public/favicon-96.png',
        // Removed in 2.0.7
        'public/ckeditor-4.11.2-custom',
        // Removed in 2.0.12
        'public/ckeditor-4.14.1-custom',
        // Removed in 2.1.0
        'modules_v4/example-footer.disable',
        'modules_v4/example-middleware.disable',
        'modules_v4/example-report.disable',
        'modules_v4/example-report.disable',
        'modules_v4/example-server-configuration.disable',
        'modules_v4/example-theme.disable',
        'modules_v4/example.disable',
        'public/favicon-196.png',
        'public/site.webmanifest',
    ];

    /**
     * Delete files and folders that belonged to an earlier version of webtrees.
     * Return a list of those that we could not delete.
     *
     * @param FilesystemOperator $filesystem
     *
     * @return array<string>
     */
    public function deleteOldWebtreesFiles(FilesystemOperator $filesystem): array
    {
        $paths_to_delete = [];

        foreach (self::OLD_PATHS as $path) {
            if (!$this->deleteFileOrFolder($filesystem, $path)) {
                $paths_to_delete[] = $path;
            }
        }

        return $paths_to_delete;
    }

    /**
     * Delete old cache files.
     *
     * @param FilesystemOperator $filesystem
     * @param string             $path
     * @param int                $max_age Seconds
     *
     * @return void
     */
    public function deleteOldFiles(FilesystemOperator $filesystem, string $path, int $max_age): void
    {
        $threshold = time() - $max_age;

        $list = $filesystem->listContents($path, FilesystemReader::LIST_DEEP);

        foreach ($list as $metadata) {
            // The timestamp can be absent or false.
            $timestamp = $metadata['timestamp'] ?? false;

            if ($timestamp !== false && $timestamp < $threshold) {
                $this->deleteFileOrFolder($filesystem, $metadata['path']);
            }
        }
    }

    /**
     * @param int $max_age_in_seconds
     *
     * @return void
     */
    public function deleteOldLogs(int $max_age_in_seconds): void
    {
        DB::table('log')
            ->whereIn('log_type', ['error', 'media'])
            ->where('log_time', '<', date('Y-m-d H:i:s', time() - $max_age_in_seconds))
            ->delete();
    }

    /**
     * @param int $max_age_in_seconds
     *
     * @return void
     */
    public function deleteOldSessions(int $max_age_in_seconds): void
    {
        DB::table('session')
            ->where('session_time', '<', date('Y-m-d H:i:s', time() - $max_age_in_seconds))
            ->delete();
    }

    /**
     * Delete a file or folder, if we can.
     *
     * @param FilesystemOperator $filesystem
     * @param string             $path
     *
     * @return bool
     */
    private function deleteFileOrFolder(FilesystemOperator $filesystem, string $path): bool
    {
        try {
            $filesystem->delete($path);
        } catch (FilesystemException | UnableToDeleteFile $ex) {
            try {
                $filesystem->deleteDirectory($path);
            } catch (FilesystemException | UnableToDeleteDirectory $ex) {
                return false;
            }
        }

        return true;
    }
}
