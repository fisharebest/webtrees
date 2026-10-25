'use strict';

import { requireElement } from '../dom';
import { initializeStatisticsChartCustomPage } from './statistics-chart-custom-page';

/**
 * Initialize statistics chart tab loading.
 */
export function initializeStatisticsChartPage (root) {
  const page = root.querySelector('#statistics-tabs');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const tabs = Array.from(page.querySelectorAll('a[data-bs-toggle="tab"][data-wt-href]'));

  tabs.forEach((tab) => {
    if (!(tab instanceof HTMLAnchorElement)) {
      throw new Error('Statistics tab controls must be anchor elements.');
    }

    tab.addEventListener('show.bs.tab', () => {
      if (tab.dataset.wtHref === undefined || tab.dataset.wtHref === '') {
        return;
      }

      const href = tab.getAttribute('href');

      if (href === null) {
        throw new Error('Statistics tab is missing href target.');
      }

      const target = requireElement(page, href, HTMLElement, 'statistics tab pane');
      const url = tab.dataset.wtHref;

      window.webtrees.load(target, url).then(() => {
        initializeStatisticsChartCustomPage(target);
        tab.dataset.wtHref = '';
      }).catch((error) => {
        console.error('Failed to load statistics tab', { url, error });
      });
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
  const targetTab = tabs.find((tab) => tab.getAttribute('href') === targetHash) ?? tabs[0];

  if (targetTab instanceof HTMLElement) {
    targetTab.click();
  }
}
