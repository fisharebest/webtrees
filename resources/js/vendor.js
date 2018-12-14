/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

import $ from "jquery";
window.$ = window.jQuery = $;

import "popper.js";
import "bootstrap";
import "datatables.net";

window.Bloodhound = require("corejs-typeahead/dist/bloodhound.min.js");
import "corejs-typeahead";

import "datatables.net-bs4";

// See https://github.com/RubaXa/Sortable/issues/1229
window.Sortable = require('sortablejs');

import "select2";

import "moment";
import "chart.js";

import "jquery-colorbox";

import "wheelzoom";

import "leaflet";
import "leaflet-providers";
window.GeoSearch = require("leaflet-geosearch");
import "leaflet.markercluster";
import "beautifymarker";
