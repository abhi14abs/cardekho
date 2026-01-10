// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function () {
    const mobileToggle = document.getElementById('mobileToggle');
    const navMenu = document.getElementById('navMenu');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    }

    // Form Validation
    const carForm = document.getElementById('carForm');
    if (carForm) {
        carForm.addEventListener('submit', function (e) {
            const checkboxes = document.querySelectorAll('input[name="car_types[]"]:checked');
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one car type.');
                return false;
            }

            const phoneInput = document.getElementById('phone');
            const phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(phoneInput.value.trim())) {
                e.preventDefault();
                alert('Please enter a valid 10-digit phone number.');
                phoneInput.focus();
                return false;
            }
        });
    }

    // Owl Carousel Initialization
    if ($('.car-slider').length) {
        $('.car-slider').owlCarousel({
            loop: true,
            margin: 20,
            nav: true,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 4
                }
            }
        });
    }

    // Checkbox styling
    const checkboxes = document.querySelectorAll('.checkbox-item');
    checkboxes.forEach(item => {
        item.addEventListener('click', function (e) {
            const input = this.querySelector('input[type="checkbox"]');
            if (e.target !== input) {
            }
        });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
});