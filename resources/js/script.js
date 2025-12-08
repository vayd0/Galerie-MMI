document.addEventListener("DOMContentLoaded", () => {
	const btn = document.getElementById("fullscreen-btn");
	const modal = document.getElementById("mini-modal");
	const close = document.getElementById("close-modal");
	if (btn && modal && close) {
		btn.addEventListener("click", () => {
			modal.classList.remove("hidden");
		});
		close.addEventListener("click", () => {
			modal.classList.add("hidden");
		});
		modal.addEventListener("click", (e) => {
			if (e.target === modal) {
				modal.classList.add("hidden");
			}
		});
	}
});
