document.addEventListener("DOMContentLoaded", () => {
  // === Variables de control ===
  let startX = 0; // Almacena la posición inicial del swipe
  let currentX = 0; // Almacena la posición actual del swipe durante el movimiento
  let isSwiping = false; // Indica si actualmente se está realizando un swipe
  let hasMoved = false; // Controla si hubo algún movimiento (para distinguir de un simple clic)

  // === Configuración de umbrales ===
  const swipeThreshold = 200; // <*** IMPORTANTE ***> Distancia mínima (en píxeles) para considerar un swipe válido <*** IMPORTANTE ***>
  const maxDistance = 300; // <*** IMPORTANTE ***> Distancia máxima para calcular la intensidad del color de fondo durante el swipe <*** IMPORTANTE ***>

  const todasFilas = document.querySelectorAll("tr[data-id]");

  const filasActivas = document.querySelectorAll("tr.vuelo-activo[data-id]");

  todasFilas.forEach((fila) => {
    fila.addEventListener("dblclick", () => {
      const id = fila.dataset.id;
      window.location.href = `detalle.php?id=${id}`;
    });
  });

  filasActivas.forEach((fila) => {
    fila.addEventListener("touchstart", (e) =>
      iniciarSwipe(e.touches[0].clientX)
    );
    fila.addEventListener("mousedown", (e) => iniciarSwipe(e.clientX));

    fila.addEventListener("touchmove", (e) =>
      moverSwipe(e.touches[0].clientX, fila)
    );
    fila.addEventListener("mousemove", (e) => moverSwipe(e.clientX, fila));

    fila.addEventListener("touchend", () => finalizarSwipe(fila));
    fila.addEventListener("mouseup", () => finalizarSwipe(fila));
    fila.addEventListener(
      "mouseleave",
      () => isSwiping && finalizarSwipe(fila)
    );

    fila.addEventListener("dragstart", (e) => e.preventDefault());
  });

  function iniciarSwipe(x) {
    startX = x;
    isSwiping = true;
    hasMoved = false;
  }

  function moverSwipe(x, fila) {
    if (!isSwiping) return;
    currentX = x;
    const moveX = currentX - startX;

    if (Math.abs(moveX) > 10) {
      hasMoved = true;
    }

    if (hasMoved) {
      fila.style.transform = `translateX(${moveX}px)`;
      cambiarColorDinamico(fila, moveX);
    }
  }

  function finalizarSwipe(fila) {
    isSwiping = false;
    const deltaX = currentX - startX;

    if (!hasMoved || Math.abs(deltaX) < swipeThreshold) {
      restaurarFila(fila);
    } else if (deltaX < -swipeThreshold) {
      actualizarEstadoVuelo(fila.dataset.id, 3, fila);
    } else if (deltaX > swipeThreshold) {
      actualizarEstadoVuelo(fila.dataset.id, 2, fila);
    }
  }

  function cambiarColorDinamico(fila, distancia) {
    const intensidad = Math.min(Math.abs(distancia) / maxDistance, 1);

    if (distancia < 0) {
      fila.style.backgroundColor = `rgba(220, 53, 69, ${intensidad})`;
    } else if (distancia > 0) {
      fila.style.backgroundColor = `rgba(40, 167, 69, ${intensidad})`;
    } else {
      fila.style.backgroundColor = "";
    }
  }

  function restaurarFila(fila) {
    fila.style.transition = "transform 0.3s ease, background-color 0.3s ease";
    fila.style.transform = "translateX(0)";
    fila.style.backgroundColor = "";
  }

  async function actualizarEstadoVuelo(id, nuevoEstado, fila) {
    try {
      const respuesta = await fetch("actualizar_estado_swipe.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id, estado: nuevoEstado }),
      });

      const resultado = await respuesta.json();

      if (resultado.success) {
        fila.classList.remove(
          "vuelo-activo",
          "vuelo-encuestado",
          "vuelo-expirado"
        );
        fila.classList.add(
          nuevoEstado === 2 ? "vuelo-encuestado" : "vuelo-expirado"
        );

        const nuevoColor =
          nuevoEstado === 2 ? "rgba(40, 167, 69, 1)" : "rgba(220, 53, 69, 1)";
        fila.style.transition = "background-color 0.3s ease";
        fila.style.backgroundColor = nuevoColor;

        const input = fila.querySelector(".input-encuestas");
        if (input) {
          input.style.transition = "background-color 0.3s ease";
          input.style.backgroundColor = nuevoColor;
        }

        mostrarNotificacion(
          `Vuelo actualizado a ${
            nuevoEstado === 2 ? "Encuestado" : "Expirado"
          }`,
          true
        );

        setTimeout(() => restaurarFila(fila), 500);
      } else {
        mostrarNotificacion("Error al actualizar el estado del vuelo.", false);
      }
    } catch (error) {
      console.error("Error al actualizar el estado:", error);
      mostrarNotificacion("Error de conexión.", false);
    }
  }

  function mostrarNotificacion(mensaje, exito) {
    const notificacion = document.createElement("div");
    notificacion.className = `notificacion ${
      exito ? "notificacion-exito" : "notificacion-error"
    }`;
    notificacion.innerHTML = `<span>${mensaje}</span>`;
    document.body.appendChild(notificacion);

    setTimeout(() => {
      notificacion.remove();
    }, 3000);
  }
});
