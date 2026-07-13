'use strict';

import { requireDatasetValue, requireElement } from '../../webtrees/dom';

/**
 * Initialize the data-fix admin page.
 */
export function initializeDataFixPage () {
  const page = document.getElementById('admin-data-fix-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const form = requireElement(page, '#data-fix-options', HTMLFormElement, 'data-fix options form in #admin-data-fix-page');
  const container = requireElement(page, '#data-fix-table-container', HTMLElement, 'data-fix table container in #admin-data-fix-page');
  const table = requireElement(page, '#data-fix-table', HTMLTableElement, 'data-fix table in #admin-data-fix-page');
  const progress = requireElement(page, '#data-fix-progress', HTMLElement, 'data-fix progress container in #admin-data-fix-page');
  const searchButton = requireElement(page, '#btn-search', HTMLButtonElement, 'data-fix search button in #admin-data-fix-page');
  const updateAllButton = requireElement(page, '#btn-update-all', HTMLButtonElement, 'data-fix update-all button in #admin-data-fix-page');
  const progressBar = requireElement(page, '#data-fix-progress .progress-bar', HTMLElement, 'data-fix progress bar in #admin-data-fix-page');

  const dataUrl = requireDatasetValue(form, 'wtDataFixUrl', 'wtDataFixUrl on #data-fix-options');
  const updateAllUrl = requireDatasetValue(form, 'wtUpdateAllUrl', 'wtUpdateAllUrl on #data-fix-options');


  let queue = [];
  /** @type {DataTable|null} */
  let dataTable = null;

  function getParams () {
    const formData = new FormData(form);
    const params = {};

    formData.forEach((value, key) => {
      params[key] = value;
    });

    return params;
  }

  function addParamsToUrl (urlString) {
    const url = new URL(urlString, window.location.href);
    const formData = new FormData(form);

    formData.forEach((value, key) => {
      url.searchParams.append(key, value);
    });

    return url.toString();
  }

  form.addEventListener('submit', (event) => {
    event.preventDefault();
  });

  container.addEventListener('click', (event) => {
    const target = event.target instanceof Element
      ? event.target.closest('[data-update-url]')
      : null;

    if (target === null) {
      return;
    }

    event.preventDefault();

    const updateUrl = requireDatasetValue(target, 'updateUrl', 'data-fix row update URL');

    window.webtrees.httpPost(updateUrl)
      .then(() => {
        if (dataTable !== null) {
          dataTable.ajax.reload(null, false);
        }
      })
      .catch((error) => {
        alert(error);
      });
  });

  searchButton.addEventListener('click', (event) => {
    event.preventDefault();

    // If we were in the middle of doing "update all", stop processing.
    queue = [];

    progress.classList.add('d-none');

    if (dataTable !== null) {
      dataTable.ajax.reload();
    } else {
      dataTable = new DataTable(table, {
        ajax: {
          url: dataUrl,
          type: 'POST',
          data: (requestData) => {
            Object.assign(requestData, getParams());
          },
        },
      });
    }

    container.classList.remove('d-none');
  });

  updateAllButton.addEventListener('click', (event) => {
    event.preventDefault();

    progressBar.innerHTML = '';
    progressBar.style.width = '0%';

    container.classList.add('d-none');
    progress.classList.remove('d-none');

    const url = addParamsToUrl(updateAllUrl);

    window.webtrees.httpPost(url)
      .then((response) => response.json())
      .then(async (data) => {
        queue = data;

        while (queue.length > 0) {
          const datum = queue.shift();

          await window.webtrees.httpPost(datum.url)
            .then(() => {
              progressBar.innerHTML = datum.progress;
              progressBar.style.width = datum.percent;
            });
        }
      })
      .catch((error) => {
        progress.innerHTML = error;
      });
  });
}

