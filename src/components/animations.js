import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export function initAnimations() {
  // Hero entrance — staggered fade in from bottom on page load
  const hero = document.querySelector("[data-hero]");
  if (hero) {
    gsap.from(hero, {
      y: 24,
      opacity: 0,
      duration: 1.4,
      ease: "power2.out",
      delay: 0.2,
    });
  }

  // Scroll-triggered fade in from bottom for .animate-scroll elements
  document.querySelectorAll(".animate-scroll").forEach((el) => {
    gsap.from(el, {
      y: 50,
      opacity: 0,
      duration: 0.8,
      ease: "power3.out",
      scrollTrigger: {
        trigger: el,
        start: "top 88%",
        once: true,
      },
    });
  });

  // Scroll-triggered staggered fade in for children of .animate-scroll-stagger
  document.querySelectorAll(".animate-scroll-stagger").forEach((el) => {
    gsap.from(el.children, {
      y: 40,
      opacity: 0,
      duration: 0.7,
      ease: "power3.out",
      stagger: 0.3,
      scrollTrigger: {
        trigger: el,
        start: "top 88%",
        once: true,
      },
    });
  });
}
