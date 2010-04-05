<?xml version="1.0" encoding="UTF-8"?>

<!--
	Google Sitemaps Stylesheets (GSStylesheets)
	 Project Home: http://sourceforge.net/projects/gstoolbox
	 Copyright (c) 2005 Baccou Bonneville SARL (http://www.baccoubonneville.com)
	 License http://www.gnu.org/copyleft/lesser.html GNU/LGPL

	 Created by Serge Baccou
	 1.0 / 20 Aug 2005

	 Changes by Johannes Müller ( http://GSiteCrawler.com/ )
	 1.1 / 20 Aug 2005 - sorting by clicking on column headers
				- open urls in new window/tab
				- some stylesheet/CSS cleanup

	 Changes by Tobias Kluge ( http://enarion.net/ )
	 1.2 / 22 Aug 2005 - moved sitemap file and sitemap index file into one file gss.xsl
	 1.5 / 27 Aug 2005 - added js and css into xslt stylesheet; only gss.xsl is needed now

	 Changes by Serge Baccou
	 1.3 / 23 Aug 2005 - some XSLT cleanup
	 1.4 / 24 Aug 2005 - sourceForge and LGPL links and logos
				- sorting is working for siteindex (see gss.js)

	 Johannes Müller ( http://gsitecrawler.com/ )
	 1.5a/ 31 Aug 2005 - added version number in footer
				- removed images (don't allow tracking on other servers)
	 1.5b/ 05 Jul 2006 - removed (unnecessary) link to (missing) CSS file
				- moved necessary items from Googles CSS file intern
				- javascript code not compatible with Opera 9.0
	 1.6/  19 Nov 2006 - Changed namespace to http://www.sitemaps.org/schemas/sitemap/0.9

	 Minor cleanup for use in PhpGedView
-->

<xsl:stylesheet version="2.0" 
	xmlns:html="http://www.w3.org/TR/REC-html40"
	xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:geo="http://www.google.com/geo/schemas/sitemap/1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="html" version="1.0" encoding="iso-8859-1" indent="yes"/>

	<!-- Root template -->    
	<xsl:template match="/">
	<html>     
	<head>
		<title>Sitemap File for </title>
		<style type="text/css">
		<![CDATA[
			<!--
			body {
				font-family: arial, sans-serif;
				font-size: 0.8em;
				height:100%;
			}
			body * {
				font-size: 100%;
			}
			h1 {
				font-weight:bold;
				font-size:1.5em;
				margin-bottom:0;
				margin-top:1px; 
			}
			h2 { 
				font-weight:bold;
				font-size:1.2em;
				margin-bottom:0; 
				color:#707070;
				margin-top:1px;
			}
			h3 {
				font-weight:bold;
				font-size:1.2em;
				margin-bottom:0; 
				color:#000;
				margin-top:1px;
			}
			td, th {
				font-family: arial, sans-serif;
				font-size: 0.9em;
			}
			.header {
				font-weight: bold;
				font-size: 1.1em;
				border-bottom: 1px solid #dbe6de;
				color: #496b9b;
				background-color: #e5ecf9;
			}
			p.sml {
				font-size:0.8em;
				margin-top:0; 
			}
			.data {
				border-collapse:collapse;
				border:1px solid #b0b0b0;
				margin-top:3px;
				width:100%;
				padding:5em;
			}
			.data td {
				border-bottom:1px solid #b0b0b0;
				text-align:left;
				padding:3px;
			}
			.sortup {
				background-position: right center;
				background-image: url(sortup.gif);
				background-repeat: no-repeat;
				font-style:italic;
				white-space:pre;
			}
			.sortdown {
				background-position: right center;
				background-image: url(sortdown.gif);
				background-repeat: no-repeat;
				font-style:italic;
				white-space:pre;
			}
			table.copyright {
				width:100%;
				border-top:1px solid #3366CC;
				margin-top:1em;
				text-align:center;
				padding-top:1em;
				vertical-align:top;
			}
			.copyright {
				color: #6F6F6F;
				font-size: 0.8em;
			}
			-->
		]]>
		</style>
		<script language="JavaScript">
		<![CDATA[
			var selectedColor = "blue";
			var defaultColor = "black";
			var hdrRows = 1;
			var numeric = '..';
			var desc = '..';
			var html = '..';
			var freq = '..';
			
			function initXsl(tabName,fileType) {
				hdrRows = 1;
				
				if(fileType=="sitemap") {
					numeric = ".3.";
					desc = ".1.";
					html = ".0.";
					freq = ".2.";
					initTable(tabName);
					setSort(tabName, 3, 1);
				} else {
					desc = ".1.";
					html = ".0.";
					initTable(tabName);
					setSort(tabName, 1, 1);
				}
				
				var theURL = document.getElementById("head1");
				theURL.innerHTML += ' ' + location;
				document.title += ': ' + location;
			}
			
			function initTable(tabName) {
				var theTab = document.getElementById(tabName);
				for(r=0;r<hdrRows;r++)
					for(c=0;c<theTab.rows[r].cells.length;c++)
						if((r+theTab.rows[r].cells[c].rowSpan)>hdrRows)
							hdrRows=r+theTab.rows[r].cells[c].rowSpan;
				for(r=0;r<hdrRows; r++){
					colNum = 0;
					for(c=0;c<theTab.rows[r].cells.length;c++, colNum++){
						if(theTab.rows[r].cells[c].colSpan<2){
							theCell = theTab.rows[r].cells[c];
							rTitle = theCell.innerHTML.replace(/<[^>]+>|&nbsp;/g,'');
							if(rTitle>""){
								theCell.title = "Change sort order for " + rTitle;
								theCell.onmouseover = function(){setCursor(this, "selected")};
								theCell.onmouseout = function(){setCursor(this, "default")};
								var sortParams = 15; // bitmapped: numeric|desc|html|freq
								if(numeric.indexOf("."+colNum+".")>-1) sortParams -= 1;
								if(desc.indexOf("."+colNum+".")>-1) sortParams -= 2;
								if(html.indexOf("."+colNum+".")>-1) sortParams -= 4;
								if(freq.indexOf("."+colNum+".")>-1) sortParams -= 8;
								theCell.onclick = new Function("sortTable(this,"+(colNum+r)+","+hdrRows+","+sortParams+")");
							}
						} else {
							colNum = colNum+theTab.rows[r].cells[c].colSpan-1;
						}
					}
				}
			}
			
			function setSort(tabName, colNum, sortDir) {
				var theTab = document.getElementById(tabName);
				theTab.rows[0].sCol = colNum;
				theTab.rows[0].sDir = sortDir;
				if (sortDir) 
					theTab.rows[0].cells[colNum].className='sortdown'
				else
					theTab.rows[0].cells[colNum].className='sortup';
			}
			
			function setCursor(theCell, mode){
				rTitle = theCell.innerHTML.replace(/<[^>]+>|&nbsp;|\W/g,'');
				if(mode=="selected"){
					if(theCell.style.color!=selectedColor) 
						defaultColor = theCell.style.color;
					theCell.style.color = selectedColor;
					theCell.style.cursor = "hand";
					window.status = "Click to sort by '"+rTitle+"'";
				} else {
					theCell.style.color = defaultColor;
					theCell.style.cursor = "";
					window.status = "";
				}
			}
			
			function sortTable(theCell, colNum, hdrRows, sortParams){
				var typnum = !(sortParams & 1);
				sDir = !(sortParams & 2);
				var typhtml = !(sortParams & 4);
				var typfreq = !(sortParams & 8);
				var tBody = theCell.parentNode;
				while((tBody.nodeName!="TBODY") && (tBody.nodeName!="TABLE")) {
					tBody = tBody.parentNode;
				}
				var tabOrd = new Array();
				if(tBody.rows[0].sCol==colNum) sDir = !tBody.rows[0].sDir;
				if (tBody.rows[0].sCol>=0)
					tBody.rows[0].cells[tBody.rows[0].sCol].className='';
				tBody.rows[0].sCol = colNum;
				tBody.rows[0].sDir = sDir;
				if (sDir) 
					tBody.rows[0].cells[colNum].className='sortdown'
				else
					tBody.rows[0].cells[colNum].className='sortup';
				for(i=0,r=hdrRows;r<tBody.rows.length;i++,r++){
					colCont = tBody.rows[r].cells[colNum].innerHTML;
					if(typhtml) colCont = colCont.replace(/<[^>]+>/g,'');
					if(typnum) {
						colCont*=1;
						if(isNaN(colCont)) colCont = 0;
					}
					if(typfreq) {
						switch(colCont.toLowerCase()) {
							case "always":  { colCont=0; break; }
							case "hourly":  { colCont=1; break; }
							case "daily":   { colCont=2; break; }
							case "weekly":  { colCont=3; break; }
							case "monthly": { colCont=4; break; }
							case "yearly":  { colCont=5; break; }
							case "never":   { colCont=6; break; }
						}
					}
					tabOrd[i] = [r, tBody.rows[r], colCont];
				}
				tabOrd.sort(compRows);
				for(i=0,r=hdrRows;r<tBody.rows.length;i++,r++){
					tBody.insertBefore(tabOrd[i][1],tBody.rows[r]);
				} 
				window.status = ""; 
			}
			
			function compRows(a, b){
				if(sDir){
					if(a[2]>b[2]) return -1;
					if(a[2]<b[2]) return 1;
				} else {
					if(a[2]>b[2]) return 1;
					if(a[2]<b[2]) return -1;
				}
				return 0;
			}
		]]>
		</script>

	</head>

	<!-- Store in $fileType if we are in a sitemap or in a siteindex -->
	<xsl:variable name="fileType">
		<xsl:choose>
			<xsl:when test="//sitemap:url">sitemap</xsl:when>
			<xsl:otherwise>siteindex</xsl:otherwise>
		</xsl:choose>      
	</xsl:variable>            

	<!-- Body -->
	<body onLoad="initXsl('table0','{$fileType}');">  

	<!-- Text and table -->
		<h1 id="head1">Sitemap</h1>        
		<xsl:choose>
			<xsl:when test="$fileType='sitemap'"><xsl:call-template name="sitemapTable"/></xsl:when>
			<xsl:otherwise><xsl:call-template name="siteindexTable"/></xsl:otherwise>
		</xsl:choose>

		<!-- Copyright notice -->          
		<br/>
		<table class="copyright" id="table_copyright">
		<tr>
			<td>
				<p>Sitemap 0.90: <a href="http://www.sitemaps.org">Sitemaps.org</a> - <a href="https://www.google.com/webmasters/sitemaps/stats">My Google Sitemaps</a> - <a href="http://sitemaps.blogspot.com/">Blog</a><br/>
				Sitemaps Stylesheets v1.6: (c) 2005-2007 <a href="http://sourceforge.net/projects/gstoolbox">Project</a></p>
			</td>
		</tr>
		</table>
	</body>
	</html>
	</xsl:template>     

	<!-- siteindexTable template -->
	<xsl:template name="siteindexTable">
		<h2>Number of sitemaps in this Sitemap Index: <xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"></xsl:value-of></h2>          
		<p class="sml">Click on the table headers to change sorting.</p>
		<table border="1" width="100%" class="data" id="table1">
		<tr class="header">
			<td>Sitemap URL</td>
			<td>Last modification date</td>
		</tr>
		<xsl:apply-templates select="sitemap:sitemapindex/sitemap:sitemap">
			<xsl:sort select="sitemap:lastmod" order="descending"/>              
		</xsl:apply-templates>  
		</table>            
	</xsl:template>  
  
	<!-- sitemapTable template -->  
	<xsl:template name="sitemapTable">
		<h2>Number of URLs in this Sitemap: <xsl:value-of select="count(sitemap:urlset/sitemap:url)"></xsl:value-of></h2>
		<p class="sml">Click on the table headers to change sorting.</p>
		<table border="1" width="100%" class="data" id="table0">
		<tr class="header">
			<td>Sitemap URL</td>
			<!-- <td>Format</td> -->
			<td>Last modification date</td>
			<td>Change freq.</td>
			<td>Priority</td>
		</tr>
		<xsl:apply-templates select="sitemap:urlset/sitemap:url">
			<xsl:sort select="sitemap:priority" order="descending"/>              
		</xsl:apply-templates>
		</table>  
	</xsl:template>    
  
	<!-- sitemap:url template -->  
	<xsl:template match="sitemap:url">
		<tr>  
			<td>
				<xsl:variable name="sitemapURL"><xsl:value-of select="sitemap:loc"/></xsl:variable>  
				<a href="{$sitemapURL}" target="_blank" ref="nofollow"><xsl:value-of select="$sitemapURL"></xsl:value-of></a>
			</td>
			<!-- <td><xsl:value-of select="geo:geo/geo:format"/></td> -->
			<td><xsl:value-of select="sitemap:lastmod"/></td>
			<td><xsl:value-of select="sitemap:changefreq"/></td>
			<td><xsl:value-of select="sitemap:priority"/></td>
		</tr>  
	</xsl:template>
  
	<!-- sitemap:sitemap template -->
	<xsl:template match="sitemap:sitemap">
		<tr>  
			<td>        
				<xsl:variable name="sitemapURL"><xsl:value-of select="sitemap:loc"/></xsl:variable>  
				<a href="{$sitemapURL}"><xsl:value-of select="$sitemapURL"></xsl:value-of></a>
			</td>
			<td><xsl:value-of select="sitemap:lastmod"/></td>
		</tr>  
	</xsl:template>  
  
</xsl:stylesheet>
