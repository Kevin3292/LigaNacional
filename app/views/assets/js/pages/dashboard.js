// app/views/assets/js/pages/dashboard.js
(function () {
  if (window.__dashInit) return;
  window.__dashInit = true;

  if (document.body.dataset.page !== 'inicio') return; // <-- clave
  if (typeof ApexCharts === 'undefined') return;

  function renderIfExists(selector, options) {
    const el = document.querySelector(selector);
    if (!el) return null;
    const chart = new ApexCharts(el, options);
    chart.render();
    return chart;
  }

  document.addEventListener('DOMContentLoaded', function () {
    const optionsProfileVisit = {
      annotations: { position: 'back' },
      dataLabels: { enabled: false },
      chart: { type: 'bar', height: 300 },
      fill: { opacity: 1 },
      series: [{ name: 'sales', data: [9,20,30,20,10,20,30,20,10,20,30,20] }],
      colors: ['#435ebe'],
      xaxis: { categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] }
    };

    const optionsVisitorsProfile = {
      series: [70, 30],
      labels: ['Male', 'Female'],
      colors: ['#435ebe','#55c6e8'],
      chart: { type: 'donut', width: '100%', height: 350 },
      legend: { position: 'bottom' },
      plotOptions: { pie: { donut: { size: '30%' } } }
    };

    const baseArea = {
      series: [{ name: 'series1', data: [310,800,600,430,540,340,605,805,430,540,340,605] }],
      chart: { height: 80, type: 'area', toolbar: { show: false } },
      colors: ['#5350e9'],
      stroke: { width: 2 },
      grid: { show: false },
      dataLabels: { enabled: false },
      xaxis: {
        type: 'datetime',
        categories: [
          '2018-09-19T00:00:00.000Z','2018-09-19T01:30:00.000Z','2018-09-19T02:30:00.000Z',
          '2018-09-19T03:30:00.000Z','2018-09-19T04:30:00.000Z','2018-09-19T05:30:00.000Z',
          '2018-09-19T06:30:00.000Z','2018-09-19T07:30:00.000Z','2018-09-19T08:30:00.000Z',
          '2018-09-19T09:30:00.000Z','2018-09-19T10:30:00.000Z','2018-09-19T11:30:00.000Z'
        ],
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { show: false }
      },
      yaxis: { labels: { show: false } },
      tooltip: { x: { format: 'dd/MM/yy HH:mm' } }
    };

    const optionsEurope    = { ...baseArea, colors: ['#5350e9'] };
    const optionsAmerica   = { ...baseArea, colors: ['#008b75'] };
    const optionsIndonesia = { ...baseArea, colors: ['#dc3545'] };

    renderIfExists('#chart-profile-visit',    optionsProfileVisit);
    renderIfExists('#chart-visitors-profile', optionsVisitorsProfile);
    renderIfExists('#chart-europe',           optionsEurope);
    renderIfExists('#chart-america',          optionsAmerica);
    renderIfExists('#chart-indonesia',        optionsIndonesia);
  });
})();
