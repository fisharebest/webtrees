<?php
/**
* Code for Extracting Shared Note Indi Links for GEDFact_assistant
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
* @subpackage GEDFact_assistant
* @version $Id$
*/
// require './config.php';
// require './includes/classes/class_wt_db.php';

//get values of input fields
	$noteid 	= addslashes($_GET['noteid']);		 //-- Note Id
	$TBLPREFIX 	= addslashes($_GET['tblprefix']);	 //-- table prefix
	$DBTYPE 	= addslashes($_GET['dbhost']);		 //-- MySQL host Name
	$DBHOST 	= addslashes($_GET['dbhost']);		 //-- MySQL host Name
	$DBUSER		= addslashes($_GET['dbuser']);		 //-- MySQL database User Name
	$DBPASS		= addslashes($_GET['dbpass']);		 //-- MySQL database User Password
	$DBNAME		= addslashes($_GET['dbname']);		 //-- The MySQL database name where you want PHPGedView to build its tables

// database info: Change password and DB Name ===================
//	$dbh = new PDO("$DBTYPE:host=$DBHOST;dbname=$DBNAME", $DBUSER, $DBPASS);
//	$statement=$DBH::prepare("SELECT 'INDI' AS type, l_from, l_to FROM {$TBLPREFIX}link, {$TBLPREFIX}individuals WHERE l_from LIKE 'I%' AND i_file=l_file AND i_id=l_from AND l_file='1' AND l_type='NOTE' AND l_to='$noteid'")->execute();

	$db = mysql_connect($DBHOST, $DBUSER, $DBPASS) or die (mysql_error());
	mysql_select_db ($DBNAME, $db) or die (mysql_error());

//Now Refresh the table .. id=from_mysql
	$row_count = 0;
	$output  = "<table width=\'250\' border=\'0\' cellpadding=\'0\' cellspacing=\'0\' class=\'table1\'>";
	$output .= "<tr>";
	$output .= "<td width=\'10\' align=\'left\' bgcolor=\'#AAAAAA\'><span class=\'style1\'>#</span></td>";
	$output .= "<td width=\'30\' align=\'left\' bgcolor=\'#AAAAAA\'><span class=\'style1\'>Id</span></td>";
	$output .= "<td wrap=\'nowrap\' width=\'80\' align=\'left\' bgcolor=\'#AAAAAA\'><span class=\'style1\'>Name</span></td>";
	$output .= "<td width=\'25\' align=\'center\' bgcolor=\'#AAAAAA\' nowrap='nowrap'><span class=\'style1\'>&nbsp;Link</span></td>";
	$output .= "<td width=\'25\' align=\'center\' bgcolor=\'#AAAAAA\' nowrap='nowrap'><span class=\'style1\'>&nbsp;&nbsp;Unlink</span></td>";
	$output .= "</tr>";

	$res_indis = mysql_query("SELECT 'INDI' AS type, l_from, l_to FROM {$TBLPREFIX}link, {$TBLPREFIX}individuals WHERE l_from LIKE 'I%' AND i_file=l_file AND i_id=l_from AND l_file='1' AND l_type='NOTE' AND l_to='$noteid'");
	
//	while ($rows = $statement->fetch(PDO::FETCH_NUM)) {
//		$output .= "<tr><td> $rows[0]</td><td> $rows[1]</td><td> $rows[2]</td></tr>";
//	}


	 while ($rows = mysql_fetch_array($res_indis)) {
		$srno = $rows['l_from'];
		$u_name = "Name for ".$rows['l_from'];
		$u_email = $rows['l_to'];
		$row_style = ($row_count % 2) ? "row1" : "row2";
		$output .= "<tr class=\'".$row_style."\'><td>".($row_count+1)."<td>".$srno."</td><td>".$u_name."</td><td align='center'><input type='radio' name='rad_".$srno."' checked /></td><td align='center'><input type='radio' name='rad_".$srno."' /></td></tr>";
		$row_count = $row_count + 1;
	}


//Free Results
	 mysql_free_result($res_indis);
//	$statement->closeCursor();

	$output .= "</table>";
	
?>

<?php //update table ?>
from_mysql_obj = document.getElementById( "from_mysql" );
from_mysql_obj.innerHTML = "<?php echo $output; ?>";

<?php //update status box ?>
my_status = document.getElementById( "status" );
my_status.innerHTML = 'Ready';




