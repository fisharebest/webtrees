<?php
// Delete old/unused files after an upgrade
// 
// webtrees: Web based Family History software
// Copyright (C) 2011 Greg Roach
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_DELETE_OLD_FILES', '');

// We may not have permission to do this.  But we can try....

// Removed in 1.0.2
@unlink(WT_ROOT.'includes/classes/class_geclippings.php');
@unlink(WT_ROOT.'includes/classes/class_gedownloadgedcom.php');
@unlink(WT_ROOT.'includes/classes/class_gewebservice.php');
@unlink(WT_ROOT.'includes/classes/class_grampsexport.php');
@unlink(WT_ROOT.'language/en.mo');
// Removed in 1.0.3
@unlink(WT_ROOT.'themechange.php');
// Removed in 1.0.4
@unlink(WT_ROOT.'themes/fab/images/notes.gif');
// Removed in 1.0.5
@unlink(WT_ROOT.'modules/lightbox/functions/lb_indi_doors_0.php');
@unlink(WT_ROOT.'modules/lightbox/functions/lb_indi_doors_1.php');
@unlink(WT_ROOT.'modules/lightbox/functions/lb_indi_tabs_0.php');
@unlink(WT_ROOT.'modules/lightbox/functions/lb_indi_tabs_1.php');
// Removed in 1.0.6
@unlink(WT_ROOT.'includes/extras/functions.ar.php');
@unlink(WT_ROOT.'includes/extras/functions.en.php');
@unlink(WT_ROOT.'includes/extras/functions.fr.php');
@unlink(WT_ROOT.'includes/extras/functions.pl.php');
@unlink(WT_ROOT.'includes/extras/functions.tr.php');
@rmdir (WT_ROOT.'includes/extras');
// Removed in 1.1.0
@unlink(WT_ROOT.'PEAR.php');
@unlink(WT_ROOT.'SOAP/HTTP/Request/Listener.php');
@rmdir (WT_ROOT.'SOAP/HTTP/Request');
@unlink(WT_ROOT.'SOAP/HTTP/Request.php');
@rmdir (WT_ROOT.'SOAP/HTTP');
@unlink(WT_ROOT.'SOAP/Net/Socket.php');
@unlink(WT_ROOT.'SOAP/Net/URL.php');
@rmdir (WT_ROOT.'SOAP/Net');
@unlink(WT_ROOT.'SOAP/Server/Email.php');
@unlink(WT_ROOT.'SOAP/Server/Email_Gateway.php');
@unlink(WT_ROOT.'SOAP/Server/TCP.php');
@rmdir (WT_ROOT.'SOAP/Server');
@unlink(WT_ROOT.'SOAP/tools/genproxy.php');
@rmdir (WT_ROOT.'SOAP/tools');
@unlink(WT_ROOT.'SOAP/Transport/HTTP.php');
@unlink(WT_ROOT.'SOAP/Transport/SMTP.php');
@unlink(WT_ROOT.'SOAP/Transport/TCP.php');
@rmdir (WT_ROOT.'SOAP/Transport');
@unlink(WT_ROOT.'SOAP/Type/dateTime.php');
@unlink(WT_ROOT.'SOAP/Type/duration.php');
@unlink(WT_ROOT.'SOAP/Type/hexBinary.php');
@rmdir (WT_ROOT.'SOAP/Type');
@unlink(WT_ROOT.'SOAP/.htaccess');
@unlink(WT_ROOT.'SOAP/Base.php');
@unlink(WT_ROOT.'SOAP/Client.php');
@unlink(WT_ROOT.'SOAP/Disco.php');
@unlink(WT_ROOT.'SOAP/Fault.php');
@unlink(WT_ROOT.'SOAP/Parser.php');
@unlink(WT_ROOT.'SOAP/Server.php');
@unlink(WT_ROOT.'SOAP/Transport.php');
@unlink(WT_ROOT.'SOAP/Value.php');
@unlink(WT_ROOT.'SOAP/WSDL.php');
@rmdir (WT_ROOT.'SOAP');
@unlink(WT_ROOT.'addremotelink.php');
@unlink(WT_ROOT.'addsearchlink.php');
@unlink(WT_ROOT.'client.php');
@unlink(WT_ROOT.'dir_editor.php');
@unlink(WT_ROOT.'edit_merge.php');
@unlink(WT_ROOT.'editconfig_gedcom.php');
@unlink(WT_ROOT.'editgedcoms.php');
@unlink(WT_ROOT.'genservice.php');
@unlink(WT_ROOT.'includes/controllers/advancedsearch_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/ancestry_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/basecontrol.php');
@unlink(WT_ROOT.'includes/controllers/descendancy_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/family_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/hourglass_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/individual_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/lifespan_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/media_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/note_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/pedigree_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/remotelink_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/repository_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/search_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/source_ctrl.php');
@unlink(WT_ROOT.'includes/controllers/timeline_ctrl.php');
@rmdir (WT_ROOT.'includes/controllers');
@unlink(WT_ROOT.'includes/classes/class_date.php');
@unlink(WT_ROOT.'includes/classes/class_event.php');
@unlink(WT_ROOT.'includes/classes/class_family.php');
@unlink(WT_ROOT.'includes/classes/class_gedcomrecord.php');
@unlink(WT_ROOT.'includes/classes/class_i18n.php');
@unlink(WT_ROOT.'includes/classes/class_localclient.php');
@unlink(WT_ROOT.'includes/classes/class_media.php');
@unlink(WT_ROOT.'includes/classes/class_menu.php');
@unlink(WT_ROOT.'includes/classes/class_menubar.php');
@unlink(WT_ROOT.'includes/classes/class_module.php');
@unlink(WT_ROOT.'includes/classes/class_note.php');
@unlink(WT_ROOT.'includes/classes/class_person.php');
@unlink(WT_ROOT.'includes/classes/class_reportbase.php');
@unlink(WT_ROOT.'includes/classes/class_reporthtml.php');
@unlink(WT_ROOT.'includes/classes/class_reportpdf.php');
@unlink(WT_ROOT.'includes/classes/class_repository.php');
@unlink(WT_ROOT.'includes/classes/class_serviceclient.php');
@unlink(WT_ROOT.'includes/classes/class_source.php');
@unlink(WT_ROOT.'includes/classes/class_stats.php');
@unlink(WT_ROOT.'includes/classes/class_treenav.php');
@unlink(WT_ROOT.'includes/classes/class_wt_db.php');
@rmdir (WT_ROOT.'includes/classes');
@unlink(WT_ROOT.'includes/family_nav.php');
@unlink(WT_ROOT.'includes/functions/functions_lang.php');
@unlink(WT_ROOT.'includes/functions/functions_tools.php');
@unlink(WT_ROOT.'js/conio/prototype.js');
@rmdir (WT_ROOT.'js/conio');
@unlink(WT_ROOT.'logs.php');
@unlink(WT_ROOT.'module_admin.php');
@unlink(WT_ROOT.'modules/batch_update/batch_update.php');
@unlink(WT_ROOT.'modules/batch_update/plugins/tmglatlon.php');
@unlink(WT_ROOT.'modules/googlemap/editconfig.php');
@unlink(WT_ROOT.'modules/googlemap/placecheck.php');
@unlink(WT_ROOT.'modules/googlemap/places.php');
@unlink(WT_ROOT.'modules/lightbox/lb_editconfig.php');
@unlink(WT_ROOT.'modules/sitemap/admin_config.php');
@unlink(WT_ROOT.'modules/sitemap/gss.xsl');
@unlink(WT_ROOT.'modules/sitemap/index.php');
@unlink(WT_ROOT.'modules/sitemap/sortdown.gif');
@unlink(WT_ROOT.'modules/sitemap/sortup.gif');
@unlink(WT_ROOT.'places/ISR/ISR_Üמ-Çüכü.txt');
@unlink(WT_ROOT.'places/ISR/ISR_Üמ-Çüכü_Üמ-Çüכü.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הµצשÆ.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הµצשÆ_µצÜ.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הµצשÆ_וץש.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הµצשÆ_ישמÆ.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הµצשÆ_כז¶וÇמ.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הµצשÆ_ץÉ¶Ü.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הÄ¶ץז.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הÄ¶ץז_¶Äמה.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הÄ¶ץז_¶חשüשÜ.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הÄ¶ץז_הÖ¶שÆ.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הÄ¶ץז_צÜח-Ü¦ששה.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הג¶ש¬.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הג¶ש¬_ÇÖ¦משÆ.txt');
@unlink(WT_ROOT.'places/ISR/ISR_הג¶ש¬_üÇ¶-Öüו.txt');
@unlink(WT_ROOT.'places/ISR/ISR_וזה.txt');
@unlink(WT_ROOT.'places/ISR/ISR_וזה_חüמ-וזה.txt');
@unlink(WT_ROOT.'places/ISR/ISR_חכצה.txt');
@unlink(WT_ROOT.'places/ISR/ISR_חכצה_חג¶ה.txt');
@unlink(WT_ROOT.'places/ISR/ISR_חכצה_חכצה.txt');
@unlink(WT_ROOT.'places/ISR/ISR_כ¶שÖמכ¬.txt');
@unlink(WT_ROOT.'places/ISR/ISR_כ¶שÖמכ¬_כ¶שÖמכ¬.txt');
@unlink(WT_ROOT.'places/ISR/ISR_כהשגה-שÖשÄ¶שÆ.txt');
@unlink(WT_ROOT.'places/ISR/ISR_כהשגה-שÖשÄ¶שÆ_כהשגה-שÖשÄ¶שÆ.txt');
@unlink(WT_ROOT.'serviceClientTest.php');
@unlink(WT_ROOT.'siteconfig.php');
@unlink(WT_ROOT.'themes/clouds/mozilla.css');
@unlink(WT_ROOT.'themes/clouds/netscape.css');
@unlink(WT_ROOT.'themes/colors/mozilla.css');
@unlink(WT_ROOT.'themes/colors/netscape.css');
@unlink(WT_ROOT.'themes/fab/mozilla.css');
@unlink(WT_ROOT.'themes/fab/netscape.css');
@unlink(WT_ROOT.'themes/minimal/mozilla.css');
@unlink(WT_ROOT.'themes/minimal/netscape.css');
@unlink(WT_ROOT.'themes/webtrees/mozilla.css');
@unlink(WT_ROOT.'themes/webtrees/netscape.css');
@unlink(WT_ROOT.'themes/xenea/mozilla.css');
@unlink(WT_ROOT.'themes/xenea/netscape.css');
@unlink(WT_ROOT.'uploadmedia.php');
@unlink(WT_ROOT.'webservice/.htaccess');
@unlink(WT_ROOT.'webservice/genealogyService.php');
@unlink(WT_ROOT.'webservice/wtServiceLogic.class.php');
@rmdir (WT_ROOT.'webservice');
@unlink(WT_ROOT.'wtinfo.php');
// ...this list is complete, up to svn 10497
