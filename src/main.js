import "non.geist";
import "non.geist/mono";

import Alpine from "alpinejs";

import initHeroCarousels from "./components/hero-carousel";
import { initAnimations } from "./components/animations";

import "./main.css";

import.meta.glob("../views/**/*.js", { eager: true });
import.meta.glob("../views/**/*.css", { eager: true });

document.addEventListener("DOMContentLoaded", () => {
  initHeroCarousels();
  initAnimations();
});

window.Alpine = Alpine;
Alpine.start();
