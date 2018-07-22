<?php use Fisharebest\Webtrees\I18N; ?>

<div class="py-4">
    <div class="row gchart osm-wrapper">
        <div id="osm-map" class="col-sm-9 wt-ajax-load osm-user-map"></div>
        <ul class='col-sm-3 osm-sidebar wt-page-options-value'></ul>
    </div>
</div>

<style>
    .osm-wrapper, .osm-user-map {
        height: 45vh
    }

    .osm-admin-map {
        height: 55vh;
        border: 1px solid darkGrey
    }

    .osm-sidebar {
        height: 100%;
        overflow-y: auto;
        padding: 0;
        margin: 0;
        border: 0;
        display: none;
        font-size: small;
    }

    .osm-sidebar .gchart {
        margin: 1px;
        padding: 2px
    }

    .osm-sidebar .gchart img {
        height: 15px;
        width: 25px
    }

    .osm-sidebar .border-danger:hover {
        cursor: not-allowed
    }

    [dir=rtl] .leaflet-right {
        right: auto;
        left: 0
    }

    [dir=rtl] .leaflet-right .leaflet-control {
        margin-right: 0;
        margin-left: 10px
    }

    [dir=rtl] .leaflet-left {
        left: auto;
        right: 0
    }

    [dir=rtl] .leaflet-left .leaflet-control {
        margin-left: 0;
        margin-right: 10px
    }
</style>

<script type="application/javascript">
  "use strict";

  window.WT_OSM = (function () {
    let baseData = {
      minZoom:         2,
      providerName:    "OpenStreetMap.Mapnik",
      providerOptions: [],
      I18N:            {
        zoomInTitle: <?= json_encode(I18N::translate('Zoom in')) ?>,
        zoomOutTitle: <?= json_encode(I18N::translate('Zoom out')) ?>,
        reset: <?= json_encode(I18N::translate('Reset to initial map state')) ?>,
        noData: <?= json_encode(I18N::translate('No mappable items')) ?>
      }
    };

    let map     = null;
    let zoom    = null;
    let markers = L.markerClusterGroup({
      showCoverageOnHover: false,
    });

    let resetControl = L.Control.extend({
      options: {
        position: "topleft",
      },

      onAdd: function (map) {
        let container     = L.DomUtil.create("div", "leaflet-bar leaflet-control leaflet-control-custom");
        container.onclick = function () {
          if (zoom) {
            map.flyTo(markers.getBounds().getCenter(), zoom);
          } else {
            map.flyToBounds(markers.getBounds().pad(0.2));
          }
          return false;
        };
        let anchor        = L.DomUtil.create("a", "leaflet-control-reset", container);
        anchor.href       = "#";
        anchor.title      = baseData.I18N.reset;
        anchor.role       = "button";
        $(anchor).attr("aria-label", "reset");
        let image = L.DomUtil.create("i", "fas fa-redo", anchor);
        image.alt = baseData.I18N.reset;

        return container;
      },
    });

    /**
     *
     * @private
     */
    let _drawMap = function () {
      map = L.map("osm-map", {
        center:      [0, 0],
        minZoom:     baseData.minZoom, // maxZoom set by leaflet-providers.js
        zoomControl: false, // remove default
      });
      L.tileLayer.provider(baseData.providerName, baseData.providerOptions).addTo(map);
      L.control.zoom({ // Add zoom with localised text
        zoomInTitle:  baseData.I18N.zoomInTitle,
        zoomOutTitle: baseData.I18N.zoomOutTitle,
      }).addTo(map);
    };

    let _addLayer = function () {
      let geoJsonLayer;
      let domObj  = ".osm-sidebar";
      let sidebar = "";

      let data = <?= json_encode($data) ?>;

      geoJsonLayer = L.geoJson(data, {
        pointToLayer:  function (feature, latlng) {
          return new L.Marker(latlng, {
            icon:  L.BeautifyIcon.icon({
              icon:            feature.properties.icon["name"],
              borderColor:     "transparent",
              backgroundColor: feature.valid ? feature.properties.icon["color"] : "transparent",
              iconShape:       "marker",
              textColor:       feature.valid ? "white" : "transparent",
            }),
            title: feature.properties.tooltip,
            alt:   feature.properties.tooltip,
            id:    feature.id,
          })
            .on("popupopen", function (e) {
              let sidebar = $(".osm-sidebar");
              let item    = sidebar.children(".gchart[data-id=" + e.target.feature.id + "]");
              item.addClass("messagebox");
              sidebar.scrollTo(item);
            })
            .on("popupclose", function () {
              $(".osm-sidebar").children(".gchart")
                .removeClass("messagebox");
            });
        },
        onEachFeature: function (feature, layer) {
          if (feature.properties.polyline) {
            let pline = L.polyline(feature.properties.polyline.points, feature.properties.polyline.options);
            markers.addLayer(pline);
          }
          layer.bindPopup(feature.properties.summary);
          let myclass = feature.valid ? "gchart" : "border border-danger";
          sidebar += `<li class="${myclass}" data-id=${feature.id}>${feature.properties.summary}</li>`;
        },
      });

      if (data.features.length > 0) {
        $(domObj).append(sidebar);
        markers.addLayer(geoJsonLayer);
        map
          .addControl(new resetControl())
          .addLayer(markers);

        if (data.features.length === 1) {
          map.setView(markers.getBounds().getCenter(), data.features[0].properties.zoom);
        } else {
          map.fitBounds(markers.getBounds().pad(0.2))
        }
      } else {
        map.fitWorld();
        $(domObj).append("<div class=\"bg-info text-white\">" + baseData.I18N.noData + "</div>");
      }

      $(domObj).slideDown(300);
    };

    /**
     *
     * @param elem
     * @returns {$}
     */

    $.fn.scrollTo = function (elem) {
      let _this = $(this);
      _this.animate({
        scrollTop: elem.offset().top - _this.offset().top + _this.scrollTop(),
      });
      return this;
    };

    return {
      drawMap: function () {
        _drawMap();
        _addLayer();

        // Activate marker popup when sidebar entry clicked
        $(".osm-sidebar")
          .on("click", ".gchart", function (e) {
            // first close any existing
            map.closePopup();
            let eventId  = $(this).data("id");
            //find the marker corresponding to the clicked event
            let mkrLayer = markers.getLayers().filter(function (v) {
              return typeof(v.feature) !== "undefined" && v.feature.id === eventId;
            });
            let mkr      = mkrLayer.pop();
            // Unfortunately zoomToShowLayer zooms to maxZoom
            // when all marker in a cluster have exactly the
            // same co-ordinates
            markers.zoomToShowLayer(mkr, function (e) {
              mkr.openPopup();
            });
            return false;
          })
          .on("click", "a", function (e) { // stop click on a person also opening the popup
            e.stopPropagation();
          });
      },
    };
  })();

  WT_OSM.drawMap();
</script>
