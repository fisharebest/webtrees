<?php
// UTF-8 versions of PHP string functions
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

function utf8_substr($string, $pos, $len=PHP_INT_MAX) {
	if ($len<0) {
		return '';
	}
	$strlen=strlen($string);
	if ($pos==0) {
		$start=0;
	} elseif ($pos>0) {
		$start=0;
		while ($pos>0 && $start<$strlen) {
			++$start;
			while ($start<$strlen && (ord($string[$start]) & 0xC0) == 0x80) {
				++$start;
			}
			--$pos;
		}
	} else {
		$start=$strlen-1;
		do {
			--$start;
			while ($start && (ord($string[$start]) & 0xC0) == 0x80) {
				--$start;
			}
			++$pos;
		} while ($start && $pos<0);
	}
	if ($len==PHP_INT_MAX || $len<0) {
		return substr($string, $start);
	}
	$end=$start;
	while ($len>0) {
		++$end;
		while ($end<$strlen && (ord($string[$end]) & 0xC0) == 0x80) {
			++$end;
		}
		--$len;
	}
	return substr($string, $start, $end-$start);
}

function utf8_strlen($string) {
	$pos=0;
	$len=strlen($string);
	$utf8_len=0;
	while ($pos<$len) {
		if ((ord($string[$pos]) & 0xC0) != 0x80) {
			++$utf8_len;
		}
		++$pos;
	}
	return $utf8_len;
}
