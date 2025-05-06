
// FAQ functionality
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const answer = question.nextElementSibling;
        question.classList.toggle('active');
        answer.classList.toggle('active');
    });
});

// FAQ category switching
document.querySelectorAll('.faq-category').forEach(category => {
    category.addEventListener('click', () => {
        // Remove active class from all categories
        document.querySelectorAll('.faq-category').forEach(c => {
            c.classList.remove('active');
        });
        
        // Add active class to clicked category
        category.classList.add('active');
        
        // Hide all FAQ items
        document.querySelectorAll('.faq-items').forEach(items => {
            items.classList.remove('active');
        });
        
        // Show selected FAQ items
        const categoryId = category.getAttribute('data-category') + '-faq';
        document.getElementById(categoryId).classList.add('active');
    });
});

// User type selection
document.querySelectorAll('.user-type-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all buttons
        document.querySelectorAll('.user-type-btn').forEach(b => {
            b.classList.remove('active');
        });
        
        // Add active class to clicked button
        btn.classList.add('active');
        
        // Update hidden input value
        document.getElementById('user_type').value = btn.getAttribute('data-type');
    });
});

// Auto-expand FAQ based on URL hash
window.addEventListener('DOMContentLoaded', () => {
    if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        const category = hash.split('-')[0];
        
        if (category) {
            const categoryBtn = document.querySelector(`.faq-category[data-category="${category}"]`);
            if (categoryBtn) {
                categoryBtn.click();
            }
            
            const question = document.getElementById(hash);
            if (question) {
                question.scrollIntoView();
                question.click();
            }
        }
    }
});
