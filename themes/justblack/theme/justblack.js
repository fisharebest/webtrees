/*
* JustBlack script for the JustBlack theme
* 
* webtrees: Web based Family History software
* Copyright (C) 2012 JustCarmen
*
* Derived from PhpGedView
* Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*
* $ Id: justblack.js for webtrees 1.4.0 2013-03-17 JustCarmen $
*/

//=========================================================================================================
//												FUNCTIONS
//=========================================================================================================

function jb_helpDialog(which, mod) {
	var url='help_text.php?help='+which+'&mod='+mod;
	$dialog = jQuery('<div style="max-height:375px; overflow-y:auto"><div><div class="loading-image"></div></div></div>')
		.dialog({						
			width: 500,
			height: 'auto',
			maxHeight: 500,
			modal: true,
			position: ['center', 'center'],
			autoOpen: true,			
			close: function(event, ui) {
				$dialog.remove();
			},
			open: function(event, ui) { 
				jQuery('.ui-widget-overlay').bind('click', function(){
					$dialog.dialog('close');	
				})
			}				
		}).load(url+' .helpcontent', function() {
			jQuery(this).dialog("option", "position", ['center', 'center'] );			
		});
	
	jQuery('.ui-dialog-title').load(url+' .helpheader');
	return false;
}

function jb_modalDialog(url, title) {
	jQuery(document).ajaxComplete(function() {
		jQuery('#config-dialog').parent('.ui-dialog').before('<div class="ui-widget-overlay" />');
	});
	// initialize the dialog box
	$tempdialog = jQuery('<div><div class="loading-image"></div></div>')
	.dialog({
		title: title,
		width: 400,
		height: 'auto',	
		autoOpen: true,
		close: function(event, ui) {
			$tempdialog.remove();
		}	
	});
	$dialog = jQuery('<div id="config-dialog" style="max-height:550px; overflow-y:auto"><div title="'+title+'"><div></div>')
		.dialog({
			title: title,			
			width: 'auto',
			height: 'auto',
			modal: false,			
			autoOpen: false,				
			open: function(event, ui) { 
				$tempdialog.dialog('close');
				if (jQuery('textarea.html-edit').length > 0) {					 
					$dialog.dialog( "option", "width", 700 );
					$dialog.dialog( "option", "height", 550 );
					if (!CKEDITOR.instances['html']) { 
						CKEDITOR.replace('html');
					}
				}
				jQuery('.ui-widget-overlay').bind('click', function(){					
					$dialog.dialog('close');
					jQuery(this).remove();	
				})			
			},
			close: function(event, ui) {					
				if (typeof CKEDITOR != 'undefined' && CKEDITOR.instances['html']) { CKEDITOR.instances.html.destroy(true);}
				$dialog.remove();
				jQuery('.ui-widget-overlay').remove();
			},
		}).load(url);
	
	// open the dialog box after some time. This is needed for the dialogbox to get loaded in center position.
	setTimeout(function() {
		$dialog.dialog('open');				
	}, 1000);
		   
	return false;
}

jQuery(window).resize(function() {
	jQuery(".ui-dialog-content").dialog("option", "position", ['center', 'center']);
});

function curPage() {
	var path = jQuery(location).attr('pathname').split("/");
	return path[path.length - 1];
}

function qstring(key, url) {
	if (url == null) var url = window.location.href;
	KeysValues = url.split(/[\?&]+/);
	for (i = 0; i < KeysValues.length; i++) {
		KeyValue= KeysValues[i].split("=");
		if (KeyValue[0] == key) {
			return KeyValue[1];
		}
	}
}

//=========================================================================================================
//												GENERAL
//=========================================================================================================
jQuery.noConflict();

jQuery(document).ready(function($){	

	/********************************************* COLORBOX MEDIA GALLERY ***********************************************/	
	$("body").on('click', 'a.gallery', function(event) {
		// Function for title correction
		function longTitles() {
			// correct long titles
			var tClass 		= $("#cboxTitle .title");
			var tID		  	= $("#cboxTitle");
			if (tClass.width() > tID.width() - 100) { // 100 because the width of the 4 buttons is 25px each
				tClass.css({"width" : tID.width() - 100, "margin-left" : "75px"});
			}
			if (tClass.height() > 25) { // 26 is 2 lines
				tID.css({"bottom" : 0});
				tClass.css({"height" : "26px"}); // max 2 lines.
			} else {
				tID.css({"bottom" : "6px"}); // set the value to vertically center a 1 line title.
				tClass.css({"height" : "auto"}); // set the value back;
			}			
		}
			
		// General (both images and pdf)
		$("a[type^=image].gallery, a[type$=pdf].gallery").colorbox({
			rel:      		"gallery",				
			current:		"",
			slideshow:		true,
			slideshowAuto:	false,
			slideshowSpeed: 3000,
			onLoad:			function() {
								$(".cboxNote, .pdf-layer").remove() // remove previous note or watermarks.
							},
			onComplete:		function() {				
								$(".cboxPhoto").wheelzoom();
								if($(".cboxPhoto").width() <= $("#cboxContent").width()) {
									$("#cboxLoadedContent").css('overflow-x', 'hidden');
								}
								$(".cboxPhoto img").on("click", function(e) {e.preventDefault();});								
								var note = $(this).data("obje-note");
								if(note != '') {
										$('#cboxContent').append('<div class="cboxNote">' + note);
										if($('.cboxPhoto').innerHeight() > $('#cboxContent').innerHeight()) {
											$('.cboxNote').css('width', $('.cboxNote').width() - 27);
										}
								}
								longTitles();																			
							}
		});
		
		// Add colorbox to images
		$("a[type^=image].gallery").colorbox({			
			photo:			true,
			scalePhotos:	false,			
			maxWidth:		"95%",
			maxHeight:		"95%",				
			fixed:			false,
			title:			function(){
								var img_title = jQuery(this).data("title");
								return "<div class=\"title\">" + img_title + "</div>";
							}
		});
		
		// default settings for all pdf's
		$("a[type$=pdf].gallery").colorbox({
			width:		"75%",
			height:		"90%",
			fixed:		true,				
			title:		function(){
							var pdf_title = $(this).data("title");
							pdf_title = '<div class="title">' + pdf_title;
							if(useWatermark == 0) pdf_title += ' &diams; <a href="' + $(this).attr("href") + '" target="_blank">' + fullPdfText + '</a>';
							pdf_title += '</div>';
							return pdf_title;
						}			
		});
		
		// use Google Docs Viewer for pdf's if theme option is set.
		if(useGviewer == 1) {
			$("a[type$=pdf].gallery").colorbox({
				scrolling:	false, // the gviewer has a scrollbar.
				html:		function(){
								var mid = qstring('mid', $(this).attr("href"));
								return '<iframe width="100%" height="100%" src="http://docs.google.com/viewer?url=' + WT_SERVER_NAME + WT_SCRIPT_PATH + WT_THEME_JUSTBLACK + 'pdfviewer.php?mid=' + mid + '&embedded=true"></iframe>';
							},
				onComplete: function() {
							if(useWatermark == 1) {
								var layerHeight = $('#cboxContent iframe').height();
								var layerWidth = $('#cboxContent iframe').width();
								$('#cboxLoadedContent')
									.append('<div class="pdf-menu"></div>' +
											'<div class="pdf-body">' +
												'<div class="pdf-watermark"><span class="text-right">' + WT_TREE_TITLE + '</span></div>' +												
											'</div>');
								$('.pdf-menu').css({
									 'width'	: layerWidth + 'px'							
								});
								$('.pdf-body').css({
									 'height'	: layerHeight - 37 +'px',
									 'width'	: layerWidth - 17 + 'px'							
								});	
								$('.pdf-watermark').css({
									 'margin-top'	: ((layerHeight - 37)/2) - 48 +'px'						
								});								
							}					
						}
			});
		}
		// use browsers default pdf viewer
		else {
			$("a[type$=pdf].gallery").colorbox({iframe:	true});		
		}		
		
		// Do not open the gallery when clicking on the mainimage on the individual page
		if($(this).parents("#indi_mainimage").length > 0) {
			$(this).colorbox({rel:"nofollow"});
		}
	});		
	
	/********************************************* TOOLTIPS ***********************************************/	
	// Tooltips for all title attributes	
	function add_tooltips() {
		$('*[title]').each(function() {
            var title = $(this).attr('title');
			$(this).on('click', function(){
				$(this).attr('title', title);	// some functions need the title attribute. Make sure it is filled when clicking the item.		
			});
        });	
	
		$(document).tooltip({
			items: '*[title]'
		});	
	}
	
	add_tooltips();	// needed when no ajaxcall is made on the particular page.
	$(document).ajaxComplete(function() { // be sure the tooltip is activated after a ajax call is made.
		add_tooltips();
	})
	
	/******************************************* DROPDOWN MENU *********************************************/	
	$('.dropdown > li').hover(function(){
		$(this).find('ul').show();
	}, function(){
		$(this).find('ul').hide();
	});
	
	// function to use with unsorted dropdownmenus like the fav-menu.
	function sortMenu(dropdownMenu) {
		var menu = $(dropdownMenu + ' .dropdown ul').children('li').get();
		menu.sort(function(a, b) {
			var val1 = $(a).text().toUpperCase();
			var val2 = $(b).text().toUpperCase();
			return (val1 < val2) ? -1 : (val1 > val2) ? 1 : 0;
		});
		$.each(menu, function(index, row) {
			$(dropdownMenu + ' .dropdown ul').append(row);
		});
	}
	
	/********************************************* MAIN MENU ***********************************************/		
	$('#main-menu').each(function(){
		$(this).find('li').hover(function(){	
			//show submenu	
			$(this).find('>ul').slideDown('slow');	
		},function () {	
			//hide submenu	
			$(this).find('>ul').hide();
		});
		
		var dTime = 1200;
		$(this).find('ul').each(function(){		
			$(this).find('li').hover(function() {			
				$(this).stop().animate({backgroundColor: '#808080'}, dTime)							
			}, function(){			
				$(this).stop().animate({backgroundColor: '#272727'}, dTime);											
			});
		});	
		
		// dynamic height of menubar
		var li_height = $(this).find('> li').height()			
		var height = $(this).find('> li > a').map(function(){
   			return $(this).height();
		});			
		var maxHeight=height[0];
		for (var i=0;i<height.length;i++) {
		 	maxHeight=Math.max(maxHeight, height[i]);
		}
		$('#topMenu').css('height', li_height + maxHeight);
		
		// No Gedcom submenu if there is just one gedcom
		if ($('#menu-tree ul li').length == 1) $('#menu-tree ul').remove();	
		
		// open admin in new browsertab			
		$(this).find('ul li#menu-admin a').attr('target','blank'); 			
	});
	
	/********************************************* LANGUAGE (FLAGS) MENU ******************************************/	
	$('#optionsmenu #lang-menu').each(function(){
		$(this).find('li').each(function(){	
			$(this).tooltip({
				position: {
					my: "center top-40",
					at: "center center"
				}
			});	
			$(this).click(function(){
				location.href = $(this).find('a').attr('href');
			});
			$(this).find('a.lang-active').removeClass().parent('li').addClass('lang-active');
		});
	});		
	
	/********************************************* FAV-MENU ******************************************/	
	var pageId = qstring('pid') || qstring('famid') || qstring('mid') || qstring('nid') || qstring('rid') || qstring('sid');
	var submenu = $('#fav-menu > ul ul');
	
	if (WT_USER_ID > 0 && typeof pageId != 'undefined') {
		var obj = submenu.find('li').not(':last');
		submenu.find('li:last a').addClass('addFav')
	}
	else {
		var obj = submenu.find('li');
	}
		
	obj.each(function(){
		var url = $(this).find('a').attr('href');
		var id = qstring('pid', url) || qstring('famid', url) || qstring('mid', url) || qstring('nid', url) || qstring('rid', url) || qstring('sid', url);		
		if(id == pageId) {	
			$(this).addClass('active');
			$('#menu-favorites > a').replaceWith($(this).html());
			$('.addFav').parent('li').remove();
		}		
	});
	
	obj.click(function(){
		$('#menu-favorites > a').replaceWith($(this).html());
	});
	
	sortMenu('#fav-menu');
	
	/**************************************** MODAL DIALOG BOXES ********************************************/		
	// replace default function with our justblack theme function (better dialog boxes)
	$(document).ajaxComplete(function() {
		$('[onclick^="helpDialog"]').each(function(){
			$(this).attr('onclick',function(index,attr){			
				return attr.replace('helpDialog', 'jb_helpDialog');				
			});		
		});
		
		$('[onclick^="modalDialog"], [onclick^="return modalDialog"]').each(function(){
			$(this).attr('onclick',function(index,attr){			
				return attr.replace('modalDialog', 'jb_modalDialog');				
			});		
		});		
	 })
	
	/********************************************* CUSTOM CONTACT LINK ***********************************************/	
	// custom contact link (in custom html block or news block for example). Give the link the class 'contact_link_admin');
	$('a.contact_link_admin').each(function() {
		var onclickItem = $('.contact_links a').attr('onclick')
		$(this).attr('onclick', onclickItem).wrap('<span class="contact_links">');		
	});	
	
	/********************************************* LOGIN FORM ***********************************************/	
	if ($('#login-page').length > 0) {						
		// login page styling
		$('#login-page #login-text b:first').wrap('<div id="login-page-title" class="subheaders ui-state-default">');	
		$('#login-page #login-page-title').prependTo('#login-page');
		$('#login-page #login-text br:first').remove();
		$('#login-page #login-text br:first').remove();
		$('#login-page #login-text, #login-page #login-box').wrapAll('<div id="login-page-block">');		
	}
	
	/********************************************* REGISTER FORM ***********************************************/	
	if ($('#login-register-page').length > 0) { 
		var title = $('#login-register-page h2').text();
		$('#login-register-page h2').remove();
		if (title != "") {
			$('<div id="login-register-page-title" class="subheaders ui-state-default">' + title + '</div>').prependTo('#login-register-page');
		}
		$('#login-register-page .largeError').removeAttr('class').css('font-weight', 'bold');
		$('#login-register-page .error').removeAttr('class');
		$('#login-register-page #register-text, #login-register-page #register-box').wrapAll('<div id="register-page-block">');
		
		$('#login-register-page #register-form label').each(function(){		
			$(this).after($(this).find('input'));
			$(this).after($(this).find('select'));		
			$(this).after($(this).find('textarea'));
		});
		$('#login-register-page #register-form textarea').before('<br />').attr('rows', '8');		
	}
	
	/************************************ EDIT USER PAGE **********************************************/
	if (curPage() == 'edituser.php') {
		var title = $('#edituser-page h2').text();
		$('#edituser-page h2').remove();
		$('<div id="edituser-page-title" class="subheaders ui-state-default">' + title + '</div>').prependTo('#edituser-page');		
		$('#edituser_submit').before('<hr class="clearfloat">');
		$('#edituser-table input:first[type=password]').each(function(){
			$(this).parent().wrapInner('<span class="pw-info">');
			$(this).prependTo($(this).parents('.value'));
		});
		
		if ($('#theme-menu').html() == "") {			
			$('select[name=form_theme]').parents('.value').prev('.label').remove();
			$('select[name=form_theme]').parents('.value').remove();
		}		
	}
	
	/************************************** HOMEPAGE AND MY PAGE ***********************************************/	
	
	// Icons for gedcom block on homepage and user welcome block on my page (these are bigger then the standard icons)
	var block = $('.gedcom_block_block, .user_welcome_block');
	block.find('.icon-indis').removeClass().addClass('icon-indi-big');
	block.find('.icon-pedigree').removeClass().addClass('icon-pedigree-big');
	block.find('.icon-mypage').removeClass().addClass('icon-mypage-big');
	block.find('a').css('font-size', '11px');
	
	// link change block styling. In the default styling the text does not fit in the block.
	$('#link_change_blocks a').after('<br />');	
	
	// gedcom and user favorites block
	$('.block .gedcom_favorites_block .action_header, .block .gedcom_favorites_block .action_headerF, .block .user_favorites_block .action_header, .block .user_favorites_block .action_headerF').each(function(){
		$(this).removeClass('person_box');
	});
	
	/************************************** INDIVIDUAL PAGE ***********************************************/	
	if (curPage() == 'individual.php') {
			
		// General
		$('<div class="divider">').appendTo('#tabs ul:first');
		$('#tabs li').each(function(){
			$(this).tooltip({
				position: {
					my: "center top+25",
					at: "center center"
				}				
			}); 
		});		
		
		$('#tabs a[title=lightbox]').on('click', function(){
			var tabindex = $(this).parent().attr('aria-controls');
			$('#' + tabindex).before('<div class="loading-image"></div>').hide();			
			$.ajax({
				complete:function(){
					$('#lightbox_content img.icon').each(function(){
						$(this).attr('src',function(index,attr){
							return attr.replace('modules_v3/lightbox/images', WT_CSS_URL + 'images/buttons');		  
						});
						$(this).css('padding-left', '5px');
					});	
					$('.loading-image').remove();
					$('#' + tabindex).show();				
				}				
			});			
		});
			
		if ($('#tabs a[title=lightbox]').parent('li').hasClass('ui-state-active')) {
			setTimeout(function() {
				$('#tabs a[title=lightbox]').trigger('click');		
			}, 10);			
		}	
	}
	
	/********************************************* MESSAGES.PHP*******************************************************/	 
	// correction. Popup is smaller than the input and textarea field.
	if (curPage() == 'message.php') {
		$('input[name=subject]').attr('size', '45');
		$('textarea[name=body]').attr('cols', '43');	
	}
	
	/************************************************ HOURGLASS CHART *****************************************************/
	if (curPage() == 'hourglass.php' && qstring('show_spouse') == '1') {
		function styleSB(){			
			 $.ajax({
				cache:true,
				async:true,
				success:function(){
					$('.person_box_template.style1').each(function(){
						var width = $(this).width();
						if(width < 250) { // spouses boxes are smaller then the default ones.
							$(this)
								.addClass('spouse_box')
								.removeAttr('style') // css styling
								.closest('table').find('tr:first .person_box_template').css('border-bottom-style', 'dashed');											
						}
					});
				},
				complete:function(data) {						
					$('a[onclick*=ChangeDis]').on('click', function(event){	// needed for dynamic added arrow links.
						styleSB();
					});
					return data;				
				}				
			 });
		};
		$('a[onclick*=ChangeDis]').on('click', function(){	
			styleSB();
		});
		styleSB();		
	}
		
	/****************************** CHILDBOX (ON PEDIGREE CHART AND HOURGLASS CHART)***************************************/
	if (curPage() == 'pedigree.php' || curPage() == 'hourglass.php') {
		$('#hourglass_chart #childbox .name1').each(function(){
			$(this).appendTo($(this).parents('#childbox'));		
		});
		$('#hourglass_chart #childbox table').remove();
		$('#hourglass_chart #childbox').removeAttr('style');			
		
		$('#childbox').each(function(){
			var childbox = $(this);
			childbox.find('br').remove();
			childbox.wrapInner('<ul>');
			childbox.find('a').wrap('<li>');		
			childbox.find('ul > span').wrap('<li class="cb_title">');
			childbox.find('span.name1').each(function(){
				var sChar = '<';
				var str = $(this).text();
				if (str.indexOf(sChar) > -1) {
					var newStr = str.replace(sChar, '');						
					$(this).text(newStr);
					$(this).parents('li').addClass('cb_child');
				}
			});
			
			var li_child = $('#hourglass_chart #childbox').parent().prev('table').find('.popup li.cb_child'); 
			li_child.each(function(){
				var child = $(this).text();
				$('#hourglass_chart #childbox li').each(function(){
					var str = $(this).text();
					if (str == child) {
						$(this).addClass('cb_child');
					}
					if ($(this).hasClass('cb_title')) {
						return false; // stop the loop
					}
				});
			});
			
			childbox.find('.cb_child').wrapAll('<li><ul>');		
			childbox.find(' > ul > li:not(:has(ul), .cb_title)').prepend('<span class="ui-icon ui-icon-triangle-1-e left">');
			childbox.find('.cb_child').prepend('<span class="ui-icon ui-icon-person left">');			    
		});
		
		if($('#hourglass_chart #childbox').length > 0) {
			var fTop = $('#footer').offset().top;
			var cTop = $('#hourglass_chart #childbox').offset().top;
			var cHeight = $('#hourglass_chart #childbox').outerHeight();
			var hMargin = cHeight - (fTop - cTop) + 60;
			
			if (hMargin > 0) {
				$('#hourglass_chart').css('margin-bottom', hMargin);
			}	
		}
	}
	
	/************************************ FANCHART PAGE (POPUPS)***************************************/
	if (curPage() == 'fanchart.php') {
		$('table.person_box td').each(function(){
			var content = $(this).html();
			$(this).parents('table').before('<div class="fanchart_box">' + content + '</div>').remove();			
		});
		
		$('.fanchart_box').each(function(){
			var fanbox = $(this);
			fanbox.find('br').remove();
			fanbox.wrapInner('<ul>');
			fanbox.find('a').wrap('<li>');
			fanbox.find('ul > span').wrap('<li class="fb_title">');
			fanbox.find('a.name1').each(function(){
				var sChar = '<';
				var str = $(this).text();
				if (str.indexOf(sChar) > -1) {
					var newStr = str.replace(sChar, '');						
					$(this).text(newStr);
					$(this).parents('li').addClass('fb_child');					
				}
			});
			fanbox.find('li:first').addClass('fb_indi');
			fanbox.find('.fb_child').prev('li').not('.fb_child').addClass('fb_parent');
			fanbox.find('.fb_child').appendTo($('.fb_child').prev('.fb_parent'));	
			fanbox.find('.fb_parent').each(function(){
				$(this).find('.fb_child').wrapAll('<ul>');
			});			
			fanbox.find(' > ul > li:not(.fb_title)').prepend('<span class="ui-icon ui-icon-triangle-1-e left">');
			fanbox.find('.fb_child').prepend('<span class="ui-icon ui-icon-person left">');	
		});	
	}	
	
	/************************************** TREE VIEW ***********************************************/	
	// load custom treeview stylesheet. Be sure it is loaded after treeview.css		
	if (curPage() == 'individual.php' || qstring('mod_action') == 'treeview') {
		$('#content a[name=tv_content]').after('<div class="loading-image"></div>');
		$.ajax({
			cache:false,
			async:false,
			beforeSend:function(){	
				$('.tv_out').hide();
				if (document.createStyleSheet) {
					document.createStyleSheet('' + WT_CSS_URL + 'treeview.css'); // For Internet Explorer
				} else {
					$('head').append('<link rel="stylesheet" type="text/css" href="' + WT_CSS_URL + 'treeview.css">');	
				}
			},
			complete:function(){								
				$('.tv_out').show(); 
			}			
		});
		$('#content .loading-image').remove();	
	}
	
	// tree view in charts block - load stylesheet in the block. because in the head it is overruled by the default one.
	$.ajax({
		complete:function(){
			$("div[id^=charts]").prepend('<link rel="stylesheet" type="text/css" href="' + WT_THEME_DIR + 'css/jb_treeview.css">');
		}
	});
	/************************************** FAMILY BOOK ***********************************************/		
	if (curPage() == 'familybook.php') {
		$('hr:last').remove(); // remove the last page-break line because it is just above the justblack divider.
	}	
	
	/************************************** MEDIALIST PAGE ********************************************/	
	if (curPage() == 'medialist.php') {	
		$('#medialist-page .list_table:eq(1)').each(function(){
			$(this).find('.list_table:last').addClass('list_table_controls');
		});
				
		// Medialist Menu - Moved from jb_lightbox.js
		$('#medialist-page #lightbox-menu').removeAttr('id').addClass('lightbox-menu');
		$('.lightbox-menu').parent('td').each(function(){
			$(this).wrapInner('<div class="lb-image_info">');
			$(this).find('.lightbox-menu').prependTo($(this));
		});			
		
		// change id's in classes for W3C validation (id's must be unique and they aren't). Put all li's in one ul (one menulist)
		$('.lightbox-menu').each(function(){		
			var lb_edit = $(this).find('#lb-image_edit').removeAttr('id').addClass('lb-image_edit');
			var lb_link = $(this).find('#lb-image_link').removeAttr('id').addClass('lb-image_link');
			var lb_view = $(this).find('#lb-image_view').removeAttr('id').addClass('lb-image_view');
			lb_edit.parent().append(lb_link, lb_view);
			$(this).find('.lb-menu').not('.lb-menu:first').remove();
		});
		
		$('.lightbox-menu ul.lb-menu li ul').wrap('<div class="popup">');
		
		$('.lightbox-menu ul.lb-menu > li').each(function(){
			var tooltip = $(this).find('> a').text();
			if($(this).hasClass('lb-image_link')) {
				$(this).find('.popup ul').prepend('<li class="lb-pop-title no-ui-icon">' + tooltip);							
			}
			else {
				if ($(this).hasClass('lb-image_edit')) {var pos = "right-18"}
				if ($(this).hasClass('lb-image_view')) {var pos = "left+15"}
				$(this).tooltip({
					position: {
						my: pos + " center-2",
						at: "center center"
					}
				});
				$(this).attr('title', tooltip);
			}
			$(this).find('> a').text('');
		});
		
		$('.lb-menu .lb-image_link').hover(function(){
			$(this).find('.popup').fadeIn('slow');
		},
		function(){
			$(this).find('.popup').fadeOut('slow');
		});
	
		$('.lightbox-menu').show();	
		
		// media link list
		$(".lb-image_info").each(function(){
			$(this).find('a[href^="individual.php"], a[href^="family.php"], a[href^="source.php"]').not('.fact_SOUR a').addClass("media_link")
			$(this).find('.media_link').next('br').remove();
			$(this).find('.media_link').wrapAll('<div class="media_link_list">');
		});
	}
	
	/************************************** MEDIAVIEWER PAGE ******************************************/
	if (curPage() == 'mediaviewer.php') {
		$('#media-tabs').find('.ui-widget-header').removeClass('ui-widget-header');
		$('#media-tabs ul').after('<div class="divider">');	
	}
	
	/********************************************* SMALL THUMBS *****************************************************/	
	// currently small thumbs (on the sourcepage for instance) are having a height of 40px and a width of auto by default.
	// This causes a messy listview.
	// In style.css the default height changed to 45px. Use this function to retrieve a cropped 60/45 (4:3) image. 	 
	// It would be better to do this on the server side, but then we have to mess with the core code.		 
	$('.media-list td img').each(function(){
		var obj = $(this);
		var src = obj.attr('src');
		var img = new Image();
		img.onload = function() {
			newWidth = 60;
			ratio = newWidth/this.width;
			newHeight = this.height * ratio;
			marginLeft = 0;
			if(newHeight < 45) {
				newHeight = 45;
				ratio = newHeight/this.height;
				newWidth = this.width * ratio;	
				marginLeft = -(newWidth - 60)/2;			
			}			
			obj.css({
				'width'  		: newWidth,
				'height' 		: newHeight,
				'margin-left'	: marginLeft			
			})
		}
		img.src = src;	
		$div = $('<div>').css({
			'width' 	: '60px',
			'height' 	: '45px',
			'display' 	: 'inline-block',
			'overflow' 	: 'hidden'		
		});
		obj.parent().wrap($div);
		obj.parents('td').css('text-align', 'center');		
	});
	
	/************************************** CLIPPINGS PAGE ********************************************/	
	if(qstring('mod') == 'clippings') {
		$('#content').addClass('clippings-page');
		$('.clippings-page li').prepend('<span class="ui-icon ui-icon-triangle-1-e left">');
	}

	/************************************** SEARCH PAGE ***********************************************/		
	if (curPage() == 'search.php') {
		var searchForm = $('#search-page form');
		var searchResult = $('#search-result-tabs');
		var titleBtn = $('#search-page h2').text();
		if (searchResult.length > 0) {			
			searchForm.hide();
			searchResult.each(function(){
				$(this).find('ul').append('<li id="search-btn" class="ui-state-default ui-corner-top"><a href="#search"><span>' + titleBtn);
				$(this).find('.ui-tabs-nav, .fg-toolbar').removeClass('ui-widget-header');
			});
			
			$('li#search-btn').on({
				mouseenter: function(){
					$(this).addClass('ui-state-hover');
				},
				mouseleave: function(){
					$(this).removeClass('ui-state-hover');
				},
				click: function(){
					$(this).addClass('ui-state-active');
					searchResult.fadeOut('slow');
					searchForm.fadeIn('slow');
				}
			});
		}
	}
	
	if (curPage() == 'search_advanced.php') {
		$('#search-page a[onclick^=addFields]').attr('onclick', 'addFields();return false;');
		var searchForm = $('#search-page form');
		var searchResult = $('#search-page .indi-list');
		var titleBtn = $('#search-page h2').text();	
		if(searchResult.length > 0) {
			searchForm.hide();
			searchResult.each(function(){
				$(this).find('.fg-toolbar').removeClass('ui-widget-header');
				var filters = $(this).find('.fg-toolbar div[class^=filtersH], .fg-toolbar .dt-clear:first').remove();
				$(this).find('.fg-toolbar div').wrapAll('<div class="fg-toolbar-border">');
				$(this).find('.fg-toolbar').prepend(filters);
				$(this).find('div[class^=filtersH]').append('<button id="search-btn" class="ui-state-default" type="button">' + titleBtn);			
			});
			
			$('#search-btn').on({
				click: function(){
					searchResult.fadeOut('slow');
					searchForm.fadeIn('slow');
				}
			});
		}
	}
	
	/************************************** FAQ PAGE ***********************************************/	
	if (qstring('mod') == 'faq') {
		$('#content').addClass('faq-page');
		$('.faq_title').addClass('ui-state-default');
		$('hr').remove();
		$('.faq_italic:first').css('padding', '10px 2px');
		$('.faq a, .faq_top a').addClass('scroll');
	}
	
	/************************************* PLACELIST PAGE *******************************************/
	if (curPage() === 'placelist.php') {
		$('#place-hierarchy').each(function(){
			$(this).find('.list_label').addClass('ui-state-default');
			$(this).find('.icon-place').remove();	
			$(this).find('.list_table li a').before('<span class="ui-icon ui-icon-triangle-1-e left">');
			$(this).find('table:first').prependTo('#places-tabs')
			$(this).find('#places-tabs .ui-widget-header').removeClass('ui-widget-header');
			$(this).find('#places-tabs ul.ui-tabs-nav').after('<div class="divider">');			
		});			
	}
	
	/************************************* OTHER *******************************************/	
	// Correction. On default pdf opens on the same page. We do not want to force users to use the browser back button.
	$('#reportengine-page form').attr("onsubmit", "this.target='_blank'");
	
		// styling of the lifespan module	
	$('.lifespan_people .icon-sex_m_9x9').parents('#inner div[id^="bar"]').css({'background-color':'#545454', 'border':'#dd6900 1px solid'});
	$('.lifespan_people .icon-sex_f_9x9').parents('#inner div[id^="bar"]').css({'background-color':'#8E8E8E', 'border':'#dd6900 1px solid'});
	$('.lifespan_people a.showit i.icon-sex_m_9x9, .lifespan_people a.showit i.icon-sex_f_9x9').hide();	
	
	// scroll to anchors
	jQuery(".scroll").click(function(event){		
		var id = jQuery(this).attr("href");
		var offset = 60;
		var target = jQuery(id).offset().top - offset;
		jQuery("html, body").animate({scrollTop:target}, 1000);
		event.preventDefault();
	});				
	
	// open all external links in new window/tab
	$("a[href^=http]").each(function(){
      if(this.href.indexOf(location.hostname) == -1) {
         $(this).attr({
            target: "_blank"
         });
      }
   })
	
});