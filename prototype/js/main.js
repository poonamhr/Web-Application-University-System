// index.html

const uelAddress = "University of East London, Romford Road, London E15 4LZ, United Kingdom";

const uelEl = document.getElementById('uel-address');
if (uelEl) {
    uelEl.textContent = uelAddress;
    uelEl.style.color = "black";
    uelEl.style.fontWeight = "600";
}

// Initializing Leaflet map
if (typeof L !== "undefined" && document.getElementById('mapCanvas')) {
    const map = L.map('mapCanvas').setView([51.5402, 0.0089], 15);

    // Loading map from OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Adding a marker at UEL location 
    L.marker([51.5402, 0.0089]).addTo(map)
        .bindPopup("<b>University of East London</b><br>Romford Road, London E15 4LZ")
        .openPopup();
}

// lab.html

function toggleContent(id, arrowId) {
    const content = document.getElementById(id);
    const arrow = document.getElementById(arrowId);

    if (!content || !arrow) return;

    const isOpen = content.style.display === 'block';
    content.style.display = isOpen ? 'none' : 'block';
    arrow.classList.toggle('rotated');
}

// post_assignment.php

document.addEventListener('DOMContentLoaded', () => {
    // Custom dropdown for course selection
    const wrapper = document.querySelector('.custom-select-wrapper');
    if (wrapper) {
        const selected = wrapper.querySelector('.custom-select-selected');
        const options = wrapper.querySelector('.custom-select-options');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');

        // Toggle dropdown options when clicking
        selected.addEventListener('click', () => {
            options.style.display = options.style.display === 'block' ? 'none' : 'block';
        });

        // Function to select an option and update hidden input
        window.selectOption = function (el, id) {
            selected.querySelector('span').textContent = el.textContent;
            hiddenInput.value = id;
            options.style.display = 'none';
        };

        // Closing dropdown if clicking outside
        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                options.style.display = 'none';
            }
        });
    }

    // Gradient focus effect for inputs, textarea, and custosm select
    document.querySelectorAll('input.form-control, textarea.form-control, .custom-select-selected')
        .forEach((el) => {
            el.addEventListener('focus', () => {
                el.style.borderColor = 'transparent';
                el.style.background = 'linear-gradient(90deg, #f9f7f3 0%, #eef1f8 100%)';
                el.style.boxShadow = '0 0 0 0.25rem rgba(106, 115, 134, 0.66)';
            });
            el.addEventListener('blur', () => {
                el.style.borderColor = '#ced4da';
                el.style.boxShadow = 'none';
            });
        });

    // Flatpickr date picker with minimum today
    if (typeof flatpickr !== "undefined") {
        flatpickr("#due_date", {
            dateFormat: "Y-m-d",
            minDate: "today"
        });
    }
});
