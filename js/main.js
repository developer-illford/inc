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