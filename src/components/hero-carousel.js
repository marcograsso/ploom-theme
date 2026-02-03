import Swiper from "swiper";
import { Navigation, Pagination } from "swiper/modules";

import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

export default function initHeroCarousels() {
  Array.from(document.querySelectorAll(".hero-carousel")).forEach(
    (carousel) => {
      new Swiper(carousel, {
        modules: [Navigation, Pagination],
        loop: true,
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
      });
    },
  );
}
