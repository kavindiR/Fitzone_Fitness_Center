// Password Confirmation Validation
const form = document.querySelector('.register-form');
const password = document.querySelector('#password');
const confirmPassword = document.querySelector('#confirm_password');

// Validate password confirmation
form.addEventListener('submit', (e) => {
    if (password.value !== confirmPassword.value) {
        alert("Passwords do not match!");
        e.preventDefault();  // Prevent form submission if passwords don't match
    }
});

// Mobile Navigation Toggle
const navLinks = document.querySelector('.nav-links');
const toggleButton = document.createElement('button');
toggleButton.textContent = '☰';  // Hamburger menu icon
toggleButton.classList.add('toggle-btn');
document.querySelector('.main-header').appendChild(toggleButton);

// Toggle navigation visibility on mobile
toggleButton.addEventListener('click', () => {
    navLinks.classList.toggle('active');
});

// Smooth Scroll for Anchor Links
const anchorLinks = document.querySelectorAll('a[href^="#"]');
anchorLinks.forEach((link) => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetId = link.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);

        window.scrollTo({
            top: targetElement.offsetTop - 60, // Adjust offset for header
            behavior: 'smooth',
        });
    });
});

/*===============================================================================================================================/*