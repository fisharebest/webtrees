<?php
/**
 * Module help text.
 *
 * This file is included from the application help_text.php script.
 * It simply needs to set $title and $text for the help topic $help_topic
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 * @version $Id$
 */

if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {
case 'add_faq_item':
	$title=i18n::translate('Add FAQ item');
	$text=i18n::translate('This option will let you add an item to the FAQ page.');
	break;

case 'add_faq_header':
	$title=i18n::translate('FAQ header');
	$text=i18n::translate('This is the title or subject of the FAQ item.<br /><br />What you enter here can be formatted. HTML tags such as &lt;b&gt; and &lt;br /&gt; are allowed, as are HTML entities such as &amp;amp; and &amp;nbsp;.  HTML tags other than &lt;br /&gt; are probably not very useful in the FAQ title and should be avoided.<br /><br />HTML entities are a very easy way to add special characters to your FAQ titles and text.  You can use symbolic names, decimal numbers, or hexadecimal numbers.  A complete list of HTML entities, their coding, and their representation by your browser can be found here:  <a href="http://htmlhelp.com/reference/html40/entities/" target="_blank">HTML entity lists</a><br /><br />On occasion, you may need to show a Tilde character&nbsp;&nbsp;<b>&#x7E;</b>&nbsp;&nbsp;or a Number Sign&nbsp;&nbsp;<b>&#x23;</b>&nbsp;&nbsp;in your URLs or text.  These characters have a special meaning to the <b>webtrees</b> Help system and can only be entered in their hexadecimal or decimal form.  Similarly, the&nbsp;&nbsp;<b>&lt;</b>&nbsp;&nbsp;and&nbsp;&nbsp;<b>&gt;</b>&nbsp;&nbsp;characters that usually enclose HTML tags must be entered in their hexadecimal or decimal forms if they are to be treated as normal text instead of signalling an HTML tag.<ul><li><b>&amp;&#x23;35;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x23;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x23;</b></li><li><b>&amp;&#x23;60;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3C;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3C;</b></li><li><b>&amp;&#x23;62;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3E;</b></li><li><b>&amp;&#x23;126;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x7E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x7E;</b></li></ul>There is a&nbsp;&nbsp;<b>&amp;tilde;</b>&nbsp;&nbsp;HTML entity, but this symbol is not interpreted as a Tilde when coded in URLs.<br /><br />You can insert references to entries in the language files or to values of global variables.  Examples: <ul><li><b>&#x23;pgv_lang[add_to_cart]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the language variable "Add to Clippings Cart", and if it were to appear in this field, would show as <b>Add to Clippings Cart</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;factarray[AFN]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the Fact name $factarray["AFN"], and if it were to appear in this field, would show as <b>Ancestral File Number (AFN)</b> when the FAQ list is viewed in the current language. </li><li><b>&#x23;WT_VERSION&#x23;&nbsp;&#x23;WT_VERSION_RELEASE&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the constant WT_VERSION, a space, and a reference to the constant WT_VERSION_RELEASE, and if they were to appear in this field, would show as <b>#WT_VERSION#&nbsp;#WT_VERSION_RELEASE#</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;GLOBALS[GEDCOM]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM, which is the name of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM]#</b>.</li><li><b>&#x23;GLOBALS[GEDCOM_TITLE]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM_TITLE, which is the title of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM_TITLE]#</b>.</li></ul><br />This feature is useful when you wish to create FAQ lists that are different for each language your site supports.  You should put your customized FAQ list titles and entries into the <i>languages/extra.xx.php</i> files (<i>xx</i> is the code for each language), using the following format:<br />$pgv_lang["faq_title1"] = "This is a sample FAQ title";<br />$pgv_lang["faq_body1"] = "This is a sample FAQ body.";');
	break;

case 'add_faq_body':
	$title=i18n::translate('FAQ body');
	$text=i18n::translate('The text of the FAQ item is entered here.<br /><br />The text can be formatted. HTML tags such as &lt;b&gt; and &lt;br /&gt; are allowed, as are HTML entities such as &amp;amp; and &amp;nbsp;.<br /><br />HTML entities are a very easy way to add special characters to your FAQ titles and text.  You can use symbolic names, decimal numbers, or hexadecimal numbers.  A complete list of HTML entities, their coding, and their representation by your browser can be found here:  <a href="http://htmlhelp.com/reference/html40/entities/" target="_blank">HTML entity lists</a><br /><br />On occasion, you may need to show a Tilde character&nbsp;&nbsp;<b>&#x7E;</b>&nbsp;&nbsp;or a Number Sign&nbsp;&nbsp;<b>&#x23;</b>&nbsp;&nbsp;in your URLs or text.  These characters have a special meaning to the <b>webtrees</b> Help system and can only be entered in their hexadecimal or decimal form.  Similarly, the&nbsp;&nbsp;<b>&lt;</b>&nbsp;&nbsp;and&nbsp;&nbsp;<b>&gt;</b>&nbsp;&nbsp;characters that usually enclose HTML tags must be entered in their hexadecimal or decimal forms if they are to be treated as normal text instead of signalling an HTML tag.<ul><li><b>&amp;&#x23;35;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x23;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x23;</b></li><li><b>&amp;&#x23;60;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3C;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3C;</b></li><li><b>&amp;&#x23;62;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3E;</b></li><li><b>&amp;&#x23;126;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x7E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x7E;</b></li></ul>There is a&nbsp;&nbsp;<b>&amp;tilde;</b>&nbsp;&nbsp;HTML entity, but this symbol is not interpreted as a Tilde when coded in URLs.<br /><br />You can insert references to entries in the language files or to values of global variables.  Examples: <ul><li><b>&#x23;pgv_lang[add_to_cart]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the language variable "Add to Clippings Cart", and if it were to appear in this field, would show as <b>Add to Clippings Cart</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;factarray[AFN]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the Fact name $factarray["AFN"], and if it were to appear in this field, would show as <b>Ancestral File Number (AFN)</b> when the FAQ list is viewed in the current language. </li><li><b>&#x23;WT_VERSION&#x23;&nbsp;&#x23;WT_VERSION_RELEASE&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the constant WT_VERSION, a space, and a reference to the constant WT_VERSION_RELEASE, and if they were to appear in this field, would show as <b>#WT_VERSION#&nbsp;#WT_VERSION_RELEASE#</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;GLOBALS[GEDCOM]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM, which is the name of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM]#</b>.</li><li><b>&#x23;GLOBALS[GEDCOM_TITLE]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM_TITLE, which is the title of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM_TITLE]#</b>.</li></ul><br />This feature is useful when you wish to create FAQ lists that are different for each language your site supports.  You should put your customized FAQ list titles and entries into the <i>languages/extra.xx.php</i> files (<i>xx</i> is the code for each language), using the following format:<br />$pgv_lang["faq_title1"] = "This is a sample FAQ title";<br />$pgv_lang["faq_body1"] = "This is a sample FAQ body.";');
	break;

case 'add_faq_order':
	$title=i18n::translate('FAQ position');
	$text=i18n::translate('This field controls the order in which the FAQ items are displayed.<br /><br />You do not have to enter the numbers sequentially.  If you leave holes in the numbering scheme, you can insert other items later.  For example, if you use the numbers 1, 6, 11, 16, you can later insert items with the missing sequence numbers.  Negative numbers and zero are allowed, and can be used to insert items in front of the first one.<br /><br />When more than one FAQ item has the same position number, only one of these items will be visible.');
	break;

case 'add_faq_visibility':
	$title=i18n::translate('FAQ visibility');
	$text=i18n::translate('You can determine whether this FAQ will be visible regardless of GEDCOM, or whether it will be visible only to the current GEDCOM.<br /><ul><li><b>ALL</b>&nbsp;&nbsp;&nbsp;The FAQ will appear in all FAQ lists, regardless of GEDCOM.</li><li><b>%s</b>&nbsp;&nbsp;&nbsp;The FAQ will appear only in the currently active GEDCOM\'s FAQ list.</li></ul>', WT_GEDCOM);
	break;

case 'delete_faq_item':
	$title=i18n::translate('Delete FAQ item');
	$text=i18n::translate('This option will let you delete an item from the FAQ page');
	break;

case 'edit_faq_item':
	$title=i18n::translate('Edit FAQ item');
	$text=i18n::translate('This option will let you edit an item on the FAQ page.');
	break;

case 'movedown_faq_item':
	$title=i18n::translate('Move FAQ item down');
	$text=i18n::translate('This option will let you move an item downwards on the FAQ page.<br /><br />Each time you use this option, the FAQ Position number of this item is increased by one.  You can achieve the same effect by editing the item in question and changing the FAQ Position field.  When more than one FAQ item has the same position number, only one of these items will be visible.');
	break;

case 'moveup_faq_item':
	$title=i18n::translate('Move FAQ item up');
	$text=i18n::translate('This option will let you move an item upwards on the FAQ page.<br /><br />Each time you use this option, the FAQ Position number of this item is reduced by one.  You can achieve the same effect by editing the item in question and changing the FAQ Position field.  When more than one FAQ item has the same position number, only one of these items will be visible.');
	break;

}
