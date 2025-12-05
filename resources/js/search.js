window.openSearch = function () {
    const el = document.getElementById("searchBarContainer");
    const searchForm = document.getElementById("searchForm");
    searchForm.classList.remove("bounceOut");
    el.classList.toggle("opacity-100");
    el.classList.toggle("hidden");
};

window.closeSearch = function () {
    const el = document.getElementById("searchBarContainer");
    const searchForm = document.getElementById("searchForm");

    searchForm.classList.add("bounceOut");
    el.classList.toggle("opacity-100");
    setTimeout(() => {
        el.classList.add("hidden");
    }, 1000);
};

document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("searchBarContainer");
    const searchForm = document.getElementById("searchForm");
    if (!container || !searchForm) return;

    searchForm.addEventListener("click", function (e) {
        e.stopPropagation();
    });

    container.addEventListener("click", function (e) {
        if (
            e.target === container ||
            e.target.classList.contains("backdrop-blur-sm")
        ) {
            closeSearch();
        }
    });

    const closeButton = document.getElementById("searchBtn");
    if (closeButton) {
        closeButton.addEventListener("click", (e) => {
            e.preventDefault();
            closeSearch();
        });
    }
});
