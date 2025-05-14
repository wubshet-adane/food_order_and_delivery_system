// Add this to your existing JavaScript or create a new file
function showRestaurantModal(restaurantId) {
    // Show loading state
    const modal = document.getElementById('restaurantModal');
    modal.style.display = 'block';
    modal.querySelector('.modal-body').innerHTML = '<div class="loading-spinner-container"><div class="loading-spinner"></div><span>Loading restaurant details...</span></div>';
    
    // Fetch restaurant details
    fetch(`get_restaurants.php?id=${restaurantId}`)
        .then(response => response.json())
        .then(data => {
            populateModal(data);
        })
        .catch(error => {
            console.error('Error:', error);
            modal.querySelector('.modal-body').innerHTML = '<p class="error-message">Failed to load restaurant details. Please try again.</p>';
        });
}

function populateModal(data) {
    // Set basic information
    document.getElementById('modalRestaurantName').textContent = data.name;
    document.getElementById('modalRestaurantStatus').textContent = data.status;
    document.getElementById('modalRestaurantStatus').className = `status-badge status-${data.status}`;
    document.getElementById('modalLocation').textContent = data.location;
    document.getElementById('modalPhone').textContent = data.phone;
    document.getElementById('modalHours').textContent = data.opening_and_closing_hour || 'Not specified';
    document.getElementById('modalRating').textContent = data.rating || 'Not rated yet';
    document.getElementById('modalDescription').textContent = data.description || 'No description provided.';
    
    // Set images
    const mainImage = document.getElementById('modalMainImage');
    const thumbnailGallery = document.getElementById('thumbnailGallery');
    
    if (data.image) {
        mainImage.src = `../restaurant/restaurantAsset/${data.image}`;
    } else {
        mainImage.src = 'assets/images/default-restaurant.jpg';
    }
    
    // Clear thumbnails
    thumbnailGallery.innerHTML = '';
    
    // Add thumbnails (example - you might have multiple images)
    if (data.image) {
        const thumbnail = document.createElement('img');
        thumbnail.src = `../restaurant/restaurantAsset/${data.image}`;
        thumbnail.className = 'thumbnail';
        thumbnail.onclick = () => {
            mainImage.src = thumbnail.src;
        };
        thumbnailGallery.appendChild(thumbnail);
    }
    
    // Add banner if exists
    if (data.banner) {
        const bannerThumb = document.createElement('img');
        bannerThumb.src = `../restaurant/restaurantAsset/${data.banner}`;
        bannerThumb.className = 'thumbnail';
        bannerThumb.onclick = () => {
            mainImage.src = bannerThumb.src;
        };
        thumbnailGallery.appendChild(bannerThumb);
    }
    
    // Set social links
    setSocialLink('modalFacebook', data.facebook, 'Facebook');
    setSocialLink('modalInstagram', data.instagramAccount, 'Instagram');
    setSocialLink('modalTiktok', data.tiktokAccount, 'TikTok');
    setSocialLink('modalTelegram', data.telegramAccount, 'Telegram');
    setSocialLink('modalWebsite', data.website, 'Website');
    
    // Set license
    const licenseLink = document.getElementById('modalLicense');
    if (data.license) {
        licenseLink.href = `../restaurant/restaurantAsset/${data.license}`;
    } else {
        licenseLink.style.display = 'none';
    }
}

function setSocialLink(elementId, url, platform) {
    const element = document.getElementById(elementId);
    if (url && url.trim() !== '') {
        // Add https:// if not present
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            url = `https://${url}`;
        }
        element.href = url;
        element.title = `${platform} profile`;
    } else {
        element.style.display = 'none';
    }
}

function closeModal() {
    document.getElementById('restaurantModal').style.display = 'none';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('restaurantModal');
    if (event.target == modal) {
        closeModal();
    }
}