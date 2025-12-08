function getModalContent(modal) {
    return (
        modal.querySelector("[data-modal-content]") ||
        modal.querySelector(".relative")
    );
}

window.openModal = function (modalId) {
    const modal = document.getElementById(modalId);
    const modalContent = getModalContent(modal);
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    setTimeout(() => {
        modalContent.classList.remove("scale-95", "opacity-0");
        modalContent.classList.add("scale-100", "opacity-100");
    }, 10);
    setTimeout(() => {
        const firstInput = modal.querySelector("input, textarea, select");
        if (firstInput) firstInput.focus();
    }, 300);
};

window.closeModal = function (modalId) {
    let modal = null;
    if (modalId) {
        modal = document.getElementById(modalId);
    } else {
        modal = document.querySelector('[id$="Modal"]:not(.hidden)');
    }
    if (!modal) return;
    const modalContent = getModalContent(modal);
    modalContent.classList.add("scale-95", "opacity-0");
    modalContent.classList.remove("scale-100", "opacity-100");
    setTimeout(() => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        modal.querySelectorAll("input, textarea, select").forEach((input) => {
            if (["checkbox", "radio"].includes(input.type))
                input.checked = false;
            else input.value = "";
        });
    }, 300);
};

document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("click", function (e) {
        const closeBtn = e.target.closest('[data-close-modal], .close-modal');
        if (closeBtn) {
            const modal = closeBtn.closest('[id$="Modal"]');
            if (modal) closeModal(modal.id);
        }
        const modalOverlay = e.target.closest('[id$="Modal"]');
        if (modalOverlay && (e.target === modalOverlay || e.target.classList.contains("bg-black/20") || e.target.classList.contains("backdrop-blur-sm"))) {
            closeModal(modalOverlay.id);
        }
        const tagDropdown = document.getElementById("tagDropdown");
        const tagInput = document.getElementById("tagInput");
        if (tagDropdown && tagDropdown.style.display === "block") {
            const modal = document.querySelector('[id$="Modal"]:not(.hidden)');
            if (modal && !modal.contains(e.target) && e.target !== tagInput && !tagDropdown.contains(e.target)) {
                tagDropdown.style.display = "none";
            }
        }
    });
});
