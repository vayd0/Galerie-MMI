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
    const searchInput = searchForm?.querySelector('input[type="text"]');
    const searchContent = document.getElementById("searchBarContent");
    const searchBtn = document.getElementById("searchBtn");

    if (!container || !searchForm || !searchInput || !searchContent || !searchBtn) return;

    searchForm.addEventListener("click", e => e.stopPropagation());
    container.addEventListener("click", e => {
        if (e.target === container || e.target.classList.contains("backdrop-blur-sm")) closeSearch();
    });

    function renderResult(type, obj) {
        if (type === "album") {
            return `<a href="/albums/${obj.id}" class="w-full flex items-center justify-between py-4 cursor-pointer album-result no-underline">
                <div class="flex items-center gap-6">
                    <i class="fa fa-folder text-darkblue text-2xl"></i>
                    <h1 class="text-white">${obj.titre}</h1>
                </div>
                <div class="mr-3 mx-auto flex items-center">
                    <h1 class="text-white">${obj.username || "Inconnu"}</h1>
                </div>
            </a>`;
        }
        if (type === "photo") {
            return `<a href="/photos/${obj.id}" class="w-full flex items-center justify-between py-4 cursor-pointer photo-result no-underline">
                <div class="flex items-center gap-6">
                    <img class="h-[2rem] aspect-square object-cover rounded" src="${obj.url}" alt="${obj.titre}">
                    <h1 class="text-white">${obj.titre}</h1>
                </div>
                <div class="mr-3 flex items-center">
                    <h1 class="text-white">${obj.username || "Inconnu"}</h1>
                </div>
            </a>`;
        }
        return "";
    }

    function renderAll() {
        fetch('/search')
            .then(res => res.ok ? res.json() : Promise.reject())
            .then(data => {
                let html = "";
                if (data.albums?.length) html += data.albums.map(a => renderResult("album", a)).join("");
                if (data.photos?.length) html += data.photos.map(p => renderResult("photo", p)).join("");
                if (!html) html = `<div class=\"text-white text-center py-4\">Aucun résultat</div>`;
                searchContent.style.marginTop = "1rem";
                searchContent.innerHTML = html;
            })
            .catch(() => {
                searchContent.innerHTML = `<div class=\"text-white text-center py-4\">Erreur lors de la recherche</div>`;
            });
    }

    function doSearch() {
        const query = searchInput.value.trim();
        if (!query) {
            renderAll();
            return;
        }
        fetch(`/search?q=${encodeURIComponent(query)}`)
            .then(res => res.ok ? res.json() : Promise.reject())
            .then(data => {
                let html = "";
                if (data.albums?.length) html += data.albums.map(a => renderResult("album", a)).join("");
                if (data.photos?.length) html += data.photos.map(p => renderResult("photo", p)).join("");
                if (!html) html = `<div class=\"text-white text-center py-4\">Aucun résultat</div>`;
                searchContent.innerHTML = html;
            })
            .catch(() => {
                searchContent.innerHTML = `<div class=\"text-white text-center py-4\">Erreur lors de la recherche</div>`;
            });
    }
    
    renderAll();

    searchInput.addEventListener("input", doSearch);
});
