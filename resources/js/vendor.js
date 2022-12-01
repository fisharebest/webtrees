/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

import $ from 'jquery';

import '@popperjs/core';
import { Alert, Button, Carousel, Collapse, Dropdown, Modal, Offcanvas, Popover, ScrollSpy, Tab, Toast, Tooltip } from 'bootstrap';
window.bootstrap = {
  Alert: Alert,
  Button: Button,
  Carousel: Carousel,
  Collapse: Collapse,
  Dropdown: Dropdown,
  Modal: Modal,
  Offcanvas: Offcanvas,
  Popover: Popover,
  ScrollSpy: ScrollSpy,
  Tab: Tab,
  Toast: Toast,
  Tooltip: Tooltip,
};

import 'datatables.net';

// Just import the subset of icons that we use in resources/views/icons/
import { dom, library } from '@fortawesome/fontawesome-svg-core';
import {
  // For resources/views/icons/*
  faBell, faCopy, faEnvelope, faFile, faFileAlt, faFileImage, faFolder, faKeyboard,
  faMinusSquare, faPlusSquare, faStar, faStickyNote, faTrashAlt, faUser
} from '@fortawesome/free-regular-svg-icons';
import {
  // For resources/views/icons/*
  faArrowDown, faArrowLeft, faArrowRight, faArrowUp, faArrowsAltV, faBan, faBars, faCalendar,
  faCaretDown, faCaretUp, faCheck, faCodeBranch, faCompress, faDownload, faExclamationTriangle,
  faExpand, faGenderless, faGripHorizontal, faGripLines, faHistory, faInfoCircle, faLanguage,
  faLink, faList, faLock, faMagic, faMap, faMapMarkerAlt, faMars, faMedkit, faPaintBrush, faPause,
  faPencilAlt, faPlay, faPlus, faPuzzlePiece, faQuestionCircle, faRedo, faSearch, faSearchLocation,
  faSearchMinus, faSearchPlus, faShareAlt, faSitemap, faSortAmountDown, faStepForward, faStop,
  faSyncAlt, faTags, faThList, faThumbtack, faTimes, faTransgender, faTree, faUndo, faUniversity,
  faUnlink, faUpload, faUsers, faVenus, faWrench,
  // For the BeautifyMarker library
  faBabyCarriage, faBullseye, faHome, faIndustry, faInfinity, faStarOfDavid, faWater
} from '@fortawesome/free-solid-svg-icons';
import 'corejs-typeahead';

import 'datatables.net-bs5';

import Sortable from 'sortablejs';

import TomSelect from 'tom-select/dist/js/tom-select.base.js';
TomSelect.define('caret_position', require('tom-select/dist/js/plugins/caret_position.js'));
TomSelect.define('clear_button', require('tom-select/dist/js/plugins/clear_button.js'));
TomSelect.define('dropdown_input', require('tom-select/dist/js/plugins/dropdown_input.js'));
TomSelect.define('remove_button', require('tom-select/dist/js/plugins/remove_button.js'));
TomSelect.define('virtual_scroll', require('tom-select/dist/js/plugins/virtual_scroll.js'));

window.TomSelect = TomSelect;

import 'hideshowpassword';

import 'moment';

import 'jquery-colorbox';
import 'pinch-zoom-element';

import 'leaflet';
import 'leaflet.markercluster';
import 'beautifymarker';
import 'leaflet-control-geocoder';
import 'leaflet.control.layers.tree';
import 'leaflet-bing-layer';

window.$ = window.jQuery = $;

library.add(
  // For resources/views/icons/*
  faBell, faCopy, faEnvelope, faFile, faFileAlt, faFileImage, faFolder, faKeyboard,
  faMap, faMinusSquare, faPlusSquare, faStar, faStickyNote, faTags, faTrashAlt, faUser
);
library.add(
  // For resources/views/icons/*
  faArrowDown, faArrowLeft, faArrowRight, faArrowUp, faArrowsAltV, faBan, faBars, faCalendar,
  faCaretDown, faCaretUp, faCheck, faCodeBranch, faCompress, faDownload, faExclamationTriangle,
  faExpand, faGenderless, faGripHorizontal, faGripLines, faHistory, faInfoCircle, faLanguage,
  faLink, faList, faLock, faMagic, faMap, faMapMarkerAlt, faMars, faMedkit, faPaintBrush, faPause,
  faPencilAlt, faPlay, faPlus, faPuzzlePiece, faQuestionCircle, faRedo, faSearch, faSearchLocation,
  faSearchMinus, faSearchPlus, faShareAlt, faSitemap, faSortAmountDown, faStepForward, faStop,
  faSyncAlt, faTags, faThList, faThumbtack, faTimes, faTransgender, faTree, faUndo, faUniversity,
  faUnlink, faUpload, faUsers, faVenus, faWrench,
  // For the BeautifyMarker library
  faBabyCarriage, faBullseye, faHome, faIndustry, faInfinity, faStarOfDavid, faWater
);
dom.watch();

window.Bloodhound = require('corejs-typeahead/dist/bloodhound.min.js');

// See https://github.com/RubaXa/Sortable/issues/1229
// window.Sortable = require('sortablejs');
window.Sortable = Sortable;
