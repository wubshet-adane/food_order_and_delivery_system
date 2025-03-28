function editMenu(menuId) {
    const modal = document.getElementById("edit-modal");
    const editButton = document.getElementById(`edit-btn-${menuId}`);

    if (!modal || !editButton) {
        console.error("Modal or Edit Button not found");
        return; // Ensure modal and button exist
    }

    // Populate modal inputs with dataset values
    document.getElementById("edit-id").value = menuId;
    document.getElementById("edit-name").value = editButton.dataset.name || "";
    document.getElementById("edit-description").value = editButton.dataset.description || "";
    document.getElementById("edit-category").value = editButton.dataset.category || "";
    document.getElementById("edit-content").value = editButton.dataset.content || "";
    document.getElementById("edit-price").value = editButton.dataset.price || "";

    // Handle image preview
    const imagePreview = document.getElementById("imagePreview");
    if (imagePreview) {
        if (editButton.dataset.image) {
            imagePreview.src = editButton.dataset.image;
            imagePreview.style.display = "block"; // Show the image
        } else {
            imagePreview.style.display = "none"; // Hide if no image
        }
    }

    modal.style.display = "block"; // Show modal
}

// Close modal when clicking the close button
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("edit-modal");
    const closeBtn = document.getElementById("close-modal");

    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    // Close modal when clicking outside
    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
});
