document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.star-rating i');
    const reviewList = document.getElementById('review-list');
    const form = document.querySelector('.review-form');
    let selectedRating = 0;

    // Handle star rating
    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            selectedRating = index + 1;
            stars.forEach((s, i) => {
                if (i < selectedRating) {
                    s.classList.add('active');
                    s.classList.replace('bx-star', 'bxs-star');
                } else {
                    s.classList.remove('active');
                    s.classList.replace('bxs-star', 'bx-star');
                }
            });
        });
    });

    // Handle form submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const textarea = form.querySelector('textarea');
        const comment = textarea.value.trim();

        if (comment && selectedRating > 0) {
            const li = document.createElement('li');
            li.innerHTML = `
                <div class="profile">
                    <img src="../src/img/voidlogo.png.png" alt="Default Avatar">
                </div>
                <div class="review-content">
                    <div class="username">Void</div>
                    <span class="comment">${comment}</span>
                    <div class="stars">${'★'.repeat(selectedRating)}${'☆'.repeat(5 - selectedRating)}</div>
                </div>`;
            reviewList.appendChild(li);

            // Reset form fields and star rating
            textarea.value = '';
            stars.forEach(s => {
                s.classList.remove('active');
                s.classList.replace('bxs-star', 'bx-star');
            });
            selectedRating = 0;
        } else {
            alert('Please add a comment and select a rating.');
        }
    });
});
