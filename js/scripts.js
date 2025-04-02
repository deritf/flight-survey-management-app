// ======================== ACTUALIZACIÓN DE ESTADO DE VUELO ========================

function actualizarEstado(id) {
  let formData = new FormData(document.getElementById("form-estado-" + id));

  fetch("cambiar_estado.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("select-estados-" + id).value = data.estado_id;
        console.log("Estado actualizado a:", data.estado_nombre);
      } else {
        mostrarNotificacion("error", data.message);
      }
    })
    .catch((error) =>
      mostrarNotificacion("error", "Error en la actualización: " + error)
    );
}

// ======================== FORMATEO AUTOMÁTICO DE FECHAS ========================

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".fecha").forEach((elemento) => {
    let fechaOriginal = elemento.textContent.trim();
    elemento.textContent = formatearFecha(fechaOriginal);
  });
});

function formatearFecha(fecha) {
  if (!fecha) return "";
  const partes = fecha.split("-");
  if (partes.length !== 3) return fecha;
  return `${partes[2]}-${partes[1]}-${partes[0].slice(2)}`;
}

// ======================== CONFIRMACIÓN DE BORRADO ========================

function confirmarBorrado(event, url) {
  event.preventDefault();
  if (confirm("¿Estás seguro de que quieres eliminar este vuelo?")) {
    window.location.href = url;
  }
}

// ======================== FILTRO DE ESTADO ========================

function filtrarEstado(estado) {
  const urlParams = new URLSearchParams(window.location.search);
  urlParams.set("estado", estado);
  urlParams.set("pagina", 1);
  window.location.href = "listado.php?" + urlParams.toString();
}

// ======================== ACTUALIZACIÓN DE ESTADOS MASIVA ========================

function actualizarEstadosVuelos() {
  if (
    confirm(
      "Se actualizarán los estados de los vuelos en función de su fecha de salida. " +
        "Los vuelos cuya fecha ya haya pasado cambiarán a 'Expirado'. ¿Quieres continuar?"
    )
  ) {
    fetch("actualizar_estados.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.mensaje) {
          window.location.href = `listado.php?status=success&message=${encodeURIComponent(
            data.mensaje
          )}`;
        } else if (data.error) {
          window.location.href = `listado.php?status=error&message=${encodeURIComponent(
            data.error
          )}`;
        }
      })
      .catch((error) => {
        window.location.href = `listado.php?status=error&message=${encodeURIComponent(
          "Hubo un problema con la actualización: " + error
        )}`;
      });
  }
}

// ======================== ACTUALIZACIÓN DE ENCUESTAS ========================

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".input-encuestas").forEach((input) => {
    input.addEventListener("click", (event) => event.stopPropagation());

    input.addEventListener("change", function () {
      const vueloId = this.dataset.id;
      const encuestas = parseInt(this.value, 10);

      if (encuestas >= 0 && encuestas <= 850) {
        fetch("actualizar_encuestas.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id=${vueloId}&encuestas=${encuestas}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              mostrarNotificacion(
                "success",
                `Encuestas actualizadas correctamente para vuelo ID: ${vueloId}`
              );
            } else {
              mostrarNotificacion(
                "error",
                "Error al actualizar encuestas: " +
                  (data.error || "Desconocido")
              );
            }
          })
          .catch((error) =>
            mostrarNotificacion("error", "Error en la solicitud: " + error)
          );
      } else {
        mostrarNotificacion(
          "error",
          "El número de encuestas debe estar entre 0 y 850."
        );
      }
    });
  });
});

function navegarConFiltros(ruta, id = null, extraParams = {}) {
  const filtros = [
    "busqueda",
    "fecha_busqueda",
    "estado",
    "busqueda_avanzada",
    "origen",
    "pais",
    "fecha_inicio",
    "fecha_fin",
  ];

  const urlParams = new URLSearchParams(window.location.search);
  const nuevaURL = new URL(ruta, window.location.href);
  const query = nuevaURL.searchParams;

  if (id !== null) {
    query.set("id", id);
  }

  filtros.forEach((f) => {
    const valor = urlParams.get(f);
    if (valor !== null && valor !== "") {
      query.set(f, valor);
    }
  });

  // Añadir parámetros extra como status, message, etc.
  for (const clave in extraParams) {
    query.set(clave, extraParams[clave]);
  }

  window.location.href = nuevaURL.toString();
}

function confirmarBorrado(event, id) {
  if (confirm("¿Estás seguro de que quieres eliminar este vuelo?")) {
    fetch(`borrar_vuelo.php?id=${id}`, { method: "GET" })
      .then((res) => res.json())
      .then((data) => {
        const status = data.success ? "success" : "error";
        const message = data.message || "Acción completada";
        navegarConFiltros("listado.php", null, { status, message });
      })
      .catch((err) => {
        navegarConFiltros("listado.php", null, {
          status: "error",
          message: "No se pudo borrar el vuelo",
        });
      });
  }
}

// ======================== CAMBIO DE TEXTO Y COLOR EN cargar_xls.php ========================

document.addEventListener("DOMContentLoaded", function () {
  const inputArchivo = document.getElementById("archivo_xls");
  const label = document.getElementById("label-archivo");
  const texto = document.getElementById("texto-archivo");

  if (inputArchivo && label && texto) {
    inputArchivo.addEventListener("change", function () {
      if (this.files && this.files.length > 0) {
        texto.textContent = "Archivo seleccionado correctamente";
        texto.title = "";
        label.classList.add("seleccionado");
      } else {
        texto.textContent = "Pulsa aquí para elegir archivo";
        label.classList.remove("seleccionado");
      }
    });
  }
});
