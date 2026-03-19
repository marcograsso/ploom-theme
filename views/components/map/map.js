import "mapbox-gl/dist/mapbox-gl.css";
import mapboxgl from "mapbox-gl";
import mapPinSvg from "../../svgs/MapPin.svg?raw";

import gsap from "gsap";
import Alpine from "alpinejs";

Alpine.magic("unescape", () => (str) => {
  const ta = document.createElement("textarea");
  ta.innerHTML = str ?? "";
  return ta.value;
});

Alpine.data("MappingLocations", (entities) => {
  let map;
  let markers = [];

  return {
    entities: entities,
    filteredEntities: entities,
    selectedEntityScope: "",
    selectedEntityType: "",
    selectedEntity: null,

    init() {
      // Center the map on the first entity's location
      let lng = this.entities[0].location.longitude;
      let lat = this.entities[0].location.latitude;

      // TODO: Move to config
      mapboxgl.accessToken = import.meta.env.VITE_MAPBOX_TOKEN;

      map = new mapboxgl.Map({
        container: "map",
        center: [lng, lat],
        zoom: 13,
        style: "mapbox://styles/mapbox/dark-v11",
      });

      this.addMarkers();
      this.selectEntity(this.entities[0].id, this.entities[0].location);
    },

    addMarkers() {
      const flavourColors = {
        arctic: "#1bc49e",
        wild: "#e376fb",
        shop: "#3567FF",
      };

      this.filteredEntities.forEach((entity, index) => {
        if (!entity.location?.latitude || !entity.location?.longitude) return;

        const isExpired =
          entity.end_date &&
          entity.end_date < new Date().toISOString().slice(0, 10);
        const color = isExpired
          ? "#cbcbcb"
          : (flavourColors[entity.flavour] ?? "#888");

        const el = document.createElement("div");
        el.innerHTML = mapPinSvg;
        el.style.cursor = "pointer";
        const path = el.querySelector("path");
        if (path) path.style.fill = color;

        const marker = new mapboxgl.Marker({ element: el })
          .setLngLat([entity.location.longitude, entity.location.latitude])
          .addTo(map);

        el.addEventListener("click", () => {
          this.selectEntity(entity.id, entity.location);
        });

        this.animateMarkerIn(marker, index);
        markers.push(marker);
      });
    },

    animateMarkerIn(marker, index = 0) {
      let tl = gsap.timeline({
        defaults: { ease: "power2.out", duration: 0.18 },
      });
      let markerEl = marker.getElement();
      tl.fromTo(
        markerEl,
        {
          top: "-5%",
          opacity: 0,
          scale: 0.8,
        },
        {
          top: "0%",
          opacity: 1,
          scale: 1,
          delay: index * 0.005,
        },
      );
      tl.to(markerEl, {
        onComplete: () => {},
      });
    },

    animateMarkerOut(marker, index = 0) {
      let tl = gsap.timeline({
        defaults: { ease: "power2.out", duration: 0.18 },
      });
      let markerEl = marker.getElement();
      tl.to(markerEl, {
        top: "-5%",
        opacity: 0,
        delay: index * 0.005,
      });
      tl.to(markerEl, {
        onComplete: () => {
          marker.remove();
        },
      });
      return tl;
    },

    async clearMarkers() {
      const animationPromises = markers.map((marker, index) =>
        this.animateMarkerOut(marker, index),
      );

      await Promise.all(animationPromises);
      markers = [];
    },

    async filterEntities() {
      this.filteredEntities = this.entities.filter((entity) => {
        // Only filter by entity scope
        if (this.selectedEntityScope && !this.selectedEntityType) {
          return entity.entity_scopes
            .map((scope) => scope.value)
            .includes(this.selectedEntityScope);
        }
        // Only filter by entity type
        if (!this.selectedEntityScope && this.selectedEntityType) {
          return entity.entity_type === this.selectedEntityType;
        }
        // Filter by entity scope and entity type
        if (this.selectedEntityScope && this.selectedEntityType) {
          return (
            entity.entity_scopes
              .map((scope) => scope.value)
              .includes(this.selectedEntityScope) &&
            entity.entity_type === this.selectedEntityType
          );
        }

        // No filters
        return true;
      });

      await this.clearMarkers();
      this.addMarkers();
    },
    updateSelectedEntityScope(event) {
      this.selectedEntityScope = event.target.value;
      this.filterEntities();
    },
    updateSelectedEntityType(event) {
      this.selectedEntityType = event.target.value;
      this.filterEntities();
    },
    setMarkerColor(marker, color) {
      const path = marker.getElement().querySelector("svg path");
      if (path) path.style.fill = color;
    },

    selectEntity(id, location, scroll = true) {
      this.selectedEntity = this.entities.find((entity) => entity.id === id);

      const flavourColors = {
        arctic: "#1bc49e",
        wild: "#e376fb",
        shop: "#ffffff",
      };

      markers.forEach((marker, i) => {
        const entity = this.entities[i];
        const isExpired =
          entity?.end_date &&
          entity.end_date < new Date().toISOString().slice(0, 10);
        marker.getElement().style.zIndex = 0;
        const path = marker.getElement().querySelector("svg path");
        if (path) {
          path.style.fill = isExpired
            ? "#cbcbcb"
            : (flavourColors[entity?.flavour] ?? "#888");
          path.style.stroke = "transparent";
          path.style.strokeWidth = "2px";
          path.style.paintOrder = "stroke fill";
        }
      });

      const marker = markers.find((marker) => {
        let longitude = marker.getLngLat().lng;
        let latitude = marker.getLngLat().lat;

        return longitude == location.longitude && latitude == location.latitude;
      });

      if (marker) {
        const selectedEntity = this.entities.find((e) => e.id === id);
        const isShop = selectedEntity?.flavour === "shop";
        const borderColor = isShop ? "#2d023f" : "#ffffff";
        marker.getElement().style.zIndex = 1000;
        const path = marker.getElement().querySelector("svg path");
        if (path) {
          path.style.stroke = borderColor;
          path.style.strokeWidth = "2px";
          path.style.paintOrder = "stroke fill";
        }
      }

      map.flyTo({
        center: [location.longitude, location.latitude],
        zoom: 13,
        duration: 1000,
      });

      // scroll the sidebar to the selected entity
      const sidebar = document.getElementById("map-sidebar");
      if (sidebar) {
        const button = sidebar.querySelector(`#entity-button-${id}`);
        if (button && scroll) {
          sidebar.scrollTo({
            top:
              button.getBoundingClientRect().top -
              sidebar.getBoundingClientRect().top,
            behavior: "smooth",
          });
        }
      }
    },

    moveTabIndexToButton(event) {
      var targetButton = event.target.closest("button");

      // Since we're using event-delegation on the host, it's possible that the click
      // event isn't targeting a button. In that case, ignore the event.
      if (!targetButton) {
        return;
      }

      for (var button of this.getAllButtons()) {
        button.tabIndex = -1;
      }

      targetButton.tabIndex = 0;
    },

    /**
     * I move the focus and active tabIndex (0) to the next button in the set of buttons
     * contained within the host element.
     */
    moveToNextButton(event) {
      // Prevent any default browser behaviors (such as scrolling the viewport).
      event.preventDefault();

      // Note: Technically, we're using event-delegation for the arrow keys. However,
      // since no other elements (other than our demo buttons) can be focused within the
      // host element, we can be confident that this was triggered by a button.
      var targetButton = event.target.closest("button");
      var allButtons = this.getAllButtons();
      var currentIndex = allButtons.indexOf(targetButton);
      // Get the NEXT button; or, loop around to the front of the collection.
      var futureButton = allButtons[currentIndex + 1] || allButtons[0];

      targetButton.tabIndex = -1;
      futureButton.tabIndex = 0;
      futureButton.focus();
    },

    /**
     * I move the focus and active tabIndex (0) to the previous button in the set of
     * buttons contained within the host element.
     */
    moveToPrevButton(event) {
      // Prevent any default browser behaviors (such as scrolling the viewport).
      event.preventDefault();

      // Note: Technically, we're using event-delegation for the arrow keys. However,
      // since no other elements (other than our demo buttons) can be focused within the
      // host element, we can be confident that this was triggered by a button.
      var targetButton = event.target.closest("button");
      var allButtons = this.getAllButtons();
      var currentIndex = allButtons.indexOf(targetButton);
      // Get the PREVIOUS button; or, loop around to the back of the collection.
      var futureButton =
        allButtons[currentIndex - 1] || allButtons[allButtons.length - 1];

      targetButton.tabIndex = -1;
      futureButton.tabIndex = 0;
      futureButton.focus();
    },

    getAllButtons() {
      return Array.from(this.$root.querySelectorAll("#map-sidebar button"));
    },
  };
});
