'use strict';

import { requireDatasetValue } from '../../webtrees/index';

/**
 * Initialize the upgrade wizard admin page.
 */
export function initializeUpgradeWizardPage () {
  const page = document.getElementById('admin-upgrade-wizard-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const spinnerHTML = '<i class="fa-solid fa-spinner fa-spin fa-3x" aria-hidden="true"></i>';

  function nextAjaxStep () {
    const step = page.querySelector('dd[data-url]:empty');

    if (!(step instanceof HTMLElement)) {
      return;
    }

    const url = requireDatasetValue(step, 'url', 'upgrade wizard step URL');

    step.innerHTML = spinnerHTML;

    window.webtrees.load(step, url)
      .then(() => {
        nextAjaxStep();
      })
      .catch((error) => {
        console.error('Upgrade wizard step failed', { url, error });
      });
  }

  nextAjaxStep();
}


