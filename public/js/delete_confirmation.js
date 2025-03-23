document.addEventListener('DOMContentLoaded', function () {
    // Select all delete buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    // Loop through each delete button and add an event listener
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            // Prevent the form from submitting immediately
            e.preventDefault();

            // Show the confirmation prompt
            const confirmation = confirm("Are you sure you want to delete?");

            // If the user confirms, submit the form
            if (confirmation) {
                // Find the closest form and submit it
                this.closest('form').submit();
            }
        });
    });
});
