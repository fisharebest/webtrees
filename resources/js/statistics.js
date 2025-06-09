/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

'use strict';

const GOOGLE_CHARTS_LIB = 'https://www.gstatic.com/charts/loader.js';

/**
 * Statistics class.
 */
class Statistics {
  /**
   * Constructor.
   *
   * @returns {Statistics}
     */
  constructor () {
    // Create singleton instance
    if (!Statistics.instance) {
      Statistics.instance = this;

      this.callbacks = [];
      this.initialized = false;
      this.loading = false;
    }

    return Statistics.instance;
  }

  /**
   * Initializes the google chart engine. Loads the chart lib only once.
   *
   * @param {String} locale - Locale, e.g. en, de, ...
   */
  init (locale) {
    if (this.loading || this.initialized) {
      return;
    }

    var that = this;

    Promise.all([
      this.load(GOOGLE_CHARTS_LIB)
    ]).then(() => {
      google.charts.load(
        'current',
        {
          packages: [
            'corechart',
            'geochart',
            'bar'
          ],
          language: locale,
          // Note: you will need to get a mapsApiKey for your project.
          // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
          mapsApiKey: ''
        }
      );

      google.charts.setOnLoadCallback(function () {
        that.callbacks.forEach((element) => {
          element();
        });
      });

      that.initialized = true;
    }).catch((error) => {
      console.log(error);
    });
  }

  /**
   * Dynamically loads a script by the given URL.
   *
   * @param   {String} url
   * @returns {Promise}
   */
  load (url) {
    if (this.loading) {
      return;
    }

    this.loading = true;

    return new Promise(function (resolve, reject) {
      const script = document.createElement('script');

      script.async = true;
      script.onload = function () {
        resolve(url);
      };
      script.onerror = function () {
        reject(url);
      };

      script.src = url;
      document.body.appendChild(script);
    });
  }

  /**
   * Adds the given callback method to the callback stack or add it directly to
   * the google charts interface once the chart engine is up and running.
   *
   * @param {Function} callback
   */
  addCallback (callback) {
    if (this.initialized) {
      google.charts.setOnLoadCallback(callback);
    } else {
      this.callbacks.push(callback);
    }

    $(window).resize(function () {
      callback();
    });
  }

  /**
   * Draws a google chart.
   *
   * @param {String} containerId
   * @param {String} chartType
   * @param {Array}  data
   * @param {Object} options
   */
  drawChart (containerId, chartType, data, options) {
    const dataTable = google.visualization.arrayToDataTable(data);

    const wrapper = new google.visualization.ChartWrapper({
      chartType: chartType,
      dataTable: dataTable,
      options: options,
      containerId: containerId
    });

    wrapper.draw();
  }

  /**
   * Draws a pie chart.
   *
   * @param {String} elementId - The element id of the HTML element the chart is rendered too
   * @param {Array}  data      - The chart data array
   * @param {Object} options   - The chart specific options to overwrite the default ones
   */
  drawPieChart (elementId, data, options) {
    // Default chart options
    const defaults = {
      title: '',
      height: '100%',
      width: '100%',
      pieStartAngle: 0,
      pieSliceText: 'none',
      pieSliceTextStyle: {
        color: '#777'
      },
      pieHole: 0.4, // Donut
      // is3D: true,  // 3D (not together with pieHole)
      legend: {
        alignment: 'center',
        // Flickers on mouseover :(
        labeledValueText: 'value',
        position: 'labeled'
      },
      chartArea: {
        left: 0,
        top: '5%',
        height: '90%',
        width: '100%'
      },
      tooltip: {
        trigger: 'none',
        text: 'both'
      },
      backgroundColor: 'transparent',
      colors: []
    };

    // Merge default with provided options
    options = Object.assign(defaults, options);

    // Create and draw the chart
    this.drawChart(elementId, 'PieChart', data, options);
  }

  /**
   * Draws a column chart.
   *
   * @param {String} elementId - The element id of the HTML element the chart is rendered too
   * @param {Array}  data      - The chart data array
   * @param {Object} options   - The chart specific options to overwrite the default ones
   */
  drawColumnChart (elementId, data, options) {
    // Default chart options
    const defaults = {
      title: '',
      subtitle: '',
      titleTextStyle: {
        color: '#757575',
        fontName: 'Roboto',
        fontSize: '16px',
        bold: false,
        italic: false
      },
      height: '100%',
      width: '100%',
      vAxis: {
        title: ''
      },
      hAxis: {
        title: ''
      },
      legend: {
        position: 'none'
      },
      backgroundColor: 'transparent'
    };

    // Merge default with provided options
    options = Object.assign(defaults, options);

    // Create and draw the chart
    this.drawChart(elementId, 'ColumnChart', data, options);
  }

  /**
   * Draws a combo chart.
   *
   * @param {String} elementId - The element id of the HTML element the chart is rendered too
   * @param {Array}  data      - The chart data array
   * @param {Object} options   - The chart specific options to overwrite the default ones
   */
  drawComboChart (elementId, data, options) {
    // Default chart options
    const defaults = {
      title: '',
      subtitle: '',
      titleTextStyle: {
        color: '#757575',
        fontName: 'Roboto',
        fontSize: '16px',
        bold: false,
        italic: false
      },
      height: '100%',
      width: '100%',
      vAxis: {
        title: ''
      },
      hAxis: {
        title: ''
      },
      legend: {
        position: 'none'
      },
      seriesType: 'bars',
      series: {
        2: {
          type: 'line'
        }
      },
      colors: [],
      backgroundColor: 'transparent'
    };

    // Merge default with provided options
    options = Object.assign(defaults, options);

    // Create and draw the chart
    this.drawChart(elementId, 'ComboChart', data, options);
  }

  /**
     * Draws a geo chart.
     *
     * @param {String} elementId - The element id of the HTML element the chart is rendered too
     * @param {Array}  data      - The chart data array
     * @param {Object} options   - The chart specific options to overwrite the default ones
     */
  drawGeoChart (elementId, data, options) {
    // Default chart options
    const defaults = {
      title: '',
      subtitle: '',
      height: '100%',
      width: '100%'
    };

    // Merge default with provided options
    options = Object.assign(defaults, options);

    // Create and draw the chart
    this.drawChart(elementId, 'GeoChart', data, options);
  }
}

// Create singleton instance of class
const statistics = new Statistics();
