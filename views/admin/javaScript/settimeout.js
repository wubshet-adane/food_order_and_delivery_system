function closeResponseById(id, delay = 5000) {
    setTimeout(() => {
        let notification = document.getElementById(id);
        if (notification) {
            notification.style.transition = 'opacity 0.5s ease-in-out'; // Smooth fade-out
            notification.style.opacity = '0'; // Start fade-out
            setTimeout(() => {
                notification.style.display = 'none'; // Hide after fade-out
            }, 500); // Wait for transition
        }
    }, delay);
}

