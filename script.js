const form = document.getElementById("loginForm");
const msgBox = document.getElementById("msg");

const forgotBtn = document.getElementById("forgot");
const modal = document.getElementById("forgotModal");
const closeBtn = document.getElementById("closeModal");

forgotBtn.addEventListener("click", function () {
    modal.style.display = "block";
});

closeBtn.addEventListener("click", function () {
    modal.style.display = "none";
});

// Close modal if user clicks outside the box
window.addEventListener("click", function (event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

// Redirect to registration page
document.getElementById("create").addEventListener("click", function () {
    window.location.href = "register.html";
});

// ---------------------------
// Client-side validation + fetch login
// ---------------------------
form.addEventListener("submit", function (e) {
    e.preventDefault(); // Stop default form submit

    const username = document.getElementById("uname").value.trim();
    const password = document.getElementById("pass").value;

    // Client-side validation
    if (!username || !password) {
        msgBox.style.color = "red";
        msgBox.textContent = "❌ Username and Password are required!";
        return;
    }

    // Send data to PHP via fetch
    const formData = new FormData();
    formData.append("username", username);
    formData.append("password", password);

    fetch("loginUser.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json()) // PHP returns JSON
    .then(data => {
        if (data.status === "success") {
            msgBox.style.color = "green";
            msgBox.textContent = "✔ Login Successful! Redirecting...";

            // Clear the input fields
            document.getElementById("uname").value = "";
            document.getElementById("pass").value = "";

            // Clear the message after 1.5 seconds (same time as redirect)
            setTimeout(() => {
                msgBox.textContent = "";
                window.location.href = "home.php";
            }, 1500);
        } else {
            msgBox.style.color = "red";
            msgBox.textContent = "❌ " + data.message;
        }
    })
    .catch(err => {
        msgBox.style.color = "red";
        msgBox.textContent = "❌ Server error!";
        console.error(err);
    });

});
