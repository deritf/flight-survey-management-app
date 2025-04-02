document.addEventListener("DOMContentLoaded", function () {
  let mensajes = document.querySelectorAll(".fade-out");

  mensajes.forEach((mensaje) => {
    let countdownSpan = mensaje.querySelector(".countdown");
    let tiempoRestante =
      typeof NOTIFICACION_TIEMPO !== "undefined" ? NOTIFICACION_TIEMPO : 8;

    countdownSpan.textContent = ` (${tiempoRestante}s)`;

    let countdown = setInterval(() => {
      tiempoRestante--;
      countdownSpan.textContent = ` (${tiempoRestante}s)`;

      if (tiempoRestante <= 0) {
        clearInterval(countdown);
        mensaje.classList.add("hidden");
      }
    }, 1000);

    setTimeout(() => {
      mensaje.style.opacity = "0";
    }, (NOTIFICACION_TIEMPO - 1) * 1000);

    setTimeout(() => {
      mensaje.style.visibility = "hidden";
    }, NOTIFICACION_TIEMPO * 1000);
  });
});
