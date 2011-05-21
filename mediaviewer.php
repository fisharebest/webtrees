<?php
// Media View Page
//
// This page displays all information about media that is selected in PHPGedView.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
// $Id$

define('WT_SCRIPT_NAME', 'mediaviewer.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

// We have finished writing session data, so release the lock
Zend_Session::writeClose();

$controller = new WT_Controller_Media();
$controller->init();

print_header($controller->getPageTitle());

if (!$controller->mediaobject) {
	echo '<b>', WT_I18N::translate('Unable to find record with ID'), '</b><br /><br />';
	print_footer();
	exit;
}

if (!$controller->mediaobject->canDisplayDetails()) {
	print_privacy_error();
	print_footer();
	exit;
}

if (WT_USE_LIGHTBOX) {
	require WT_ROOT.WT_MODULES_DIR.'lightbox/lb_defaultconfig.php';
	require WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lb_call_js.php';
}

/* Note:
 *  if $controller->getLocalFilename() is not set, then an invalid MID was passed in
 *  if $controller->pid is not set, then a filename was passed in that is not in the gedcom
 */
$filename = $controller->getLocalFilename();

global $tmb;

//The next set of code draws the table that displays information about the person
?>
<table width="70%" class="list_table">
	<tr>
		<td class="name_head" colspan="2">
			<?php echo PrintReady($controller->mediaobject->getFullName()); ?>
			<?php echo PrintReady($controller->mediaobject->getAddName()); ?> <br /><br />
			<?php if ($controller->mediaobject->isMarkedDeleted()) echo "<span class=\"error\">".WT_I18N::translate('This record has been marked for deletion upon admin approval.')."</span>"; ?>
		</td>
	</tr>
	<tr>
		<td align="center" width="150">
			<?php
			// display image
			if ($controller->canDisplayDetails()) {
				echo $controller->mediaobject->displayMedia(array('download'=>true, 'align'=>'none', 'alertnotfound'=>true));
			}
			?>
		</td>
		<td valign="top">
			<table width="100%">
				<tr>
					<td>
						<table class="facts_table<?php echo $TEXT_DIRECTION=='ltr'?'':'_rtl'; ?>">
							<?php
								$facts = $controller->getFacts($SHOW_MEDIA_FILENAME);
								foreach ($facts as $f=>$factrec) {
									print_fact($factrec);
								}
							?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="center" colspan="2">
			<br /><b><?php echo WT_I18N::translate('The image relates to:'); ?></b><br /><br />
			<?php

				// Individuals linked to this media object
				if ($controller->mediaobject->countLinkedIndividuals()) {
					print_indi_table($controller->mediaobject->fetchLinkedIndividuals(), $controller->mediaobject->getFullName());
				}

				// Families linked to this media object
				if ($controller->mediaobject->countLinkedFamilies()) {
					print_fam_table($controller->mediaobject->fetchLinkedFamilies(), $controller->mediaobject->getFullName());
				}

				// Sources linked to this media object
				if ($controller->mediaobject->countLinkedSources()) {
					print_sour_table($controller->mediaobject->fetchLinkedSources(), $controller->mediaobject->getFullName());
				}

				// Repositories linked to this media object
				if ($controller->mediaobject->countLinkedRepositories()) {
					print_repo_table($controller->mediaobject->fetchLinkedRepositories(), $controller->mediaobject->getFullName());
				}

				// Notes linked to this media object
				if ($controller->mediaobject->countLinkedNotes()) {
					print_note_table($controller->mediaobject->fetchLinkedNotes(), $controller->mediaobject->getFullName());
				}

			?>
		</td>
	</tr>
</table>
<?php

// These JavaScript functions are needed for the code to work properly with the menu.
?>
<script type="text/javascript">
<!--

// javascript function to open the lightbox view
function lightboxView() {
// var string = "<?php echo $tmb; ?>";
// alert(string);
// document.write(string);
// <?php echo $tmb; ?>
	return false;
}

// javascript function to open the original imageviewer.php page
function openImageView() {
	window.open("imageview.php?filename=<?php echo urlencode($filename); ?>", "Image View");
	return false;
}
// javascript function to open a window with the raw gedcom in it
function show_gedcom_record(shownew) {
	fromfile="";
	if (shownew=="yes") fromfile='&fromfile=1';
	var recwin = window.open("gedrecord.php?pid=<?php echo $controller->pid; ?>"+fromfile, "_blank", "top=50, left=50, width=600, height=400, scrollbars=1, scrollable=1, resizable=1");
}

function showchanges() {
	window.location = '<?php echo $controller->mediaobject->getRawUrl(); ?>&show_changes=yes';
}

function ilinkitem(mediaid, type) {
	window.open('inverselink.php?mediaid='+mediaid+'&linkto='+type+'&'+sessionname+'='+sessionid, '_blank', 'top=50, left=50, width=570, height=630, resizable=1, scrollbars=1');
	return false;
}
//-->
</script>

<br /><br /><br />
<?php
print_footer();
