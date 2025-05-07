
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.marquee-container');
        const cards = document.querySelector('.recommendation-cards');
        const leftBtn = document.querySelector('.arrow-left');
        const rightBtn = document.querySelector('.arrow-right');
        const closeBtn = document.querySelector('.close-banner');
        
        // Scroll buttons functionality
        leftBtn.addEventListener('click', () => {
            container.scrollBy({ left: -300, behavior: 'smooth' });
        });
        
        rightBtn.addEventListener('click', () => {
            container.scrollBy({ left: 300, behavior: 'smooth' });
        });
        
        // Close banner functionality
        closeBtn.addEventListener('click', () => {
            const banner = document.querySelector('.recommendation');
            banner.style.display = 'none';
            
            // Optional: Store preference in localStorage
            localStorage.setItem('hidePromoBanner', 'true');
        });
        
        // Check if banner was previously closed
        if(localStorage.getItem('hidePromoBanner') === 'true') {
            document.querySelector('.recommendation').style.display = 'none';
        }
        
        // Enable/disable arrows based on scroll position
        container.addEventListener('scroll', updateArrowVisibility);
        
        function updateArrowVisibility() {
            const scrollLeft = container.scrollLeft;
            const maxScroll = container.scrollWidth - container.clientWidth;
            
            leftBtn.style.visibility = scrollLeft > 0 ? 'visible' : 'hidden';
            rightBtn.style.visibility = scrollLeft < maxScroll - 1 ? 'visible' : 'hidden';
        }
        
        // Initialize
        updateArrowVisibility();
    });
