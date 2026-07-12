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

import {
  ArcElement,
  BarController,
  BarElement,
  CategoryScale,
  Chart,
  DoughnutController,
  Legend,
  LineController,
  LineElement,
  LinearScale,
  PointElement,
  Title,
  Tooltip,
} from 'chart.js';
import {
  ChoroplethController,
  ColorScale,
  GeoFeature,
  ProjectionScale,
} from 'chartjs-chart-geo';
import { feature } from 'topojson-client';
import countriesTopology from 'world-atlas/countries-110m.json';

Chart.register(
  ArcElement,
  BarController,
  BarElement,
  CategoryScale,
  ChoroplethController,
  ColorScale,
  DoughnutController,
  GeoFeature,
  Legend,
  LineController,
  LineElement,
  LinearScale,
  PointElement,
  ProjectionScale,
  Title,
  Tooltip,
);

const COUNTRY_FEATURES = feature(countriesTopology, countriesTopology.objects.countries).features;
const COUNTRY_FEATURE_BY_ID = new Map(COUNTRY_FEATURES.map((country) => [String(country.id), country]));
const CHART_RENDERERS = new Map();
const CHART_INSTANCES = new WeakMap();

const REGION_PROJECTION_PRESETS = {
  world: { projection: 'equalEarth', projectionScale: 1.0, projectionOffset: [0, 0] },
  '002': { projection: 'mercator', projectionScale: 2.1, projectionOffset: [0, -10] },
  '005': { projection: 'mercator', projectionScale: 2.6, projectionOffset: [40, 10] },
  '021': { projection: 'mercator', projectionScale: 2.4, projectionOffset: [10, -20] },
  '142': { projection: 'mercator', projectionScale: 2.1, projectionOffset: [-10, -10] },
  '145': { projection: 'mercator', projectionScale: 3.4, projectionOffset: [10, -8] },
  '150': { projection: 'mercator', projectionScale: 3.0, projectionOffset: [15, -55] },
};


const THEME_DEFAULT_COLORS = {
  bodyColor: '#212529',
  secondaryColor: '#6c757d',
  borderColor: '#dee2e6',
  secondaryBackground: '#f8f9fa',
};

// Default options per chart type, merged with per-element overrides.
const DEFAULT_OPTIONS = {
  pie: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '40%',
    plugins: {
      legend: {
        display: true,
        position: 'right',
      },
      tooltip: {
        enabled: true,
      },
    },
  },
  column: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false,
      },
      tooltip: {
        enabled: true,
      },
    },
    scales: {
      y: {
        beginAtZero: true,
      },
    },
  },
  combo: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
      },
      tooltip: {
        enabled: true,
      },
    },
    scales: {
      y: {
        beginAtZero: true,
      },
    },
  },
  geo: {
    responsive: true,
    maintainAspectRatio: false,
    showOutline: true,
    showGraticule: false,
    plugins: {
      legend: {
        display: false,
      },
      tooltip: {
        enabled: true,
      },
    },
    scales: {
      projection: {
        projection: 'equalEarth',
      },
      color: {
        quantize: 5,
      },
    },
  },
};

/**
 * @param {string} cssVariable
 * @param {string} fallback
 * @returns {string}
 */
function cssColor (cssVariable, fallback) {
  const value = getComputedStyle(document.documentElement)
    .getPropertyValue(cssVariable)
    .trim();

  return value === '' ? fallback : value;
}

/**
 * @returns {{bodyColor: string, secondaryColor: string, borderColor: string, secondaryBackground: string}}
 */
function chartThemeColors () {
  return {
    bodyColor: cssColor('--bs-body-color', THEME_DEFAULT_COLORS.bodyColor),
    secondaryColor: cssColor('--bs-secondary-color', THEME_DEFAULT_COLORS.secondaryColor),
    borderColor: cssColor('--bs-border-color', THEME_DEFAULT_COLORS.borderColor),
    secondaryBackground: cssColor('--bs-secondary-bg', THEME_DEFAULT_COLORS.secondaryBackground),
  };
}

/**
 * @returns {Record<string, Record<string, unknown>>}
 */
function themedDefaultOptions () {
  const colors = chartThemeColors();

  return {
    pie: mergeOptions(DEFAULT_OPTIONS.pie, {
      plugins: {
        legend: {
          labels: {
            color: colors.bodyColor,
          },
        },
        title: {
          color: colors.bodyColor,
        },
      },
    }),
    column: mergeOptions(DEFAULT_OPTIONS.column, {
      plugins: {
        legend: {
          labels: {
            color: colors.bodyColor,
          },
        },
        title: {
          color: colors.bodyColor,
        },
        subtitle: {
          color: colors.secondaryColor,
        },
      },
      scales: {
        x: {
          ticks: {
            color: colors.bodyColor,
          },
          grid: {
            color: colors.borderColor,
          },
          title: {
            color: colors.bodyColor,
          },
        },
        y: {
          ticks: {
            color: colors.bodyColor,
          },
          grid: {
            color: colors.borderColor,
          },
          title: {
            color: colors.bodyColor,
          },
        },
      },
    }),
    combo: mergeOptions(DEFAULT_OPTIONS.combo, {
      plugins: {
        legend: {
          labels: {
            color: colors.bodyColor,
          },
        },
        title: {
          color: colors.bodyColor,
        },
        subtitle: {
          color: colors.secondaryColor,
        },
      },
      scales: {
        x: {
          ticks: {
            color: colors.bodyColor,
          },
          grid: {
            color: colors.borderColor,
          },
          title: {
            color: colors.bodyColor,
          },
        },
        y: {
          ticks: {
            color: colors.bodyColor,
          },
          grid: {
            color: colors.borderColor,
          },
          title: {
            color: colors.bodyColor,
          },
        },
      },
    }),
    geo: mergeOptions(DEFAULT_OPTIONS.geo, {
      plugins: {
        title: {
          color: colors.bodyColor,
        },
        subtitle: {
          color: colors.secondaryColor,
        },
      },
    }),
  };
}

/**
 * @param {Record<string, unknown>} base
 * @param {Record<string, unknown>} overrides
 * @returns {Record<string, unknown>}
 */
function mergeOptions (base, overrides) {
  const options = { ...base };

  Object.entries(overrides).forEach(([key, value]) => {
    const baseValue = options[key];

    if (isObject(baseValue) && isObject(value)) {
      options[key] = mergeOptions(baseValue, value);
    } else {
      options[key] = value;
    }
  });

  return options;
}

/**
 * @param {unknown} value
 * @returns {boolean}
 */
function isObject (value) {
  return typeof value === 'object' && value !== null && !Array.isArray(value);
}

/**
 * Parse JSON safely and throw an informative error when data is invalid.
 *
 * @param {string} serialized
 * @param {string} attribute
 * @returns {unknown}
 */
function parseJson (serialized, attribute) {
  try {
    return JSON.parse(serialized);
  } catch {
    throw new Error(`Invalid JSON in ${attribute}`);
  }
}

/**
 * @param {HTMLElement} element
 * @returns {HTMLCanvasElement}
 */
function chartCanvas (element) {
  if (element instanceof HTMLCanvasElement) {
    return element;
  }

  let canvas = element.querySelector('canvas[data-wt-chart-canvas]');

  if (canvas === null) {
    canvas = document.createElement('canvas');
    canvas.dataset.wtChartCanvas = '';
    element.appendChild(canvas);
  }

  if (element.clientHeight === 0) {
    element.style.minHeight = '20rem';
  }

  return canvas;
}

/**
 * Register a renderer for one data-wt-chart-type value.
 *
 * @param {string} type
 * @param {(canvas: HTMLCanvasElement, data: unknown, options: Record<string, unknown>, element: HTMLElement) => Chart} renderer
 */
function registerChartRenderer (type, renderer) {
  CHART_RENDERERS.set(type, renderer);
}

/**
 * @param {unknown} rawData
 * @param {string} chartType
 * @returns {{labels: Array<string>, datasets: Array<Record<string, unknown>>}}
 */
function cartesianData (rawData, chartType) {
  if (
    !isObject(rawData)
    || !Array.isArray(rawData.labels)
    || !Array.isArray(rawData.datasets)
  ) {
    throw new Error(`${chartType} chart data must use { labels, datasets } format.`);
  }

  return rawData;
}

/**
 * Look up a world-atlas feature by its ISO 3166-1 numeric code.
 *
 * @param {string} numericId
 * @returns {any|null}
 */
function geoFeatureForNumericId (numericId) {
  return COUNTRY_FEATURE_BY_ID.get(numericId) || null;
}

/**
 * @param {string} color
 * @returns {[number, number, number]}
 */
function hexToRgb (color) {
  const hex = color.replace('#', '').trim();
  const normalized = hex.length === 3
    ? hex.split('').map((value) => value + value).join('')
    : hex;

  return [
    Number.parseInt(normalized.slice(0, 2), 16),
    Number.parseInt(normalized.slice(2, 4), 16),
    Number.parseInt(normalized.slice(4, 6), 16),
  ];
}

/**
 * @param {[number, number, number]} rgb
 * @returns {string}
 */
function rgbToHex (rgb) {
  return `#${rgb.map((value) => {
    const normalized = Math.max(0, Math.min(255, Math.round(value)));
    return normalized.toString(16).padStart(2, '0');
  }).join('')}`;
}

/**
 * @param {string} startColor
 * @param {string} endColor
 * @param {number} value
 * @returns {string}
 */
function interpolateHexColor (startColor, endColor, value) {
  const start = hexToRgb(startColor);
  const end = hexToRgb(endColor);
  const normalized = Math.max(0, Math.min(1, value));

  return rgbToHex([
    start[0] + (end[0] - start[0]) * normalized,
    start[1] + (end[1] - start[1]) * normalized,
    start[2] + (end[2] - start[2]) * normalized,
  ]);
}


/**
 * @param {unknown} rawData
 * @returns {{features: Array<{id: string, label: string, value: number}>}}
 */
function normalizeGeoData (rawData) {
  if (!isObject(rawData) || !Array.isArray(rawData.features)) {
    throw new Error('Geo chart data must use { features } format.');
  }

  return rawData;
}

/**
 * Draw a single chart inside the given container element.
 *
 * @param {HTMLElement} element - Container with data-wt-chart-* attributes
 */
function drawChart (element) {
  const chartType = element.dataset.wtChartType;
  const renderer = CHART_RENDERERS.get(chartType);

  if (renderer === undefined) {
    throw new Error(`Unknown chart type: ${chartType}`);
  }

  const data = parseJson(element.dataset.wtChartData, 'data-wt-chart-data');
  const overrides = parseJson(element.dataset.wtChartOptions || '{}', 'data-wt-chart-options');

  if (!isObject(overrides)) {
    throw new Error('Chart options must be a JSON object.');
  }

  const themeAwareDefaults = themedDefaultOptions();
  const options = mergeOptions(themeAwareDefaults[chartType] || {}, overrides);
  const canvas = chartCanvas(element);
  const previousChart = CHART_INSTANCES.get(canvas);

  if (previousChart !== undefined) {
    previousChart.destroy();
  }

  CHART_INSTANCES.set(canvas, renderer(canvas, data, options, element));
}

/**
 * Find all unrendered chart containers on the page and draw them.
 * Marks each element after drawing so it is not drawn twice.
 */
function drawNewCharts () {
  const elements = document.querySelectorAll('[data-wt-chart-type]:not([data-wt-chart-rendered])');

  elements.forEach((element) => {
    try {
      drawChart(element);
      element.setAttribute('data-wt-chart-rendered', '');
    } catch (error) {
      console.error(error);
    }
  });
}

/**
 * Check the DOM for chart containers and initialize them.
 * Called on DOMContentLoaded and whenever new nodes are added to the document.
 */
function scanForCharts () {
  drawNewCharts();
}

registerChartRenderer('pie', (canvas, rawData, options) => {
  const pieData = cartesianData(rawData, 'Pie');

  return new Chart(canvas, {
    type: 'doughnut',
    data: pieData,
    options,
  });
});

registerChartRenderer('column', (canvas, rawData, options) => {
  const columnData = cartesianData(rawData, 'Column');

  return new Chart(canvas, {
    type: 'bar',
    data: columnData,
    options,
  });
});

registerChartRenderer('combo', (canvas, rawData, options) => {
  const comboData = cartesianData(rawData, 'Combo');

  return new Chart(canvas, {
    type: 'bar',
    data: comboData,
    options,
  });
});

registerChartRenderer('geo', (canvas, rawData, options) => {
  const geoData = normalizeGeoData(rawData);

  const dataset = geoData.features
    .map((entry) => {
      const featureMatch = geoFeatureForNumericId(entry.id);

      if (featureMatch === null) {
        return null;
      }

      return {
        feature: featureMatch,
        value: entry.value,
      };
    })
    .filter((entry) => entry !== null);

  const colors = options.colorAxis && Array.isArray(options.colorAxis.colors)
    ? options.colorAxis.colors
    : ['#9ecae1', '#08519c'];
  const region = typeof options.region === 'string' ? options.region : 'world';
  const projection = REGION_PROJECTION_PRESETS[region] || REGION_PROJECTION_PRESETS.world;
  const outline = region === 'world'
    ? COUNTRY_FEATURES
    : dataset.map((entry) => entry.feature);
  const resolvedOutline = outline.length > 0 ? outline : COUNTRY_FEATURES;
  const outlineFeatureCollection = {
    type: 'FeatureCollection',
    features: resolvedOutline,
  };

  const geoOptions = {
    responsive: true,
    maintainAspectRatio: false,
    showOutline: true,
    showGraticule: false,
    plugins: {
      legend: {
        display: false,
      },
      tooltip: {
        enabled: true,
      },
      title: {
        display: options.plugins?.title?.display === true,
        text: options.plugins?.title?.text || '',
      },
    },
    scales: {
      projection: {
        axis: 'x',
        projection: projection.projection,
        projectionScale: projection.projectionScale,
        projectionOffset: projection.projectionOffset,
      },
      color: {
        axis: 'x',
        quantize: 5,
        interpolate: (value) => interpolateHexColor(colors[0], colors[1], value),
      },
    },
  };

  return new Chart(canvas, {
    type: 'choropleth',
    data: {
      labels: dataset.map((entry) => entry.feature.properties.name),
      datasets: [
        {
          outline: outlineFeatureCollection,
          data: dataset,
        },
      ],
    },
    options: geoOptions,
  });
});

// Observe the DOM for chart containers injected via AJAX.
const observer = new MutationObserver(scanForCharts);

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    scanForCharts();
    observer.observe(document.body, { childList: true, subtree: true });
  });
} else {
  scanForCharts();
  observer.observe(document.body, { childList: true, subtree: true });
}
