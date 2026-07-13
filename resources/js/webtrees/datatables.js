/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

import {initializeWhenReady} from './dom';


/**
 * Initialize column filter checkboxes within a table.
 *
 * @param {HTMLTableElement} element
 * @param {DataTable} dataTable
 */
function initializeFilterButtons(element, dataTable) {
    element.addEventListener('click', function (event) {
        const checkbox = event.target.closest('input[data-filter-column]');

        if (checkbox === null) {
            return;
        }

        // Deselect other options in the same group
        const group = checkbox.closest('.btn-group');
        if (group) {
            group.querySelectorAll('input[type="checkbox"]').forEach(function (sibling) {
                if (sibling !== checkbox) {
                    sibling.checked = false;
                }
            });
        }

        // Apply (or clear) this filter
        const filter = checkbox.checked ? checkbox.dataset.filterValue : '';
        const column = dataTable.column(parseInt(checkbox.dataset.filterColumn, 10));
        column.search(filter).draw();
    });
}

/**
 * Initialize the "show parents" toggle button within a table.
 *
 * @param {HTMLTableElement} element
 */
function initializeParentToggle(element) {
    element.addEventListener('click', function (event) {
        const toggle = event.target.closest('#btn-toggle-parents');

        if (toggle === null) {
            return;
        }

        element.querySelectorAll('.wt-individual-list-parents').forEach(function (parent) {
            if (parent.style.display === 'none') {
                parent.style.removeProperty('display');
            } else {
                parent.style.display = 'none';
            }
        });
    });
}

function initializeTables() {
    document.querySelectorAll('table.wt-datatables.d-none').forEach(function (element) {
        const dataTable = new DataTable(element);

        if (element.classList.contains('wt-table-individual') || element.classList.contains('wt-table-family')) {
            initializeFilterButtons(element, dataTable);
            initializeParentToggle(element);
        }

        // DataTables start hidden to prevent FOUC.
        element.classList.remove('d-none');
    });
}

/**
 * Configure DataTables and initialize matching tables on initial load and DOM updates.
 */
export function initializeDatatables() {
    DataTable.ext.oSort['text-asc'] = (x, y) => x.localeCompare(y, document.documentElement.lang, {sensitivity: 'base'});
    DataTable.ext.oSort['text-desc'] = (x, y) => y.localeCompare(x, document.documentElement.lang, {sensitivity: 'base'});

    initializeWhenReady(() => initializeTables());
}
