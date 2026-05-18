// seller.js — Shared seller panel JS

// Auto-dismiss alerts after 4s
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert-success').forEach(el => {
        setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .5s'; setTimeout(() => el.remove(), 500); }, 4000);
    });
});
