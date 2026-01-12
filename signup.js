document.addEventListener("DOMContentLoaded", function () {

  const signupForm = document.getElementById("signupForm");
  if (signupForm) {
    signupForm.addEventListener("submit", function (e) {
      e.preventDefault();

    //   const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value;

      if (password.length < 6) {
        Swal.fire("Weak Password", "Minimum 6 characters required", "error");
        return;
      }

      this.submit(); // ✅ finally submit
    });
  }

});
