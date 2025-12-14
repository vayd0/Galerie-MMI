import gsap from "gsap";
import SplitType from "split-type";

const title = document.getElementsByClassName("entrance-text");
const desc = document.getElementsByClassName("entrance-desc");

gsap.set(desc, { opacity: 1 });

gsap.set("h1", { opacity: 1 });

let splitText = new SplitType(title, { type: "chars" });

const splitDesc = new SplitType(desc, {
    types: "lines, words, chars",
    tagName: "span",
});

document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        const toast = document.querySelector(".toast-gsap");

        gsap.from(splitDesc.words, {
            opacity: 0.3,
            duration: 1,
            ease: "bounce.out",
            stagger: 0.1,
        });

        gsap.from(splitText.chars, {
            y: 10,
            autoAlpha: 0,
            stagger: 0.1,
        });

        if (toast) {
            gsap.set(toast, { x: -500, opacity: 0 });
            const tl = gsap.timeline();
            tl.to(toast, {
                x: 0,
                opacity: 1,
                duration: 0.6,
                ease: "back.out(1.7)",
            }).to(
                toast,
                { x: 500, opacity: 0, duration: 0.6, ease: "back.in(1.7)" },
                "+=3"
            );
        }
    }, 1200);
});

window.animateTo = function (element, x, y, options = {}) {
    const currentX = gsap.getProperty(element, "x") || 0;
    const currentY = gsap.getProperty(element, "y") || 0;

    gsap.to(element, {
        x: currentX + x,
        y: currentY + y,
        duration: options.duration || 0.8,
        ease: options.ease || "power2.out",
        ...options,
    });
};
