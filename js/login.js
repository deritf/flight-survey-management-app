async function enviarFormulario() {
  const form = document.getElementById("miForm");
  const formData = new FormData(form);

  try {
    const response = await fetch("../public/login.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      window.location.href = result.redirect;
    } else {
      alert(result.message || "Credenciales incorrectas.");
    }
  } catch (error) {
    console.error("Error en el servidor:", error);
    alert("Ocurrió un error en el servidor.");
  }
}
