<?php
/*
 * 	Footer for the JustLight theme
 *  
 *  webtrees: Web based Family History software
 *  Copyright (C) 2014 webtrees development team.
 *  Copyright (C) 2014 JustCarmen.
 * 
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or (at your option) any later version.
 * 
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
?>
</main><!-- /.content -->
<div style="width: 500px; margin:auto; padding:10px;">
	<?php include(WT_ROOT."/addthis.php"); ?>
</div>
<?php include(WT_ROOT."adsense-responsive.php"); ?>
<?php if ($view != 'simple') { ?>
	<div id="push"></div>
	</div><!-- /.wrap -->
	<footer>
		<div class="top">			
			<?php if (WT_LOCALE=='tr') {
				echo '<a href="/privacypolicy.html" target="_blank" class="', $TEXT_DIRECTION, '">Gizlilik Politikasi</a>';        
			} else {
				echo '<a href="/privacypolicy.html" target="_blank" class="', $TEXT_DIRECTION, '">Privacy policy</a>';        
			} ?>
			<?php echo contact_links() ?>
			<div class="logo">
				<a href="<?php echo WT_WEBTREES_URL ?>" target="_blank" class="icon-webtrees" title="<?php echo WT_WEBTREES, ' ', WT_VERSION ?>"></a><br/>
				<a href="http://www.justcarmen.nl" target="_blank">Design: justcarmen.nl</a>
			</div>
		</div><!-- /.footer top -->
		<div class="container bottom">
			<?php
			if ($WT_TREE && $WT_TREE->getPreference('SHOW_STATS')) {
				echo execution_stats();
			}
			?>
		</div><!-- /.footer bottom -->
	</footer><!-- /.footer -->
	<?php
}
