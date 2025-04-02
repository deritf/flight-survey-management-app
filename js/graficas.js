document.addEventListener("DOMContentLoaded", function () {
  cargarGraficaEstados();
  cargarGraficaIslas();
});

function cargarGraficaEstados() {
  var chartEstados = echarts.init(
    document.getElementById("grafica-vuelos-estados")
  );
  var optionEstados = {
    tooltip: { trigger: "item", formatter: "{b}: {c} ({d}%)" },
    series: [
      {
        name: "Vuelos",
        type: "pie",
        radius: "50%",
        data: estadosData,
        emphasis: {
          itemStyle: {
            shadowBlur: 10,
            shadowOffsetX: 0,
            shadowColor: "rgba(0, 0, 0, 0.5)",
          },
        },
      },
    ],
  };
  chartEstados.setOption(optionEstados);
}

function cargarGraficaIslas() {
  var chartIslas = echarts.init(
    document.getElementById("grafica-vuelos-islas")
  );
  var optionIslas = {
    tooltip: { trigger: "axis", axisPointer: { type: "shadow" } },
    xAxis: {
      type: "category",
      data: islasLabels,
      axisLabel: { rotate: 30 },
    },
    yAxis: { type: "value", name: "Cantidad de Vuelos" },
    series: [
      {
        name: "Vuelos",
        type: "bar",
        barWidth: "60%",
        data: islasValues,
        itemStyle: { color: colores.barrasIslas },
      },
    ],
  };
  chartIslas.setOption(optionIslas);
}

function cargarDatosPorIsla() {
  let islaSeleccionada = document.getElementById("selector-isla").value;
  if (!islaSeleccionada) return;

  fetch(
    `obtener_vuelos_por_isla.php?isla=${encodeURIComponent(islaSeleccionada)}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        alert("Error: " + data.error);
        return;
      }

      let nombresPaises = data.map((item) => item.pais);
      let cantidadVuelos = data.map((item) => item.cantidad);

      let chart = echarts.init(document.getElementById("grafica-destinos"));
      let option = {
        tooltip: { trigger: "axis", axisPointer: { type: "shadow" } },
        xAxis: { type: "category", data: nombresPaises },
        yAxis: { type: "value" },
        series: [
          {
            name: "Vuelos",
            type: "bar",
            barWidth: "60%",
            data: cantidadVuelos,
            itemStyle: { color: colores.barrasDestinos },
          },
        ],
      };

      chart.setOption(option);
    })
    .catch((error) => console.error("Error cargando datos:", error));
}

function cargarDatosPorDia() {
  let fechaSeleccionada = document.getElementById("selector-fecha").value;
  if (!fechaSeleccionada) {
    alert("Por favor, selecciona una fecha.");
    return;
  }

  fetch(
    `obtener_vuelos_por_dia.php?fecha=${encodeURIComponent(fechaSeleccionada)}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        console.error("Error:", data.error);
        alert("No hay datos disponibles para esta fecha.");
        return;
      }

      let islas = data.map((item) => item.isla);
      let cantidades = data.map((item) => item.cantidad_vuelos);

      let option = {
        tooltip: { trigger: "axis", axisPointer: { type: "shadow" } },
        xAxis: { type: "category", data: islas, axisLabel: { rotate: 30 } },
        yAxis: { type: "value", name: "Cantidad de Vuelos" },
        series: [
          {
            name: "Vuelos",
            type: "bar",
            barWidth: "60%",
            data: cantidades,
            itemStyle: { color: colores.barrasDias },
          },
        ],
      };

      let chart = echarts.init(document.getElementById("grafica-origenes-dia"));
      chart.setOption(option);
    })
    .catch((error) => console.error("Error cargando datos:", error));
}
