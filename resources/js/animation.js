import { split } from "animejs";
import gsap from "gsap";
import SplitType from "split-type";
import { ScrollTrigger, ScrollToPlugin } from "gsap/all";

const title = document.getElementsByClassName("entrance-text");
const desc = document.getElementsByClassName("entrance-desc");

gsap.set(desc, { opacity: 1 });


gsap.set("h1", { opacity: 1 });

let splitText = new SplitType(title, { type: "chars" });

gsap.from(splitText.chars, {
  y: 10,
  autoAlpha: 0,
  stagger: 0.1
});

const splitDesc = new SplitType(desc, {
    types: "lines, words, chars",
    tagName: "span",
});

gsap.from(splitDesc.words, {
    opacity: 0.3,
    duration: 1,
    ease: "bounce.out",
    stagger: 0.1,
});

document.addEventListener('DOMContentLoaded', () => {
    const toast = document.querySelector('.toast-gsap');
    if (toast) {
        gsap.fromTo(
            toast,
            { y: 100, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.6, ease: "power3.out" }
        );

        setTimeout(() => {
            gsap.to(toast, { y: 100, opacity: 0, duration: 0.6, ease: "power3.in" });
        }, 3000);
    }
});

window.animateTo = function(element, x, y, options = {}) {
    const currentX = gsap.getProperty(element, "x") || 0;
    const currentY = gsap.getProperty(element, "y") || 0;

    gsap.to(element, {
        x: currentX + x,
        y: currentY + y,
        duration: options.duration || 0.8,
        ease: options.ease || "power2.out",
        ...options
    });
};