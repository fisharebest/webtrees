'use strict';

import { requireDatasetValue, requireElement } from '../dom';

// Keep these values in sync with app/Module/StatisticsChartModule.php.
const X_AXIS = {
  INDIVIDUAL_MAP: '1',
  BIRTH_MAP: '2',
  DEATH_MAP: '3',
  MARRIAGE_MAP: '4',
  BIRTH_MONTH: '11',
  DEATH_MONTH: '12',
  MARRIAGE_MONTH: '13',
  FIRST_CHILD_MONTH: '14',
  FIRST_MARRIAGE_MONTH: '15',
  AGE_AT_DEATH: '18',
  AGE_AT_MARRIAGE: '19',
  AGE_AT_FIRST_MARRIAGE: '20',
  NUMBER_OF_CHILDREN: '21',
};

const SURNAME_DISTRIBUTION = 'surname_distribution_chart';
const Z_AXIS = {
  TIME: '302',
};

/**
 * Initialize custom statistics chart controls.
 *
 * @param {ParentNode} root
 */
export function initializeStatisticsChartCustomPage (root) {
  root.querySelectorAll('form[data-wt-statistics-custom-chart]').forEach((form) => {
    if (!(form instanceof HTMLFormElement)) {
      throw new Error('Statistics custom chart container must be a form.');
    }

    const chartTargetSelector = requireDatasetValue(form, 'wtStatisticsCustomTarget', 'statistics custom chart target selector');
    const spinnerMarkup = requireDatasetValue(form, 'wtStatisticsSpinner', 'statistics custom chart spinner markup');

    const chartTarget = requireElement(document, chartTargetSelector, HTMLElement, 'statistics custom chart target');
    const zNone = requireElement(form, '#z_none', HTMLInputElement, 'statistics z-axis none radio');
    const zSex = requireElement(form, '#z_sex', HTMLInputElement, 'statistics z-axis sex radio');
    const zAxisBoundaries = requireElement(form, '#z-axis-boundaries-periods', HTMLSelectElement, 'statistics date periods selector');
    const xYears = requireElement(form, '#x_years', HTMLElement, 'statistics age interval fieldset');
    const xYearsMarriage = requireElement(form, '#x_years_m', HTMLElement, 'statistics marriage age interval fieldset');
    const mapOptions = requireElement(form, '#map_opt', HTMLElement, 'statistics map options container');
    const axes = requireElement(form, '#axes', HTMLElement, 'statistics categories fieldset');
    const results = requireElement(form, '#zyaxes', HTMLElement, 'statistics results fieldset');
    const chartType = requireElement(form, '#chart_type', HTMLElement, 'statistics map chart type fieldset');
    const surnameOptions = requireElement(form, '#surname_opt', HTMLElement, 'statistics surname options');
    const chartTypeSelect = requireElement(form, 'select[name="chart_type"]', HTMLSelectElement, 'statistics chart type select');
    const surnameInput = requireElement(form, '#SURN', HTMLInputElement, 'statistics surname autocomplete input');

    const toggleVisibility = (element, visible) => {
      element.classList.toggle('d-none', !visible);
    };

    const updateSurnameOptions = () => {
      toggleVisibility(surnameOptions, chartTypeSelect.value === SURNAME_DISTRIBUTION);
    };

    const updateDatePeriodsControl = () => {
      const selected = requireElement(form, 'input[name="z-as"]:checked', HTMLInputElement, 'statistics selected z-axis radio');
      zAxisBoundaries.disabled = selected.value !== Z_AXIS.TIME;
    };

    const setMapMode = (showMap, showChartType) => {
      toggleVisibility(mapOptions, showMap);
      toggleVisibility(chartType, showMap && showChartType);
      toggleVisibility(axes, !showMap);
      toggleVisibility(results, !showMap);

      if (!showMap || !showChartType) {
        toggleVisibility(surnameOptions, false);
      }
    };

    const applyXAxisSelection = () => {
      const selected = requireElement(form, 'input[name="x-as"]:checked', HTMLInputElement, 'statistics selected x-axis radio');
      const xAxis = selected.value;

      toggleVisibility(xYears, false);
      toggleVisibility(xYearsMarriage, false);

      switch (xAxis) {
      case X_AXIS.BIRTH_MONTH:
      case X_AXIS.DEATH_MONTH:
      case X_AXIS.FIRST_CHILD_MONTH:
        zSex.disabled = false;
        setMapMode(false, false);
        break;

      case X_AXIS.AGE_AT_DEATH:
        zSex.disabled = false;
        toggleVisibility(xYears, true);
        setMapMode(false, false);
        break;

      case X_AXIS.AGE_AT_MARRIAGE:
      case X_AXIS.AGE_AT_FIRST_MARRIAGE:
        zSex.disabled = false;
        toggleVisibility(xYearsMarriage, true);
        setMapMode(false, false);
        break;

      case X_AXIS.MARRIAGE_MONTH:
      case X_AXIS.FIRST_MARRIAGE_MONTH:
      case X_AXIS.NUMBER_OF_CHILDREN:
        zNone.checked = true;
        zSex.disabled = true;
        setMapMode(false, false);
        break;

      case X_AXIS.INDIVIDUAL_MAP:
        setMapMode(true, true);
        updateSurnameOptions();
        break;

      case X_AXIS.BIRTH_MAP:
      case X_AXIS.MARRIAGE_MAP:
      case X_AXIS.DEATH_MAP:
        setMapMode(true, false);
        break;

      default:
        throw new Error('Unsupported statistics x-axis value: ' + xAxis);
      }

      updateDatePeriodsControl();
    };

    form.querySelectorAll('input[name="x-as"]').forEach((radio) => {
      if (!(radio instanceof HTMLInputElement)) {
        throw new Error('Statistics x-axis option must be an input element.');
      }

      radio.addEventListener('change', applyXAxisSelection);
    });

    form.querySelectorAll('input[name="z-as"]').forEach((radio) => {
      if (!(radio instanceof HTMLInputElement)) {
        throw new Error('Statistics z-axis option must be an input element.');
      }

      radio.addEventListener('change', updateDatePeriodsControl);
    });

    chartTypeSelect.addEventListener('change', updateSurnameOptions);

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      chartTarget.innerHTML = spinnerMarkup;

      window.webtrees.httpPost(form.action, new FormData(form))
        .then((response) => response.text())
        .then((html) => {
          chartTarget.innerHTML = html;
        })
        .catch((error) => {
          chartTarget.textContent = String(error);
        });
    });

    window.webtrees.autocomplete('#' + surnameInput.id, form);

    applyXAxisSelection();
  });
}
