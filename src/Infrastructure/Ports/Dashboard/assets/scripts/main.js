(() => {
    "use strict";

    const inputs = document.querySelectorAll("input");

    inputs.forEach((input) => {
        input.addEventListener("blur", () => {
            // clean up error state on blur
            input.removeAttribute("aria-invalid");
        });
    });
})();
