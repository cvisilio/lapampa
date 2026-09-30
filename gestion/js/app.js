/**
 * Sistema de Gestión Gubernamental — La Pampa
 * app.js — Funciones de interfaz y utilidades
 */

/* ── Inicialización ─────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  initSidebar();
  initSidebarScrollTrap();
  initFlashMessages();
  initConfirmButtons();
  initProgressBars();
  initSearchFilter();
  initSelectOrganismo();
  initAssignModal();
  initRangeDisplays();
  initFormValidation();
});

/* ── Sidebar toggle (mobile) ─────────────────────────────────── */
function initSidebar() {
  const toggleBtn = document.getElementById('sidebar-toggle');
  const sidebar   = document.getElementById('sidebar');
  const overlay   = document.getElementById('sidebar-overlay');

  // El layout principal usa Alpine.js (includes/header.php)
  if (document.body.hasAttribute('x-data')) {
    return;
  }

  if (!toggleBtn || !sidebar) return;

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    overlay?.classList.toggle('hidden');
  });

  overlay?.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.add('hidden');
  });
}

/* ── Evitar que el scroll del menú mueva/refresque la página ─── */
function initSidebarScrollTrap() {
  const sidebar = document.getElementById('sidebar');
  const nav = sidebar?.querySelector('nav');
  if (!sidebar || !nav) return;

  sidebar.addEventListener('wheel', (event) => {
    const canScroll = nav.scrollHeight > nav.clientHeight + 1;

    if (!canScroll) {
      event.preventDefault();
      return;
    }

    const delta = event.deltaY;
    const atTop = nav.scrollTop <= 0;
    const atBottom = nav.scrollTop + nav.clientHeight >= nav.scrollHeight - 1;

    if ((delta < 0 && atTop) || (delta > 0 && atBottom)) {
      event.preventDefault();
    }
  }, { passive: false });
}

/* ── Auto-cerrar mensajes flash ──────────────────────────────── */
function initFlashMessages() {
  document.querySelectorAll('[data-flash]').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity 0.5s';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 500);
    }, 4000);
  });
}

/* ── Botones de confirmación ─────────────────────────────────── */
function initConfirmButtons() {
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      const msg = el.dataset.confirm || '¿Estás seguro?';
      if (!confirm(msg)) e.preventDefault();
    });
  });
}

/* ── Animar barras de progreso al cargar ─────────────────────── */
function initProgressBars() {
  document.querySelectorAll('.progress-bar[data-width]').forEach(bar => {
    const target = parseInt(bar.dataset.width) || 0;
    bar.style.width = '0%';
    requestAnimationFrame(() => {
      setTimeout(() => { bar.style.width = target + '%'; }, 100);
    });
  });
}

/* ── Filtro de búsqueda en tablas (client-side) ──────────────── */
function initSearchFilter() {
  const input = document.getElementById('table-search');
  if (!input) return;

  const tableId = input.dataset.table || 'data-table';
  const table   = document.getElementById(tableId);
  if (!table) return;

  input.addEventListener('input', () => {
    const q = input.value.toLowerCase().trim();
    table.querySelectorAll('tbody tr').forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(q) ? '' : 'none';
    });
  });
}

/* ── Filtrar select de funcionarios por organismo ────────────── */
function initSelectOrganismo() {
  const selOrg  = document.getElementById('sel-organismo');
  const selFunc = document.getElementById('sel-funcionario');

  if (!selOrg || !selFunc) return;

  // Guardar todas las opciones originales
  const allOpts = Array.from(selFunc.querySelectorAll('option')).map(o => ({
    value:     o.value,
    text:      o.textContent,
    organismo: o.dataset.organismo || '',
  }));

  selOrg.addEventListener('change', () => {
    const orgId = selOrg.value;
    selFunc.innerHTML = '<option value="">— Seleccioná funcionario —</option>';

    allOpts.forEach(opt => {
      if (!opt.value) return;
      if (!orgId || opt.organismo === orgId) {
        const el = document.createElement('option');
        el.value       = opt.value;
        el.textContent = opt.text;
        selFunc.appendChild(el);
      }
    });
  });

  // Aplicar filtro inicial al cargar (si ya hay organismo seleccionado)
  selOrg.dispatchEvent(new Event('change'));
}

/* ── Modal de asignación con carga AJAX de funcionarios ───────── */
function initAssignModal() {
  const modal = document.getElementById('assign-modal');
  if (!modal) return;

  const idInput = document.getElementById('assign-modal-id');
  const codeEl = document.getElementById('assign-modal-code');
  const estadoSelect = document.getElementById('assign-modal-estado');
  const organismoSelect = document.getElementById('assign-modal-organismo');
  const funcionarioSelect = document.getElementById('assign-modal-funcionario');
  const form = document.getElementById('assign-modal-form');
  const submitBtn = form?.querySelector('button[type="submit"]');
  const openButtons = document.querySelectorAll('.js-open-assign-modal');
  const closeButtons = modal.querySelectorAll('.js-assign-close');

  if (!idInput || !estadoSelect || !organismoSelect || !funcionarioSelect || !form || !submitBtn) return;

  const closeModal = () => modal.classList.add('hidden');
  const openModal = () => modal.classList.remove('hidden');

  const setPlaceholder = (text = '— Funcionario —') => {
    funcionarioSelect.innerHTML = '';
    const option = document.createElement('option');
    option.value = '';
    option.textContent = text;
    funcionarioSelect.appendChild(option);
  };

  const loadFuncionarios = async (organismoId, selectedFuncionario = '') => {
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
    if (!organismoId) {
      setPlaceholder('— Seleccioná organismo —');
      submitBtn.disabled = false;
      submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
      return;
    }

    setPlaceholder('Cargando funcionarios...');
    try {
      const resp = await fetch(`modulos/api.php?action=funcionarios_by_organismo&organismo_id=${encodeURIComponent(organismoId)}`);
      const data = await resp.json();
      setPlaceholder(data.length ? '— Funcionario —' : '— Sin funcionarios activos —');

      data.forEach(fn => {
        const option = document.createElement('option');
        option.value = String(fn.id);
        option.textContent = fn.cargo ? `${fn.nombre_apellido} (${fn.cargo})` : fn.nombre_apellido;
        funcionarioSelect.appendChild(option);
      });

      if (selectedFuncionario && funcionarioSelect.querySelector(`option[value="${selectedFuncionario}"]`)) {
        funcionarioSelect.value = selectedFuncionario;
      } else {
        funcionarioSelect.value = '';
      }
    } catch (err) {
      setPlaceholder('— Error al cargar —');
    } finally {
      submitBtn.disabled = false;
      submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
    }
  };

  openButtons.forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id || '';
      const codigo = btn.dataset.codigo || '—';
      const estado = btn.dataset.estado || '';
      const organismo = btn.dataset.organismo || '';
      const funcionario = btn.dataset.funcionario || '';

      idInput.value = id;
      codeEl.textContent = codigo;
      estadoSelect.value = estado;
      organismoSelect.value = organismo;

      openModal();
      await loadFuncionarios(organismo, funcionario);
      organismoSelect.focus();
    });
  });

  organismoSelect.addEventListener('change', () => {
    loadFuncionarios(organismoSelect.value, '');
  });

  closeButtons.forEach(btn => btn.addEventListener('click', closeModal));
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
  });

  form.addEventListener('submit', () => {
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';
  });
}

/* ── Mostrar valor de inputs range ───────────────────────────── */
function initRangeDisplays() {
  document.querySelectorAll('input[type="range"][data-display]').forEach(input => {
    const display = document.getElementById(input.dataset.display);
    if (!display) return;
    const suffix = input.dataset.suffix || '%';

    const update = () => { display.textContent = input.value + suffix; };
    update();
    input.addEventListener('input', update);
  });
}

/* ── Validación de formularios ───────────────────────────────── */
function initFormValidation() {
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', e => {
      let valid = true;

      form.querySelectorAll('[required]').forEach(field => {
        const wrapper = field.closest('.form-group') || field.parentElement;
        wrapper.querySelector('.field-error')?.remove();

        if (!field.value.trim()) {
          valid = false;
          field.classList.add('border-red-400');
          const err = document.createElement('p');
          err.className = 'field-error text-xs text-red-500 mt-1';
          err.textContent = 'Este campo es obligatorio.';
          wrapper.appendChild(err);
        } else {
          field.classList.remove('border-red-400');
        }
      });

      if (!valid) {
        e.preventDefault();
        const firstErr = form.querySelector('.border-red-400');
        firstErr?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstErr?.focus();
      }
    });
  });
}

/* ── Utilidades globales ─────────────────────────────────────── */

/**
 * Mostrar un panel colapsable
 * Uso: <button onclick="toggle('mi-panel')">
 */
function toggle(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.toggle('hidden');
}

/**
 * Copiar texto al portapapeles
 */
function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    showToast('Copiado al portapapeles', 'success');
  });
}

/**
 * Toast de notificación temporal
 */
function showToast(msg, tipo = 'info') {
  const colors = {
    success: 'bg-green-600',
    error:   'bg-red-600',
    warning: 'bg-yellow-500',
    info:    'bg-blue-600',
  };
  const toast = document.createElement('div');
  toast.className = `fixed bottom-5 right-5 z-50 text-white text-sm px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 ${colors[tipo] || colors.info}`;
  toast.innerHTML = `<i class="fas fa-check-circle"></i> ${msg}`;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.transition = 'opacity 0.4s';
    toast.style.opacity = '0';
    setTimeout(() => toast.remove(), 400);
  }, 3000);
}

/**
 * Actualizar estado de proyecto vía AJAX (sin recarga)
 */
async function actualizarEstado(proyectoId, nuevoEstado, avance) {
  try {
    const fd = new FormData();
    fd.append('cambiar_estado', '1');
    fd.append('proyecto_id',   proyectoId);
    fd.append('nuevo_estado',  nuevoEstado);
    fd.append('avance',        avance);

    const resp = await fetch('proyectos.php', { method: 'POST', body: fd });
    if (resp.ok) {
      showToast('Estado actualizado correctamente', 'success');
      setTimeout(() => location.reload(), 800);
    }
  } catch (err) {
    showToast('Error al actualizar', 'error');
  }
}

/**
 * Filtro dinámico de tabla por múltiples columnas
 */
function filterTable(inputId, tableId, cols = []) {
  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);
  if (!input || !table) return;

  input.addEventListener('input', () => {
    const q = input.value.toLowerCase();
    table.querySelectorAll('tbody tr').forEach(row => {
      const cells = Array.from(row.querySelectorAll('td'));
      const match = cols.length
        ? cols.some(i => cells[i]?.textContent.toLowerCase().includes(q))
        : row.textContent.toLowerCase().includes(q);
      row.style.display = match ? '' : 'none';
    });
  });
}

/**
 * Formatear número como pesos argentinos
 */
function formatPesos(n) {
  return '$' + Number(n).toLocaleString('es-AR', { minimumFractionDigits: 0 });
}

/**
 * Calcular días restantes hasta una fecha
 */
function diasRestantes(fechaStr) {
  if (!fechaStr) return null;
  const hoy  = new Date();
  const meta = new Date(fechaStr);
  const diff = Math.ceil((meta - hoy) / (1000 * 60 * 60 * 24));
  return diff;
}

/**
 * Aplicar clase a badges de días restantes
 */
function renderDiasRestantes(containerId, fechaStr) {
  const el = document.getElementById(containerId);
  if (!el) return;
  const dias = diasRestantes(fechaStr);
  if (dias === null) return;

  let cls = 'badge-success', txt = '';
  if (dias < 0)  { cls = 'badge-danger';  txt = `Vencido hace ${Math.abs(dias)} días`; }
  else if (dias === 0) { cls = 'badge-warning'; txt = 'Vence hoy'; }
  else if (dias <= 7)  { cls = 'badge-warning'; txt = `${dias} día(s) restante(s)`; }
  else                 { cls = 'badge-success'; txt = `${dias} días restantes`; }

  el.innerHTML = `<span class="badge ${cls}">${txt}</span>`;
}

/**
 * Inicializar gráfico de barras genérico con Chart.js
 */
function initBarChart(canvasId, labels, data, label = '', color = '#3B82F6') {
  const ctx = document.getElementById(canvasId);
  if (!ctx || !window.Chart) return;

  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label,
        data,
        backgroundColor: color + 'CC',
        borderColor: color,
        borderWidth: 1,
        borderRadius: 4,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: !!label } },
      scales: {
        y: { beginAtZero: true, grid: { color: '#F1F5F9' } },
        x: { grid: { display: false } }
      }
    }
  });
}

/**
 * Mostrar/ocultar spinner de carga en botón de submit
 */
function initSubmitSpinner() {
  document.querySelectorAll('[data-submit-spinner]').forEach(form => {
    form.addEventListener('submit', () => {
      const btn = form.querySelector('[type="submit"]');
      if (!btn) return;
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
    });
  });
}
