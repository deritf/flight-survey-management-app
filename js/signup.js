async function enviarFormulario() {
  const form = document.getElementById("miForm");
  const formData = new FormData(form);

  try {
    const response = await fetch("../public/signup.php", {
      method: "POST",
      body: formData,
    });

    const text = await response.text();
    console.log("Respuesta del servidor:", text);

    const data = JSON.parse(text);

    if (data.success) {
      alert(data.message);
      form.reset();

      limpiarMensajesError();
    } else {
      if (document.getElementById("errUsu")) {
        document.getElementById("errUsu").textContent = data.errUsu || "";
      }
      if (document.getElementById("errPass")) {
        document.getElementById("errPass").textContent = data.errPass || "";
      }
      if (document.getElementById("errMail")) {
        document.getElementById("errMail").textContent = data.errMail || "";
      }
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Error al procesar el registro: " + error.message);
  }
}

function limpiarMensajesError() {
  if (document.getElementById("errUsu")) {
    document.getElementById("errUsu").textContent = "";
  }
  if (document.getElementById("errPass")) {
    document.getElementById("errPass").textContent = "";
  }
  if (document.getElementById("errMail")) {
    document.getElementById("errMail").textContent = "";
  }
}
