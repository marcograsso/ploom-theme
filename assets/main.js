import "non.geist";
import "non.geist/mono";
import "./styles/main.css";

import Alpine from "alpinejs";

import initHeroCarousels from "./scripts/components/hero-carousel";

document.addEventListener("DOMContentLoaded", () => {
  initHeroCarousels();
});

window.Alpine = Alpine;
Alpine.start();
