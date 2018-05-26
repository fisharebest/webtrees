<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use FilesystemIterator;
use Fisharebest\Algorithm\MyersDiff;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Intervention\Image\ImageManager;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Throwable;

/**
 * Controller for the administration pages
 */
class AdminController extends AbstractBaseController {
	// This is a list of old files and directories, from earlier versions of webtrees.
	// git diff 1.7.9..master --name-status | grep ^D
	const OLD_FILES = [
		// Removed in 1.0.2
		WT_ROOT . 'language/en.mo',
		// Removed in 1.0.3
		WT_ROOT . 'themechange.php',
		// Removed in 1.1.0
		WT_ROOT . 'addremotelink.php',
		WT_ROOT . 'addsearchlink.php',
		WT_ROOT . 'client.php',
		WT_ROOT . 'dir_editor.php',
		WT_ROOT . 'editconfig_gedcom.php',
		WT_ROOT . 'editgedcoms.php',
		WT_ROOT . 'edit_merge.php',
		WT_ROOT . 'edit_news.php',
		WT_ROOT . 'genservice.php',
		WT_ROOT . 'logs.php',
		WT_ROOT . 'manageservers.php',
		WT_ROOT . 'media.php',
		WT_ROOT . 'module_admin.php',
		//WT_ROOT.'modules', // Do not delete - users may have stored custom modules/data here
		WT_ROOT . 'opensearch.php',
		WT_ROOT . 'PEAR.php',
		WT_ROOT . 'pgv_to_wt.php',
		WT_ROOT . 'places',
		//WT_ROOT.'robots.txt', // Do not delete this - it may contain user data
		WT_ROOT . 'serviceClientTest.php',
		WT_ROOT . 'siteconfig.php',
		WT_ROOT . 'SOAP',
		WT_ROOT . 'themes/clouds/mozilla.css',
		WT_ROOT . 'themes/clouds/netscape.css',
		WT_ROOT . 'themes/colors/mozilla.css',
		WT_ROOT . 'themes/colors/netscape.css',
		WT_ROOT . 'themes/fab/mozilla.css',
		WT_ROOT . 'themes/fab/netscape.css',
		WT_ROOT . 'themes/minimal/mozilla.css',
		WT_ROOT . 'themes/minimal/netscape.css',
		WT_ROOT . 'themes/webtrees/mozilla.css',
		WT_ROOT . 'themes/webtrees/netscape.css',
		WT_ROOT . 'themes/webtrees/style_rtl.css',
		WT_ROOT . 'themes/xenea/mozilla.css',
		WT_ROOT . 'themes/xenea/netscape.css',
		WT_ROOT . 'uploadmedia.php',
		WT_ROOT . 'useradmin.php',
		WT_ROOT . 'webservice',
		WT_ROOT . 'wtinfo.php',
		// Removed in 1.1.2
		WT_ROOT . 'treenav.php',
		// Removed in 1.2.0
		WT_ROOT . 'themes/clouds/jquery',
		WT_ROOT . 'themes/colors/jquery',
		WT_ROOT . 'themes/fab/jquery',
		WT_ROOT . 'themes/minimal/jquery',
		WT_ROOT . 'themes/webtrees/jquery',
		WT_ROOT . 'themes/xenea/jquery',
		// Removed in 1.2.2
		WT_ROOT . 'themes/clouds/chrome.css',
		WT_ROOT . 'themes/clouds/opera.css',
		WT_ROOT . 'themes/clouds/print.css',
		WT_ROOT . 'themes/clouds/style_rtl.css',
		WT_ROOT . 'themes/colors/chrome.css',
		WT_ROOT . 'themes/colors/opera.css',
		WT_ROOT . 'themes/colors/print.css',
		WT_ROOT . 'themes/colors/style_rtl.css',
		WT_ROOT . 'themes/fab/chrome.css',
		WT_ROOT . 'themes/fab/opera.css',
		WT_ROOT . 'themes/minimal/chrome.css',
		WT_ROOT . 'themes/minimal/opera.css',
		WT_ROOT . 'themes/minimal/print.css',
		WT_ROOT . 'themes/minimal/style_rtl.css',
		WT_ROOT . 'themes/xenea/chrome.css',
		WT_ROOT . 'themes/xenea/opera.css',
		WT_ROOT . 'themes/xenea/print.css',
		WT_ROOT . 'themes/xenea/style_rtl.css',
		// Removed in 1.2.3
		//WT_ROOT.'modules_v2', // Do not delete - users may have stored custom modules/data here
		// Removed in 1.2.4
		WT_ROOT . 'modules_v3/gedcom_favorites/help_text.php',
		WT_ROOT . 'modules_v3/GEDFact_assistant/_MEDIA/media_3_find.php',
		WT_ROOT . 'modules_v3/GEDFact_assistant/_MEDIA/media_3_search_add.php',
		WT_ROOT . 'modules_v3/GEDFact_assistant/_MEDIA/media_5_input.js',
		WT_ROOT . 'modules_v3/GEDFact_assistant/_MEDIA/media_5_input.php',
		WT_ROOT . 'modules_v3/GEDFact_assistant/_MEDIA/media_7_parse_addLinksTbl.php',
		WT_ROOT . 'modules_v3/GEDFact_assistant/_MEDIA/media_query_1a.php',
		WT_ROOT . 'modules_v3/GEDFact_assistant/_MEDIA/media_query_2a.php',
		WT_ROOT . 'modules_v3/GEDFact_assistant/_MEDIA/media_query_3a.php',
		WT_ROOT . 'modules_v3/lightbox/css/album_page_RTL2.css',
		WT_ROOT . 'modules_v3/lightbox/css/album_page_RTL.css',
		WT_ROOT . 'modules_v3/lightbox/css/album_page_RTL_ff.css',
		WT_ROOT . 'modules_v3/lightbox/css/clearbox_music.css',
		WT_ROOT . 'modules_v3/lightbox/css/clearbox_music_RTL.css',
		WT_ROOT . 'modules_v3/user_favorites/db_schema',
		WT_ROOT . 'modules_v3/user_favorites/help_text.php',
		WT_ROOT . 'search_engine.php',
		WT_ROOT . 'themes/clouds/modules.css',
		WT_ROOT . 'themes/colors/modules.css',
		WT_ROOT . 'themes/fab/modules.css',
		WT_ROOT . 'themes/minimal/modules.css',
		WT_ROOT . 'themes/webtrees/modules.css',
		WT_ROOT . 'themes/xenea/modules.css',
		// Removed in 1.2.5
		WT_ROOT . 'modules_v3/clippings/index.php',
		WT_ROOT . 'modules_v3/googlemap/css/googlemap_style.css',
		WT_ROOT . 'modules_v3/googlemap/css/wt_v3_places_edit.css',
		WT_ROOT . 'modules_v3/googlemap/index.php',
		WT_ROOT . 'modules_v3/lightbox/index.php',
		WT_ROOT . 'modules_v3/recent_changes/help_text.php',
		WT_ROOT . 'modules_v3/todays_events/help_text.php',
		WT_ROOT . 'sidebar.php',
		// Removed in 1.2.6
		WT_ROOT . 'modules_v3/sitemap/admin_index.php',
		WT_ROOT . 'modules_v3/sitemap/help_text.php',
		WT_ROOT . 'modules_v3/tree/css/styles',
		WT_ROOT . 'modules_v3/tree/css/treebottom.gif',
		WT_ROOT . 'modules_v3/tree/css/treebottomleft.gif',
		WT_ROOT . 'modules_v3/tree/css/treebottomright.gif',
		WT_ROOT . 'modules_v3/tree/css/tree.jpg',
		WT_ROOT . 'modules_v3/tree/css/treeleft.gif',
		WT_ROOT . 'modules_v3/tree/css/treeright.gif',
		WT_ROOT . 'modules_v3/tree/css/treetop.gif',
		WT_ROOT . 'modules_v3/tree/css/treetopleft.gif',
		WT_ROOT . 'modules_v3/tree/css/treetopright.gif',
		WT_ROOT . 'modules_v3/tree/css/treeview_print.css',
		WT_ROOT . 'modules_v3/tree/help_text.php',
		WT_ROOT . 'modules_v3/tree/images/print.png',
		// Removed in 1.2.7
		WT_ROOT . 'login_register.php',
		WT_ROOT . 'modules_v3/top10_givnnames/help_text.php',
		WT_ROOT . 'modules_v3/top10_surnames/help_text.php',
		// Removed in 1.3.0
		WT_ROOT . 'admin_site_ipaddress.php',
		WT_ROOT . 'downloadgedcom.php',
		WT_ROOT . 'export_gedcom.php',
		WT_ROOT . 'gedcheck.php',
		WT_ROOT . 'images',
		WT_ROOT . 'modules_v3/googlemap/admin_editconfig.php',
		WT_ROOT . 'modules_v3/googlemap/admin_placecheck.php',
		WT_ROOT . 'modules_v3/googlemap/flags.php',
		WT_ROOT . 'modules_v3/googlemap/images/pedigree_map.gif',
		WT_ROOT . 'modules_v3/googlemap/pedigree_map.php',
		WT_ROOT . 'modules_v3/lightbox/admin_config.php',
		WT_ROOT . 'modules_v3/lightbox/album.php',
		WT_ROOT . 'modules_v3/tree/css/vline.jpg',
		// Removed in 1.3.1
		WT_ROOT . 'imageflush.php',
		WT_ROOT . 'modules_v3/googlemap/wt_v3_pedigree_map.js.php',
		WT_ROOT . 'modules_v3/lightbox/js/tip_balloon_RTL.js',
		// Removed in 1.3.2
		WT_ROOT . 'modules_v3/address_report',
		WT_ROOT . 'modules_v3/lightbox/functions/lb_horiz_sort.php',
		WT_ROOT . 'modules_v3/random_media/help_text.php',
		// Removed in 1.4.0
		WT_ROOT . 'imageview.php',
		WT_ROOT . 'media/MediaInfo.txt',
		WT_ROOT . 'media/thumbs/ThumbsInfo.txt',
		WT_ROOT . 'modules_v3/GEDFact_assistant/css/media_0_inverselink.css',
		WT_ROOT . 'modules_v3/lightbox/help_text.php',
		WT_ROOT . 'modules_v3/lightbox/images/blank.gif',
		WT_ROOT . 'modules_v3/lightbox/images/close_1.gif',
		WT_ROOT . 'modules_v3/lightbox/images/image_add.gif',
		WT_ROOT . 'modules_v3/lightbox/images/image_copy.gif',
		WT_ROOT . 'modules_v3/lightbox/images/image_delete.gif',
		WT_ROOT . 'modules_v3/lightbox/images/image_edit.gif',
		WT_ROOT . 'modules_v3/lightbox/images/image_link.gif',
		WT_ROOT . 'modules_v3/lightbox/images/images.gif',
		WT_ROOT . 'modules_v3/lightbox/images/image_view.gif',
		WT_ROOT . 'modules_v3/lightbox/images/loading.gif',
		WT_ROOT . 'modules_v3/lightbox/images/next.gif',
		WT_ROOT . 'modules_v3/lightbox/images/nextlabel.gif',
		WT_ROOT . 'modules_v3/lightbox/images/norm_2.gif',
		WT_ROOT . 'modules_v3/lightbox/images/overlay.png',
		WT_ROOT . 'modules_v3/lightbox/images/prev.gif',
		WT_ROOT . 'modules_v3/lightbox/images/prevlabel.gif',
		WT_ROOT . 'modules_v3/lightbox/images/private.gif',
		WT_ROOT . 'modules_v3/lightbox/images/slideshow.jpg',
		WT_ROOT . 'modules_v3/lightbox/images/transp80px.gif',
		WT_ROOT . 'modules_v3/lightbox/images/zoom_1.gif',
		WT_ROOT . 'modules_v3/lightbox/js',
		WT_ROOT . 'modules_v3/lightbox/music',
		WT_ROOT . 'modules_v3/lightbox/pic',
		WT_ROOT . 'themes/_administration/jquery',
		WT_ROOT . 'themes/webtrees/chrome.css',
		// Removed in 1.4.1
		WT_ROOT . 'modules_v3/lightbox/images/image_edit.png',
		WT_ROOT . 'modules_v3/lightbox/images/image_view.png',
		// Removed in 1.4.2
		WT_ROOT . 'modules_v3/lightbox/images/image_view.png',
		WT_ROOT . 'modules_v3/top10_pageviews/help_text.php',
		WT_ROOT . 'themes/_administration/jquery-ui-1.10.0',
		WT_ROOT . 'themes/clouds/jquery-ui-1.10.0',
		WT_ROOT . 'themes/colors/jquery-ui-1.10.0',
		WT_ROOT . 'themes/fab/jquery-ui-1.10.0',
		WT_ROOT . 'themes/minimal/jquery-ui-1.10.0',
		WT_ROOT . 'themes/webtrees/jquery-ui-1.10.0',
		WT_ROOT . 'themes/xenea/jquery-ui-1.10.0',
		// Removed in 1.5.0
		WT_ROOT . 'modules_v3/GEDFact_assistant/_CENS/census_note_decode.php',
		WT_ROOT . 'modules_v3/GEDFact_assistant/_CENS/census_asst_date.php',
		WT_ROOT . 'modules_v3/googlemap/wt_v3_googlemap.js.php',
		WT_ROOT . 'modules_v3/lightbox/functions/lightbox_print_media.php',
		WT_ROOT . 'modules_v3/upcoming_events/help_text.php',
		WT_ROOT . 'modules_v3/stories/help_text.php',
		WT_ROOT . 'modules_v3/user_messages/help_text.php',
		WT_ROOT . 'themes/_administration/favicon.png',
		WT_ROOT . 'themes/_administration/images',
		WT_ROOT . 'themes/_administration/msie.css',
		WT_ROOT . 'themes/_administration/style.css',
		WT_ROOT . 'themes/clouds/favicon.png',
		WT_ROOT . 'themes/clouds/images',
		WT_ROOT . 'themes/clouds/msie.css',
		WT_ROOT . 'themes/clouds/style.css',
		WT_ROOT . 'themes/colors/css',
		WT_ROOT . 'themes/colors/favicon.png',
		WT_ROOT . 'themes/colors/images',
		WT_ROOT . 'themes/colors/ipad.css',
		WT_ROOT . 'themes/colors/msie.css',
		WT_ROOT . 'themes/fab/favicon.png',
		WT_ROOT . 'themes/fab/images',
		WT_ROOT . 'themes/fab/msie.css',
		WT_ROOT . 'themes/fab/style.css',
		WT_ROOT . 'themes/minimal/favicon.png',
		WT_ROOT . 'themes/minimal/images',
		WT_ROOT . 'themes/minimal/msie.css',
		WT_ROOT . 'themes/minimal/style.css',
		WT_ROOT . 'themes/webtrees/favicon.png',
		WT_ROOT . 'themes/webtrees/images',
		WT_ROOT . 'themes/webtrees/msie.css',
		WT_ROOT . 'themes/webtrees/style.css',
		WT_ROOT . 'themes/xenea/favicon.png',
		WT_ROOT . 'themes/xenea/images',
		WT_ROOT . 'themes/xenea/msie.css',
		WT_ROOT . 'themes/xenea/style.css',
		// Removed in 1.5.1
		WT_ROOT . 'themes/_administration/css-1.5.0',
		WT_ROOT . 'themes/clouds/css-1.5.0',
		WT_ROOT . 'themes/colors/css-1.5.0',
		WT_ROOT . 'themes/fab/css-1.5.0',
		WT_ROOT . 'themes/minimal/css-1.5.0',
		WT_ROOT . 'themes/webtrees/css-1.5.0',
		WT_ROOT . 'themes/xenea/css-1.5.0',
		// Removed in 1.5.2
		WT_ROOT . 'themes/_administration/css-1.5.1',
		WT_ROOT . 'themes/clouds/css-1.5.1',
		WT_ROOT . 'themes/colors/css-1.5.1',
		WT_ROOT . 'themes/fab/css-1.5.1',
		WT_ROOT . 'themes/minimal/css-1.5.1',
		WT_ROOT . 'themes/webtrees/css-1.5.1',
		WT_ROOT . 'themes/xenea/css-1.5.1',
		// Removed in 1.5.3
		WT_ROOT . 'modules_v3/GEDFact_assistant/_CENS/census_asst_help.php',
		WT_ROOT . 'modules_v3/googlemap/admin_places.php',
		WT_ROOT . 'modules_v3/googlemap/defaultconfig.php',
		WT_ROOT . 'modules_v3/googlemap/googlemap.php',
		WT_ROOT . 'modules_v3/googlemap/placehierarchy.php',
		WT_ROOT . 'modules_v3/googlemap/places_edit.php',
		WT_ROOT . 'modules_v3/googlemap/util.js',
		WT_ROOT . 'modules_v3/googlemap/wt_v3_places_edit.js.php',
		WT_ROOT . 'modules_v3/googlemap/wt_v3_places_edit_overlays.js.php',
		WT_ROOT . 'modules_v3/googlemap/wt_v3_street_view.php',
		WT_ROOT . 'readme.html',
		WT_ROOT . 'themes/_administration/css-1.5.2',
		WT_ROOT . 'themes/clouds/css-1.5.2',
		WT_ROOT . 'themes/colors/css-1.5.2',
		WT_ROOT . 'themes/fab/css-1.5.2',
		WT_ROOT . 'themes/minimal/css-1.5.2',
		WT_ROOT . 'themes/webtrees/css-1.5.2',
		WT_ROOT . 'themes/xenea/css-1.5.2',
		// Removed in 1.6.0
		WT_ROOT . 'downloadbackup.php',
		WT_ROOT . 'modules_v3/ckeditor/ckeditor-4.3.2-custom',
		WT_ROOT . 'site-php-version.php',
		WT_ROOT . 'themes/_administration/css-1.5.3',
		WT_ROOT . 'themes/clouds/css-1.5.3',
		WT_ROOT . 'themes/colors/css-1.5.3',
		WT_ROOT . 'themes/fab/css-1.5.3',
		WT_ROOT . 'themes/minimal/css-1.5.3',
		WT_ROOT . 'themes/webtrees/css-1.5.3',
		WT_ROOT . 'themes/xenea/css-1.5.3',
		// Removed in 1.6.2
		WT_ROOT . 'themes/_administration/css-1.6.0',
		WT_ROOT . 'themes/_administration/jquery-ui-1.10.3',
		WT_ROOT . 'themes/clouds/css-1.6.0',
		WT_ROOT . 'themes/clouds/jquery-ui-1.10.3',
		WT_ROOT . 'themes/colors/css-1.6.0',
		WT_ROOT . 'themes/colors/jquery-ui-1.10.3',
		WT_ROOT . 'themes/fab/css-1.6.0',
		WT_ROOT . 'themes/fab/jquery-ui-1.10.3',
		WT_ROOT . 'themes/minimal/css-1.6.0',
		WT_ROOT . 'themes/minimal/jquery-ui-1.10.3',
		WT_ROOT . 'themes/webtrees/css-1.6.0',
		WT_ROOT . 'themes/webtrees/jquery-ui-1.10.3',
		WT_ROOT . 'themes/xenea/css-1.6.0',
		WT_ROOT . 'themes/xenea/jquery-ui-1.10.3',
		WT_ROOT . 'themes/_administration/css-1.6.0',
		WT_ROOT . 'themes/_administration/jquery-ui-1.10.3',
		// Removed in 1.7.0
		WT_ROOT . 'admin_site_other.php',
		WT_ROOT . 'js',
		WT_ROOT . 'language/en_GB.mo',
		// Replaced with en-GB.mo
		WT_ROOT . 'language/en_US.mo',
		// Replaced with en-US.mo
		WT_ROOT . 'language/pt_BR.mo',
		// Replaced with pt-BR.mo
		WT_ROOT . 'language/zh_CN.mo',
		// Replaced with zh-Hans.mo
		WT_ROOT . 'language/extra',
		WT_ROOT . 'library',
		WT_ROOT . 'modules_v3/batch_update/admin_batch_update.php',
		WT_ROOT . 'modules_v3/batch_update/plugins',
		WT_ROOT . 'modules_v3/charts/help_text.php',
		WT_ROOT . 'modules_v3/ckeditor/ckeditor-4.4.1-custom',
		WT_ROOT . 'modules_v3/clippings/clippings_ctrl.php',
		WT_ROOT . 'modules_v3/clippings/help_text.php',
		WT_ROOT . 'modules_v3/faq/help_text.php',
		WT_ROOT . 'modules_v3/gedcom_favorites/db_schema',
		WT_ROOT . 'modules_v3/gedcom_news/db_schema',
		WT_ROOT . 'modules_v3/googlemap/db_schema',
		WT_ROOT . 'modules_v3/googlemap/help_text.php',
		WT_ROOT . 'modules_v3/html/help_text.php',
		WT_ROOT . 'modules_v3/logged_in/help_text.php',
		WT_ROOT . 'modules_v3/review_changes/help_text.php',
		WT_ROOT . 'modules_v3/todo/help_text.php',
		WT_ROOT . 'modules_v3/tree/class_treeview.php',
		WT_ROOT . 'modules_v3/user_blog/db_schema',
		WT_ROOT . 'modules_v3/yahrzeit/help_text.php',
		WT_ROOT . 'save.php',
		WT_ROOT . 'themes/_administration/css-1.6.2',
		WT_ROOT . 'themes/_administration/templates',
		WT_ROOT . 'themes/_administration/header.php',
		WT_ROOT . 'themes/_administration/footer.php',
		WT_ROOT . 'themes/clouds/css-1.6.2',
		WT_ROOT . 'themes/clouds/templates',
		WT_ROOT . 'themes/clouds/header.php',
		WT_ROOT . 'themes/clouds/footer.php',
		WT_ROOT . 'themes/colors/css-1.6.2',
		WT_ROOT . 'themes/colors/templates',
		WT_ROOT . 'themes/colors/header.php',
		WT_ROOT . 'themes/colors/footer.php',
		WT_ROOT . 'themes/fab/css-1.6.2',
		WT_ROOT . 'themes/fab/templates',
		WT_ROOT . 'themes/fab/header.php',
		WT_ROOT . 'themes/fab/footer.php',
		WT_ROOT . 'themes/minimal/css-1.6.2',
		WT_ROOT . 'themes/minimal/templates',
		WT_ROOT . 'themes/minimal/header.php',
		WT_ROOT . 'themes/minimal/footer.php',
		WT_ROOT . 'themes/webtrees/css-1.6.2',
		WT_ROOT . 'themes/webtrees/templates',
		WT_ROOT . 'themes/webtrees/header.php',
		WT_ROOT . 'themes/webtrees/footer.php',
		WT_ROOT . 'themes/xenea/css-1.6.2',
		WT_ROOT . 'themes/xenea/templates',
		WT_ROOT . 'themes/xenea/header.php',
		WT_ROOT . 'themes/xenea/footer.php',
		// Removed in 1.7.2
		WT_ROOT . 'assets/js-1.7.0',
		// Removed in 1.7.3
		WT_ROOT . 'modules_v3/GEDFact_assistant/census/date.js',
		WT_ROOT . 'modules_v3/GEDFact_assistant/census/dynamicoptionlist.js',
		// Removed in 1.7.4
		WT_ROOT . 'assets/js-1.7.2',
		WT_ROOT . 'themes/_administration/css-1.7.0',
		WT_ROOT . 'themes/clouds/css-1.7.0',
		WT_ROOT . 'themes/colors/css-1.7.0',
		WT_ROOT . 'themes/fab/css-1.7.0',
		WT_ROOT . 'themes/minimal/css-1.7.0',
		WT_ROOT . 'themes/webtrees/css-1.7.0',
		WT_ROOT . 'themes/xenea/css-1.7.0',
		// Removed in 1.7.5
		WT_ROOT . 'themes/_administration/css-1.7.4',
		WT_ROOT . 'themes/clouds/css-1.7.4',
		WT_ROOT . 'themes/colors/css-1.7.4',
		WT_ROOT . 'themes/fab/css-1.7.4',
		WT_ROOT . 'themes/minimal/css-1.7.4',
		WT_ROOT . 'themes/webtrees/css-1.7.4',
		WT_ROOT . 'themes/xenea/css-1.7.4',
		// Removed in 1.7.7
		WT_ROOT . 'assets/js-1.7.4',
		WT_ROOT . 'modules_v3/googlemap/images/css_sprite_facts.png',
		WT_ROOT . 'modules_v3/googlemap/images/flag_shadow.png',
		WT_ROOT . 'modules_v3/googlemap/images/shadow-left-large.png',
		WT_ROOT . 'modules_v3/googlemap/images/shadow-left-small.png',
		WT_ROOT . 'modules_v3/googlemap/images/shadow-right-large.png',
		WT_ROOT . 'modules_v3/googlemap/images/shadow-right-small.png',
		WT_ROOT . 'modules_v3/googlemap/images/shadow50.png',
		WT_ROOT . 'modules_v3/googlemap/images/transparent-left-large.png',
		WT_ROOT . 'modules_v3/googlemap/images/transparent-left-small.png',
		WT_ROOT . 'modules_v3/googlemap/images/transparent-right-large.png',
		WT_ROOT . 'modules_v3/googlemap/images/transparent-right-small.png',
		// Removed in 1.7.8
		WT_ROOT . 'themes/clouds/css-1.7.5',
		WT_ROOT . 'themes/colors/css-1.7.5',
		WT_ROOT . 'themes/fab/css-1.7.5',
		WT_ROOT . 'themes/minimal/css-1.7.5',
		WT_ROOT . 'themes/webtrees/css-1.7.5',
		WT_ROOT . 'themes/xenea/css-1.7.5',
		// Removed in 2.0.0
		WT_ROOT . 'addmedia.php',
		WT_ROOT . 'admin_media.php',
		WT_ROOT . 'admin_media_upload.php',
		WT_ROOT . 'admin_module_blocks.php',
		WT_ROOT . 'admin_module_charts.php',
		WT_ROOT . 'admin_module_menus.php',
		WT_ROOT . 'admin_module_reports.php',
		WT_ROOT . 'admin_module_sidebar.php',
		WT_ROOT . 'admin_module_tabs.php',
		WT_ROOT . 'admin_modules.php',
		WT_ROOT . 'admin_site_access.php',
		WT_ROOT . 'admin_site_clean.php',
		WT_ROOT . 'admin_site_info.php',
		WT_ROOT . 'admin_site_logs.php',
		WT_ROOT . 'admin_site_merge.php',
		WT_ROOT . 'admin_site_readme.php',
		WT_ROOT . 'admin_trees_check.php',
		WT_ROOT . 'admin_trees_config.php',
		WT_ROOT . 'admin_trees_download.php',
		WT_ROOT . 'admin_trees_duplicates.php',
		WT_ROOT . 'admin_trees_export.php',
		WT_ROOT . 'admin_trees_manage.php',
		WT_ROOT . 'admin_trees_merge.php',
		WT_ROOT . 'admin_trees_places.php',
		WT_ROOT . 'admin_trees_renumber.php',
		WT_ROOT . 'admin_trees_unconnected.php',
		WT_ROOT . 'admin_users_bulk.php',
		WT_ROOT . 'assets/js-1.7.7',
		WT_ROOT . 'autocomplete.php',
		WT_ROOT . 'block_edit.php',
		WT_ROOT . 'branches.php',
		WT_ROOT . 'compact.php',
		WT_ROOT . 'data/html_purifier_cache',
		WT_ROOT . 'descendancy.php',
		WT_ROOT . 'edituser.php',
		WT_ROOT . 'edit_changes.php',
		WT_ROOT . 'expand_view.php',
		WT_ROOT . 'familybook.php',
		WT_ROOT . 'famlist.php',
		WT_ROOT . 'fanchart.php',
		WT_ROOT . 'help_text.php',
		WT_ROOT . 'hourglass.php',
		WT_ROOT . 'hourglass_ajax.php',
		WT_ROOT . 'import.php',
		WT_ROOT . 'index_edit.php',
		WT_ROOT . 'indilist.php',
		WT_ROOT . 'lifespan.php',
		WT_ROOT . 'login.php',
		WT_ROOT . 'logout.php',
		WT_ROOT . 'medialist.php',
		WT_ROOT . 'message.php',
		WT_ROOT . 'notelist.php',
		WT_ROOT . 'packages',
		WT_ROOT . 'pedigree.php',
		WT_ROOT . 'relationship.php',
		WT_ROOT . 'repolist.php',
		WT_ROOT . 'reportengine.php',
		WT_ROOT . 'search.php',
		WT_ROOT . 'search_advanced.php',
		WT_ROOT . 'site-offline.php',
		WT_ROOT . 'site-unavailable.php',
		WT_ROOT . 'sourcelist.php',
		WT_ROOT . 'statistics.php',
		WT_ROOT . 'statisticsplot.php',
		WT_ROOT . 'themes/_administration/css-1.7.5',
		WT_ROOT . 'themes/_administration/jquery-ui-1.11.2',
		WT_ROOT . 'themes/clouds/css-1.7.8',
		WT_ROOT . 'themes/clouds/jquery-ui-1.11.2',
		WT_ROOT . 'themes/colors/css-1.7.8',
		WT_ROOT . 'themes/colors/jquery-ui-1.11.2',
		WT_ROOT . 'themes/fab/css-1.7.8',
		WT_ROOT . 'themes/fab/jquery-ui-1.11.2',
		WT_ROOT . 'themes/minimal/css-1.7.8',
		WT_ROOT . 'themes/minimal/jquery-ui-1.11.2',
		WT_ROOT . 'themes/webtrees/css-1.7.8',
		WT_ROOT . 'themes/webtrees/jquery-ui-1.11.2',
		WT_ROOT . 'themes/xenea/css-1.7.8',
		WT_ROOT . 'themes/xenea/jquery-ui-1.11.2',
		WT_ROOT . 'timeline.php',
	];

	protected $layout = 'layouts/administration';

	/**
	 * Show the admin page for blocks.
	 *
	 * @return Response
	 */
	public function blocks(): Response {
		return $this->components('block', 'blocks', I18N::translate('Block'), I18N::translate('Blocks'));
	}

	/**
	 * Show the admin page for charts.
	 *
	 * @return Response
	 */
	public function charts(): Response {
		return $this->components('chart', 'charts', I18N::translate('Chart'), I18N::translate('Charts'));
	}

	/**
	 * The control panel shows a summary of the site and links to admin functions.
	 *
	 * @return Response
	 */
	public function controlPanel(): Response {
		return $this->viewResponse('admin/control-panel', [
			'title'           => I18N::translate('Control panel'),
			'server_warnings' => $this->serverWarnings(),
			'latest_version'  => $this->latestVersion(),
			'all_users'       => User::all(),
			'administrators'  => User::administrators(),
			'managers'        => User::managers(),
			'moderators'      => User::moderators(),
			'unapproved'      => User::unapproved(),
			'unverified'      => User::unverified(),
			'all_trees'       => Tree::getAll(),
			'changes'         => $this->totalChanges(),
			'individuals'     => $this->totalIndividuals(),
			'families'        => $this->totalFamilies(),
			'sources'         => $this->totalSources(),
			'media'           => $this->totalMediaObjects(),
			'repositories'    => $this->totalRepositories(),
			'notes'           => $this->totalNotes(),
			'files_to_delete' => $this->filesToDelete(),
			'all_modules'     => Module::getInstalledModules('disabled'),
			'deleted_modules' => $this->deletedModuleNames(),
			'config_modules'  => Module::configurableModules(),
		]);
	}

	/**
	 * Managers see a restricted version of the contol panel.
	 *
	 * @return Response
	 */
	public function controlPanelManager(): Response {
		$all_trees = array_filter(Tree::getAll(), function (Tree $tree) {
			return Auth::isManager($tree);
		});

		return $this->viewResponse('admin/control-panel-manager', [
			'title'        => I18N::translate('Control panel'),
			'all_trees'    => $all_trees,
			'changes'      => $this->totalChanges(),
			'individuals'  => $this->totalIndividuals(),
			'families'     => $this->totalFamilies(),
			'sources'      => $this->totalSources(),
			'media'        => $this->totalMediaObjects(),
			'repositories' => $this->totalRepositories(),
			'notes'        => $this->totalNotes(),
		]);
	}

	/**
	 * Show the edit history for a tree.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function changesLog(Request $request): Response {
		$tree_list = [];
		foreach (Tree::getAll() as $tree) {
			if (Auth::isManager($tree)) {
				$tree_list[$tree->getName()] = $tree->getTitle();
			}
		}

		$user_list = ['' => ''];
		foreach (User::all() as $tmp_user) {
			$user_list[$tmp_user->getUserName()] = $tmp_user->getUserName();
		}

		// First and last change in the database.
		$earliest = Database::prepare("SELECT IFNULL(DATE(MIN(change_time)), CURDATE()) FROM `##change`")->fetchOne();
		$latest   = Database::prepare("SELECT IFNULL(DATE(MAX(change_time)), CURDATE()) FROM `##change`")->fetchOne();

		$action   = $request->get('action');
		$ged      = $request->get('ged');
		$from     = $request->get('from', $earliest);
		$to       = $request->get('to', $latest);
		$type     = $request->get('type', '');
		$oldged   = $request->get('oldged', '');
		$newged   = $request->get('newged', '');
		$xref     = $request->get('xref', '');
		$username = $request->get('username', '');
		$search   = $request->get('search', []);
		$search   = $search['value'] ?? null;

		if (!array_key_exists($ged, $tree_list)) {
			$ged = reset($tree_list);
		}

		$statuses = [
			''         => '',
			'accepted' => /* I18N: the status of an edit accepted/rejected/pending */
				I18N::translate('accepted'),
			'rejected' => /* I18N: the status of an edit accepted/rejected/pending */
				I18N::translate('rejected'),
			'pending'  => /* I18N: the status of an edit accepted/rejected/pending */
				I18N::translate('pending'),
		];

		return $this->viewResponse('admin/changes-log', [
			'action'    => $action,
			'earliest'  => $earliest,
			'from'      => $from,
			'ged'       => $ged,
			'latest'    => $latest,
			'newged'    => $newged,
			'oldged'    => $oldged,
			'search'    => $search,
			'statuses'  => $statuses,
			'title'     => I18N::translate('Changes log'),
			'to'        => $to,
			'tree_list' => $tree_list,
			'type'      => $type,
			'username'  => $username,
			'user_list' => $user_list,
			'xref'      => $xref,
		]);
	}

	/**
	 * Show the edit history for a tree.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function changesLogData(Request $request): Response {
		list($select, , $where, $args1) = $this->changesQuery($request);
		list($order_by, $limit, $args2) = $this->dataTablesPagination($request);

		$rows = Database::prepare(
			$select . $where . $order_by . $limit
		)->execute(array_merge($args1, $args2))->fetchAll();

		// Total filtered/unfiltered rows
		$recordsFiltered = (int) Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
		$recordsTotal    = (int) Database::prepare("SELECT COUNT(*) FROM `##change`")->fetchOne();

		$data      = [];
		$algorithm = new MyersDiff;

		foreach ($rows as $row) {
			$old_lines = preg_split('/[\n]+/', $row->old_gedcom, -1, PREG_SPLIT_NO_EMPTY);
			$new_lines = preg_split('/[\n]+/', $row->new_gedcom, -1, PREG_SPLIT_NO_EMPTY);

			$differences = $algorithm->calculate($old_lines, $new_lines);
			$diff_lines  = [];

			foreach ($differences as $difference) {
				switch ($difference[1]) {
					case MyersDiff::DELETE:
						$diff_lines[] = '<del>' . $difference[0] . '</del>';
						break;
					case MyersDiff::INSERT:
						$diff_lines[] = '<ins>' . $difference[0] . '</ins>';
						break;
					default:
						$diff_lines[] = $difference[0];
				}
			}

			// Only convert valid xrefs to links
			$tree   = Tree::findByName($row->gedcom_name);
			$record = GedcomRecord::getInstance($row->xref, $tree);
			$data[] = [
				$row->change_id,
				$row->change_time,
				I18N::translate($row->status),
				$record ? '<a href="' . e($record->url()) . '">' . $record->getXref() . '</a>' : $row->xref,
				'<div class="gedcom-data" dir="ltr">' .
				preg_replace_callback('/@(' . WT_REGEX_XREF . ')@/',
					function ($match) use ($tree) {
						$record = GedcomRecord::getInstance($match[1], $tree);

						return $record ? '<a href="' . e($record->url()) . '">' . $match[0] . '</a>' : $match[0];
					},
					implode("\n", $diff_lines)
				) .
				'</div>',
				$row->user_name,
				$row->gedcom_name,
			];
		}

		// See http://www.datatables.net/usage/server-side
		return new JsonResponse([
			'draw'            => (int) $request->get('draw'),
			'recordsTotal'    => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data'            => $data,
		]);
	}

	/**
	 * Show the edit history for a tree.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function changesLogDownload(Request $request): Response {
		list($select, , $where, $args) = $this->changesQuery($request);

		$rows = Database::prepare($select . $where)->execute($args)->fetchAll();

		// Convert to CSV
		$rows = array_map(function (stdClass $row) {
			return implode(',', [
				'"' . $row->change_time . '"',
				'"' . $row->status . '"',
				'"' . $row->xref . '"',
				'"' . strtr($row->old_gedcom, '"', '""') . '"',
				'"' . strtr($row->new_gedcom, '"', '""') . '"',
				'"' . strtr($row->user_name, '"', '""') . '"',
				'"' . strtr($row->gedcom_name, '"', '""') . '"',
			]);
		}, $rows);

		$response    = new Response(implode("\n", $rows));
		$disposition = $response->headers->makeDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'changes.csv'
		);
		$response->headers->set('Content-Disposition', $disposition);
		$response->headers->set('Content-Type', 'text/csv; charset=UTF-8');

		return $response;
	}

	/**
	 * Delete the database settings for a deleted module.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function deleteModuleSettings(Request $request): RedirectResponse {
		$module_name = $request->get('module_name');

		Database::prepare(
			"DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) JOIN `##module` USING (module_name) WHERE module_name = :module_name"
		)->execute([
			'module_name' => $module_name,
		]);

		Database::prepare(
			"DELETE `##block` FROM `##block` JOIN `##module` USING (module_name) WHERE module_name = :module_name"
		)->execute([
			'module_name' => $module_name,
		]);

		Database::prepare(
			"DELETE FROM `##module_setting` WHERE module_name = :module_name"
		)->execute([
			'module_name' => $module_name,
		]);

		Database::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name = :module_name"
		)->execute([
			'module_name' => $module_name,
		]);

		Database::prepare(
			"DELETE FROM `##module` WHERE module_name = :module_name"
		)->execute([
			'module_name' => $module_name,
		]);

		FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been deleted.', $module_name), 'success');

		return new RedirectResponse(route('admin-modules'));
	}

	/**
	 * If media objects are wronly linked to top-level records, reattach them
	 * to facts/events.
	 *
	 * @return Response
	 */
	public function fixLevel0Media(): Response {
		return $this->viewResponse('admin/fix-level-0-media', [
			'title' => I18N::translate('Link media objects to facts and events'),
		]);
	}

	/**
	 * Move a link to a media object from a level 0 record to a level 1 record.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function fixLevel0MediaAction(Request $request): Response {
		$fact_id   = $request->get('fact_id');
		$indi_xref = $request->get('indi_xref');
		$obje_xref = $request->get('obje_xref');
		$tree_id   = $request->get('tree_id');

		$tree = Tree::findById($tree_id);
		if ($tree !== null) {
			$individual = Individual::getInstance($indi_xref, $tree);
			$media      = Media::getInstance($obje_xref, $tree);
			if ($individual !== null && $media !== null) {
				foreach ($individual->getFacts() as $fact1) {
					if ($fact1->getFactId() === $fact_id) {
						$individual->updateFact($fact_id, $fact1->getGedcom() . "\n2 OBJE @" . $obje_xref . '@', false);
						foreach ($individual->getFacts('OBJE') as $fact2) {
							if ($fact2->getTarget() === $media) {
								$individual->deleteFact($fact2->getFactId(), false);
							}
						}
						break;
					}
				}
			}
		}

		return new Response;
	}

	/**
	 * If media objects are wronly linked to top-level records, reattach them
	 * to facts/events.
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function fixLevel0MediaData(Request $request): JsonResponse {
		$ignore_facts = ['FAMC', 'FAMS', 'NAME', 'SEX', 'CHAN', 'NOTE', 'OBJE', 'SOUR', 'RESN'];

		$start  = (int) $request->get('start', 0);
		$length = (int) $request->get('length', 20);
		$search = $request->get('search', []);
		$search = $search['value'] ?? '';

		$select1 = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS m.*, i.* from `##media` AS m" .
			" JOIN `##media_file` USING (m_id, m_file)" .
			" JOIN `##link` AS l ON m.m_file = l.l_file AND m.m_id = l.l_to" .
			" JOIN `##individuals` AS i ON l.l_file = i.i_file AND l.l_from = i.i_id" .
			" WHERE i.i_gedcom LIKE CONCAT('%\n1 OBJE @', m.m_id, '@%')";

		$select2 = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS count(*) from `##media` AS m" .
			" JOIN `##media_file` USING (m_id, m_file)" .
			" JOIN `##link` AS l ON m.m_file = l.l_file AND m.m_id = l.l_to" .
			" JOIN `##individuals` AS i ON l.l_file = i.i_file AND l.l_from = i.i_id" .
			" WHERE i.i_gedcom LIKE CONCAT('%\n1 OBJE @', m.m_id, '@%')";

		$where = '';
		$args  = [];

		if ($search !== '') {
			$where           .= " AND (multimedia_file_refn LIKE CONCAT('%', :search1, '%') OR multimedia_file_refn LIKE CONCAT('%', :search2, '%'))";
			$args['search1'] = $search;
			$args['search2'] = $search;
		}

		$limit          = " LIMIT :limit OFFSET :offset";
		$args['limit']  = $length;
		$args['offset'] = $start;

		// Need a consistent order
		$order_by = " ORDER BY i.i_file, i.i_id, m.m_id";

		$data = Database::prepare(
			$select1 . $where . $order_by . $limit
		)->execute(
			$args
		)->fetchAll();

		// Total filtered/unfiltered rows
		$recordsFiltered = (int) Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
		$recordsTotal    = (int) Database::prepare($select2)->fetchOne();

		// Turn each row from the query into a row for the table
		$data = array_map(function (stdClass $datum) use ($ignore_facts) {
			$tree       = Tree::findById($datum->m_file);
			$media      = Media::getInstance($datum->m_id, $tree, $datum->m_gedcom);
			$individual = Individual::getInstance($datum->i_id, $tree, $datum->i_gedcom);

			$facts = $individual->getFacts(null, true);
			$facts = array_filter($facts, function (Fact $fact) use ($ignore_facts) {
				return !$fact->isPendingDeletion() && !in_array($fact->getTag(), $ignore_facts);
			});

			// The link to the media object may have been deleted in a pending change.
			$deleted = true;
			foreach ($individual->getFacts('OBJE') as $fact) {
				if ($fact->getTarget() === $media && !$fact->isPendingDeletion()) {
					$deleted = false;
				}
			}
			if ($deleted) {
				$facts = [];
			}

			$facts = array_map(function (Fact $fact) use ($individual, $media, $tree) {
				return view('admin/fix-level-0-media-action', [
					'fact'       => $fact,
					'individual' => $individual,
					'media'      => $media,
				]);
			}, $facts);

			return [
				$tree->getName(),
				$media->displayImage(100, 100, 'fit', ['class' => 'img-thumbnail']),
				'<a href="' . e($media->url()) . '">' . $media->getFullName() . '</a>',
				'<a href="' . e($individual->url()) . '">' . $individual->getFullName() . '</a>',
				implode(' ', $facts),
			];
		}, $data);

		return new JsonResponse([
			'draw'            => (int) $request->get('draw'),
			'recordsTotal'    => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data'            => $data,
		]);
	}

	/**
	 * Import custom thumbnails from webtres 1.x.
	 *
	 * @return Response
	 */
	public function webtrees1Thumbnails(): Response {
		return $this->viewResponse('admin/webtrees1-thumbnails', [
			'title' => I18N::translate('Import custom thumbnails from webtrees version 1'),
		]);
	}

	/**
	 * Import custom thumbnails from webtres 1.x.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function webtrees1ThumbnailsAction(Request $request): Response {
		$thumbnail = $request->get('thumbnail', '');
		$action    = $request->get('action', '');
		$xrefs     = $request->get('xref', []);
		$geds      = $request->get('ged', []);

		$media_objects = [];

		foreach ($xrefs as $key => $xref) {
			$tree            = Tree::findByName($geds[$key]);
			$media_objects[] = Media::getInstance($xref, $tree);
		}

		$thumbnail = WT_DATA_DIR . $thumbnail;

		switch ($action) {
			case 'delete':
				if (file_exists($thumbnail)) {
					unlink($thumbnail);
				}
				break;

			case 'add':
				$image_size = getimagesize($thumbnail);
				list(, $extension) = explode('/', $image_size['mime']);
				$move_to = dirname(dirname($thumbnail)) . '/' . sha1_file($thumbnail) . '.' . $extension;
				rename($thumbnail, $move_to);

				foreach ($media_objects as $media_object) {
					$prefix = WT_DATA_DIR . $media_object->getTree()->getPreference('MEDIA_DIRECTORY');
					$gedcom = "1 FILE " . substr($move_to, strlen($prefix)) . "\n2 FORM " . $extension;

					if ($media_object->firstImageFile() === null) {
						// The media object doesn't have an image.  Add this as a secondary file.
						$media_object->createFact($gedcom, true);
					} else {
						// The media object already has an image.  Show this custom one in preference.
						$gedcom = '0 @' . $media_object->getXref() . "@ OBJE\n" . $gedcom;
						foreach ($media_object->getFacts() as $fact) {
							$gedcom .= "\n" . $fact->getGedcom();
						}
						$media_object->updateRecord($gedcom, true);
					}

					// Accept the changes, to keep the filesystem in sync with the GEDCOM data.
					FunctionsImport::acceptAllChanges($media_object->getxref(), $media_object->getTree()->getTreeId());
				}
				break;
		}

		return new JsonResponse([]);
	}

	/**
	 * Import custom thumbnails from webtres 1.x.
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function webtrees1ThumbnailsData(Request $request): JsonResponse {
		$start  = (int) $request->get('start', 0);
		$length = (int) $request->get('length', 20);
		$search = $request->get('search', []);
		$search = $search['value'] ?? '';

		// Fetch all thumbnails
		$thumbnails = [];

		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(WT_DATA_DIR, FilesystemIterator::FOLLOW_SYMLINKS));

		foreach ($iterator as $iteration) {
			if ($iteration->isFile() && strpos($iteration->getPathname(), '/thumbs/') !== false) {
				$thumbnails[] = $iteration->getPathname();
			}
		}

		$recordsTotal = count($thumbnails);

		if ($search !== '') {
			$thumbnails = array_filter($thumbnails, function (string $thumbnail) use ($search) {
				return stripos($thumbnail, $search) !== false;
			});
		}

		$recordsFiltered = count($thumbnails);

		$thumbnails = array_slice($thumbnails, $start, $length);

		// Turn each filename into a row for the table
		$data = array_map(function (string $thumbnail) {
			$original = $this->findOriginalFileFromThumbnail($thumbnail);

			$original_url  = route('unused-media-thumbnail', [
				'folder' => dirname($original),
				'file'   => basename($original),
				'w'      => 100,
				'h'      => 100,
			]);
			$thumbnail_url = route('unused-media-thumbnail', [
				'folder' => dirname($thumbnail),
				'file'   => basename($thumbnail),
				'w'      => 100,
				'h'      => 100,
			]);

			$difference = $this->imageDiff($thumbnail, $original);

			$original_path  = substr($original, strlen(WT_DATA_DIR));
			$thumbnail_path = substr($thumbnail, strlen(WT_DATA_DIR));

			$media = $this->findMediaObjectsForMediaFile($original_path);

			$media_links = array_map(function (Media $media) {
				return '<a href="' . e($media->url()) . '">' . $media->getFullName() . '</a>';
			}, $media);

			$media_links = implode('<br>', $media_links);

			$action = view('admin/webtrees1-thumbnails-form', [
				'difference' => $difference,
				'media'      => $media,
				'thumbnail'  => $thumbnail_path,
			]);

			return [
				'<img src="' . e($thumbnail_url) . '" title="' . e($thumbnail_path) . '">',
				'<img src="' . e($original_url) . '" title="' . e($original_path) . '">',
				$media_links,
				I18N::percentage($difference / 100.0, 0),
				$action,
			];
		}, $thumbnails);

		return new JsonResponse([
			'draw'            => (int) $request->get('draw'),
			'recordsTotal'    => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data'            => $data,
		]);
	}

	/**
	 * Merge two genealogy records.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function mergeRecords(Request $request): Response {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');
		$title = I18N::translate('Merge records') . ' — ' . e($tree->getTitle());

		$xref1 = $request->get('xref1', '');
		$xref2 = $request->get('xref2', '');

		$record1 = GedcomRecord::getInstance($xref1, $tree);
		$record2 = GedcomRecord::getInstance($xref2, $tree);

		if ($xref1 !== '' && $record1 === null) {
			$xref1 = '';
		}

		if ($xref2 !== '' && $record2 === null) {
			$xref2 = '';
		}

		if ($record1 === $record2) {
			$xref2 = '';
		}

		if ($record1 !== null && $record2 && $record1::RECORD_TYPE !== $record2::RECORD_TYPE) {
			$xref2 = '';
		}

		if ($xref1 === '' || $xref2 === '') {
			return $this->viewResponse('admin/merge-records-step-1', [
				'individual1' => $record1 instanceof Individual ? $record1 : null,
				'individual2' => $record2 instanceof Individual ? $record2 : null,
				'family1'     => $record1 instanceof Family ? $record1 : null,
				'family2'     => $record2 instanceof Family ? $record2 : null,
				'source1'     => $record1 instanceof Source ? $record1 : null,
				'source2'     => $record2 instanceof Source ? $record2 : null,
				'repository1' => $record1 instanceof Repository ? $record1 : null,
				'repository2' => $record2 instanceof Repository ? $record2 : null,
				'media1'      => $record1 instanceof Media ? $record1 : null,
				'media2'      => $record2 instanceof Media ? $record2 : null,
				'note1'       => $record1 instanceof Note ? $record1 : null,
				'note2'       => $record2 instanceof Note ? $record2 : null,
				'title'       => $title,
			]);
		}

		// Facts found both records
		$facts = [];
		// Facts found in only one record
		$facts1 = [];
		$facts2 = [];

		foreach ($record1->getFacts() as $fact) {
			if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
				$facts1[$fact->getFactId()] = $fact;
			}
		}

		foreach ($record2->getFacts() as $fact) {
			if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
				$facts2[$fact->getFactId()] = $fact;
			}
		}

		foreach ($facts1 as $id1 => $fact1) {
			foreach ($facts2 as $id2 => $fact2) {
				if ($fact1->getFactId() === $fact2->getFactId()) {
					$facts[] = $fact1;
					unset($facts1[$id1]);
					unset($facts2[$id2]);
				}
			}
		}

		return $this->viewResponse('admin/merge-records-step-2', [
			'facts'   => $facts,
			'facts1'  => $facts1,
			'facts2'  => $facts2,
			'record1' => $record1,
			'record2' => $record2,
			'title'   => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function mergeRecordsAction(Request $request): Response {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');
		$xref1 = $request->get('xref1', '');
		$xref2 = $request->get('xref2', '');
		$keep1 = $request->get('keep1', []);
		$keep2 = $request->get('keep2', []);

		$record1 = GedcomRecord::getInstance($xref1, $tree);
		$record2 = GedcomRecord::getInstance($xref2, $tree);

		// Facts found both records
		$facts = [];
		// Facts found in only one record
		$facts1 = [];
		$facts2 = [];

		foreach ($record1->getFacts() as $fact) {
			if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
				$facts1[$fact->getFactId()] = $fact;
			}
		}

		foreach ($record2->getFacts() as $fact) {
			if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
				$facts2[$fact->getFactId()] = $fact;
			}
		}

		// If we are not auto-accepting, then we can show a link to the pending deletion
		if (Auth::user()->getPreference('auto_accept')) {
			$record2_name = $record2->getFullName();
		} else {
			$record2_name = '<a class="alert-link" href="' . e($record2->url()) . '">' . $record2->getFullName() . '</a>';
		}

		// Update records that link to the one we will be removing.
		$ids = FunctionsDb::fetchAllLinks($xref2, $tree->getTreeId());

		foreach ($ids as $id) {
			$record = GedcomRecord::getInstance($id, $tree);
			if (!$record->isPendingDeletion()) {
				FlashMessages::addMessage(I18N::translate(
				/* I18N: The placeholders are the names of individuals, sources, etc. */
					'The link from “%1$s” to “%2$s” has been updated.',
					'<a class="alert-link" href="' . e($record->url()) . '">' . $record->getFullName() . '</a>',
					$record2_name
				), 'info');
				$gedcom = str_replace('@' . $xref2 . '@', '@' . $xref1 . '@', $record->getGedcom());
				$gedcom = preg_replace(
					'/(\n1.*@.+@.*(?:(?:\n[2-9].*)*))((?:\n1.*(?:\n[2-9].*)*)*\1)/',
					'$2',
					$gedcom
				);
				$record->updateRecord($gedcom, true);
			}
		}

		// Update any linked user-accounts
		Database::prepare(
			"UPDATE `##user_gedcom_setting`" .
			" SET setting_value=?" .
			" WHERE gedcom_id=? AND setting_name='gedcomid' AND setting_value=?"
		)->execute([$xref2, $tree->getTreeId(), $xref1]);

		// Merge hit counters
		$hits = Database::prepare(
			"SELECT page_name, SUM(page_count)" .
			" FROM `##hit_counter`" .
			" WHERE gedcom_id=? AND page_parameter IN (?, ?)" .
			" GROUP BY page_name"
		)->execute([$tree->getTreeId(), $xref1, $xref2])->fetchAssoc();

		foreach ($hits as $page_name => $page_count) {
			Database::prepare(
				"UPDATE `##hit_counter` SET page_count=?" .
				" WHERE gedcom_id=? AND page_name=? AND page_parameter=?"
			)->execute([$page_count, $tree->getTreeId(), $page_name, $xref1]);
		}

		Database::prepare(
			"DELETE FROM `##hit_counter`" .
			" WHERE gedcom_id=? AND page_parameter=?"
		)->execute([$tree->getTreeId(), $xref2]);

		$gedcom = '0 @' . $record1->getXref() . '@ ' . $record1::RECORD_TYPE;
		foreach ($facts as $fact_id => $fact) {
			$gedcom .= "\n" . $fact->getGedcom();
		}
		foreach ($facts1 as $fact_id => $fact) {
			if (in_array($fact_id, $keep1)) {
				$gedcom .= "\n" . $fact->getGedcom();
			}
		}
		foreach ($facts2 as $fact_id => $fact) {
			if (in_array($fact_id, $keep2)) {
				$gedcom .= "\n" . $fact->getGedcom();
			}
		}

		$record1->updateRecord($gedcom, true);
		$record2->deleteRecord();
		FunctionsDb::updateFavorites($xref2, $xref1, $tree);
		FlashMessages::addMessage(I18N::translate(
		/* I18N: Records are individuals, sources, etc. */
			'The records “%1$s” and “%2$s” have been merged.',
			'<a class="alert-link" href="' . e($record1->url()) . '">' . $record1->getFullName() . '</a>',
			$record2_name
		), 'success');

		return new RedirectResponse(route('merge-records', ['ged' => $tree->getName()]));
	}

	/**
	 * Show the administrator a list of modules.
	 *
	 * @return Response
	 */
	public function modules(): Response {
		$module_status = Database::prepare("SELECT module_name, status FROM `##module`")->fetchAssoc();

		return $this->viewResponse('admin/modules', [
			'title'             => I18N::translate('Module administration'),
			'modules'           => Module::getInstalledModules('disabled'),
			'module_status'     => $module_status,
			'deleted_modules'   => $this->deletedModuleNames(),
			'core_module_names' => Module::getCoreModuleNames(),
		]);
	}

	/**
	 * Show the admin page for menus.
	 *
	 * @return Response
	 */
	public function menus(): Response {
		return $this->components('menu', 'menus', I18N::translate('Menu'), I18N::translate('Menus'));
	}

	/**
	 * Show the admin page for reports.
	 *
	 * @return Response
	 */
	public function reports(): Response {
		return $this->components('report', 'reports', I18N::translate('Report'), I18N::translate('Reports'));
	}

	/**
	 * Show the admin page for sidebars.
	 *
	 * @return Response
	 */
	public function sidebars(): Response {
		return $this->components('sidebar', 'sidebars', I18N::translate('Sidebar'), I18N::translate('Sidebars'));
	}

	/**
	 * Show the admin page for tabs.
	 *
	 * @return Response
	 */
	public function tabs(): Response {
		return $this->components('tab', 'tabs', I18N::translate('Tab'), I18N::translate('Tabs'));
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function treePrivacyEdit(Request $request): Response {
		/** @var Tree $tree */
		$tree                 = $request->attributes->get('tree');
		$title                = e($tree->getName()) . ' — ' . I18N::translate('Privacy');
		$all_tags             = $this->tagsForPrivacy($tree);
		$privacy_constants    = $this->privacyConstants();
		$privacy_restrictions = $this->privacyRestrictions($tree);

		return $this->viewResponse('admin/trees-privacy', [
			'all_tags'             => $all_tags,
			'count_trees'          => count(Tree::getAll()),
			'privacy_constants'    => $privacy_constants,
			'privacy_restrictions' => $privacy_restrictions,
			'title'                => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function treePrivacyUpdate(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		foreach ((array) $request->get('delete') as $default_resn_id) {
			Database::prepare(
				"DELETE FROM `##default_resn` WHERE default_resn_id = :default_resn_id"
			)->execute([
				'default_resn_id' => $default_resn_id,
			]);
		}

		$xrefs     = (array) $request->get('xref');
		$tag_types = (array) $request->get('tag_type');
		$resns     = (array) $request->get('resn');

		foreach ($xrefs as $n => $xref) {
			$tag_type = (string) $tag_types[$n];
			$resn     = (string) $resns[$n];

			if ($tag_type !== '' || $xref !== '') {
				// Delete any existing data
				if ($xref === '') {
					Database::prepare(
						"DELETE FROM `##default_resn` WHERE gedcom_id = :tree_id AND tag_type = :tag_type AND xref IS NULL"
					)->execute([
						'tree_id'  => $tree->getTreeId(),
						'tag_type' => $tag_type,
					]);
				}
				if ($tag_type === '') {
					Database::prepare(
						"DELETE FROM `##default_resn` WHERE gedcom_id = ? AND xref = ? AND tag_type IS NULL"
					)->execute([
						'tree_id' => $tree->getTreeId(),
						'xref'    => $xref,
					]);
				}

				// Add (or update) the new data
				Database::prepare(
					"REPLACE INTO `##default_resn` (gedcom_id, xref, tag_type, resn)" .
					" VALUES (:tree_id, NULLIF(:xref, ''), NULLIF(:tag_type, ''), :resn)"
				)->execute([
					'tree_id'  => $tree->getTreeId(),
					'xref'     => $xref,
					'tag_type' => $tag_type,
					'resn'     => $resn,
				]);
			}
		}

		$tree->setPreference('HIDE_LIVE_PEOPLE', $request->get('HIDE_LIVE_PEOPLE'));
		$tree->setPreference('KEEP_ALIVE_YEARS_BIRTH', $request->get('KEEP_ALIVE_YEARS_BIRTH', '0'));
		$tree->setPreference('KEEP_ALIVE_YEARS_DEATH', $request->get('KEEP_ALIVE_YEARS_DEATH', '0'));
		$tree->setPreference('MAX_ALIVE_AGE', $request->get('MAX_ALIVE_AGE', '100'));
		$tree->setPreference('REQUIRE_AUTHENTICATION', $request->get('REQUIRE_AUTHENTICATION'));
		$tree->setPreference('SHOW_DEAD_PEOPLE', $request->get('SHOW_DEAD_PEOPLE'));
		$tree->setPreference('SHOW_LIVING_NAMES', $request->get('SHOW_LIVING_NAMES'));
		$tree->setPreference('SHOW_PRIVATE_RELATIONSHIPS', $request->get('SHOW_PRIVATE_RELATIONSHIPS'));

		FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', e($tree->getTitle()), 'success'));

		// Coming soon...
		if ((bool) $request->get('all_trees')) {
			FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.', e($tree->getTitle())), 'success');
		}
		if ((bool) $request->get('new_trees')) {
			FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.', e($tree->getTitle())), 'success');
		}


		return new RedirectResponse(route('admin-trees', ['ged' => $tree->getName()]));
	}

	/**
	 * Update the access levels of the modules.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function updateModuleAccess(Request $request): RedirectResponse {
		$component = $request->get('component');
		$modules   = Module::getAllModulesByComponent($component);

		foreach ($modules as $module) {
			foreach (Tree::getAll() as $tree) {
				$key          = 'access-' . $module->getName() . '-' . $tree->getTreeId();
				$access_level = (int) $request->get($key, $module->defaultAccessLevel());

				Database::prepare("REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" . " VALUES (:module_name, :tree_id, :component, :access_level)")->execute([
					'module_name'  => $module->getName(),
					'tree_id'      => $tree->getTreeId(),
					'component'    => $component,
					'access_level' => $access_level,
				]);
			}
		}

		return new RedirectResponse(route('admin-' . $component . 's'));
	}

	/**
	 * Update the enabled/disabled status of the modules.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function updateModuleStatus(Request $request): RedirectResponse {
		$modules       = Module::getInstalledModules('disabled');
		$module_status = Database::prepare("SELECT module_name, status FROM `##module`")->fetchAssoc();

		foreach ($modules as $module) {
			$new_status = (bool) $request->get('status-' . $module->getName()) ? 'enabled' : 'disabled';
			$old_status = $module_status[$module->getName()];

			if ($new_status !== $old_status) {
				Database::prepare("UPDATE `##module` SET status = :status WHERE module_name = :module_name")->execute([
					'status'      => $new_status,
					'module_name' => $module->getName(),
				]);

				if ($new_status === 'enabled') {
					FlashMessages::addMessage(I18N::translate('The module “%s” has been enabled.', $module->getTitle()), 'success');
				} else {
					FlashMessages::addMessage(I18N::translate('The module “%s” has been disabled.', $module->getTitle()), 'success');
				}
			}
		}

		return new RedirectResponse(route('admin-modules'));
	}

	/**
	 * Create a response object from a view.
	 *
	 * @param string  $name
	 * @param mixed[] $data
	 * @param int     $status
	 *
	 * @return Response
	 */
	protected function viewResponse($name, $data, $status = Response::HTTP_OK): Response {
		$html = view($this->layout, [
			'content' => view($name, $data),
			'title'   => strip_tags($data['title']),
		]);

		return new Response($html, $status);
	}

	/**
	 * Generate a WHERE clause for filtering the changes log.
	 *
	 * @param Request $request
	 *
	 * @return array
	 *
	 */
	private function changesQuery(Request $request): array {
		$from     = $request->get('from', '');
		$to       = $request->get('to', '');
		$type     = $request->get('type', '');
		$oldged   = $request->get('oldged', '');
		$newged   = $request->get('newged', '');
		$xref     = $request->get('xref', '');
		$username = $request->get('username', '');
		$ged      = $request->get('ged', '');
		$search   = $request->get('search', '');
		$search   = $search['value'] ?? '';

		$where = ' WHERE 1';
		$args  = [];
		if ($search !== '') {
			$where            .= " AND (old_gedcom LIKE CONCAT('%', :search_1, '%') OR new_gedcom LIKE CONCAT('%', :search_2, '%'))";
			$args['search_1'] = $search;
			$args['search_2'] = $search;
		}
		if ($from !== '') {
			$where        .= " AND change_time >= :from";
			$args['from'] = $from;
		}
		if ($to !== '') {
			$where      .= ' AND change_time < TIMESTAMPADD(DAY, 1 , :to)'; // before end of the day
			$args['to'] = $to;
		}
		if ($type !== '') {
			$where          .= ' AND status = :status';
			$args['status'] = $type;
		}
		if ($oldged !== '') {
			$where           .= " AND old_gedcom LIKE CONCAT('%', :old_ged, '%')";
			$args['old_ged'] = $oldged;
		}
		if ($newged !== '') {
			$where           .= " AND new_gedcom LIKE CONCAT('%', :new_ged, '%')";
			$args['new_ged'] = $newged;
		}
		if ($xref !== '') {
			$where        .= " AND xref = :xref";
			$args['xref'] = $xref;
		}
		if ($username !== '') {
			$where        .= " AND user_name LIKE CONCAT('%', :user, '%')";
			$args['user'] = $username;
		}
		if ($ged !== '') {
			$where       .= " AND gedcom_name LIKE CONCAT('%', :ged, '%')";
			$args['ged'] = $ged;
		}

		$select = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS change_id, change_time, status, xref, old_gedcom, new_gedcom, IFNULL(user_name, '<none>') AS user_name, gedcom_name FROM `##change`";
		$delete = 'DELETE `##change` FROM `##change`';

		$join = ' LEFT JOIN `##user` USING (user_id) JOIN `##gedcom` USING (gedcom_id)';

		return [$select . $join, $delete . $join, $where, $args];
	}

	/**
	 * Show the admin page for blocks, charts, menus, reports, sidebars, tabs, etc..
	 *
	 * @param string $component
	 * @param string $route
	 * @param string $component_title
	 * @param string $title
	 *
	 * @return Response
	 */
	private function components($component, $route, $component_title, $title): Response {
		return $this->viewResponse('admin/module-components', [
			'component'       => $component,
			'component_title' => $component_title,
			'modules'         => Module::getAllModulesByComponent($component),
			'title'           => $title,
			'route'           => $route,
		]);
	}

	/**
	 * Conver request parameters into paging/sorting for datatables
	 *
	 * @param $request
	 *
	 * @return array
	 */
	private function dataTablesPagination(Request $request): array {
		$start  = (int) $request->get('start', '0');
		$length = (int) $request->get('length', '0');
		$order  = $request->get('order', []);
		$args   = [];

		if (is_array($order) && !empty($order)) {
			$order_by = ' ORDER BY ';
			foreach ($order as $key => $value) {
				if ($key > 0) {
					$order_by .= ',';
				}
				// Columns in datatables are numbered from zero.
				// Columns in MySQL are numbered starting with one.
				switch ($value['dir']) {
					case 'asc':
						$order_by .= (1 + $value['column']) . ' ASC ';
						break;
					case 'desc':
						$order_by .= (1 + $value['column']) . ' DESC ';
						break;
				}
			}
		} else {
			$order_by = '';
		}

		if ($length) {
			Auth::user()->setPreference('admin_site_change_page_size', $length);
			$limit          = ' LIMIT :limit OFFSET :offset';
			$args['limit']  = $length;
			$args['offset'] = $start;
		} else {
			$limit = "";
		}

		return [$order_by, $limit, $args];
	}

	/**
	 * Generate a list of module names which exist in the database but not on disk.
	 *
	 * @return string[]
	 */
	private function deletedModuleNames() {
		$database_modules = Database::prepare("SELECT module_name FROM `##module`")->fetchOneColumn();
		$disk_modules     = Module::getInstalledModules('disabled');

		return array_diff($database_modules, array_keys($disk_modules));
	}

	/**
	 * A list of old files that need to be deleted.
	 *
	 * @return string[]
	 */
	private function filesToDelete() {
		$files_to_delete = [];
		foreach (self::OLD_FILES as $file) {
			// Delete the file, if we can.
			if (file_exists($file) && !File::delete($file)) {
				$files_to_delete[] = $file;
			}
		}

		return $files_to_delete;
	}

	/**
	 * Find the media object that uses a particular media file.
	 *
	 * @param string $file
	 *
	 * @return Media[]
	 */
	private function findMediaObjectsForMediaFile(string $file): array {
		$rows = Database::prepare(
			"SELECT DISTINCT m.*" .
			" FROM  `##media` as m" .
			" JOIN  `##media_file` USING (m_file, m_id)" .
			" JOIN  `##gedcom_setting` ON (m_file = gedcom_id AND setting_name = 'MEDIA_DIRECTORY')" .
			" WHERE CONCAT(setting_value, multimedia_file_refn) = :file"
		)->execute([
			'file' => $file,
		])->fetchAll();

		$media = [];

		foreach ($rows as $row) {
			$tree    = Tree::findById($row->m_file);
			$media[] = Media::getInstance($row->m_id, $tree, $row->m_gedcom);
		}

		return array_filter($media);
	}

	/**
	 * Find the original image that corresponds to a (webtrees 1.x) thumbnail file.
	 *
	 * @param string $thumbnail
	 *
	 * @return string
	 */
	private function findOriginalFileFromThumbnail(string $thumbnail): string {
		// First option - a file with the same name
		$original = str_replace('/thumbs/', '/', $thumbnail);

		// Second option - a .PNG thumbnail for some other image type
		if (substr_compare($original, '.png', -4, 4) === 0) {
			$pattern = substr($original, 0, -3) . '*';
			$matches = glob($pattern);
			if (!empty($matches) && is_file($matches[0])) {
				$original = $matches[0];
			}
		}

		return $original;
	}

	/**
	 * Compare two images, and return a quantified difference.
	 *
	 * 0 (different) ... 100 (same)
	 *
	 * @param string $thumbanil
	 * @param string $original
	 *
	 * @return int
	 */
	private function imageDiff($thumbanil, $original): int {
		try {
			if (getimagesize($thumbanil) === false) {
				return 100;
			}
		} catch (Throwable $ex) {
			// If the first file is not an image then similarity is unimportant.
			// Response with an exact match, so the GUI will recommend deleting it.
			return 100;
		}

		try {
			if (getimagesize($original) === false) {
				return 0;
			}
		} catch (Throwable $ex) {
			// If the first file is not an image then the thumbnail .
			// Response with an exact mismatch, so the GUI will recommend importing it.
			return 0;
		}

		$pixels1 = $this->scaledImagePixels($thumbanil);
		$pixels2 = $this->scaledImagePixels($original);

		$max_difference = 0;

		foreach ($pixels1 as $x => $row) {
			foreach ($row as $y => $pixel) {
				$max_difference = max($max_difference, abs($pixel - $pixels2[$x][$y]));
			}
		}

		// The maximum difference is 255 (black versus white).
		return 100 - (int) ($max_difference * 100 / 255);
	}

	/**
	 * Scale an image to 10x10 and read the individual pixels.
	 *
	 * This is a slow operation, add we will do it many times on
	 * the "import wetbrees 1 thumbnails" page so cache the results.
	 *
	 * @param string $path
	 *
	 * @return int[][]
	 */
	private function scaledImagePixels($path): array {
		$size       = 10;
		$sha1       = sha1_file($path);
		$cache_file = WT_DATA_DIR . 'cache/' . $sha1 . '.php';

		if (file_exists($cache_file)) {
			return include $cache_file;
		}

		$manager = new ImageManager;
		$image   = $manager->make($path)->resize($size, $size);

		$pixels = [];
		for ($x = 0; $x < $size; ++$x) {
			$pixels[$x] = [];
			for ($y = 0; $y < $size; ++$y) {
				$pixel          = $image->pickColor($x, $y);
				$pixels[$x][$y] = (int) (($pixel[0] + $pixel[1] + $pixel[2]) / 3);
			}
		}

		file_put_contents($cache_file, '<?php return ' . var_export($pixels, true) . ';');

		return $pixels;
	}

	/**
	 * Look for the latest version of webtrees.
	 *
	 * @return string
	 */
	private function latestVersion() {
		$latest_version_txt = Functions::fetchLatestVersion();
		if (preg_match('/^[0-9.]+\|[0-9.]+\|/', $latest_version_txt)) {
			list($latest_version) = explode('|', $latest_version_txt);
		} else {
			// Cannot determine the latest version.
			$latest_version = '';
		}

		return $latest_version;
	}

	/**
	 * Names of our privacy levels
	 *
	 * @return array
	 */
	private function privacyConstants(): array {
		return [
			'none'         => I18N::translate('Show to visitors'),
			'privacy'      => I18N::translate('Show to members'),
			'confidential' => I18N::translate('Show to managers'),
			'hidden'       => I18N::translate('Hide from everyone'),
		];
	}

	/**
	 * The current privacy restrictions for a tree.
	 *
	 * @param Tree $tree
	 *
	 * @return array
	 */
	private function privacyRestrictions(Tree $tree): array {
		$restrictions = Database::prepare(
			"SELECT default_resn_id, tag_type, xref, resn" .
			" FROM `##default_resn`" .
			" LEFT JOIN `##name` ON (gedcom_id = n_file AND xref = n_id AND n_num = 0)" .
			" WHERE gedcom_id = :tree_id"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		foreach ($restrictions as $resn) {
			$resn->record = GedcomRecord::getInstance($resn->xref, $tree);
			if ($resn->tag_type) {
				$resn->tag_label = GedcomTag::getLabel($resn->tag_type);
			} else {
				$resn->tag_label = '';
			}
		}

		usort($restrictions, function (stdClass $x, stdClass $y) {
			return I18N::strcasecmp($x->tag_label, $y->tag_label);
		});

		return $restrictions;
	}

	/**
	 * Generate a list of potential problems with the server.
	 *
	 * @return string[]
	 */
	private function serverWarnings() {
		$php_support_url   = 'https://secure.php.net/supported-versions.php';
		$version_parts     = explode('.', PHP_VERSION);
		$php_minor_version = $version_parts[0] . $version_parts[1];
		$today             = date('Y-m-d');
		$warnings          = [];

		if ($php_minor_version === '70' && $today >= '2017-12-03' || $php_minor_version === '71' && $today >= '2019-12-01') {
			$warnings[] = I18N::translate('Your web server is using PHP version %s, which is no longer receiving security updates. You should upgrade to a later version as soon as possible.', PHP_VERSION) . ' <a href="' . $php_support_url . '">' . $php_support_url . '</a>';
		} elseif ($php_minor_version === '70' && $today >= '2017-12-03' || $php_minor_version === '71' && $today >= '2018-12-01') {
			$warnings[] = I18N::translate('Your web server is using PHP version %s, which is no longer maintained. You should upgrade to a later version.', PHP_VERSION) . ' <a href="' . $php_support_url . '">' . $php_support_url . '</a>';
		}

		return $warnings;
	}

	/**
	 * Generate a list of potential problems with the server.
	 *
	 * @param Tree $tree
	 *
	 * @return string[]
	 */
	private function tagsForPrivacy(Tree $tree): array {
		$tags = array_unique(array_merge(
			explode(',', $tree->getPreference('INDI_FACTS_ADD')),
			explode(',', $tree->getPreference('INDI_FACTS_UNIQUE')),
			explode(',', $tree->getPreference('FAM_FACTS_ADD')),
			explode(',', $tree->getPreference('FAM_FACTS_UNIQUE')),
			explode(',', $tree->getPreference('NOTE_FACTS_ADD')),
			explode(',', $tree->getPreference('NOTE_FACTS_UNIQUE')),
			explode(',', $tree->getPreference('SOUR_FACTS_ADD')),
			explode(',', $tree->getPreference('SOUR_FACTS_UNIQUE')),
			explode(',', $tree->getPreference('REPO_FACTS_ADD')),
			explode(',', $tree->getPreference('REPO_FACTS_UNIQUE')),
			['SOUR', 'REPO', 'OBJE', '_PRIM', 'NOTE', 'SUBM', 'SUBN', '_UID', 'CHAN']
		));

		$all_tags = [];
		foreach ($tags as $tag) {
			if ($tag) {
				$all_tags[$tag] = GedcomTag::getLabel($tag);
			}
		}

		uasort($all_tags, '\Fisharebest\Webtrees\I18N::strcasecmp');

		return $all_tags;
	}

	/**
	 * Count the number of pending changes in each tree.
	 *
	 * @return string[]
	 */
	private function totalChanges() {
		return Database::prepare("SELECT SQL_CACHE g.gedcom_id, COUNT(change_id)" . " FROM `##gedcom` AS g" . " LEFT JOIN `##change` AS c ON g.gedcom_id = c.gedcom_id AND status = 'pending'" . " GROUP BY g.gedcom_id")->fetchAssoc();
	}

	/**
	 * Count the number of families in each tree.
	 *
	 * @return string[]
	 */
	private function totalFamilies() {
		return Database::prepare("SELECT SQL_CACHE gedcom_id, COUNT(f_id)" . " FROM `##gedcom`" . " LEFT JOIN `##families` ON gedcom_id = f_file" . " GROUP BY gedcom_id")->fetchAssoc();
	}

	/**
	 * Count the number of individuals in each tree.
	 *
	 * @return string[]
	 */
	private function totalIndividuals() {
		return Database::prepare("SELECT SQL_CACHE gedcom_id, COUNT(i_id)" . " FROM `##gedcom`" . " LEFT JOIN `##individuals` ON gedcom_id = i_file" . " GROUP BY gedcom_id")->fetchAssoc();
	}

	/**
	 * Count the number of media objects in each tree.
	 *
	 * @return string[]
	 */
	private function totalMediaObjects() {
		return Database::prepare("SELECT SQL_CACHE gedcom_id, COUNT(m_id)" . " FROM `##gedcom`" . " LEFT JOIN `##media` ON gedcom_id = m_file" . " GROUP BY gedcom_id")->fetchAssoc();
	}

	/**
	 * Count the number of notes in each tree.
	 *
	 * @return string[]
	 */
	private function totalNotes() {
		return Database::prepare("SELECT SQL_CACHE gedcom_id, COUNT(o_id)" . " FROM `##gedcom`" . " LEFT JOIN `##other` ON gedcom_id = o_file AND o_type = 'NOTE'" . " GROUP BY gedcom_id"

		)->fetchAssoc();
	}

	/**
	 * Count the number of repositorie in each tree.
	 *
	 * @return string[]
	 */
	private function totalRepositories() {
		return Database::prepare("SELECT SQL_CACHE gedcom_id, COUNT(o_id)" . " FROM `##gedcom`" . " LEFT JOIN `##other` ON gedcom_id = o_file AND o_type = 'REPO'" . " GROUP BY gedcom_id")->fetchAssoc();
	}

	/**
	 * Count the number of sources in each tree.
	 *
	 * @return string[]
	 */
	private function totalSources() {
		return Database::prepare("SELECT SQL_CACHE gedcom_id, COUNT(s_id)" . " FROM `##gedcom`" . " LEFT JOIN `##sources` ON gedcom_id = s_file" . " GROUP BY gedcom_id")->fetchAssoc();
	}
}
