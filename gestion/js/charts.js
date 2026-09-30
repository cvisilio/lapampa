/**
 * charts.js — Inicialización de gráficos del dashboard
 * Requiere Chart.js cargado previamente
 */

/* ── Paleta de colores institucional ─────────────────────────── */
const COLORES = {
  primary:  '#1D4ED8',
  success:  '#059669',
  warning:  '#D97706',
  danger:   '#DC2626',
  purple:   '#7C3AED',
  teal:     '#0D9488',
  orange:   '#EA580C',
  pink:     '#DB2777',
  gray:     '#6B7280',
  indigo:   '#4338CA',
};

const PALETA = Object.values(COLORES);

/* Defaults globales de Chart.js */
if (window.Chart) {
  Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, sans-serif";
  Chart.defaults.font.size   = 12;
  Chart.defaults.color       = '#64748B';
}

/* ── Gráfico: Proyectos por estado (doughnut) ─────────────────── */
function initChartEstadosProyectos(canvasId, labels, data) {
  const ctx = document.getElementById(canvasId);
  if (!ctx) return;

  const colMap = {
    'idea':         '#A78BFA',
    'formulacion':  '#FCD34D',
    'aprobado':     '#60A5FA',
    'en_ejecucion': '#34D399',
    'completado':   '#059669',
    'cancelado':    '#F87171',
  };

  const backgroundColor = labels.map(l => colMap[l.toLowerCase().replace(' ','_')] || '#94A3B8');

  return new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1).replace('_',' ')),
      datasets: [{
        data,
        backgroundColor,
        borderWidth: 3,
        borderColor: '#fff',
        hoverOffset: 6,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '68%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: { boxWidth: 12, padding: 10, font: { size: 11 } }
        },
        tooltip: {
          callbacks: {
            label: ctx => ` ${ctx.label}: ${ctx.parsed} proyecto(s)`
          }
        }
      }
    }
  });
}

/* ── Gráfico: Avance general (gauge / doughnut) ───────────────── */
function initChartAvance(canvasId, avancePct) {
  const ctx = document.getElementById(canvasId);
  if (!ctx) return;

  const color = avancePct >= 75 ? COLORES.success
              : avancePct >= 40 ? COLORES.primary
              : COLORES.warning;

  return new Chart(ctx, {
    type: 'doughnut',
    data: {
      datasets: [{
        data: [avancePct, 100 - avancePct],
        backgroundColor: [color, '#F1F5F9'],
        borderWidth: 0,
        hoverOffset: 0,
      }]
    },
    options: {
      responsive: true,
      cutout: '78%',
      plugins: {
        legend:  { display: false },
        tooltip: { enabled: false },
      }
    }
  });
}

/* ── Gráfico: Presupuesto ejecutado vs total (barras) ─────────── */
function initChartPresupuesto(canvasId, labels, ejecutado, total) {
  const ctx = document.getElementById(canvasId);
  if (!ctx) return;

  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'Ejecutado',
          data: ejecutado,
          backgroundColor: COLORES.success + 'CC',
          borderColor: COLORES.success,
          borderWidth: 1,
          borderRadius: 4,
        },
        {
          label: 'Total asignado',
          data: total,
          backgroundColor: COLORES.primary + '40',
          borderColor: COLORES.primary + '80',
          borderWidth: 1,
          borderRadius: 4,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } },
        tooltip: {
          callbacks: {
            label: ctx => ` ${ctx.dataset.label}: $${Number(ctx.parsed.y).toLocaleString('es-AR')}`
          }
        }
      },
      scales: {
        x: { grid: { display: false } },
        y: {
          beginAtZero: true,
          grid: { color: '#F1F5F9' },
          ticks: {
            callback: v => '$' + (v >= 1e6 ? (v/1e6).toFixed(1)+'M' : (v/1e3).toFixed(0)+'K')
          }
        }
      }
    }
  });
}

/* ── Gráfico: Acciones por estado (horizontal bars) ──────────── */
function initChartAccionesEstado(canvasId, labels, data) {
  const ctx = document.getElementById(canvasId);
  if (!ctx) return;

  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        data,
        backgroundColor: [
          COLORES.gray + 'CC',
          COLORES.primary + 'CC',
          COLORES.success + 'CC',
          COLORES.danger + 'CC',
        ],
        borderRadius: 4,
        borderWidth: 0,
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { beginAtZero: true, grid: { color: '#F1F5F9' } },
        y: { grid: { display: false } }
      }
    }
  });
}

/* ── Gráfico: Proyectos por organismo (barras) ────────────────── */
function initChartOrganismos(canvasId, labels, data, colores) {
  const ctx = document.getElementById(canvasId);
  if (!ctx) return;

  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Proyectos',
        data,
        backgroundColor: (colores || []).map(c => c + 'CC') || COLORES.primary + 'CC',
        borderRadius: 4,
        borderWidth: 0,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
        y: { beginAtZero: true, grid: { color: '#F1F5F9' } }
      }
    }
  });
}

/* ── Gráfico: Línea de tiempo de creación de proyectos ────────── */
function initChartTimeline(canvasId, labels, data) {
  const ctx = document.getElementById(canvasId);
  if (!ctx) return;

  return new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Proyectos creados',
        data,
        borderColor: COLORES.primary,
        backgroundColor: COLORES.primary + '18',
        fill: true,
        tension: 0.4,
        pointBackgroundColor: COLORES.primary,
        pointRadius: 4,
        pointHoverRadius: 6,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, grid: { color: '#F1F5F9' } }
      }
    }
  });
}
