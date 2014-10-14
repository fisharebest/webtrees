<?php
// Log out from the current session
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

use WT\Auth;
use WT\Log;

define('WT_SCRIPT_NAME', 'logout.php');
require './includes/session.php';

if (Auth::id()) {
	Log::addAuthenticationLog('Logout: ' . Auth::user()->getUserName() . '/' . Auth::user()->getRealName());
	Auth::logout();
}

header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH);
