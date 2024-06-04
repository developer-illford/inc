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



document.addEventListener('DOMContentLoaded', () => {
    const element = document.getElementById('typed-text');
    const text = "Welcome <br>To INC Shipping LLC";
    let index = 0;
    const speed = 100; // Adjust typing speed here

    const typeText = () => {
        if (index < text.length) {
            if (text[index] === '<' && text.substring(index, index + 4) === '<br>') {
                element.innerHTML += '<br>';
                index += 4;
            } else {
                element.innerHTML += text[index];
                index++;
            }
            setTimeout(typeText, speed);
        }
    };

    element.innerHTML = ''; // Clear existing text
    typeText();
});



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
