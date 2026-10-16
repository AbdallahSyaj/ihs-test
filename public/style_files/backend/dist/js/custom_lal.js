// imgInp.onchange = evt => {
//     const [file] = imgInp.files
//     if (file) {
//         blah.src = URL.createObjectURL(file)
//     }
// }

// Declare imgInp and blah globally so they can be accessed across the script
// Make sure the script runs after the DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    // Select the input element and the image element
    const imgInp = document.getElementById('imgInp');
    const blah = document.getElementById('blah');

    // Check if the elements exist before attaching event listeners
    if (imgInp && blah) {
        // Attach an onchange event listener to the input element
        imgInp.onchange = evt => {
            const [file] = imgInp.files;
            if (file) {
                blah.src = URL.createObjectURL(file);
            }
        };
    }
});



