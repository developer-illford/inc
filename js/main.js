// ***********************************************************************
// Function to toggle hamburger menu
// ***********************************************************************

function hamburgerToggle() {
    var x = document.getElementById("hamburger_mobile_menu");
    if (x.style.display === "block") {
        x.style.display = "none";
    } else {
        x.style.display = "block";
    }
}



// ***********************************************************************
// Hamburger menu Service toggler
// ***********************************************************************
document.addEventListener('DOMContentLoaded', function () {
    var servicesLink = document.getElementById('services_link');
    var servicesDropdown = document.getElementById('services_dropdown');

    servicesLink.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent default anchor click behavior
        if (servicesDropdown.style.display === 'block') {
            servicesDropdown.style.display = 'none';
        } else {
            servicesDropdown.style.display = 'block';
        }
    });
});






// ***********************************************************************
// Function for typing animation in home page
// ***********************************************************************
document.addEventListener('DOMContentLoaded', () => {
    const elements = document.querySelectorAll('.typed-text');
    const speed = 100; // Adjust typing speed here

    elements.forEach(element => {
        const text = element.getAttribute('data-text');
        let index = 0;

        const typeText = () => {
            if (index < text.length) {
                element.innerHTML += text[index];
                index++;
                setTimeout(typeText, speed);
            }
        };

        element.innerHTML = ''; // Clear existing text
        typeText();
    });
});








// ***********************************************************************
// counter
// ***********************************************************************
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.count');
    const achivementsSection = document.querySelector('.home_achivements');

    // Function to count up
    const countUp = (counter) => {
        const target = +counter.getAttribute('data-target');
        let count = 0;

        const increment = target / 200;
        const updateCount = () => {
            count = +counter.innerText.replace(/\D/g, ''); // Remove non-numeric characters
            if (count < target) {
                counter.innerText = Math.ceil(count + increment) + (counter.innerText.includes('%') ? '%' : '+');
                setTimeout(updateCount, 10);
            } else {
                counter.innerText = target + (counter.innerText.includes('%') ? '%' : '+');
            }
        };

        updateCount();

    };

    // Intersection Observer to detect when the section is in view
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                counters.forEach(counter => countUp(counter));
                observer.disconnect(); // Stop observing once animation starts
            }
        });
    }, { threshold: 0.1 });

    observer.observe(achivementsSection);
});











// ***********************************************************************
// cookies
// ***********************************************************************
window.addEventListener('scroll', function () {
    // Check if the cookies div has already been closed
    if (localStorage.getItem('cookiesAccepted') === 'true') {
        return;
    }

    var scrollPosition = window.scrollY + window.innerHeight;
    var documentHeight = document.documentElement.scrollHeight;

    // Check if user is 100px from the bottom
    if (scrollPosition >= documentHeight - 100) {
        document.getElementById('cookies_float').style.display = 'flex';
    }
});

document.querySelectorAll('.coookie_buttons button').forEach(button => {
    button.addEventListener('click', function () {
        document.querySelectorAll('.cookies_float').forEach(element => {
            element.style.display = 'none';
        });
        // Set a flag in localStorage to indicate that the cookies div has been closed
        localStorage.setItem('cookiesAccepted', 'true');
    });
});