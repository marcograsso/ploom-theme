import "non.geist";
import "non.geist/mono";

import Alpine from "alpinejs";

import initHeroCarousels from "./scripts/components/hero-carousel";

import "./styles/main.css";

import.meta.glob("../views/**/*.js", { eager: true });
import.meta.glob("../views/**/*.css", { eager: true });

document.addEventListener("DOMContentLoaded", () => {
  initHeroCarousels();
});

window.Alpine = Alpine;
Alpine.start();
