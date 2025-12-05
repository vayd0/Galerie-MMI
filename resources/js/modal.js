window.openModal = function (modalId) {
    const modal = document.getElementById(modalId);
    const modalContent =
        modal.querySelector("[data-modal-content]") ||
        modal.querySelector(".relative");

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
    const modal = document.getElementById(
        modalId || "addAlbumModal" || "addPhotoModal"
    );
    const modalContent =
        modal.querySelector("[data-modal-content]") ||
        modal.querySelector(".relative");

    modalContent.classList.add("scale-95", "opacity-0");
    modalContent.classList.remove("scale-100", "opacity-100");

    setTimeout(() => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");

        const inputs = modal.querySelectorAll("input, textarea, select");
        inputs.forEach((input) => {
            if (input.type === "checkbox" || input.type === "radio") {
                input.checked = false;
            } else {
                input.value = "";
            }
        });
    }, 300);
};

document.addEventListener("DOMContentLoaded", function () {
    const modals = document.querySelectorAll('[id$="Modal"]');
    const stars = document.querySelectorAll("#starRating i");
    const noteInput = document.getElementById("noteValue");
    let currentRating = 3;

    function updateStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove("text-gray-300");
                star.classList.add("text-light-lime");
            } else {
                star.classList.remove("text-light-lime");
                star.classList.add("text-gray-300");
            }
        });
    }

    if (stars.length && noteInput) {
        stars.forEach((star, index) => {
            star.addEventListener("click", function () {
                currentRating = index + 1;
                noteInput.value = currentRating;
                updateStars(currentRating);
            });

            star.addEventListener("mouseenter", function () {
                updateStars(index + 1);
            });
        });

        const starRating = document.getElementById("starRating");
        if (starRating) {
            starRating.addEventListener("mouseleave", function () {
                updateStars(currentRating);
            });
        }

        updateStars(currentRating);
    }

    modals.forEach((modal) => {
        const modalId = modal.id;
        const modalContent =
            modal.querySelector("[data-modal-content]") ||
            modal.querySelector(".relative");

        modalContent.addEventListener("click", function (e) {
            e.stopPropagation();
        });

        modal.addEventListener("click", function (e) {
            if (
                e.target === this ||
                e.target.classList.contains("bg-black/20") ||
                e.target.classList.contains("backdrop-blur-sm")
            ) {
                closeModal(modalId);
            }
        });

        const closeButtons = modal.querySelectorAll(
            "[data-close-modal], .close-modal"
        );
        closeButtons.forEach((button) => {
            button.addEventListener("click", () => closeModal(modalId));
        });
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            const openModal = document.querySelector(
                '[id$="Modal"]:not(.hidden)'
            );
            if (openModal) {
                closeModal(openModal.id);
            }
        }
    });

    const fileInput = document.getElementById("fileInput");
    const urlInput = document.getElementById("url");

    fileInput.addEventListener("change", function () {
        if (fileInput.files.length > 0) {
            urlInput.removeAttribute("required");
            urlInput.type = "text"
            urlInput.value = fileInput.files[0].name;
        }
    });
});
