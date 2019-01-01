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

namespace Fisharebest\Webtrees\Services;

use Exception;
use Fisharebest\Webtrees\Database;
use League\Flysystem\Filesystem;

/**
 * Clean up old data, files and folders.
 */
class HousekeepingService
{
    // This is a list of old files and directories, from earlier versions of webtrees.
    // git diff 1.7.9..master --name-status | grep ^D
    const OLD_PATHS = [
        // Removed in 1.0.2
        'language/en.mo',
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
        //WT_ROOT.'modules', // Do not delete - users may have stored custom modules/data here
        'opensearch.php',
        'PEAR.php',
        'pgv_to_wt.php',
        'places',
        //WT_ROOT.'robots.txt', // Do not delete this - it may contain user data
        'serviceClientTest.php',
        'siteconfig.php',
        'SOAP',
        'themes/clouds/mozilla.css',
        'themes/clouds/netscape.css',
        'themes/colors/mozilla.css',
        'themes/colors/netscape.css',
        'themes/fab/mozilla.css',
        'themes/fab/netscape.css',
        'themes/minimal/mozilla.css',
        'themes/minimal/netscape.css',
        'themes/webtrees/mozilla.css',
        'themes/webtrees/netscape.css',
        'themes/webtrees/style_rtl.css',
        'themes/xenea/mozilla.css',
        'themes/xenea/netscape.css',
        'uploadmedia.php',
        'useradmin.php',
        'webservice',
        'wtinfo.php',
        // Removed in 1.1.2
        'treenav.php',
        // Removed in 1.2.0
        'themes/clouds/jquery',
        'themes/colors/jquery',
        'themes/fab/jquery',
        'themes/minimal/jquery',
        'themes/webtrees/jquery',
        'themes/xenea/jquery',
        // Removed in 1.2.2
        'themes/clouds/chrome.css',
        'themes/clouds/opera.css',
        'themes/clouds/print.css',
        'themes/clouds/style_rtl.css',
        'themes/colors/chrome.css',
        'themes/colors/opera.css',
        'themes/colors/print.css',
        'themes/colors/style_rtl.css',
        'themes/fab/chrome.css',
        'themes/fab/opera.css',
        'themes/minimal/chrome.css',
        'themes/minimal/opera.css',
        'themes/minimal/print.css',
        'themes/minimal/style_rtl.css',
        'themes/xenea/chrome.css',
        'themes/xenea/opera.css',
        'themes/xenea/print.css',
        'themes/xenea/style_rtl.css',
        // Removed in 1.2.3
        //WT_ROOT.'modules_v2', // Do not delete - users may have stored custom modules/data here
        // Removed in 1.2.4
        'modules_v3/gedcom_favorites/help_text.php',
        'modules_v3/GEDFact_assistant/_MEDIA/media_3_find.php',
        'modules_v3/GEDFact_assistant/_MEDIA/media_3_search_add.php',
        'modules_v3/GEDFact_assistant/_MEDIA/media_5_input.js',
        'modules_v3/GEDFact_assistant/_MEDIA/media_5_input.php',
        'modules_v3/GEDFact_assistant/_MEDIA/media_7_parse_addLinksTbl.php',
        'modules_v3/GEDFact_assistant/_MEDIA/media_query_1a.php',
        'modules_v3/GEDFact_assistant/_MEDIA/media_query_2a.php',
        'modules_v3/GEDFact_assistant/_MEDIA/media_query_3a.php',
        'modules_v3/lightbox/css/album_page_RTL2.css',
        'modules_v3/lightbox/css/album_page_RTL.css',
        'modules_v3/lightbox/css/album_page_RTL_ff.css',
        'modules_v3/lightbox/css/clearbox_music.css',
        'modules_v3/lightbox/css/clearbox_music_RTL.css',
        'modules_v3/user_favorites/db_schema',
        'modules_v3/user_favorites/help_text.php',
        'search_engine.php',
        'themes/clouds/modules.css',
        'themes/colors/modules.css',
        'themes/fab/modules.css',
        'themes/minimal/modules.css',
        'themes/webtrees/modules.css',
        'themes/xenea/modules.css',
        // Removed in 1.2.5
        'modules_v3/clippings/index.php',
        'modules_v3/googlemap/css/googlemap_style.css',
        'modules_v3/googlemap/css/wt_v3_places_edit.css',
        'modules_v3/googlemap/index.php',
        'modules_v3/lightbox/index.php',
        'modules_v3/recent_changes/help_text.php',
        'modules_v3/todays_events/help_text.php',
        'sidebar.php',
        // Removed in 1.2.6
        'modules_v3/sitemap/admin_index.php',
        'modules_v3/sitemap/help_text.php',
        'modules_v3/tree/css/styles',
        'modules_v3/tree/css/treebottom.gif',
        'modules_v3/tree/css/treebottomleft.gif',
        'modules_v3/tree/css/treebottomright.gif',
        'modules_v3/tree/css/tree.jpg',
        'modules_v3/tree/css/treeleft.gif',
        'modules_v3/tree/css/treeright.gif',
        'modules_v3/tree/css/treetop.gif',
        'modules_v3/tree/css/treetopleft.gif',
        'modules_v3/tree/css/treetopright.gif',
        'modules_v3/tree/css/treeview_print.css',
        'modules_v3/tree/help_text.php',
        'modules_v3/tree/images/print.png',
        // Removed in 1.2.7
        'login_register.php',
        'modules_v3/top10_givnnames/help_text.php',
        'modules_v3/top10_surnames/help_text.php',
        // Removed in 1.3.0
        'admin_site_ipaddress.php',
        'downloadgedcom.php',
        'export_gedcom.php',
        'gedcheck.php',
        'images',
        'modules_v3/googlemap/admin_editconfig.php',
        'modules_v3/googlemap/admin_placecheck.php',
        'modules_v3/googlemap/flags.php',
        'modules_v3/googlemap/images/pedigree_map.gif',
        'modules_v3/googlemap/pedigree_map.php',
        'modules_v3/lightbox/admin_config.php',
        'modules_v3/lightbox/album.php',
        'modules_v3/tree/css/vline.jpg',
        // Removed in 1.3.1
        'imageflush.php',
        'modules_v3/googlemap/wt_v3_pedigree_map.js.php',
        'modules_v3/lightbox/js/tip_balloon_RTL.js',
        // Removed in 1.3.2
        'modules_v3/address_report',
        'modules_v3/lightbox/functions/lb_horiz_sort.php',
        'modules_v3/random_media/help_text.php',
        // Removed in 1.4.0
        'imageview.php',
        'media/MediaInfo.txt',
        'media/thumbs/ThumbsInfo.txt',
        'modules_v3/GEDFact_assistant/css/media_0_inverselink.css',
        'modules_v3/lightbox/help_text.php',
        'modules_v3/lightbox/images/blank.gif',
        'modules_v3/lightbox/images/close_1.gif',
        'modules_v3/lightbox/images/image_add.gif',
        'modules_v3/lightbox/images/image_copy.gif',
        'modules_v3/lightbox/images/image_delete.gif',
        'modules_v3/lightbox/images/image_edit.gif',
        'modules_v3/lightbox/images/image_link.gif',
        'modules_v3/lightbox/images/images.gif',
        'modules_v3/lightbox/images/image_view.gif',
        'modules_v3/lightbox/images/loading.gif',
        'modules_v3/lightbox/images/next.gif',
        'modules_v3/lightbox/images/nextlabel.gif',
        'modules_v3/lightbox/images/norm_2.gif',
        'modules_v3/lightbox/images/overlay.png',
        'modules_v3/lightbox/images/prev.gif',
        'modules_v3/lightbox/images/prevlabel.gif',
        'modules_v3/lightbox/images/private.gif',
        'modules_v3/lightbox/images/slideshow.jpg',
        'modules_v3/lightbox/images/transp80px.gif',
        'modules_v3/lightbox/images/zoom_1.gif',
        'modules_v3/lightbox/js',
        'modules_v3/lightbox/music',
        'modules_v3/lightbox/pic',
        'themes/webtrees/chrome.css',
        // Removed in 1.4.1
        'modules_v3/lightbox/images/image_edit.png',
        'modules_v3/lightbox/images/image_view.png',
        // Removed in 1.4.2
        'modules_v3/lightbox/images/image_view.png',
        'modules_v3/top10_pageviews/help_text.php',
        'themes/clouds/jquery-ui-1.10.0',
        'themes/colors/jquery-ui-1.10.0',
        'themes/fab/jquery-ui-1.10.0',
        'themes/minimal/jquery-ui-1.10.0',
        'themes/webtrees/jquery-ui-1.10.0',
        'themes/xenea/jquery-ui-1.10.0',
        // Removed in 1.5.0
        'modules_v3/GEDFact_assistant/_CENS/census_note_decode.php',
        'modules_v3/GEDFact_assistant/_CENS/census_asst_date.php',
        'modules_v3/googlemap/wt_v3_googlemap.js.php',
        'modules_v3/lightbox/functions/lightbox_print_media.php',
        'modules_v3/upcoming_events/help_text.php',
        'modules_v3/stories/help_text.php',
        'modules_v3/user_messages/help_text.php',
        'themes/clouds/favicon.png',
        'themes/clouds/images',
        'themes/clouds/msie.css',
        'themes/clouds/style.css',
        'themes/colors/css',
        'themes/colors/favicon.png',
        'themes/colors/images',
        'themes/colors/ipad.css',
        'themes/colors/msie.css',
        'themes/fab/favicon.png',
        'themes/fab/images',
        'themes/fab/msie.css',
        'themes/fab/style.css',
        'themes/minimal/favicon.png',
        'themes/minimal/images',
        'themes/minimal/msie.css',
        'themes/minimal/style.css',
        'themes/webtrees/favicon.png',
        'themes/webtrees/images',
        'themes/webtrees/msie.css',
        'themes/webtrees/style.css',
        'themes/xenea/favicon.png',
        'themes/xenea/images',
        'themes/xenea/msie.css',
        'themes/xenea/style.css',
        // Removed in 1.5.1
        'themes/clouds/css-1.5.0',
        'themes/colors/css-1.5.0',
        'themes/fab/css-1.5.0',
        'themes/minimal/css-1.5.0',
        'themes/webtrees/css-1.5.0',
        'themes/xenea/css-1.5.0',
        // Removed in 1.5.2
        'themes/clouds/css-1.5.1',
        'themes/colors/css-1.5.1',
        'themes/fab/css-1.5.1',
        'themes/minimal/css-1.5.1',
        'themes/webtrees/css-1.5.1',
        'themes/xenea/css-1.5.1',
        // Removed in 1.5.3
        'modules_v3/GEDFact_assistant/_CENS/census_asst_help.php',
        'modules_v3/googlemap/admin_places.php',
        'modules_v3/googlemap/defaultconfig.php',
        'modules_v3/googlemap/googlemap.php',
        'modules_v3/googlemap/placehierarchy.php',
        'modules_v3/googlemap/places_edit.php',
        'modules_v3/googlemap/util.js',
        'modules_v3/googlemap/wt_v3_places_edit.js.php',
        'modules_v3/googlemap/wt_v3_places_edit_overlays.js.php',
        'modules_v3/googlemap/wt_v3_street_view.php',
        'readme.html',
        'themes/clouds/css-1.5.2',
        'themes/colors/css-1.5.2',
        'themes/fab/css-1.5.2',
        'themes/minimal/css-1.5.2',
        'themes/webtrees/css-1.5.2',
        'themes/xenea/css-1.5.2',
        // Removed in 1.6.0
        'downloadbackup.php',
        'modules_v3/ckeditor/ckeditor-4.3.2-custom',
        'site-php-version.php',
        'themes/clouds/css-1.5.3',
        'themes/colors/css-1.5.3',
        'themes/fab/css-1.5.3',
        'themes/minimal/css-1.5.3',
        'themes/webtrees/css-1.5.3',
        'themes/xenea/css-1.5.3',
        // Removed in 1.6.2
        'themes/clouds/jquery-ui-1.10.3',
        'themes/colors/css-1.6.0',
        'themes/colors/jquery-ui-1.10.3',
        'themes/fab/css-1.6.0',
        'themes/fab/jquery-ui-1.10.3',
        'themes/minimal/css-1.6.0',
        'themes/minimal/jquery-ui-1.10.3',
        'themes/webtrees/css-1.6.0',
        'themes/webtrees/jquery-ui-1.10.3',
        'themes/xenea/css-1.6.0',
        'themes/xenea/jquery-ui-1.10.3',
        // Removed in 1.7.0
        'admin_site_other.php',
        'js',
        'language/en_GB.mo',
        // Replaced with en-GB.mo
        'language/en_US.mo',
        // Replaced with en-US.mo
        'language/pt_BR.mo',
        // Replaced with pt-BR.mo
        'language/zh_CN.mo',
        // Replaced with zh-Hans.mo
        'language/extra',
        'library',
        'modules_v3/batch_update/admin_batch_update.php',
        'modules_v3/batch_update/plugins',
        'modules_v3/charts/help_text.php',
        'modules_v3/ckeditor/ckeditor-4.4.1-custom',
        'modules_v3/clippings/clippings_ctrl.php',
        'modules_v3/clippings/help_text.php',
        'modules_v3/faq/help_text.php',
        'modules_v3/gedcom_favorites/db_schema',
        'modules_v3/gedcom_news/db_schema',
        'modules_v3/googlemap/db_schema',
        'modules_v3/googlemap/help_text.php',
        'modules_v3/html/help_text.php',
        'modules_v3/logged_in/help_text.php',
        'modules_v3/review_changes/help_text.php',
        'modules_v3/todo/help_text.php',
        'modules_v3/tree/class_treeview.php',
        'modules_v3/user_blog/db_schema',
        'modules_v3/yahrzeit/help_text.php',
        'save.php',
        'themes/clouds/css-1.6.2',
        'themes/clouds/templates',
        'themes/clouds/header.php',
        'themes/clouds/footer.php',
        'themes/colors/css-1.6.2',
        'themes/colors/templates',
        'themes/colors/header.php',
        'themes/colors/footer.php',
        'themes/fab/css-1.6.2',
        'themes/fab/templates',
        'themes/fab/header.php',
        'themes/fab/footer.php',
        'themes/minimal/css-1.6.2',
        'themes/minimal/templates',
        'themes/minimal/header.php',
        'themes/minimal/footer.php',
        'themes/webtrees/css-1.6.2',
        'themes/webtrees/templates',
        'themes/webtrees/header.php',
        'themes/webtrees/footer.php',
        'themes/xenea/css-1.6.2',
        'themes/xenea/templates',
        'themes/xenea/header.php',
        'themes/xenea/footer.php',
        // Removed in 1.7.2
        'assets/js-1.7.0',
        // Removed in 1.7.3
        'modules_v3/GEDFact_assistant/census/date.js',
        'modules_v3/GEDFact_assistant/census/dynamicoptionlist.js',
        // Removed in 1.7.4
        'assets/js-1.7.2',
        'themes/clouds/css-1.7.0',
        'themes/colors/css-1.7.0',
        'themes/fab/css-1.7.0',
        'themes/minimal/css-1.7.0',
        'themes/webtrees/css-1.7.0',
        'themes/xenea/css-1.7.0',
        // Removed in 1.7.5
        'themes/clouds/css-1.7.4',
        'themes/colors/css-1.7.4',
        'themes/fab/css-1.7.4',
        'themes/minimal/css-1.7.4',
        'themes/webtrees/css-1.7.4',
        'themes/xenea/css-1.7.4',
        // Removed in 1.7.7
        'assets/js-1.7.4',
        'modules_v3/googlemap/images/css_sprite_facts.png',
        'modules_v3/googlemap/images/flag_shadow.png',
        'modules_v3/googlemap/images/shadow-left-large.png',
        'modules_v3/googlemap/images/shadow-left-small.png',
        'modules_v3/googlemap/images/shadow-right-large.png',
        'modules_v3/googlemap/images/shadow-right-small.png',
        'modules_v3/googlemap/images/shadow50.png',
        'modules_v3/googlemap/images/transparent-left-large.png',
        'modules_v3/googlemap/images/transparent-left-small.png',
        'modules_v3/googlemap/images/transparent-right-large.png',
        'modules_v3/googlemap/images/transparent-right-small.png',
        // Removed in 1.7.8
        'themes/clouds/css-1.7.5',
        'themes/colors/css-1.7.5',
        'themes/fab/css-1.7.5',
        'themes/minimal/css-1.7.5',
        'themes/webtrees/css-1.7.5',
        'themes/xenea/css-1.7.5',
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
        'lifespan.php',
        'login.php',
        'logout.php',
        'mediafirewall.php',
        'medialist.php',
        'message.php',
        'module.php',
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
        'themes/_administration',
        'themes/_custom',
        'themes/clouds/css-1.7.8',
        'themes/clouds/jquery-ui-1.11.2',
        'themes/colors/css-1.7.8',
        'themes/colors/jquery-ui-1.11.2',
        'themes/fab/css-1.7.8',
        'themes/fab/jquery-ui-1.11.2',
        'themes/minimal/css-1.7.8',
        'themes/minimal/jquery-ui-1.11.2',
        'themes/webtrees/css-1.7.8',
        'themes/webtrees/jquery-ui-1.11.2',
        'themes/xenea/css-1.7.8',
        'themes/xenea/jquery-ui-1.11.2',
        'timeline.php',
    ];

    /**
     * Delete files and folders that belonged to an earlier version of webtrees.
     * Return a list of those that we could not delete.
     *
     * @param Filesystem $filesystem
     *
     * @return array
     */
    public function deleteOldWebtreesFiles(Filesystem $filesystem): array
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
     * @param Filesystem $filesystem
     * @param string     $path_to_cache
     * @param int        $max_age_in_seconds
     *
     * @return void
     */
    public function deleteOldCacheFiles(Filesystem $filesystem, string $path_to_cache, int $max_age_in_seconds)
    {
        $list = $filesystem->listContents($path_to_cache, true);

        foreach ($list as $metadata) {
            if ($metadata['timestamp'] ?? WT_TIMESTAMP < WT_TIMESTAMP - $max_age_in_seconds) {
                $this->deleteFileOrFolder($filesystem, $metadata['path']);
            }
        }
    }

    /**
     * @param int $max_age_in_seconds
     *
     * @return void
     */
    public function deleteOldLogs(int $max_age_in_seconds)
    {
        if (Database::isConnected()) {
            Database::prepare(
                "DELETE FROM `##log` WHERE log_type IN ('error', 'media') AND log_time < FROM_UNIXTIME(:timestamp)"
            )->execute([
                'timestamp' => WT_TIMESTAMP - $max_age_in_seconds
            ]);
        }
    }

    /**
     * @param int $max_age_in_seconds
     *
     * @return void
     */
    public function deleteOldSessions(int $max_age_in_seconds)
    {
        if (Database::isConnected()) {
            Database::prepare(
                "DELETE FROM `##session` WHERE session_time < FROM_UNIXTIME(:timestamp)"
            )->execute([
                'timestamp' => WT_TIMESTAMP - $max_age_in_seconds
            ]);
        }
    }

    /**
     * Delete a file or folder, if we can.
     *
     * @param Filesystem $filesystem
     * @param string     $path
     *
     * @return bool
     */
    private function deleteFileOrFolder(Filesystem $filesystem, string $path): bool
    {
        if ($filesystem->has($path)) {
            try {
                $metadata = $filesystem->getMetadata($path);

                if ($metadata['type'] === 'dir') {
                    $filesystem->deleteDir($path);
                }

                if ($metadata['type'] === 'file') {
                    $filesystem->delete($path);
                }
            } catch (Exception $ex) {
                return false;
            }
        }

        return true;
    }
}
