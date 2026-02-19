// --- TAB SWITCHING LOGIC ---
function switchTab(tabId) {
    // 1. Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.classList.remove('active');
    });

    // 2. Show the selected section
    const selectedSection = document.getElementById(tabId);
    if (selectedSection) {
        selectedSection.classList.add('active');
        
        // Reset animations for child elements if you want them to re-animate
        // This is optional but adds a nice touch
        const children = selectedSection.children;
        for(let child of children) {
            child.style.animation = 'none';
            child.offsetHeight; /* trigger reflow */
            child.style.animation = null; 
        }
    }

    // 3. Update Nav Buttons
    const navButtons = document.querySelectorAll('.nav-tab');
    navButtons.forEach(btn => {
        btn.classList.remove('active');
        // Check if the button's click handler targets this tabId
        if (btn.getAttribute('onclick').includes(`'${tabId}'`)) {
            btn.classList.add('active');
        }
    });
    
    // Smooth scroll to top when switching
    window.scrollTo(0,0);
}

// --- TYPEWRITER EFFECT ---
const textElement = document.getElementById('typewriter');
// Personalized phrases based on Vishwaraj's skills
const phrases = [
    "Machine Learning Enthusiast", 
    "Problem Solver", 
    "Python & C++ Developer"
];
let phraseIndex = 0;
let charIndex = 0;
let isDeleting = false;
let typeSpeed = 100;

function type() {
    const currentPhrase = phrases[phraseIndex];
    
    if (isDeleting) {
        textElement.textContent = currentPhrase.substring(0, charIndex - 1);
        charIndex--;
        typeSpeed = 50; // Faster when deleting
    } else {
        textElement.textContent = currentPhrase.substring(0, charIndex + 1);
        charIndex++;
        typeSpeed = 100; // Normal typing speed
    }

    if (!isDeleting && charIndex === currentPhrase.length) {
        isDeleting = true;
        typeSpeed = 2000; // Pause at end of phrase
    } else if (isDeleting && charIndex === 0) {
        isDeleting = false;
        phraseIndex = (phraseIndex + 1) % phrases.length;
        typeSpeed = 500; // Pause before next phrase
    }

    setTimeout(type, typeSpeed);
}

// Initialize animations and effects
document.addEventListener('DOMContentLoaded', () => {
    type();
    
    // Optional: Add simple fade-in for project cards on scroll if needed in future
    // using IntersectionObserver
});