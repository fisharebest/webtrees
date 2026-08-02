'use strict';

import { requireElement } from '../../webtrees/index';

/**
 * Toggle visitor/member/manager feedback badges for one privacy setting row.
 *
 * @param {HTMLElement} page
 * @param {string} selector
 * @param {string} who
 * @param {boolean} access
 */
function setPrivacyFeedback (page, selector, who, access) {
  const control = requireElement(page, selector, HTMLElement, `${selector} control in #admin-trees-privacy-page`);

  const row = control.closest('.row');

  if (!(row instanceof HTMLElement)) {
    throw new Error('Expected privacy control to be inside a .row group.');
  }

  row.querySelectorAll(`.${who}`).forEach((badge) => {
    badge.classList.toggle('bg-success', access);
    badge.classList.toggle('bg-secondary', !access);
  });

  row.querySelectorAll(`.${who} i`).forEach((icon) => {
    icon.classList.toggle('fa-check', access);
    icon.classList.toggle('fa-times', !access);
  });
}

/**
 * @param {HTMLSelectElement} element
 * @returns {number}
 */
function selectedNumber (element) {
  return Number.parseInt(element.value, 10);
}

/**
 * Initialize the trees-privacy admin page.
 */
export function initializeTreesPrivacyPage () {
  const page = document.getElementById('admin-trees-privacy-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const requireAuthentication = requireElement(page, '[name="REQUIRE_AUTHENTICATION"]', HTMLSelectElement, 'REQUIRE_AUTHENTICATION select in #admin-trees-privacy-page');
  const showDeadPeople = requireElement(page, '[name="SHOW_DEAD_PEOPLE"]', HTMLSelectElement, 'SHOW_DEAD_PEOPLE select in #admin-trees-privacy-page');
  const hideLivePeople = requireElement(page, '[name="HIDE_LIVE_PEOPLE"]', HTMLSelectElement, 'HIDE_LIVE_PEOPLE select in #admin-trees-privacy-page');
  const showLivingNames = requireElement(page, '[name="SHOW_LIVING_NAMES"]', HTMLSelectElement, 'SHOW_LIVING_NAMES select in #admin-trees-privacy-page');
  const showPrivateRelationships = requireElement(page, '[name="SHOW_PRIVATE_RELATIONSHIPS"]', HTMLSelectElement, 'SHOW_PRIVATE_RELATIONSHIPS select in #admin-trees-privacy-page');
  const resnTable = requireElement(page, '#default-resn', HTMLTableElement, 'default RESN table in #admin-trees-privacy-page');
  const resnTemplate = requireElement(page, '#new-resn-template', HTMLScriptElement, 'new RESN template in #admin-trees-privacy-page');
  const addResnButton = requireElement(page, '#add-resn', HTMLButtonElement, 'add RESN button in #admin-trees-privacy-page');
  const tableBody = requireElement(resnTable, 'tbody', HTMLTableSectionElement, 'default RESN tbody in #admin-trees-privacy-page');

  const updatePrivacyFeedback = () => {
    const requireAuthenticationValue = selectedNumber(requireAuthentication);
    const showDeadPeopleValue = selectedNumber(showDeadPeople);
    const hideLivePeopleValue = selectedNumber(hideLivePeople);
    const showLivingNamesValue = selectedNumber(showLivingNames);
    const showPrivateRelationshipsValue = selectedNumber(showPrivateRelationships);

    setPrivacyFeedback(page, '[name="REQUIRE_AUTHENTICATION"]', 'visitors', requireAuthenticationValue === 0);
    setPrivacyFeedback(page, '[name="REQUIRE_AUTHENTICATION"]', 'members', true);

    setPrivacyFeedback(page, '[name="SHOW_DEAD_PEOPLE"]', 'visitors', requireAuthenticationValue === 0 && (showDeadPeopleValue >= 2 || hideLivePeopleValue === 0));
    setPrivacyFeedback(page, '[name="SHOW_DEAD_PEOPLE"]', 'members', showDeadPeopleValue >= 1 || hideLivePeopleValue === 0);

    setPrivacyFeedback(page, '[name="HIDE_LIVE_PEOPLE"]', 'visitors', requireAuthenticationValue === 0 && hideLivePeopleValue === 0);
    setPrivacyFeedback(page, '[name="HIDE_LIVE_PEOPLE"]', 'members', true);

    setPrivacyFeedback(page, '[name="SHOW_LIVING_NAMES"]', 'visitors', requireAuthenticationValue === 0 && showLivingNamesValue >= 2);
    setPrivacyFeedback(page, '[name="SHOW_LIVING_NAMES"]', 'members', showLivingNamesValue >= 1);
    setPrivacyFeedback(page, '[name="SHOW_LIVING_NAMES"]', 'managers', showLivingNamesValue >= 0);

    setPrivacyFeedback(page, '[name="SHOW_PRIVATE_RELATIONSHIPS"]', 'visitors', requireAuthenticationValue === 0 && showPrivateRelationshipsValue >= 1);
    setPrivacyFeedback(page, '[name="SHOW_PRIVATE_RELATIONSHIPS"]', 'members', showPrivateRelationshipsValue >= 1);
  };

  [requireAuthentication, hideLivePeople, showDeadPeople, showLivingNames, showPrivateRelationships].forEach((element) => {
    element.addEventListener('change', updatePrivacyFeedback);
  });

  resnTable.addEventListener('change', (event) => {
    const checkbox = event.target instanceof Element
      ? event.target.closest('input[type="checkbox"]')
      : null;

    if (!(checkbox instanceof HTMLInputElement)) {
      return;
    }

    const row = checkbox.closest('tr');

    if (row instanceof HTMLTableRowElement) {
      row.classList.toggle('text-muted', checkbox.checked);
    }
  });

  addResnButton.addEventListener('click', () => {
    tableBody.insertAdjacentHTML('afterbegin', resnTemplate.innerHTML);

    const row = requireElement(tableBody, 'tr', HTMLTableRowElement, 'new privacy restriction row');

    if (typeof window.webtrees?.initializeTomSelect !== 'function') {
      throw new Error('Missing webtrees.initializeTomSelect().');
    }

    row.querySelectorAll('select.tom-select').forEach((element) => {
      if (element instanceof HTMLSelectElement) {
        window.webtrees.initializeTomSelect(element);
      }
    });

    const recordTypeSelector = row.querySelector('.record-type-selector');

    if (!(recordTypeSelector instanceof HTMLSelectElement)) {
      throw new Error('Expected record type selector in new privacy restriction row.');
    }

    if (typeof window.webtrees?.initializeIFSRO !== 'function') {
      throw new Error('Missing webtrees.initializeIFSRO().');
    }

    window.webtrees.initializeIFSRO(recordTypeSelector, row);
  });

  updatePrivacyFeedback();
}

