'use strict';

/**
 * Parse JSON from a data-* attribute.
 *
 * @param {string} json
 * @param {string} description
 * @returns {*}
 */
export function parseJsonDataset(json, description) {
  try {
    return JSON.parse(json);
  } catch {
    throw new Error('Invalid JSON dataset value for ' + description + '.');
  }
}

/**
 * Find a feature marker in a marker cluster by feature id.
 *
 * @param {L.MarkerClusterGroup} markers
 * @param {number} featureId
 * @returns {L.Layer|null}
 */
export function findFeatureLayer(markers, featureId) {
  return markers.getLayers().find((layer) => layer.feature !== undefined && layer.feature.id === featureId) ?? null;
}

/**
 * Zoom to and open the popup for a feature id.
 *
 * @param {L.MarkerClusterGroup} markers
 * @param {number} featureId
 * @returns {boolean}
 */
export function zoomToFeaturePopup(markers, featureId) {
  const marker = findFeatureLayer(markers, featureId);

  if (marker === null || typeof marker.openPopup !== 'function') {
    return false;
  }

  markers.zoomToShowLayer(marker, () => marker.openPopup());

  return true;
}

/**
 * Prevent nested links inside sidebar list items from triggering item click handlers.
 *
 * @param {HTMLElement} item
 */
export function stopNestedLinkClicks(item) {
  item.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', (event) => {
      event.stopPropagation();
    });
  });
}

