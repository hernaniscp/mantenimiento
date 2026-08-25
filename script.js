const CLAVE_ALMACENAMIENTO = "mantenimiento_equipos";

function obtenerEquipos() {
  const datos = localStorage.getItem(CLAVE_ALMACENAMIENTO);
  return datos ? JSON.parse(datos) : [];
}

function guardarEquipos(equipos) {
  localStorage.setItem(CLAVE_ALMACENAMIENTO, JSON.stringify(equipos));
}

function renderizarEquipos() {
  const equipos = obtenerEquipos();
  const cuerpoTabla = document.getElementById("cuerpoTablaEquipos");
  const mensajeVacio = document.getElementById("mensajeVacio");

  cuerpoTabla.innerHTML = "";

  if (equipos.length === 0) {
    mensajeVacio.style.display = "block";
  } else {
    mensajeVacio.style.display = "none";

    equipos.forEach((equipo) => {
      const fila = document.createElement("tr");
      const claseBadge = equipo.estado === "ok" ? "ok" : "pendiente";
      const textoBadge = equipo.estado === "ok" ? "Al día" : "Pendiente";

      fila.innerHTML = `
        <td>${equipo.nombre}</td>
        <td>${equipo.tipo}</td>
        <td>${equipo.ubicacion || "-"}</td>
        <td><span class="badge ${claseBadge}">${textoBadge}</span></td>
        <td><button class="btn-quitar" data-id="${equipo.id}">Quitar</button></td>
      `;
      cuerpoTabla.appendChild(fila);
    });
  }

  actualizarContadores(equipos);
}

function actualizarContadores(equipos) {
  document.getElementById("statTotal").textContent = equipos.length;
  // "Mantenimientos realizados" NO se calcula a partir del estado del equipo:
  // ese panel se usará más adelante para los mantenimientos que se registren
  // desde la sección "Mantenimientos". Por ahora queda en 0.
  document.getElementById("statRealizados").textContent = 0;
  document.getElementById("statPendientes").textContent =
    equipos.filter((e) => e.estado === "pendiente").length;
}

function agregarEquipo() {
  const nombre = document.getElementById("inputNombre").value.trim();
  const tipo = document.getElementById("inputTipo").value;
  const ubicacion = document.getElementById("inputUbicacion").value.trim();
  const estado = document.getElementById("inputEstado").value;

  if (!nombre) {
    alert("Por favor ingresá el nombre del equipo.");
    return;
  }

  const equipos = obtenerEquipos();
  equipos.push({
    id: Date.now(),
    nombre,
    tipo,
    ubicacion,
    estado,
  });

  guardarEquipos(equipos);
  renderizarEquipos();
  cerrarFormulario();
}

function quitarEquipo(id) {
  if (!confirm("¿Seguro que querés quitar este equipo?")) return;

  let equipos = obtenerEquipos();
  equipos = equipos.filter((e) => e.id !== id);
  guardarEquipos(equipos);
  renderizarEquipos();
}

function abrirFormulario() {
  document.getElementById("formAgregar").classList.add("abierto");
}

function cerrarFormulario() {
  document.getElementById("formAgregar").classList.remove("abierto");
  document.getElementById("inputNombre").value = "";
  document.getElementById("inputUbicacion").value = "";
  document.getElementById("inputTipo").selectedIndex = 0;
  document.getElementById("inputEstado").selectedIndex = 0;
}

document.getElementById("btnToggleForm").addEventListener("click", abrirFormulario);
document.getElementById("btnCancelar").addEventListener("click", cerrarFormulario);
document.getElementById("btnGuardar").addEventListener("click", agregarEquipo);

// Delegación de eventos para los botones "Quitar" que se generan dinámicamente
document.getElementById("cuerpoTablaEquipos").addEventListener("click", (evento) => {
  if (evento.target.classList.contains("btn-quitar")) {
    const id = Number(evento.target.getAttribute("data-id"));
    quitarEquipo(id);
  }
});

renderizarEquipos();
