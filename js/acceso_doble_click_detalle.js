document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".fila-clickable").forEach((fila) => {
    const estado = parseInt(fila.dataset.estado, 10);
    const id = fila.dataset.id;

    if (estado === 1) {
      fila.addEventListener("dblclick", () => {
        navegarConFiltros("detalle.php", id);
      });
    }
  });
});
