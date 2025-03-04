const stars = document.querySelectorAll('.star-rating i');
const ratingValue = document.querySelector('.rating-value');
let selectedRating = 0;

stars.forEach((star, index) => {
  star.addEventListener('mouseover', () => {
    highlightStars(index);
  });

  star.addEventListener('mouseout', () => {
    resetStars();
  });

  star.addEventListener('click', () => {
    selectedRating = index + 1;
    ratingValue.textContent = `${selectedRating} / 5`;
    sendRating(selectedRating);
  });
});

function highlightStars(index) {
  stars.forEach((star, i) => {
    star.classList.toggle('hovered', i <= index);
  });
}

function resetStars() {
  stars.forEach((star, i) => {
    star.classList.toggle('hovered', false);
    star.classList.toggle('active', i < selectedRating);
  });
}

function sendRating(rating) {
    fetch('save_rating.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rating })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Server response:', data.message);
        alert(data.message); // Show success or error message
    })
    .catch(error => {
        console.error('Error:', error);
    });
    
}