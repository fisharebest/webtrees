<?php
// JW Player module for phpGedView
//
// Display wmv video media Items using JW Player in webtrees
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2007 to 2009  PGV Development Team.  All rights reserved.
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

?>
<html xmlns="http://www.w3.org/1999/xhtml" <?php // echo WT_I18N::html_markup(); ?>>
<head>
	<META HTTP-EQUIV="Expires" CONTENT="Tue, 01 Jan 1980 1:00:00 GMT">
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
	<title>JW Player for Windows Media Videos</title>
	<script src="silverlight.js"></script>
	<script src="wmvplayer.js"></script>
</head>
<body bgcolor="#000000">
<center>

<?php
global $pid, $GEDCOM ;
global $wmvVideo;
$wmvVideo=$_GET['wmvVideo'];
?>

<div id="myplayer">The player will be placed here</div>

<script>
	var elm = document.getElementById("myplayer");
	var src = 'wmvplayer.xaml';
	var cfg = {
		file:'<?php echo $wmvVideo; ?>',
		<?php if (preg_match("/\.mp3$/i", $wmvVideo)) { ?>
			logo:'audio.png',
		<?php } ?>
		autostart:'true',
		overstretch:'true',
		width:'480',
		height:'365'
	};
	var ply = new jeroenwijering.Player(elm,src,cfg);
</script>

</center>
</body>
</html>
