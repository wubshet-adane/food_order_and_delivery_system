document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("edit-modal");
    const closeBtn = document.querySelector(".close");
    const editButtons = document.querySelectorAll(".edit-btn");

    editButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            document.getElementById("edit-id").value = btn.dataset.id;
            document.getElementById("edit-name").value = btn.dataset.name;
            document.getElementById("edit-description").value = btn.dataset.description;
            document.getElementById("edit-price").value = btn.dataset.price;
            document.getElementById("edit-image").value = btn.dataset.image;
            modal.style.display = "block";
        });
    });

    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
});
