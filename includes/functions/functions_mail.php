<?php
/**
 * Mail specific functions
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
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
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_MAIL_PHP', '');

/**
 * this function is a wrapper to the php mail() function so that we can change settings globally
 * for more info on format="flowed" see: http://www.joeclark.org/ffaq.html
 * for deatiled info on MIME (RFC 1521) email see: http://www.freesoft.org/CIE/RFC/1521/index.htm
 */
function webtreesMail($to, $from, $subject, $message) {
	$SMTP_ACTIVE   =get_site_setting('SMTP_ACTIVE');
	$SMTP_HOST     =get_site_setting('SMTP_HOST');
	$SMTP_HELO     =get_site_setting('SMTP_HELO');
	$SMTP_FROM_NAME=get_site_setting('SMTP_FROM_NAME');
	$SMTP_PORT     =get_site_setting('SMTP_PORT');
	$SMTP_AUTH     =get_site_setting('SMTP_AUTH');
	$SMTP_AUTH_USER=get_site_setting('SMTP_AUTH_USER');
	$SMTP_AUTH_PASS=get_site_setting('SMTP_AUTH_PASS');
	$SMTP_SSL      =get_site_setting('SMTP_SSL');
	global $WT_STORE_MESSAGES, $TEXT_DIRECTION;
	$mailFormat = "plain";
	//$mailFormat = "html";
	//$mailFormat = "multipart";

	$mailFormatText = "text/plain";

	$boundary = "PGV-123454321-PGV"; //unique identifier for multipart
	$boundary2 = "PGV-123454321-PGV2";

	if ($TEXT_DIRECTION == "rtl") { // needed for rtl but we can change this to a global config
		$mailFormat = "html";
	}

	if ($mailFormat == "html") {
		$mailFormatText = "text/html";
	} else if ($mailFormat == "multipart") {
		$mailFormatText = "multipart/related; \n\tboundary=\"$boundary\""; //for double display use:multipart/mixed
	} else {
		$mailFormatText = "text/plain";
	}

	$extraHeaders = "From: $from\nContent-type: $mailFormatText;";

	if ($mailFormat != "multipart") {
		$extraHeaders .= "\tcharset=\"UTF-8\";\tformat=\"flowed\"\nContent-Transfer-Encoding: 8bit";
	}

	if ($mailFormat == "html" || $mailFormat == "multipart") {
		$extraHeaders .= "\nMime-Version: 1.0";
	}

	$extraHeaders .= "\n";


	if ($mailFormat == "html") {
		//wrap message in html
		$htmlMessage = "";
		$htmlMessage .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$htmlMessage .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" ".i18n::html_markup().">";
		$htmlMessage .= "<head>";
		$htmlMessage .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />";
		$htmlMessage .= "</head>";
		$htmlMessage .= "<body dir=\"$TEXT_DIRECTION\"><pre>";
		$htmlMessage .= $message; //add message
		$htmlMessage .= "</pre></body>";
		$htmlMessage .= "</html>";
		$message = $htmlMessage;
	} else if ($mailFormat == "multipart") {
		//wrap message in html
		$htmlMessage = "--$boundary\n";
		$htmlMessage .= "Content-Type: multipart/alternative; \n\tboundary=--$boundary2\n\n";
		$htmlMessage = "--$boundary2\n";
		$htmlMessage .= "Content-Type: text/plain; \n\tcharset=\"UTF-8\";\n\tformat=\"flowed\"\nContent-Transfer-Encoding: 8bit\n\n";
		$htmlMessage .= $message;
		$htmlMessage .= "\n\n--$boundary2\n";
		$htmlMessage .= "Content-Type: text/html; \n\tcharset=\"UTF-8\";\n\tformat=\"flowed\"\nContent-Transfer-Encoding: 8bit\n\n";
		$htmlMessage .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$htmlMessage .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" ".i18n::html_markup().">";
		$htmlMessage .= "<head>";
		$htmlMessage .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />";
		$htmlMessage .= "</head>";
		$htmlMessage .= "<body dir=\"$TEXT_DIRECTION\"><pre>";
		$htmlMessage .= $message; //add message
		$htmlMessage .= "</pre>";
		$htmlMessage .= "<img src=\"cid:wtlogo@wtserver\" alt=\"\" style=\"border: 0px; display: block; margin-left: auto; margin-right: auto;\" />";
		$htmlMessage .= "</body>";
		$htmlMessage .= "</html>";
		$htmlMessage .= "\n--$boundary2--\n";
		$htmlMessage .= "\n--$boundary\n";
		$htmlMessage .= getWTMailLogo();
		$htmlMessage .= "\n\n\n\n--$boundary--";
		$message = $htmlMessage;
	}
	// if SMTP mail is set active AND we have SMTP settings available, use the PHPMailer classes
	if ($SMTP_ACTIVE=='external'  && ( $SMTP_HOST && $SMTP_PORT ) ) {
		require_once WT_ROOT.'library/phpmailer/class.phpmailer.php';
		$mail_object = new PHPMailer();
		$mail_object->IsSMTP();
		$mail_object->SetLanguage('en','languages/');
		if ( $SMTP_AUTH && ( $SMTP_AUTH_USER && $SMTP_AUTH_PASS ) ) {
			$mail_object->SMTPAuth = $SMTP_AUTH;
			$mail_object->Username = $SMTP_AUTH_USER;
			$mail_object->Password = $SMTP_AUTH_PASS;
		}
		if ($SMTP_SSL=='ssl') {
			$mail_object->SMTPSecure = 'ssl';
		} else if ($SMTP_SSL=='tls') {
			$mail_object->SMTPSecure = 'tls';
		}
		$mail_object->Host = $SMTP_HOST;
		$mail_object->Port = $SMTP_PORT;
		$mail_object->Hostname = $SMTP_HELO;
		$mail_object->From = $from;
		if (!empty($SMTP_FROM_NAME) && $from!=$SMTP_AUTH_USER) {
			$mail_object->FromName = $SMTP_FROM_NAME;
			$mail_object->AddAddress($to);
		}
		else {
			$mail_object->FromName = $mail_object->AddAddress($to);
		}
		$mail_object->Subject = hex4email( $subject, 'UTF-8');
		$mail_object->ContentType = $mailFormatText;
		if ( $mailFormat != "multipart" ) {
			$mail_object->ContentType = $mailFormatText . '; format="flowed"';
			$mail_object->CharSet = 'UTF-8';
			$mail_object->Encoding = '8bit';
		}
		if ( $mailFormat == "html" || $mailFormat == "multipart" ) {
			$mail_object->AddCustomHeader( 'Mime-Version: 1.0' );
			$mail_object->IsHTML(true);
		}
		$mail_object->Body = $message;
		// attempt to send mail
		if ( ! $mail_object->Send() ) {
			echo 'Message was not sent.<br />';
			echo 'Mailer error: ' . $mail_object->ErrorInfo . '<br />';
			return;
		} else {
			// SMTP OK
			return;
		}
	} elseif ($SMTP_ACTIVE=='internal') {
		// use original PHP mail sending function	
		mail($to, hex4email($subject, 'UTF-8'), $message, $extraHeaders);
	}
}

function getWTMailLogo() {
// the following is a base64 encoded webtrees logo for use in html formatted email.
	$wtLogo =
"Content-Type: image/png;
	name=\"gedview.png\"
Content-Transfer-Encoding: base64
Content-ID: <wtlogo@wtserver>
Content-Description: gedview.png
Content-Location: gedview.png

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";

return $wtLogo;
}

/**
 * hex encode a string
 *
 * this function encodes a string in quoted_printable format
 * found at http://us3.php.net/bin2hex
 */
function hex4email ($string,$charset) {
	//-- check if the string has extended characters in it
	$str = utf8_decode($string);
	//-- if the strings are the same no conversion is necessary
	if ($str==$string) return $string;
	//-- convert to string into quoted_printable format
	$string = bin2hex ($string);
	$encoded = chunk_split($string, 2, '=');
	$encoded = preg_replace ("/=$/","",$encoded);
	$string = "=?$charset?Q?=" . $encoded . "?=";
	return $string;
}


function RFC2047Encode($string, $charset) {
	if (preg_match('/[^a-z ]/i', $string)) {
		$string = preg_replace('/([^a-z ])/ie', 'sprintf("=%02x", ord(StripSlashes("\\1")))', $string);
		$string = str_replace(' ', '_', $string);
		return "=?$charset?Q?$string?=";
	}
}

?>
