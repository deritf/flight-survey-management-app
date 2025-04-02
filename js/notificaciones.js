document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const status = urlParams.get("status");
  const message = urlParams.get("message");

  const existingNotification = document.querySelector(".notificacion");

  if (status && message && !existingNotification) {
    mostrarNotificacion(status, decodeURIComponent(message));
  }

  if (status || message) {
    urlParams.delete("status");
    urlParams.delete("message");
    const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
    window.history.replaceState({}, document.title, newUrl);
  }

  if (
    existingNotification &&
    !existingNotification.classList.contains("handled")
  ) {
    existingNotification.classList.add("handled");
    iniciarCuentaAtras(existingNotification);
  }
});

function mostrarNotificacion(status, mensaje) {
  const notification = document.createElement("div");
  const icon = status === "success" ? "&#10004;" : "&#9888;";
  const className =
    status === "success"
      ? "notificacion notificacion-exito"
      : "notificacion notificacion-error";

  notification.className = `${className} fade-out handled`;
  notification.innerHTML = `
          <span class='close-btn' onclick='cerrarNotificacion(this)'>&times;</span>
          <span class="icono">${icon}</span> ${mensaje}
          <span class='countdown'></span>
      `;

  document.body.appendChild(notification);
  iniciarCuentaAtras(notification);
}

function iniciarCuentaAtras(mensaje) {
  let countdownSpan = mensaje.querySelector(".countdown");
  let tiempoRestante = NOTIFICACION_TIEMPO;

  countdownSpan.textContent = ` (${tiempoRestante}s)`;

  const countdown = setInterval(() => {
    tiempoRestante--;
    countdownSpan.textContent = ` (${tiempoRestante}s)`;

    if (tiempoRestante <= 0) {
      clearInterval(countdown);
      cerrarNotificacion(mensaje);
    }
  }, 1000);

  setTimeout(() => {
    mensaje.style.opacity = "0";
  }, (NOTIFICACION_TIEMPO - 1) * 1000);

  setTimeout(() => {
    if (mensaje.parentNode) {
      mensaje.parentNode.removeChild(mensaje);
    }
  }, NOTIFICACION_TIEMPO * 1000);
}

function cerrarNotificacion(elemento) {
  const mensaje = elemento.closest(".fade-out");
  if (mensaje) {
    mensaje.style.opacity = "0";
    setTimeout(() => {
      if (mensaje.parentNode) {
        mensaje.parentNode.removeChild(mensaje);
      }
    }, 500);
  }
}
