const getModalContent = (modal) => modal.querySelector('[data-modal-content]') || modal.querySelector('.relative');

window.openModal = (modalId) => {
    const modal = document.getElementById(modalId);
    const modalContent = getModalContent(modal);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
    setTimeout(() => {
        const firstInput = modal.querySelector('input, textarea, select');
        firstInput?.focus();
    }, 300);
};

window.closeModal = (modalId) => {
    const modal = modalId ? document.getElementById(modalId) : document.querySelector('[id$="Modal"]:not(.hidden)');
    if (!modal) return;
    const modalContent = getModalContent(modal);
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.querySelectorAll('input, textarea, select').forEach(input => {
            if (["checkbox", "radio"].includes(input.type)) input.checked = false;
            else input.value = '';
        });
    }, 300);
};

document.addEventListener("DOMContentLoaded", function () {
    const tagInput = document.getElementById("tagInput");
    const tagDropdown = document.getElementById("tagDropdown");
    const fileInput = document.getElementById("fileInput");
    const urlInput = document.getElementById("url");
    if (fileInput && urlInput) {
        fileInput.addEventListener('change', () => {
            if (fileInput.files?.length) {
                urlInput.value = fileInput.files[0].name;
                urlInput.readOnly = true;
            } else {
                urlInput.value = '';
                urlInput.readOnly = false;
            }
        });
        urlInput.addEventListener('input', () => {
            if (urlInput.value.trim()) {
                fileInput.value = '';
                urlInput.readOnly = false;
            }
        });
    }

    if (tagInput && tagDropdown) {
        const showEmptyTagMessage = () => {
            tagDropdown.innerHTML = '<div class="py-3 px-4 text-gray-400 text-sm select-none">Premier tag, à toi de jouer !</div>';
        };
        const showTagSuggestions = (inputValue) => {
            const allTags = (window.tagsFromBlade || []).map(tag => tag.nom);
            const filtered = allTags.filter(tag =>
                tag.toLowerCase().includes(inputValue.toLowerCase()) && !selectedTags.includes(tag)
            );
            let html = filtered.map(tag => 
                `<div class="tag-suggestion py-3 px-4 cursor-pointer hover:bg-blue-50 select-none text-sm flex items-center gap-2" data-tag="${tag}">
                    <i class='fa-solid fa-tag text-blue'></i> <span>${tag}</span>
                </div>`
            ).join('');
            if (inputValue && !allTags.map(t => t.toLowerCase()).includes(inputValue.toLowerCase()) && !selectedTags.includes(inputValue)) {
                html += `<div id="addTagSuggestion" class="py-3 px-4 text-blue text-sm cursor-pointer hover:bg-blue-50 select-none flex items-center gap-2">+ Ajouter <span class="font-semibold">${inputValue}</span></div>`;
            }
            tagDropdown.innerHTML = html || showEmptyTagMessage();
        };

        const selectedTagsDiv = document.getElementById("selectedTags");
        const tagsHiddenInput = document.getElementById("tagsHidden");
        let selectedTags = [];

        const renderSelectedTags = () => {
            selectedTagsDiv.innerHTML = '';
            selectedTags.forEach(tag => {
                const tagEl = document.createElement('span');
                tagEl.className = 'bg-blue/10 text-blue px-3 py-1 rounded-xl flex items-center gap-2 text-sm font-medium';
                tagEl.innerHTML = `${tag} <button type="button" class="ml-1 text-blue hover:text-red-500 remove-tag" data-tag="${tag}"><i class="fa-solid fa-times text-blue"></i></button>`;
                selectedTagsDiv.appendChild(tagEl);
            });
            tagsHiddenInput.value = selectedTags.join(',');
        };

        selectedTagsDiv?.addEventListener("click", function (e) {
            if (e.target.closest(".remove-tag")) {
                const tag = e.target
                    .closest(".remove-tag")
                    .getAttribute("data-tag");
                selectedTags = selectedTags.filter((t) => t !== tag);
                renderSelectedTags();
            }
        });

        const createTag = async (tag) => {
            if (!tag.trim() || selectedTags.includes(tag)) return;
            try {
                const response = await fetch('/tags/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ nom: tag })
                });
                if (response.ok) {
                    selectedTags.push(tag);
                    renderSelectedTags();
                    tagInput.value = '';
                    tagDropdown.style.display = 'none';
                } else {
                    alert('Erreur lors de la création du tag.');
                }
            } catch (e) {
                alert('Erreur réseau lors de la création du tag.');
            }
        };
        ['focus', 'click'].forEach(evt => tagInput.addEventListener(evt, () => {
            showTagSuggestions(tagInput.value.trim());
            tagDropdown.style.display = 'block';
        }));
        tagInput.addEventListener('input', () => showTagSuggestions(tagInput.value.trim()));

        tagDropdown.addEventListener('click', e => {
            const addTagSuggestion = document.getElementById('addTagSuggestion');
            if (addTagSuggestion && (e.target === addTagSuggestion || addTagSuggestion.contains(e.target))) {
                const tag = tagInput.value.trim();
                if (tag) createTag(tag);
                return;
            }
            const tagDiv = e.target.closest('.tag-suggestion');
            if (tagDiv) {
                const tag = tagDiv.getAttribute('data-tag');
                if (tag && !selectedTags.includes(tag)) {
                    selectedTags.push(tag);
                    renderSelectedTags();
                    tagInput.value = '';
                    tagDropdown.style.display = 'none';
                }
            }
        });

        const addTagBtn = document.getElementById('addTagBtn');
        addTagBtn?.addEventListener('click', () => {
            const tag = tagInput.value.trim();
            if (tag) createTag(tag);
        });
    }
    const starRating = document.getElementById('starRating');
    const noteValue = document.getElementById('noteValue');
    if (starRating && noteValue) {
        let currentRating = parseInt(noteValue.value) || 3;
        const stars = Array.from(starRating.querySelectorAll('i[data-rating]'));
        const updateStars = rating => {
            stars.forEach(star => {
                const starNum = parseInt(star.getAttribute('data-rating'));
                star.classList.toggle('text-light-lime', starNum <= rating);
                star.classList.toggle('text-gray-300', starNum > rating);
            });
        };
        updateStars(currentRating);
        stars.forEach(star => {
            star.addEventListener('mouseenter', () => updateStars(parseInt(star.getAttribute('data-rating'))));
            star.addEventListener('mouseleave', () => updateStars(currentRating));
            star.addEventListener('click', () => {
                currentRating = parseInt(star.getAttribute('data-rating'));
                noteValue.value = currentRating;
                updateStars(currentRating);
            });
        });
        starRating.addEventListener('keydown', e => {
            if (e.key >= '1' && e.key <= '5') {
                currentRating = parseInt(e.key);
                noteValue.value = currentRating;
                updateStars(currentRating);
            }
        });
    }

    document.addEventListener("click", function (e) {
        if (["INPUT", "TEXTAREA", "SELECT"].includes(e.target.tagName)) {
            return;
        }
        const closeBtn = e.target.closest("[data-close-modal], .close-modal");
        if (closeBtn) {
            const modal = closeBtn.closest('[id$="Modal"]');
            if (modal) closeModal(modal.id);
        }
        const modalOverlay = e.target.closest('[id$="Modal"]');
        if (
            modalOverlay &&
            (e.target === modalOverlay ||
                e.target.classList.contains("bg-black/20") ||
                e.target.classList.contains("backdrop-blur-sm"))
        ) {
            closeModal(modalOverlay.id);
        }
        const tagDropdown = document.getElementById("tagDropdown");
        if (tagDropdown && tagDropdown.style.display === "block") {
            if (e.target !== tagInput && !tagDropdown.contains(e.target)) {
                tagDropdown.style.display = "none";
            }
        }
    });
});
