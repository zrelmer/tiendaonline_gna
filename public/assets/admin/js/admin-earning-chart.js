(function () {
    'use strict';

    function renderEarningChart() {
        var el = document.querySelector('#bar-chart-earning');
        var data = window.__adminEarningChart;

        if (!el || typeof ApexCharts === 'undefined' || !data) {
            return;
        }

        el.innerHTML = '';

        var options = {
            series: (data.series || []).map(function (item) {
                return {
                    name: item.name,
                    data: item.data,
                };
            }),
            chart: {
                type: 'bar',
                height: 320,
                toolbar: {
                    show: false,
                },
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 4,
                },
            },
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'right',
            },
            colors: ['#0da487', '#2483e2'],
            dataLabels: {
                enabled: false,
            },
            markers: {
                size: 1,
            },
            xaxis: {
                categories: data.labels || [],
                labels: {
                    show: true,
                },
            },
            yaxis: [
                {
                    seriesName: 'Ingresos',
                    title: {
                        text: 'Ingresos (Q)',
                    },
                    labels: {
                        formatter: function (value) {
                            return 'Q ' + Number(value).toLocaleString('es-GT', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                            });
                        },
                    },
                },
                {
                    seriesName: 'Pedidos',
                    opposite: true,
                    title: {
                        text: 'Pedidos',
                    },
                    labels: {
                        formatter: function (value) {
                            return Number(value).toLocaleString('es-GT', {
                                maximumFractionDigits: 0,
                            });
                        },
                    },
                },
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (value, opts) {
                        if (opts.seriesIndex === 0) {
                            return 'Q ' + Number(value).toLocaleString('es-GT', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            });
                        }

                        return Number(value).toLocaleString('es-GT') + ' pedido(s)';
                    },
                },
            },
            responsive: [{
                breakpoint: 1400,
                options: {
                    chart: {
                        height: 300,
                    },
                },
            }, {
                breakpoint: 992,
                options: {
                    chart: {
                        height: 210,
                    },
                },
            }, {
                breakpoint: 578,
                options: {
                    chart: {
                        height: 200,
                    },
                },
            }],
        };

        var chart = new ApexCharts(el, options);
        chart.render();
        window.__adminEarningChartInstance = chart;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderEarningChart);
    } else {
        renderEarningChart();
    }
})();
