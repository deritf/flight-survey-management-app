document.addEventListener("DOMContentLoaded", function () {
  document.querySelector("form").addEventListener("submit", function () {
    const successMessage = document.querySelector(".success-message");
    if (successMessage) {
      setTimeout(() => {
        window.location.href = "listado.php";
      }, 3000);
    }
  });
});
