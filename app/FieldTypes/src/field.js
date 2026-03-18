const $ = jQuery;

class MapboxField {
  constructor($field) {
    this.$field = $field;
    this.init();
  }
  init() {
    var $container = this.$field.find(".acf-mapbox-field");

    var apiKey = $container.data("api-key");
    var country = $container.data("country") || "us";

    var $form = this.$field.find(".acf-mapbox-form");
    var $addressValue = this.$field.find(".acf-mapbox-address-value");
    var $latValue = this.$field.find(".acf-mapbox-latitude-value");
    var $lngValue = this.$field.find(".acf-mapbox-longitude-value");

    var $minimap = this.$field.find("mapbox-address-minimap")[0];

    if (!apiKey) {
      console.error("Mapbox API key is required");
      return;
    }

    // Prevent form submission
    $form.on("submit", function (e) {
      e.preventDefault();
      return false;
    });

    // Create autofill collection - it automatically attaches to inputs with autocomplete="address-line1"
    var autofillCollection = mapboxsearch.autofill({
      accessToken: apiKey,
      options: {
        country: country,
      },
    });

    // Listen for retrieve event when user selects an address
    autofillCollection.addEventListener("retrieve", function (event) {
      var feature = event.detail.features[0];

      if (feature && feature.geometry && feature.geometry.coordinates) {
        var coordinates = feature.geometry.coordinates;
        var address =
          feature.properties.full_address || feature.properties.name || "";

        // Update hidden input values
        $addressValue.val(address);

        // A quanto pare alcuni fields vengono precompilati automaticamente da Mapbox

        $latValue.val(coordinates[1]); // latitude
        $lngValue.val(coordinates[0]); // longitude

        // Update the minimap
        if ($minimap) {
          $minimap.feature = feature;
          $minimap.showMarker = true;
        }

        // Trigger change event for ACF
        $addressValue.trigger("change");
      }
    });

    // Configure minimap
    if ($minimap) {
      $minimap.accessToken = apiKey;

      // $minimap.defaultMapStyle = ["mapbox", "outdoors-v11"];
      $minimap.theme = {
        variables: {
          border: "13px solid #bbb",
          borderRadius: "18px",
          boxShadow: "0 2px 8px #000",
        },
      };

      $minimap.feature = {
        type: "Feature",
        geometry: { type: "Point", coordinates: [11.355563, 44.488245] },
        properties: {},
      };

      // Load existing location if available
      var existingLat = $latValue.val();
      var existingLng = $lngValue.val();
      var existingAddress = $addressValue.val();

      if (existingLat && existingLng && existingAddress) {
        // Create a feature from existing data
        var existingFeature = {
          type: "Feature",
          geometry: {
            type: "Point",
            coordinates: [parseFloat(existingLng), parseFloat(existingLat)],
          },
          properties: {
            full_address: existingAddress,
          },
        };
        $minimap.feature = existingFeature;
        $minimap.showMarker = true;
      }

      // Handle marker adjustment
      $minimap.addEventListener("saveMarkerLocation", (event) => {
        if (event.detail && event.detail.coordinates) {
          var coords = event.detail.coordinates;
          $latValue.val(coords.lat);
          $lngValue.val(coords.lng);

          // Trigger change event for ACF
          $latValue.trigger("change");
        }
      });
    }
  }
}

if (typeof acf !== "undefined") {
  if (typeof acf.add_action !== "undefined") {
    acf.add_action("ready append", function ($el) {
      acf.get_fields({ type: "mapbox" }, $el).each(function () {
        new MapboxField($(this));
      });
    });
  }
}
