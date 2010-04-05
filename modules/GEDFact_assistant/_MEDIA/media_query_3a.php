<?php
/**
 * Media Link Assistant Control module for phpGedView
 *
 * Media Link information about an individual
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Census Assistant
 * @version $Id$
 */

$iid2		= safe_GET('iid');

print_simple_header(i18n::translate('Link Media'));

$record=GedcomRecord::getInstance($iid2);
$headjs="";
if ($record) {
	if ($record->getType()=='FAM') {
		$idrecord=Family::getInstance($iid2);
		if ($idrecord->getHusbId()) {
			$headjs=$idrecord->getHusbId();
		}else{
			$headjs=$idrecord->getWifeId();
		}
	}
	?>
	<script language="JavaScript" type="text/javascript">
	function insertId() {
		if (window.opener.document.getElementById('addlinkQueue')) {
			// alert('Please move this alert window and examine the contents of the pop-up window, then click OK')
			window.opener.insertRowToTable("<?php echo $record->getXref(); ?>", "<?php echo htmlSpecialChars($record->getFullName()); ?>", "<?php echo $headjs;?>");
			window.close();
		}
	}
	</script>
	<?php

} else {
	?>
	<script language="JavaScript" type="text/javascript">
	function insertId() {
		window.opener.alert('<?php echo strtoupper($iid2); ?> - <?php echo i18n::translate('Not a valid Individual, Family or Source ID') ?>');
		window.close();
	}
	</script>
	<?php
}
?>

<script language="JavaScript" type="text/javascript">
 window.onLoad = insertId();
</script>
