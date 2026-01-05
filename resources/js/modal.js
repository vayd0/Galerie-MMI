const getModalContent = (modal) => modal.querySelector('[data-modal-content]') || modal.querySelector('.relative');

window.openModal = (modalId) => {
    const modal = document.getElementById(modalId);
    const modalContent = getModalContent(modal);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    requestAnimationFrame(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
        setTimeout(() => modal.querySelector('input, textarea, select')?.focus(), 250);
    });
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
            input.type.match(/^(checkbox|radio)$/) ? input.checked = false : input.value = '';
        });
    }, 300);
};

const getCsrfToken = () => window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const showAlert = (message, type = 'error') => {
    alert(message);
};
L
const initFileUrlHandler = () => {
    const fileInput = document.getElementById("fileInput");
    const urlInput = document.getElementById("url");
    
    if (!fileInput || !urlInput) return;
    
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
};

class TagManager {
    constructor(config) {
        this.input = config.input;
        this.dropdown = config.dropdown;
        this.selectedTagsDiv = config.selectedTagsDiv;
        this.hiddenInput = config.hiddenInput;
        this.tagsData = config.tagsData || [];
        this.selectedTags = config.initialTags || [];
        this.emptyMessage = config.emptyMessage || 'Aucun tag disponible';
        this.addTagBtnId = config.addTagBtnId;
        this.addSuggestionId = config.addSuggestionId;
        
        this.init();
    }
    
    init() {
        if (!this.input || !this.dropdown) return;
        
        this.renderSelectedTags();
        this.bindEvents();
    }
    
    showTagSuggestions(inputValue) {
        const allTags = this.tagsData.map(tag => tag.nom || tag);
        const filtered = allTags.filter(tag =>
            tag.toLowerCase().includes(inputValue.toLowerCase()) && !this.selectedTags.includes(tag)
        );
        
        let html = filtered.map(tag => 
            `<div class="tag-suggestion py-3 px-4 cursor-pointer hover:bg-blue-50 select-none text-sm flex items-center gap-2" data-tag="${tag}">
                <i class='fa-solid fa-tag text-blue'></i> <span>${tag}</span>
            </div>`
        ).join('');
        
        if (inputValue && !allTags.map(t => t.toLowerCase()).includes(inputValue.toLowerCase()) && !this.selectedTags.includes(inputValue)) {
            html += `<div id="${this.addSuggestionId}" class="py-3 px-4 text-blue text-sm cursor-pointer hover:bg-blue-50 select-none flex items-center gap-2">+ Ajouter <span class="font-semibold">${inputValue}</span></div>`;
        }
        
        this.dropdown.innerHTML = html || `<div class="py-3 px-4 text-gray-400 text-sm select-none">${this.emptyMessage}</div>`;
        this.dropdown.style.display = 'block';
    }
    
    renderSelectedTags() {
        if (!this.selectedTagsDiv) return;
        
        this.selectedTagsDiv.innerHTML = '';
        this.selectedTags.forEach(tag => {
            const tagEl = document.createElement('span');
            tagEl.className = 'bg-blue/10 text-blue px-3 py-1 rounded-xl flex items-center gap-2 text-sm font-medium';
            tagEl.innerHTML = `${tag} <button type="button" class="ml-1 text-blue hover:text-red-500 remove-tag" data-tag="${tag}"><i class="fa-solid fa-times text-blue"></i></button>`;
            this.selectedTagsDiv.appendChild(tagEl);
        });
        
        if (this.hiddenInput) {
            this.hiddenInput.value = this.selectedTags.join(',');
        }
    }
    
    async createTag(tag) {
        if (!tag.trim() || this.selectedTags.includes(tag)) return;
        
        try {
            const response = await fetch('/tags/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ nom: tag })
            });
            
            if (response.ok) {
                this.selectedTags.push(tag);
                this.renderSelectedTags();
                this.input.value = '';
                this.dropdown.style.display = 'none';
            } else {
                showAlert('Erreur lors de la création du tag.');
            }
        } catch (e) {
            showAlert('Erreur réseau lors de la création du tag.');
        }
    }
    
    addTag(tag) {
        if (tag && !this.selectedTags.includes(tag)) {
            this.selectedTags.push(tag);
            this.renderSelectedTags();
            this.input.value = '';
            this.dropdown.style.display = 'none';
        }
    }
    
    removeTag(tag) {
        this.selectedTags = this.selectedTags.filter(t => t !== tag);
        this.renderSelectedTags();
    }
    
    bindEvents() {
        ['focus', 'click'].forEach(evt => 
            this.input.addEventListener(evt, () => this.showTagSuggestions(this.input.value.trim()))
        );
        this.input.addEventListener('input', () => this.showTagSuggestions(this.input.value.trim()));
        
        this.dropdown.addEventListener('click', e => {
            const addSuggestion = document.getElementById(this.addSuggestionId);
            if (addSuggestion?.contains(e.target)) {
                this.createTag(this.input.value.trim());
                return;
            }
            
            const tagDiv = e.target.closest('.tag-suggestion');
            if (tagDiv) {
                this.addTag(tagDiv.getAttribute('data-tag'));
            }
        });
        
        this.selectedTagsDiv?.addEventListener('click', e => {
            const removeBtn = e.target.closest('.remove-tag');
            if (removeBtn) {
                this.removeTag(removeBtn.getAttribute('data-tag'));
            }
        });
        
        const addBtn = document.getElementById(this.addTagBtnId);
        addBtn?.addEventListener('click', () => {
            this.createTag(this.input.value.trim());
        });
    }
}

class StarRating {
    constructor(config) {
        this.container = config.container;
        this.hiddenInput = config.hiddenInput;
        this.initialRating = parseInt(config.hiddenInput?.value) || 3;
        this.currentRating = this.initialRating;
        this.stars = Array.from(this.container?.querySelectorAll('i[data-rating]') || []);
        
        this.init();
    }
    
    init() {
        if (!this.container || !this.hiddenInput) return;
        
        this.updateStars(this.currentRating);
        this.bindEvents();
    }
    
    updateStars(rating) {
        this.stars.forEach(star => {
            const starNum = parseInt(star.getAttribute('data-rating'));
            star.classList.toggle('text-light-lime', starNum <= rating);
            star.classList.toggle('text-gray-300', starNum > rating);
        });
    }
    
    setRating(rating) {
        this.currentRating = rating;
        this.hiddenInput.value = rating;
        this.updateStars(rating);
    }
    
    bindEvents() {
        this.stars.forEach(star => {
            const rating = parseInt(star.getAttribute('data-rating'));
            star.addEventListener('mouseenter', () => this.updateStars(rating));
            star.addEventListener('mouseleave', () => this.updateStars(this.currentRating));
            star.addEventListener('click', () => this.setRating(rating));
        });
        
        this.container.addEventListener('keydown', e => {
            if (e.key >= '1' && e.key <= '5') {
                this.setRating(parseInt(e.key));
            }
        });
    }
}

const initEditPhotoForm = () => {
    const form = document.getElementById('editPhotoForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.set('_method', 'PUT');
        
        try {
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            if (response.ok) {
                window.location.reload();
            } else {
                showAlert('Erreur lors de la modification de la photo.');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showAlert('Erreur réseau lors de la modification de la photo.');
        }
    });
};

const initGlobalClickHandler = () => {
    document.addEventListener("click", function (e) {
        if (["INPUT", "TEXTAREA", "SELECT"].includes(e.target.tagName)) return;
        
        const closeBtn = e.target.closest("[data-close-modal], .close-modal");
        if (closeBtn) {
            const modal = closeBtn.closest('[id$="Modal"]');
            if (modal) closeModal(modal.id);
            return;
        }
        
        const modalOverlay = e.target.closest('[id$="Modal"]');
        if (modalOverlay && (e.target === modalOverlay || 
            e.target.classList.contains("bg-black/20") || 
            e.target.classList.contains("backdrop-blur-sm"))) {
            closeModal(modalOverlay.id);
            return;
        }
        
        ['tagDropdown', 'editTagDropdown'].forEach(dropdownId => {
            const dropdown = document.getElementById(dropdownId);
            const input = document.getElementById(dropdownId.replace('Dropdown', 'Input'));
            
            if (dropdown?.style.display === "block" && 
                e.target !== input && 
                !dropdown.contains(e.target)) {
                dropdown.style.display = "none";
            }
        });
    });
};

let mainTagManager, editTagManager, mainStarRating, editStarRating;

window.initializeEditPhotoTags = function(photoTags) {
    if (editTagManager) {
        editTagManager.selectedTags = photoTags || [];
        editTagManager.renderSelectedTags();
    }
};

window.updateEditPhotoRating = function(rating) {
    editStarRating?.setRating(rating);
};

document.addEventListener("DOMContentLoaded", function () {
    initFileUrlHandler();
    
    mainTagManager = new TagManager({
        input: document.getElementById("tagInput"),
        dropdown: document.getElementById("tagDropdown"),
        selectedTagsDiv: document.getElementById("selectedTags"),
        hiddenInput: document.getElementById("tagsHidden"),
        tagsData: window.tagsFromBlade || [],
        emptyMessage: 'Premier tag, à toi de jouer !',
        addTagBtnId: 'addTagBtn',
        addSuggestionId: 'addTagSuggestion'
    });
    
    editTagManager = new TagManager({
        input: document.getElementById("editTagInput"),
        dropdown: document.getElementById("editTagDropdown"),
        selectedTagsDiv: document.getElementById("editSelectedTags"),
        hiddenInput: document.getElementById("editTagsHidden"),
        tagsData: window.editPhotoTagsFromBlade || [],
        emptyMessage: 'Aucun tag disponible',
        addTagBtnId: 'editAddTagBtn',
        addSuggestionId: 'editAddTagSuggestion'
    });
    
    mainStarRating = new StarRating({
        container: document.getElementById('starRating'),
        hiddenInput: document.getElementById('noteValue')
    });
    
    editStarRating = new StarRating({
        container: document.getElementById('editStarRating'),
        hiddenInput: document.getElementById('editNoteValue')
    });
    
    initEditPhotoForm();
    initGlobalClickHandler();
});
