document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const fullName = document.getElementById("fullName").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        const role = document.getElementById("role").value;

        // Basic validation
        if (fullName === "" || email === "" || password === "" || confirmPassword === "" || role === "") {
            alert("Please fill in all fields.");
            return;
        }

        if (!email.endsWith("@ashesi.edu.gh")) {
            alert("Please use a valid Ashesi email (name@ashesi.edu.gh).");
            return;
        }

        if (password.length < 8) {
            alert("Password must be at least 6 characters long.");
            return;
        }

        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return;
        }

        alert(`${fullName} registered successfully as ${role}.`);
        form.reset();
    });
});
