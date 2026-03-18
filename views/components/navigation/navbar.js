import { gsap } from "gsap";
import { ScrollToPlugin } from "gsap/ScrollToPlugin";

gsap.registerPlugin(ScrollToPlugin);

const navbar = document.getElementById("navbar");
if (navbar) {
  const navbarBorder = document.getElementById("navbar-border");
  if (navbarBorder) {
    navbarBorder.style.background =
      "linear-gradient(90deg, rgba(245, 245, 245, 0) 0%, rgba(245, 245, 245, 0.8) 50.48%, rgba(245, 245, 245, 0) 100%)";
  }

  const blurOverlay = document.getElementById("navbar-blur");
  if (blurOverlay) {
    blurOverlay.style.backdropFilter = "blur(3px)";
    blurOverlay.style.background = "rgba(0,0,0,0.2)";
    blurOverlay.style.maskImage =
      "linear-gradient(90deg, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 20%, rgba(0,0,0,1) 80%, rgba(0,0,0,0) 100%)";
  }

  const onScroll = () => {
    if (window.scrollY > 20) {
      blurOverlay?.classList.replace("opacity-0", "opacity-100");
    } else {
      blurOverlay?.classList.replace("opacity-100", "opacity-0");
    }
  };
  window.addEventListener("scroll", onScroll, { passive: true });
}

const scrollToTarget = (target) => {
  const el = document.querySelector(target);
  if (!el) return;
  const offsetY = (navbar ? navbar.offsetHeight : 0) + 80;
  gsap.to(window, {
    duration: 1,
    scrollTo: { y: el, offsetY },
    ease: "power3.inOut",
  });
};

document.querySelectorAll("[data-scroll-to]").forEach((link) => {
  link.addEventListener("click", (e) => {
    e.preventDefault();
    const target = link.dataset.scrollTo;
    if (!document.querySelector(target)) {
      window.location.href = "/" + target;
      return;
    }
    scrollToTarget(target);
  });
});

if (window.location.hash) {
  window.addEventListener("load", () => {
    setTimeout(() => scrollToTarget(window.location.hash), 100);
  });
}
