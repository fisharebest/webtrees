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

/**
 * Initialize autocomplete elements.
 *
 * @param {string} selector
 */
export function autocomplete(selector) {
  // Use typeahead/bloodhound for autocomplete
  $(selector).each(function () {
    const that = this;
    $(this).typeahead(null, {
      display: 'value',
      limit: 10,
      minLength: 2,
      source: new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
          url: this.dataset.wtAutocompleteUrl,
          replace: function (url, uriEncodedQuery) {
            const symbol = (url.indexOf('?') > 0) ? '&' : '?';
            if (that.dataset.wtAutocompleteExtra === 'SOUR') {
              let row_group = that.closest('.wt-nested-edit-fields').previousElementSibling;
              while (row_group.querySelector('select') === null) {
                row_group = row_group.previousElementSibling;
              }
              const element = row_group.querySelector('select');
              const extra = element.options[element.selectedIndex].value.replace(/@/g, '');
              return url + symbol + 'query=' + uriEncodedQuery + '&extra=' + encodeURIComponent(extra);
            }
            return url + symbol + 'query=' + uriEncodedQuery;
          }
        }
      })
    });
  });
}

