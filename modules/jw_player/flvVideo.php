<?php
/**
 * JW Player module for phpGedView
 *
 * Display flv video media Items using JW Player in PGV
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2007 to 2010  PGV Development Team.  All rights reserved.
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
 * @subpackage Module
 * @version $Id$
 * @author Brian Holland
 */
 ?>
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo i18n::html_markup(); ?>>
<head>
	<META HTTP-EQUIV="Expires" CONTENT="Tue, 01 Jan 1980 1:00:00 GMT">
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
	<title>JW Player for Flash</title>
	<script type="text/javascript" src="modules/jw_player/swfobject.js"></script>
</head>
<body bgcolor="#000000">
<center>

<?php
global $pid, $GEDCOM ;
global $flvVideo, $SERVER_URL;
$flvVideo="../../".decrypt(safe_GET('flvVideo'));
$preview="";
// echo "<font color=white>".$flvVideo."</font>";
?>

	<!-- START OF THE PLAYER EMBEDDING TO COPY-PASTE -->
	<!--	
	<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="400" height="315">
		<param name="movie" value="player.swf" />
		<param name="allowfullscreen" value="true" />
		<param name="allowscriptaccess" value="always" />
		<param name="flashvars" value="file=video.flv&image=preview.jpg" />
		<embed
			type="application/x-shockwave-flash"
			id="player2"
			name="player2"
			src="player.swf" 
			width="400" 
			height="315"
			allowscriptaccess="always" 
			allowfullscreen="true"
			flashvars="file=http://www.youtube.com/watch?v=TF6cnLnEARo"
		/>
	</object>
	-->
	<!-- END OF THE PLAYER EMBEDDING -->
	

	<div id="container"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>
	<script type="text/javascript">
		var video 	= "<?php print $flvVideo; ?>";
		var preview = "<?php print $preview; ?>";
		var s1 = new SWFObject("modules/jw_player/player.swf","ply","480","365","9","#000000");
		s1.addParam("allowfullscreen","true");
		s1.addParam("allowscriptaccess","always");
		s1.addParam("stretching","fill");
		s1.addParam("flashvars","file=" +video+ "&image=" +preview+ "&autostart=true" );
		s1.write("container");
  </script> 



</center>
</body>

</html>
