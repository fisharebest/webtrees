'use strict';

import { requireDatasetValue, requireElement } from '../dom';

/**
 * Initialize census assistant controls in edit forms.
 */
export function initializeCensusAssistantPage (root) {
  root.querySelectorAll('form').forEach((form) => {
    if (!(form instanceof HTMLFormElement)) {
      return;
    }

    const assistant = form.querySelector('#census-assistant');
    const assistantLink = form.querySelector('.census-assistant-link');

    if (!(assistant instanceof HTMLElement) || !(assistantLink instanceof HTMLElement)) {
      return;
    }

    const hiddenCensus = requireElement(form, '.census-assistant-class', HTMLInputElement, 'census hidden input');
    const titleInput = requireElement(form, '#census-assistant-title', HTMLInputElement, 'census title input');
    const assistantTable = requireElement(form, '#census-assistant-table', HTMLTableElement, 'census assistant table');
    const assistantTableHead = requireElement(assistantTable, 'thead', HTMLTableSectionElement, 'census assistant header');
    const assistantTableBody = requireElement(assistantTable, 'tbody', HTMLTableSectionElement, 'census assistant body');
    const individualSelect = requireElement(form, '#census-assistant-individual', HTMLSelectElement, 'census individual selector');

    const censusHeaderUrl = requireDatasetValue(assistant, 'wtCensusHeaderUrl', 'census header URL');
    const censusIndividualUrl = requireDatasetValue(assistant, 'wtCensusIndividualUrl', 'census individual URL');
    const transcriptLabel = requireDatasetValue(assistant, 'wtCensusTranscriptLabel', 'census transcript label');
    const householdLabel = requireDatasetValue(assistant, 'wtCensusHouseholdLabel', 'census household label');
    const individualName = requireDatasetValue(assistant, 'wtCensusIndividualName', 'census individual name');

    const postCensus = (url, formData) => {
      formData.append('_csrf', requireElement(document, 'meta[name=csrf]', HTMLMetaElement, 'CSRF meta tag').content);

      return window.webtrees.httpPost(url, formData).then((response) => response.text());
    };

    const addSelectedIndividual = () => {
      const censusSelector = requireElement(form, '.census-selector', HTMLSelectElement, 'census selector');
      const censusOption = censusSelector.options[censusSelector.selectedIndex];
      const selectedIndividual = individualSelect.options[individualSelect.selectedIndex];

      if (censusOption === undefined || selectedIndividual === undefined || selectedIndividual.value === '') {
        return;
      }

      const census = requireDatasetValue(censusOption, 'wtCensus', 'census identifier');
      const headInput = assistantTable.querySelector('td input');
      const head = headInput instanceof HTMLInputElement ? headInput.value : selectedIndividual.value;

      const formData = new FormData();
      formData.append('census', census);
      formData.append('head', head);
      formData.append('xref', selectedIndividual.value);

      postCensus(censusIndividualUrl, formData).then((html) => {
        assistantTableBody.insertAdjacentHTML('beforeend', html);
        window.webtrees.resetTomSelect(individualSelect.tomselect, '', '');
      });
    };

    const selectCensus = (select) => {
      const option = select.options[select.selectedIndex];

      if (option === undefined) {
        return;
      }

      const census = requireDatasetValue(option, 'wtCensus', 'census identifier');
      const censusPlace = requireDatasetValue(option, 'wtPlace', 'census place');
      const censusDate = requireDatasetValue(option, 'wtDate', 'census date');
      const censusYear = censusDate.slice(-4);

      hiddenCensus.value = census;
      assistant.hidden = true;

      if (option.value !== '') {
        assistantLink.hidden = false;
      } else {
        assistantLink.hidden = true;
      }

      titleInput.value = censusYear + ' ' + censusPlace + ' - ' + transcriptLabel + ' - ' + individualName + ' - ' + householdLabel;

      const formData = new FormData();
      formData.append('census', census);

      postCensus(censusHeaderUrl, formData).then((html) => {
        assistantTableHead.innerHTML = html;
        assistantTableBody.innerHTML = '';
      });
    };

    form.querySelectorAll('.census-selector').forEach((selector) => {
      if (!(selector instanceof HTMLSelectElement)) {
        throw new Error('Census selector must be a select element.');
      }

      selector.addEventListener('change', () => selectCensus(selector));
    });

    assistantLink.addEventListener('click', (event) => {
      event.preventDefault();

      form.querySelectorAll('.census-selector').forEach((selector) => {
        if (selector instanceof HTMLElement) {
          selector.hidden = true;
        }
      });

      assistantLink.hidden = true;
      assistant.hidden = false;
      addSelectedIndividual();
    });

    form.querySelectorAll('.census-assistant-add').forEach((button) => {
      if (!(button instanceof HTMLButtonElement)) {
        throw new Error('Census assistant add control must be a button element.');
      }

      button.addEventListener('click', addSelectedIndividual);
    });

    assistantTable.addEventListener('click', (event) => {
      const deleteButton = event.target instanceof Element ? event.target.closest('.wt-icon-delete') : null;

      if (!(deleteButton instanceof Element)) {
        return;
      }

      const row = deleteButton.closest('tr');

      if (row instanceof HTMLElement) {
        row.remove();
      }
    });
  });
}
