window.openSearch = function () {
    const el = document.getElementById("searchBarContainer");
    el.classList.toggle("hidden");
};

window.closeSearch = function () {
    const el = document.getElementById("searchBarContainer");
    const searchEl = document.getElementById("searchBar").querySelector("div");

    searchEl.classList.add("bounceOut");
    setTimeout(() => {
        el.classList.add("hidden");
        searchEl.classList.remove("bounceOut");
    }, 1000);
};

document.addEventListener("DOMContentLoaded", () => {
    const el = document.getElementById("searchBar");
    const elContent = el.querySelector("div");

    elContent.addEventListener("click", function (e) {
        e.stopPropagation();
    });

    el.addEventListener("click", function (e) {
        if (
            e.target === el ||
            e.target.classList.contains("bg-black/10") ||
            e.target.classList.contains("backdrop-blur-sm")
        ) {
            closeSearch();
        }
    });

    const closeButtons = el.querySelectorAll(".searchBtn");
    closeButtons.forEach((button) => {
        button.addEventListener("click", () => closeSearch());
    });
});
