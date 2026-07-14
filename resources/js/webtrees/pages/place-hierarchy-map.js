'use strict';

import { requireDatasetValue, requireElement } from '../dom';
import { initializeFeaturePanelMap } from '../map-panel';
import { parseJsonDataset, stopNestedLinkClicks, zoomToFeaturePopup } from '../map-sidebar';

/**
 * Initialize place hierarchy map pages.
 */
export function initializePlaceHierarchyMapPage(root) {
  root.querySelectorAll('[data-wt-place-hierarchy-map]').forEach((container) => {
      if (!(container instanceof HTMLElement)) {
        throw new Error('Place hierarchy map container must be an HTML element.');
      }

      const mapElement = requireElement(container, '[data-wt-map-canvas]', HTMLDivElement, 'place hierarchy map canvas');
      const sidebar = requireElement(container, '[data-wt-map-sidebar]', HTMLElement, 'place hierarchy map sidebar');
      const leafletConfig = parseJsonDataset(requireDatasetValue(container, 'wtLeafletConfig', 'place hierarchy leaflet config'), 'place hierarchy leaflet config');
      const markerData = parseJsonDataset(requireDatasetValue(container, 'wtMapMarkers', 'place hierarchy marker data'), 'place hierarchy marker data');
      const mapBounds = parseJsonDataset(requireDatasetValue(container, 'wtMapBounds', 'place hierarchy map bounds'), 'place hierarchy map bounds');
      const sidebarHtml = requireDatasetValue(container, 'wtMapSidebar', 'place hierarchy sidebar html');

      if (mapElement.id === '') {
        throw new Error('Place hierarchy map canvas must have an id attribute.');
      }

      const { map, markers } = initializeFeaturePanelMap({
        mapElementId: mapElement.id,
        leafletConfig,
        sidebar,
        geoJsonData: markerData,
        sidebarHtml,
        fitBounds: mapBounds,
        resetWithoutMarkers: 'none',
        highlightSelector: (featureId) => '.mapped[data-wt-feature-id="' + featureId + '"]',
        clearHighlightSelector: '.mapped',
        createMarker: (feature, latlng) => new L.Marker(latlng, {
          icon: L.BeautifyIcon.icon({
            icon: 'bullseye fas',
            borderColor: 'transparent',
            backgroundColor: '#1e90ff',
            iconShape: 'marker',
            textColor: 'white',
          }),
          title: feature.properties.tooltip,
          id: feature.id,
        }),
        onEachFeature: (feature, layer) => {
          layer.bindPopup(feature.properties.popup);
        },
      });


      sidebar.querySelectorAll('.mapped').forEach((item) => {
        if (!(item instanceof HTMLElement)) {
          return;
        }

        const featureId = Number.parseInt(item.dataset.wtFeatureId ?? '', 10);

        if (!Number.isInteger(featureId)) {
          return;
        }

        item.addEventListener('click', () => {
          map.closePopup();
          zoomToFeaturePopup(markers, featureId);
          return false;
        });

        stopNestedLinkClicks(item);
      });
    });
}
