'use strict';

import { requireDatasetValue, requireElement } from '../dom';

/**
 * Initialize the descendancy sidebar search and branch toggles.
 */
export function initializeDescendancySidebarPage (root) {
  root.querySelectorAll('form[data-wt-descendancy-sidebar]').forEach((form) => {
    if (!(form instanceof HTMLFormElement)) {
      throw new Error('Descendancy sidebar container must be a form element.');
    }

    if (form.dataset.wtDescendancySidebarInitialized === '1') {
      return;
    }

    form.dataset.wtDescendancySidebarInitialized = '1';

    const searchInput = requireElement(form, '#sb_desc_name', HTMLInputElement, 'descendancy search input');
    const content = requireElement(document, '#sb_desc_content', HTMLElement, 'descendancy results container');
    const searchUrl = requireDatasetValue(form, 'wtDescendancySearchUrl', 'descendancy search URL');
    const placeholder = requireDatasetValue(form, 'wtDescendancyPlaceholder', 'descendancy search placeholder');

    let timerId = 0;

    form.addEventListener('submit', (event) => {
      event.preventDefault();
    });

    searchInput.addEventListener('focus', () => {
      searchInput.select();
    });

    searchInput.addEventListener('blur', () => {
      if (searchInput.value === '') {
        searchInput.value = placeholder;
      }
    });

    searchInput.addEventListener('input', () => {
      if (timerId !== 0) {
        window.clearTimeout(timerId);
      }

      timerId = window.setTimeout(() => {
        const query = searchInput.value;

        if (query.length > 1) {
          window.webtrees.load(content, searchUrl + encodeURIComponent(query));
        }
      }, 500);
    });

    content.addEventListener('click', (event) => {
      const toggle = event.target instanceof Element ? event.target.closest('.sb_desc_indi') : null;

      if (!(toggle instanceof HTMLElement)) {
        return;
      }

      event.preventDefault();

      const state = toggle.querySelector('.plusminus');
      const target = toggle.parentElement?.querySelector('div');

      if (!(state instanceof HTMLElement) || !(target instanceof HTMLElement)) {
        throw new Error('Descendancy branch toggle structure is invalid.');
      }

      const isClosed = state.classList.contains('icon-plus');

      if (isClosed) {
        if (target.innerHTML.trim() !== '') {
          target.style.removeProperty('display');
        } else if (toggle.dataset.wtHref !== undefined && toggle.dataset.wtHref !== '#') {
          target.style.display = 'none';
          window.webtrees.load(target, toggle.dataset.wtHref).then(() => {
            if (target.innerHTML.trim() !== '') {
              target.style.removeProperty('display');
            }
          });
        }
      } else {
        target.style.display = 'none';
       }

       state.classList.toggle('icon-minus');
       state.classList.toggle('icon-plus');
     });
   });
 }


