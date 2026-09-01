const API_EQUIPOS = "api/equipos.php";
const API_MANTENIMIENTOS = "api/mantenimientos.php";

// ==========================================================
// CARGA DE DATOS
// ==========================================================

async function obtenerEquipos() {
  const respuesta = await fetch(API_EQUIPOS);
  if (!respuesta.ok) {
    console.error("Error al obtener equipos");
    return [];
  }
  return await respuesta.json();
}

async function obtenerMantenimientos() {
  const respuesta = await fetch(API_MANTENIMIENTOS);
  if (!respuesta.ok) {
    console.error("Error al obtener mantenimientos");
    return [];
  }
  return await respuesta.json();
}

// Punto central: trae equipos y mantenimientos, y refresca todo lo que
// depende de ellos (tabla de equipos, selector, tablas de mantenimientos,
// historial y las estadísticas del panel principal).
async function actualizarPanel() {
  const [equipos, mantenimientos] = await Promise.all([
    obtenerEquipos(),
    obtenerMantenimientos(),
  ]);

  renderizarEquipos(equipos);
  llenarSelectEquipos(equipos);
  renderizarMantenimientosPendientes(mantenimientos);
  renderizarHistorial(mantenimientos);
  actualizarContadores(equipos, mantenimientos);
}

// ==========================================================
// PANEL PRINCIPAL (estadísticas)
// ==========================================================

function actualizarContadores(equipos, mantenimientos) {
  const realizados = mantenimientos.filter((m) => m.estado === "realizado").length;
  const pendientes = mantenimientos.filter((m) => m.estado === "pendiente").length;

  document.getElementById("statTotal").textContent = equipos.length;
  document.getElementById("statRealizados").textContent = realizados;
  document.getElementById("statPendientes").textContent = pendientes;
}

// ==========================================================
// EQUIPOS
// ==========================================================

function renderizarEquipos(equipos) {
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
        <td>${equipo.descripcion || "-"}</td>
        <td>${equipo.tipo}</td>
        <td>${equipo.ubicacion || "-"}</td>
        <td><span class="badge ${claseBadge}">${textoBadge}</span></td>
        <td><button class="btn-quitar" data-id="${equipo.id}">Quitar</button></td>
      `;
      cuerpoTabla.appendChild(fila);
    });
  }
}

async function agregarEquipo() {
  const nombre = document.getElementById("inputNombre").value.trim();
  const descripcion = document.getElementById("inputDescripcionEquipo").value.trim();
  const tipo = document.getElementById("inputTipo").value;
  const ubicacion = document.getElementById("inputUbicacion").value.trim();
  const estado = document.getElementById("inputEstado").value;

  if (!nombre) {
    alert("Por favor ingresá el nombre del equipo.");
    return;
  }

  const respuesta = await fetch(API_EQUIPOS, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ nombre, descripcion, tipo, ubicacion, estado }),
  });

  if (!respuesta.ok) {
    const error = await respuesta.json();
    alert(error.error || "No se pudo guardar el equipo.");
    return;
  }

  await actualizarPanel();
  cerrarFormulario();
}

async function quitarEquipo(id) {
  if (!confirm("¿Seguro que querés quitar este equipo? También se van a borrar sus mantenimientos.")) return;

  const respuesta = await fetch(`${API_EQUIPOS}?id=${id}`, {
    method: "DELETE",
  });

  if (!respuesta.ok) {
    alert("No se pudo quitar el equipo.");
    return;
  }

  await actualizarPanel();
}

function abrirFormulario() {
  document.getElementById("formAgregar").classList.add("abierto");
}

function cerrarFormulario() {
  document.getElementById("formAgregar").classList.remove("abierto");
  document.getElementById("inputNombre").value = "";
  document.getElementById("inputDescripcionEquipo").value = "";
  document.getElementById("inputUbicacion").value = "";
  document.getElementById("inputTipo").selectedIndex = 0;
  document.getElementById("inputEstado").selectedIndex = 0;
}

// ==========================================================
// MANTENIMIENTOS
// ==========================================================

function llenarSelectEquipos(equipos) {
  const select = document.getElementById("selectEquipoMantenimiento");
  const seleccionActual = select.value;

  select.innerHTML = "";

  if (equipos.length === 0) {
    const opcion = document.createElement("option");
    opcion.textContent = "No hay equipos registrados";
    opcion.value = "";
    select.appendChild(opcion);
    return;
  }

  equipos.forEach((equipo) => {
    const opcion = document.createElement("option");
    opcion.value = equipo.id;
    opcion.textContent = `${equipo.nombre} (${equipo.tipo})`;
    select.appendChild(opcion);
  });

  // Si el equipo que estaba elegido todavía existe, lo dejamos seleccionado
  if ([...select.options].some((o) => o.value === seleccionActual)) {
    select.value = seleccionActual;
  }
}

function renderizarMantenimientosPendientes(mantenimientos) {
  const pendientes = mantenimientos.filter((m) => m.estado === "pendiente");
  const cuerpoTabla = document.getElementById("cuerpoTablaMantenimientos");
  const mensajeVacio = document.getElementById("mensajeVacioMantenimientos");

  cuerpoTabla.innerHTML = "";

  if (pendientes.length === 0) {
    mensajeVacio.style.display = "block";
  } else {
    mensajeVacio.style.display = "none";

    pendientes.forEach((m) => {
      const fila = document.createElement("tr");
      fila.innerHTML = `
        <td>${m.equipo_nombre}</td>
        <td>${formatearFechaAlta(m.equipo_fecha_alta)}</td>
        <td>${m.tipo_trabajo}</td>
        <td>${m.descripcion || "-"}</td>
        <td>${formatearFecha(m.fecha)}</td>
        <td><span class="badge pendiente">Pendiente</span></td>
        <td class="celda-acciones">
          <button class="btn-completar" data-id="${m.id}">Marcar realizado</button>
          <button class="btn-quitar" data-id="${m.id}">Quitar</button>
        </td>
      `;
      cuerpoTabla.appendChild(fila);
    });
  }
}

function renderizarHistorial(mantenimientos) {
  const cuerpoTabla = document.getElementById("cuerpoTablaHistorial");
  const mensajeVacio = document.getElementById("mensajeVacioHistorial");

  cuerpoTabla.innerHTML = "";

  if (mantenimientos.length === 0) {
    mensajeVacio.style.display = "block";
  } else {
    mensajeVacio.style.display = "none";

    mantenimientos.forEach((m) => {
      const fila = document.createElement("tr");
      const claseBadge = m.estado === "realizado" ? "ok" : "pendiente";
      const textoBadge = m.estado === "realizado" ? "Realizado" : "Pendiente";

      fila.innerHTML = `
        <td>${m.equipo_nombre}</td>
        <td>${formatearFechaAlta(m.equipo_fecha_alta)}</td>
        <td>${m.tipo_trabajo}</td>
        <td>${m.descripcion || "-"}</td>
        <td>${formatearFecha(m.fecha)}</td>
        <td><span class="badge ${claseBadge}">${textoBadge}</span></td>
        <td class="celda-acciones">
          <button class="btn-quitar-historial" data-id="${m.id}">Quitar</button>
        </td>
      `;
      cuerpoTabla.appendChild(fila);
    });
  }
}

function formatearFecha(fechaISO) {
  if (!fechaISO) return "-";
  const [anio, mes, dia] = fechaISO.split("-");
  return `${dia}/${mes}/${anio}`;
}

// La fecha de alta viene como TIMESTAMP de MySQL, ej: "2026-08-27 20:31:05"
function formatearFechaAlta(fechaHora) {
  if (!fechaHora) return "-";
  const soloFecha = fechaHora.split(" ")[0];
  return formatearFecha(soloFecha);
}

async function agregarMantenimiento() {
  const equipo_id = document.getElementById("selectEquipoMantenimiento").value;
  const tipo_trabajo = document.getElementById("inputTipoTrabajo").value;
  const descripcion = document.getElementById("inputDescripcion").value.trim();
  const estado = document.getElementById("inputEstadoMantenimiento").value;
  const fecha = document.getElementById("inputFechaMantenimiento").value;

  if (!equipo_id) {
    alert("Por favor seleccioná un equipo (primero tenés que tener al menos uno registrado).");
    return;
  }

  const respuesta = await fetch(API_MANTENIMIENTOS, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ equipo_id, tipo_trabajo, descripcion, estado, fecha }),
  });

  if (!respuesta.ok) {
    const error = await respuesta.json();
    alert(error.error || "No se pudo guardar el mantenimiento.");
    return;
  }

  await actualizarPanel();
  cerrarFormularioMantenimiento();
}

async function marcarComoRealizado(id) {
  const respuesta = await fetch(`${API_MANTENIMIENTOS}?id=${id}`, {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ estado: "realizado" }),
  });

  if (!respuesta.ok) {
    alert("No se pudo actualizar el mantenimiento.");
    return;
  }

  await actualizarPanel();
}

async function eliminarMantenimiento(id) {
  if (!confirm("¿Seguro que querés quitar este mantenimiento?")) return;

  const respuesta = await fetch(`${API_MANTENIMIENTOS}?id=${id}`, {
    method: "DELETE",
  });

  if (!respuesta.ok) {
    alert("No se pudo quitar el mantenimiento.");
    return;
  }

  await actualizarPanel();
}

function abrirFormularioMantenimiento() {
  const inputFecha = document.getElementById("inputFechaMantenimiento");
  if (!inputFecha.value) {
    inputFecha.value = new Date().toISOString().slice(0, 10);
  }
  document.getElementById("formMantenimiento").classList.add("abierto");
}

function cerrarFormularioMantenimiento() {
  document.getElementById("formMantenimiento").classList.remove("abierto");
  document.getElementById("inputTipoTrabajo").selectedIndex = 0;
  document.getElementById("inputEstadoMantenimiento").selectedIndex = 0;
  document.getElementById("inputDescripcion").value = "";
  document.getElementById("inputFechaMantenimiento").value = "";
}

// ==========================================================
// EVENTOS
// ==========================================================

document.getElementById("btnToggleForm").addEventListener("click", abrirFormulario);
document.getElementById("btnCancelar").addEventListener("click", cerrarFormulario);
document.getElementById("btnGuardar").addEventListener("click", agregarEquipo);

document.getElementById("btnToggleFormMantenimiento").addEventListener("click", abrirFormularioMantenimiento);
document.getElementById("btnCancelarMantenimiento").addEventListener("click", cerrarFormularioMantenimiento);
document.getElementById("btnGuardarMantenimiento").addEventListener("click", agregarMantenimiento);

// Delegación de eventos para los botones que se generan dinámicamente
document.getElementById("cuerpoTablaEquipos").addEventListener("click", (evento) => {
  if (evento.target.classList.contains("btn-quitar")) {
    const id = Number(evento.target.getAttribute("data-id"));
    quitarEquipo(id);
  }
});

document.getElementById("cuerpoTablaMantenimientos").addEventListener("click", (evento) => {
  const id = Number(evento.target.getAttribute("data-id"));
  if (evento.target.classList.contains("btn-completar")) {
    marcarComoRealizado(id);
  } else if (evento.target.classList.contains("btn-quitar")) {
    eliminarMantenimiento(id);
  }
});

document.getElementById("cuerpoTablaHistorial").addEventListener("click", (evento) => {
  if (evento.target.classList.contains("btn-quitar-historial")) {
    const id = Number(evento.target.getAttribute("data-id"));
    eliminarMantenimiento(id);
  }
});

actualizarPanel();
