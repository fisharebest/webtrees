<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\Functions;

define('WT_SCRIPT_NAME', 'admin.php');

require './includes/session.php';

// This is a list of old files and directories, from earlier versions of webtrees, that can be deleted.
// It was generated with the help of a command like this:
// git diff 1.6.0..master --name-status | grep ^D
$old_files = array(
	// Removed in 1.0.2
	WT_ROOT . 'language/en.mo',
	// Removed in 1.0.3
	WT_ROOT . 'themechange.php',
	// Removed in 1.0.4
	// Removed in 1.0.5
	// Removed in 1.0.6
	WT_ROOT . 'includes/extras',
	// Removed in 1.1.0
	WT_ROOT . 'addremotelink.php',
	WT_ROOT . 'addsearchlink.php',
	WT_ROOT . 'client.php',
	WT_ROOT . 'dir_editor.php',
	WT_ROOT . 'editconfig_gedcom.php',
	WT_ROOT . 'editgedcoms.php',
	WT_ROOT . 'edit_merge.php',
	WT_ROOT . 'genservice.php',
	WT_ROOT . 'includes/classes',
	WT_ROOT . 'includes/controllers',
	WT_ROOT . 'includes/family_nav.php',
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
	// Removed in 1.1.1
	// Removed in 1.1.2
	WT_ROOT . 'treenav.php',
	// Removed in 1.2.0
	WT_ROOT . 'themes/clouds/jquery',
	WT_ROOT . 'themes/colors/jquery',
	WT_ROOT . 'themes/fab/jquery',
	WT_ROOT . 'themes/minimal/jquery',
	WT_ROOT . 'themes/webtrees/jquery',
	WT_ROOT . 'themes/xenea/jquery',
	// Removed in 1.2.1
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
	WT_ROOT . 'includes/cssparser.inc.php',
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
	WT_ROOT . 'includes/media_reorder_count.php',
	WT_ROOT . 'includes/media_tab_head.php',
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
	WT_ROOT . 'includes/dmsounds_UTF8.php',
	WT_ROOT . 'includes/grampsxml.rng',
	WT_ROOT . 'includes/session_spider.php',
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
	WT_ROOT . 'includes/set_gedcom_defaults.php',
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
	WT_ROOT . 'includes/media_reorder.php',
	WT_ROOT . 'includes/old_messages.php',
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
	// Removed in 1.6.1
	WT_ROOT . 'includes/authentication.php',
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
	WT_ROOT . 'includes/config_data.php',
	WT_ROOT . 'includes/db_schema',
	WT_ROOT . 'includes/fonts',
	WT_ROOT . 'includes/functions',
	WT_ROOT . 'includes/hitcount.php',
	WT_ROOT . 'includes/reportheader.php',
	WT_ROOT . 'includes/specialchars.php',
	WT_ROOT . 'js',
	WT_ROOT . 'language/en_GB.mo', // Replaced with en-GB.mo
	WT_ROOT . 'language/en_US.mo', // Replaced with en-US.mo
	WT_ROOT . 'language/pt_BR.mo', // Replaced with pt-BR.mo
	WT_ROOT . 'language/zh_CN.mo', // Replaced with zh-Hans.mo
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
	WT_ROOT . 'packages/bootstrap-3.3.4',
	WT_ROOT . 'packages/bootstrap-datetimepicker-4.0.0',
	WT_ROOT . 'packages/ckeditor-4.4.7-custom',
	WT_ROOT . 'packages/font-awesome-4.3.0',
	WT_ROOT . 'packages/jquery-1.11.2',
	WT_ROOT . 'packages/jquery-2.1.3',
	WT_ROOT . 'packages/moment-2.10.3',
	// Removed in 1.7.3
	WT_ROOT . 'includes/php_53_compatibility.php',
	WT_ROOT . 'modules_v3/GEDFact_assistant/census/date.js',
	WT_ROOT . 'modules_v3/GEDFact_assistant/census/dynamicoptionlist.js',
	WT_ROOT . 'packages/jquery-cookie-1.4.1/jquery.cookie.js',
	// Removed in 1.7.4
	WT_ROOT . 'assets/js-1.7.2',
	WT_ROOT . 'themes/_administration/css-1.7.0',
	WT_ROOT . 'themes/clouds/css-1.7.0',
	WT_ROOT . 'themes/colors/css-1.7.0',
	WT_ROOT . 'themes/fab/css-1.7.0',
	WT_ROOT . 'themes/minimal/css-1.7.0',
	WT_ROOT . 'themes/webtrees/css-1.7.0',
	WT_ROOT . 'themes/xenea/css-1.7.0',
	WT_ROOT . 'packages/bootstrap-3.3.5',
	WT_ROOT . 'packages/bootstrap-datetimepicker-4.15.35',
	WT_ROOT . 'packages/jquery-1.11.3',
	WT_ROOT . 'packages/jquery-2.1.4',
	WT_ROOT . 'packages/moment-2.10.6',
	// Removed in 1.7.5
	WT_ROOT . 'themes/_administration/css-1.7.4',
	WT_ROOT . 'themes/clouds/css-1.7.4',
	WT_ROOT . 'themes/colors/css-1.7.4',
	WT_ROOT . 'themes/fab/css-1.7.4',
	WT_ROOT . 'themes/minimal/css-1.7.4',
	WT_ROOT . 'themes/webtrees/css-1.7.4',
	WT_ROOT . 'themes/xenea/css-1.7.4',
);

// Delete old files (if we can).
$files_to_delete = array();
foreach ($old_files as $file) {
	if (file_exists($file) && !File::delete($file)) {
		$files_to_delete[] = $file;
	}
}

$controller = new PageController;
$controller
	->restrictAccess(Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Control panel') . ' — ' . /* I18N: A summary of the system status */ I18N::translate('Dashboard'))
	->pageHeader();

// Check for updates
$latest_version_txt = Functions::fetchLatestVersion();
if (preg_match('/^[0-9.]+\|[0-9.]+\|/', $latest_version_txt)) {
	list($latest_version) = explode('|', $latest_version_txt);
} else {
	// Cannot determine the latest version
	$latest_version = '';
}

$update_available = Auth::isAdmin() && $latest_version && version_compare(WT_VERSION, $latest_version) < 0;

// Total number of users
$total_users = User::count();

// Administrators
$administrators = Database::prepare(
	"SELECT SQL_CACHE user_id, real_name FROM `##user` JOIN `##user_setting` USING (user_id) WHERE setting_name='canadmin' AND setting_value='1'"
)->fetchAll();

// Managers
$managers = Database::prepare(
	"SELECT SQL_CACHE user_id, real_name FROM `##user` JOIN `##user_gedcom_setting` USING (user_id)" .
	" WHERE setting_name = 'canedit' AND setting_value='admin'" .
	" GROUP BY user_id, real_name" .
	" ORDER BY real_name"
)->fetchAll();

// Moderators
$moderators = Database::prepare(
	"SELECT SQL_CACHE user_id, real_name FROM `##user` JOIN `##user_gedcom_setting` USING (user_id)" .
	" WHERE setting_name = 'canedit' AND setting_value='accept'" .
	" GROUP BY user_id, real_name" .
	" ORDER BY real_name"
)->fetchAll();

// Number of users who have not verified their email address
$unverified = Database::prepare(
	"SELECT SQL_CACHE user_id, real_name FROM `##user` JOIN `##user_setting` USING (user_id)" .
	" WHERE setting_name = 'verified' AND setting_value = '0'" .
	" ORDER BY real_name"
)->fetchAll();

// Number of users whose accounts are not approved by an administrator
$unapproved = Database::prepare(
	"SELECT SQL_CACHE user_id, real_name FROM `##user` JOIN `##user_setting` USING (user_id)" .
	" WHERE setting_name = 'verified_by_admin' AND setting_value = '0'" .
	" ORDER BY real_name"
)->fetchAll();

// Users currently logged in
$logged_in = Database::prepare(
	"SELECT SQL_NO_CACHE DISTINCT user_id, real_name FROM `##user` JOIN `##session` USING (user_id)" .
	" ORDER BY real_name"
)->fetchAll();

// Count of records
$individuals = Database::prepare(
	"SELECT SQL_CACHE gedcom_id, COUNT(i_id) AS count FROM `##gedcom` LEFT JOIN `##individuals` ON gedcom_id = i_file GROUP BY gedcom_id"
)->fetchAssoc();
$families = Database::prepare(
	"SELECT SQL_CACHE gedcom_id, COUNT(f_id) AS count FROM `##gedcom` LEFT JOIN `##families` ON gedcom_id = f_file GROUP BY gedcom_id"
)->fetchAssoc();
$sources = Database::prepare(
	"SELECT SQL_CACHE gedcom_id, COUNT(s_id) AS count FROM `##gedcom` LEFT JOIN `##sources` ON gedcom_id = s_file GROUP BY gedcom_id"
)->fetchAssoc();
$media = Database::prepare(
	"SELECT SQL_CACHE gedcom_id, COUNT(m_id) AS count FROM `##gedcom` LEFT JOIN `##media` ON gedcom_id = m_file GROUP BY gedcom_id"
)->fetchAssoc();
$repositories = Database::prepare(
	"SELECT SQL_CACHE gedcom_id, COUNT(o_id) AS count FROM `##gedcom` LEFT JOIN `##other` ON gedcom_id = o_file AND o_type = 'REPO' GROUP BY gedcom_id"
)->fetchAssoc();
$changes = Database::prepare(
	"SELECT SQL_CACHE g.gedcom_id, COUNT(change_id) AS count FROM `##gedcom` AS g LEFT JOIN `##change` AS c ON g.gedcom_id = c.gedcom_id AND status = 'pending' GROUP BY g.gedcom_id"
)->fetchAssoc();

// Server warnings
// Note that security support for 5.6 ends after security support for 7.0
$server_warnings = array();
if (
	PHP_VERSION_ID < 50500 ||
	PHP_VERSION_ID < 50600 && date('Y-m-d') >= '2016-07-10' ||
	PHP_VERSION_ID < 70000 && date('Y-m-d') >= '2018-12-31' ||
	PHP_VERSION_ID >= 70000 && PHP_VERSION_ID < 70100 && date('Y-m-d') >= '2018-12-03'
) {
	$server_warnings[] =
		I18N::translate('Your web server is using PHP version %s, which is no longer receiving security updates. You should upgrade to a later version as soon as possible.', PHP_VERSION) .
		'<br><a href="https://php.net/supported-versions.php">https://php.net/supported-versions.php</a>';
} elseif (
	PHP_VERSION_ID < 50600 ||
	PHP_VERSION_ID < 70000 && date('Y-m-d') >= '2016-12-31' ||
	PHP_VERSION_ID < 70100 && date('Y-m-d') >= '2017-12-03'
) {
	$server_warnings[] =
		I18N::translate('Your web server is using PHP version %s, which is no longer maintained. You should upgrade to a later version.', PHP_VERSION) .
		 '<br><a href="https://php.net/supported-versions.php">https://php.net/supported-versions.php</a>';
}

?>
<h1><?php echo $controller->getPageTitle(); ?></h1>

<p>
	<?php echo I18N::translate('These pages provide access to all the configuration settings and management tools for this webtrees site.'); ?>
</p>

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

	<!-- SERVER WARNINGS -->
	<?php if ($server_warnings): ?>
		<div class="panel panel-danger">
			<div class="panel-heading" role="tab" id="server-heading">
				<h2 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion" href="#server-panel" aria-expanded="true" aria-controls="server-panel">
						<?php echo I18N::translate('Server information'); ?>
					</a>
				</h2>
			</div>
			<div id="server-panel" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="server-heading">
				<div class="panel-body">
					<?php foreach ($server_warnings as $server_warning): ?>
					<p>
						<?php echo $server_warning; ?>
					</p>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- WEBTREES VERSION -->
	<div class="panel <?php echo Auth::isAdmin() && $update_available ? 'panel-danger' : 'panel-primary'; ?>">
		<div class="panel-heading" role="tab" id="webtrees-version-heading">
			<h2 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#webtrees-version-panel" aria-expanded="true" aria-controls="webtrees-version-panel">
					<?php echo WT_WEBTREES, ' ', WT_VERSION; ?>
				</a>
			</h2>
		</div>
		<div id="webtrees-version-panel" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="webtrees-version-heading">
			<div class="panel-body">
				<p>
					<?php echo /* I18N: %s is a URL/link to the project website */ I18N::translate('Support and documentation can be found at %s.', '<a href="https://webtrees.net/">webtrees.net</a>'); ?>
				</p>
				<?php if (Auth::isAdmin()): ?>
				<p>
					<?php if ($latest_version === ''): ?>
					<?php echo I18N::translate('No upgrade information is available.'); ?>
					<?php elseif ($update_available): ?>
					<?php echo I18N::translate('A new version of webtrees is available.'); ?>
					<a href="admin_site_upgrade.php" class="error">
						<?php echo /* I18N: %s is a version number */ I18N::translate('Upgrade to webtrees %s.', Filter::escapeHtml($latest_version)); ?>
					</a>
					<?php else: ?>
						<?php echo I18N::translate('This is the latest version of webtrees. No upgrade is available.'); ?>
					<?php endif; ?>
				</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- USERS -->
	<?php if (Auth::isAdmin()): ?>
	<div class="panel <?php echo $unapproved || $unverified ? 'panel-danger' : 'panel-primary'; ?>">
		<div class="panel-heading" role="tab" id="users-heading">
			<h2 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#users-panel" aria-expanded="false" aria-controls="users-panel">
					<?php echo I18N::translate('Users'); ?>
				</a>
			</h2>
		</div>
		<div id="users-panel" class="panel-collapse collapse" role="tabpanel" aria-labelledby="users-heading">
			<div class="panel-body">
				<table class="table table-condensed">
					<caption class="sr-only">
						<?php echo I18N::translate('Users'); ?>
					</caption>
					<tbody>
						<tr>
							<th class="col-xs-3">
								<?php echo I18N::translate('Total number of users'); ?>
							</th>
							<td class="col-xs-9">
								<a href="admin_users.php">
									<?php echo I18N::number($total_users); ?>
								</a>
							</td>
						</tr>
						<tr>
							<th>
								<?php echo I18N::translate('Administrators'); ?>
							</th>
							<td>
								<?php foreach ($administrators as $n => $user): ?>
									<?php echo $n ? I18N::$list_separator : ''; ?>
									<a href="admin_users.php?action=edit&user_id=<?php echo $user->user_id; ?>" dir="auto">
										<?php echo Filter::escapeHtml($user->real_name); ?>
									</a>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr>
							<th>
								<?php echo I18N::translate('Managers'); ?>
							</th>
							<td>
								<?php foreach ($managers as $n => $user): ?>
									<?php echo $n ? I18N::$list_separator : ''; ?>
									<a href="admin_users.php?action=edit&user_id=<?php echo $user->user_id; ?>" dir="auto">
										<?php echo Filter::escapeHtml($user->real_name); ?>
									</a>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr>
							<th>
								<?php echo I18N::translate('Moderators'); ?>
							</th>
							<td>
								<?php foreach ($moderators as $n => $user): ?>
									<?php echo $n ? I18N::$list_separator : ''; ?>
									<a href="admin_users.php?action=edit&user_id=<?php echo $user->user_id; ?>" dir="auto">
										<?php echo Filter::escapeHtml($user->real_name); ?>
									</a>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr class="<?php echo $unverified ? 'danger' : ''; ?>">
							<th>
								<?php echo I18N::translate('Not verified by the user'); ?>
							</th>
							<td>
								<?php foreach ($unverified as $n => $user): ?>
									<?php echo $n ? I18N::$list_separator : ''; ?>
									<a href="admin_users.php?action=edit&user_id=<?php echo $user->user_id; ?>" dir="auto">
										<?php echo Filter::escapeHtml($user->real_name); ?>
									</a>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr class="<?php echo $unapproved ? 'danger' : ''; ?>">
							<th>
								<?php echo I18N::translate('Not approved by an administrator'); ?>
							</th>
							<td>
								<?php foreach ($unapproved as $n => $user): ?>
									<?php echo $n ? I18N::$list_separator : ''; ?>
									<a href="admin_users.php?action=edit&user_id=<?php echo $user->user_id; ?>" dir="auto">
										<?php echo Filter::escapeHtml($user->real_name); ?>
									</a>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr>
							<th>
								<?php echo I18N::translate('Users who are signed in'); ?>
							</th>
							<td>
								<?php foreach ($logged_in as $n => $user): ?>
								<?php echo $n ? I18N::$list_separator : ''; ?>
									<a href="admin_users.php?action=edit&user_id=<?php echo $user->user_id; ?>" dir="auto">
										<?php echo Filter::escapeHtml($user->real_name); ?>
									</a>
								<?php endforeach; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- FAMILY TREES -->
	<div class="panel <?php echo array_sum($changes) ? 'panel-danger' : 'panel-primary'; ?>">
		<div class="panel-heading" role="tab" id="trees-heading">
			<h2 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#trees-panel" aria-expanded="false" aria-controls="trees-panel">
					<?php echo I18N::translate('Family trees'); ?>
				</a>
			</h2>
		</div>
		<div id="trees-panel" class="panel-collapse collapse" role="tabpanel" aria-labelledby="trees-heading">
			<div class="panel-body">
				<table class="table table-condensed">
					<caption class="sr-only">
						<?php echo I18N::translate('Family trees'); ?>
					</caption>
					<thead>
						<tr>
							<th class="col-xs-5"><?php echo I18N::translate('Family tree'); ?></th>
							<th class="col-xs-2 text-right flip"><?php echo I18N::translate('Pending changes'); ?></th>
							<th class="col-xs-1 text-right flip"><?php echo I18N::translate('Individuals'); ?></th>
							<th class="col-xs-1 text-right flip"><?php echo I18N::translate('Families'); ?></th>
							<th class="col-xs-1 text-right flip"><?php echo I18N::translate('Sources'); ?></th>
							<th class="col-xs-1 text-right flip"><?php echo I18N::translate('Repositories'); ?></th>
							<th class="col-xs-1 text-right flip"><?php echo I18N::translate('Media'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach (Tree::getAll() as $tree): ?>
						<tr class="<?php echo $changes[$tree->getTreeId()] ? 'danger' : ''; ?>">
							<td>
								<a href="index.php?ctype=gedcom&amp;ged=<?php echo $tree->getNameUrl(); ?>">
									<?php echo $tree->getNameHtml(); ?>
									-
									<?php echo $tree->getTitleHtml(); ?>
								</a>
							</td>
							<td class="text-right flip">
								<?php if ($changes[$tree->getTreeId()]): ?>
								<a onclick="window.open('edit_changes.php', '_blank', chan_window_specs); return false;" href="#">
									<?php echo I18N::number($changes[$tree->getTreeId()]); ?>
									<span class="sr-only"><?php echo I18N::translate('Pending changes'); ?> <?php echo $tree->getTitleHtml(); ?></span>
								</a>
								<?php else: ?>
								-
								<?php endif; ?>
							</td>
							<td class="text-right flip">
								<?php if ($individuals[$tree->getTreeId()]): ?>
								<a href="indilist.php?ged=<?php echo $tree->getNameUrl(); ?>">
									<?php echo I18N::number($individuals[$tree->getTreeId()]); ?>
									<span class="sr-only"><?php echo I18N::translate('Individuals'); ?> <?php echo $tree->getTitleHtml(); ?></span>
								</a>
								<?php else: ?>
									-
								<?php endif; ?>
								</td>
							<td class="text-right flip">
								<?php if ($families[$tree->getTreeId()]): ?>
								<a href="famlist.php?ged=<?php echo $tree->getNameUrl(); ?>">
									<?php echo I18N::number($families[$tree->getTreeId()]); ?>
									<span class="sr-only"><?php echo I18N::translate('Families'); ?> <?php echo $tree->getTitleHtml(); ?></span>
								</a>
								<?php else: ?>
								-
								<?php endif; ?>
								</td>
							<td class="text-right flip">
								<?php if ($sources[$tree->getTreeId()]): ?>
								<a href="sourcelist.php?ged=<?php echo $tree->getNameUrl(); ?>">
									<?php echo I18N::number($sources[$tree->getTreeId()]); ?>
									<span class="sr-only"><?php echo I18N::translate('Sources'); ?> <?php echo $tree->getTitleHtml(); ?></span>
								</a>
								<?php else: ?>
								-
								<?php endif; ?>
							</td>
							<td class="text-right flip">
								<?php if ($repositories[$tree->getTreeId()]): ?>
								<a href="repolist.php?ged=<?php echo $tree->getNameUrl(); ?>">
									<?php echo I18N::number($repositories[$tree->getTreeId()]); ?>
									<span class="sr-only"><?php echo I18N::translate('Repositories'); ?> <?php echo $tree->getTitleHtml(); ?></span>
								</a>
								<?php else: ?>
									-
								<?php endif; ?>
							</td>
							<td class="text-right flip">
								<?php if ($media[$tree->getTreeId()]): ?>
								<a href="medialist.php?ged=<?php echo $tree->getNameUrl(); ?>">
									<?php echo I18N::number($media[$tree->getTreeId()]); ?>
									<span class="sr-only"><?php echo I18N::translate('Media objects'); ?> <?php echo $tree->getTitleHtml(); ?></span>
								</a>
								<?php else: ?>
								-
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr>
							<td>
								<?php echo I18N::translate('Total'); ?>
								-
								<?php echo I18N::plural('%s family tree', '%s family trees', count(Tree::getAll()), I18N::number(count(Tree::getAll()))); ?>
							</td>
							<td class="text-right flip">
								<?php echo I18N::number(array_sum($changes)); ?>
							</td>
							<td class="text-right flip">
								<?php echo I18N::number(array_sum($individuals)); ?>
							</td>
							<td class="text-right flip">
								<?php echo I18N::number(array_sum($families)); ?>
							</td>
							<td class="text-right flip">
								<?php echo I18N::number(array_sum($sources)); ?>
							</td>
							<td class="text-right flip">
								<?php echo I18N::number(array_sum($repositories)); ?>
							</td>
							<td class="text-right flip">
								<?php echo I18N::number(array_sum($media)); ?>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>

	<!-- OLD FILES -->
	<?php if (Auth::isAdmin() && $files_to_delete): ?>
	<div class="panel panel-danger">
		<div class="panel-heading" role="tab" id="old-files-heading">
			<h2 class="panel-title">
				<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#old-files-panel" aria-expanded="false" aria-controls="old-files-panel">
					<?php echo I18N::translate('Old files found'); ?>
				</a>
			</h2>
		</div>
		<div id="old-files-panel" class="panel-collapse collapse" role="tabpanel" aria-labelledby="old-files-heading">
			<div class="panel-body">
				<p>
					<?php echo I18N::translate('Files have been found from a previous version of webtrees. Old files can sometimes be a security risk. You should delete them.'); ?>
				</p>
				<ul class="list-unstyled">
					<?php foreach ($files_to_delete as $file_to_delete): ?>
						<li dir="ltr"><code><?php echo Filter::escapeHtml($file_to_delete); ?></code></li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
	</div>
	<?php endif; ?>

</div>
