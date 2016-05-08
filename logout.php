<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

define('WT_SCRIPT_NAME', 'logout.php');
require './includes/session.php';

if (Auth::id()) {
	Log::addAuthenticationLog('Logout: ' . Auth::user()->getUserName() . '/' . Auth::user()->getRealName());
	Auth::logout();
	FlashMessages::addMessage(I18N::translate('You have signed out.'), 'info');
}

header('Location: ' . WT_BASE_URL);
