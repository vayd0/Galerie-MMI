document.addEventListener("DOMContentLoaded", function () {
    const editTagInput = document.getElementById("editAlbumTagInput");
    const editTagDropdown = document.getElementById("editAlbumTagDropdown");
    const editSelectedTagsDiv = document.getElementById("editAlbumSelectedTags");
    const editTagsHiddenInput = document.getElementById("editAlbumTagsHidden");
    const editAddTagBtn = document.getElementById("editAlbumAddTagBtn");

    let editSelectedTags = [];

    window.initializeEditAlbumTags = function(albumTags) {
        editSelectedTags = albumTags || [];
        renderEditSelectedTags();
    };

    const showEditEmptyTagMessage = () => {
        if (editTagDropdown) {
            editTagDropdown.innerHTML = '<div class="py-3 px-4 text-gray-400 text-sm select-none">Aucun tag disponible</div>';
        }
    };

    const showEditTagSuggestions = (inputValue) => {
        if (!editTagDropdown) return;
        
        const allTags = (window.editAlbumTagsFromBlade || []).map(tag => tag.nom || tag);
        const filtered = allTags.filter(tag =>
            tag.toLowerCase().includes(inputValue.toLowerCase()) && !editSelectedTags.includes(tag)
        );
        
        let html = filtered.map(tag => 
            `<div class="tag-suggestion py-3 px-4 cursor-pointer hover:bg-blue-50 select-none text-sm flex items-center gap-2" data-tag="${tag}">
                <i class='fa-solid fa-tag text-blue'></i> <span>${tag}</span>
            </div>`
        ).join('');
        
        if (inputValue && !allTags.map(t => t.toLowerCase()).includes(inputValue.toLowerCase()) && !editSelectedTags.includes(inputValue)) {
            html += `<div id="editAddTagSuggestion" class="py-3 px-4 text-blue text-sm cursor-pointer hover:bg-blue-50 select-none flex items-center gap-2">+ Ajouter <span class="font-semibold">${inputValue}</span></div>`;
        }
        
        editTagDropdown.innerHTML = html || '<div class="py-3 px-4 text-gray-400 text-sm select-none">Aucun tag trouvé</div>';
    };

    const renderEditSelectedTags = () => {
        if (!editSelectedTagsDiv || !editTagsHiddenInput) return;
        
        editSelectedTagsDiv.innerHTML = '';
        editSelectedTags.forEach(tag => {
            const tagEl = document.createElement('span');
            tagEl.className = 'bg-blue/10 text-blue px-3 py-1 rounded-xl flex items-center gap-2 text-sm font-medium';
            tagEl.innerHTML = `${tag} <button type="button" class="ml-1 text-blue hover:text-red-500 remove-edit-tag" data-tag="${tag}"><i class="fa-solid fa-times text-blue"></i></button>`;
            editSelectedTagsDiv.appendChild(tagEl);
        });
        editTagsHiddenInput.value = editSelectedTags.join(',');
    };

    if (editSelectedTagsDiv) {
        editSelectedTagsDiv.addEventListener("click", function (e) {
            if (e.target.closest(".remove-edit-tag")) {
                const tag = e.target.closest(".remove-edit-tag").getAttribute("data-tag");
                editSelectedTags = editSelectedTags.filter((t) => t !== tag);
                renderEditSelectedTags();
            }
        });
    }

    const createEditTag = async (tag) => {
        if (!tag.trim() || editSelectedTags.includes(tag)) return;
        try {
            const response = await fetch('/tags/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ nom: tag })
            });
            if (response.ok) {
                editSelectedTags.push(tag);
                renderEditSelectedTags();
                if (editTagInput) editTagInput.value = '';
                if (editTagDropdown) editTagDropdown.classList.add('hidden');
            } else {
                alert('Erreur lors de la création du tag.');
            }
        } catch (e) {
            alert('Erreur réseau lors de la création du tag.');
        }
    };

    if (editTagInput && editTagDropdown) {
        ['focus', 'click'].forEach(evt => editTagInput.addEventListener(evt, () => {
            showEditTagSuggestions(editTagInput.value.trim());
            editTagDropdown.classList.remove('hidden');
        }));

        editTagInput.addEventListener('input', () => showEditTagSuggestions(editTagInput.value.trim()));

        editTagDropdown.addEventListener('click', e => {
            const editAddTagSuggestion = document.getElementById('editAddTagSuggestion');
            if (editAddTagSuggestion && (e.target === editAddTagSuggestion || editAddTagSuggestion.contains(e.target))) {
                const tag = editTagInput.value.trim();
                if (tag) createEditTag(tag);
                return;
            }
            const tagDiv = e.target.closest('.tag-suggestion');
            if (tagDiv) {
                const tag = tagDiv.getAttribute('data-tag');
                if (tag && !editSelectedTags.includes(tag)) {
                    editSelectedTags.push(tag);
                    renderEditSelectedTags();
                    editTagInput.value = '';
                    editTagDropdown.classList.add('hidden');
                }
            }
        });

        if (editAddTagBtn) {
            editAddTagBtn.addEventListener('click', () => {
                const tag = editTagInput.value.trim();
                if (tag) createEditTag(tag);
            });
        }
    }

    document.addEventListener('click', (e) => {
        if (editTagDropdown && !editTagDropdown.classList.contains('hidden')) {
            if (e.target !== editTagInput && !editTagDropdown.contains(e.target)) {
                editTagDropdown.classList.add('hidden');
            }
        }
    });
});
