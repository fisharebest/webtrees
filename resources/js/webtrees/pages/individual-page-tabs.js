'use strict';

/**
 * Initialize individual page tabs.
 */
export function initializeIndividualPageTabs () {
  const page = document.getElementById('individual-tabs');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const tabs = Array.from(page.querySelectorAll('a[data-bs-toggle="tab"]'));

  tabs.forEach((tab) => {
    if (!(tab instanceof HTMLAnchorElement)) {
      throw new Error('Individual tabs must be anchor elements.');
    }

    tab.addEventListener('show.bs.tab', () => {
      const href = tab.getAttribute('href');

      if (href === null || !href.startsWith('#')) {
        return;
      }

      const target = page.querySelector(href);

      if (!(target instanceof HTMLElement) || target.dataset.wtTabLoadDone === '1') {
        return;
      }

      if (target.innerHTML.trim() !== '') {
        target.dataset.wtTabLoadDone = '1';
        return;
      }

      target.dataset.wtTabLoadDone = '1';
      const url = tab.dataset.wtHref;

      if (url === undefined || url === '') {
        return;
      }

      const download = window.webtrees.httpGet(url).then((response) => response.text());

      tab.addEventListener('shown.bs.tab', () => {
        download.then((html) => {
          target.innerHTML = html;
        }).catch((error) => {
          console.error('Failed to load individual tab content', { url, error });
        });
      }, { once: true });
    });

    tab.addEventListener('shown.bs.tab', (event) => {
      const shownTab = event.target;

      if (!(shownTab instanceof HTMLAnchorElement)) {
        return;
      }

      const href = shownTab.getAttribute('href');

      if (href !== null && href.startsWith('#')) {
        window.location.hash = 'tab-' + href.substring(1);
      }
    });
  });

  const targetHash = window.location.hash.replace('tab-', '');
  const targetTab = tabs.find((tab) => tab instanceof HTMLAnchorElement && tab.getAttribute('href') === targetHash) ?? tabs[0];

  if (targetTab instanceof HTMLElement) {
    targetTab.click();
  }
}

