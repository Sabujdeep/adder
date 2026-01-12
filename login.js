  document.getElementById("signupBtn").addEventListener("click", function () {
  window.location.href = "signup.php";
});


document.getElementById("loginForm").addEventListener("submit", function (e) {
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value;

  const gmailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;

  // ❌ Invalid Gmail
  if (!gmailRegex.test(email)) {
    e.preventDefault();

    Swal.fire({
      icon: "error",
      title: "Invalid Email",
      text: "Please enter a valid Gmail address",
      confirmButtonColor: "#0d6efd",
    });
    return;
  }

  // ❌ Empty password
  if (password.length === 0) {
    e.preventDefault();
    Swal.fire("Error", "Password is required", "error");
    return;
  }

  // ✅ VALID → LET FORM SUBMIT TO PHP
});




// Sign up

// document.getElementById("signupForm").addEventListener("submit", function (e) {
//   const email = document.getElementById("email").value.trim();
//   const password = document.getElementById("password").value;

//   const gmailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;

//   if (!gmailRegex.test(email)) {
//     e.preventDefault();
//     Swal.fire("Invalid Email", "Use a valid Gmail address", "error");
//     return;
//   }

//   if (password.length < 6) {
//     e.preventDefault();
//     Swal.fire("Weak Password", "Minimum 6 characters required", "error");
//     return;
//   }
// });

