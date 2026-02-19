document.getElementById("registrationForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let valid = true;

    // Get values
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const mobile = document.getElementById("mobile").value.trim();
    const address = document.getElementById("address").value.trim();
    const city = document.getElementById("city").value.trim();
    const pincode = document.getElementById("pincode").value.trim();
    const country = document.getElementById("country").value.trim();

    // Clear previous errors
    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.getElementById("successMessage").textContent = "";

    // Name validation
    if (!/^[A-Za-z ]{3,}$/.test(name)) {
        document.getElementById("nameError").textContent = "Name must contain only letters (min 3 characters)";
        valid = false;
    }

    // Email validation
    if (!/^\S+@\S+\.\S+$/.test(email)) {
        document.getElementById("emailError").textContent = "Enter a valid email address";
        valid = false;
    }

    // Password validation
    if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(password)) {
        document.getElementById("passwordError").textContent =
            "Password must be 8+ chars with uppercase, lowercase & number";
        valid = false;
    }

    // Mobile validation
    if (!/^\d{10}$/.test(mobile)) {
        document.getElementById("mobileError").textContent = "Mobile must be exactly 10 digits";
        valid = false;
    }

    // Address validation
    if (address.length < 5) {
        document.getElementById("addressError").textContent = "Address must be at least 5 characters";
        valid = false;
    }

    // City validation
    if (!/^[A-Za-z ]+$/.test(city)) {
        document.getElementById("cityError").textContent = "City must contain only letters";
        valid = false;
    }

    // Pincode validation
    if (!/^\d{6}$/.test(pincode)) {
        document.getElementById("pincodeError").textContent = "Pincode must be exactly 6 digits";
        valid = false;
    }

    // Country validation
    if (country === "") {
        document.getElementById("countryError").textContent = "Country cannot be empty";
        valid = false;
    }

    if (valid) {
        document.getElementById("successMessage").textContent = "Registration Successful!";
        document.getElementById("registrationForm").reset();
    }
});
