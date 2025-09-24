document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.read-more-btn').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const wrapper = this.closest('.card-content').querySelector('.card-excerpt-wrapper');
            const isExpanded = wrapper.classList.contains('expanded');
            wrapper.classList.toggle('expanded');
            this.textContent = isExpanded ? 'Read More' : 'Read Less';
        });
    });
});