(function () {
    'use strict';

    function renderRevenueChart() {
        var el = document.querySelector('#report-chart');
        var data = window.__adminRevenueChart;

        if (!el || typeof ApexCharts === 'undefined' || !data) {
            return;
        }

        el.innerHTML = '';

        var options = {
            series: [{
                name: 'Ingresos',
                data: data.series || [],
            }],
            chart: {
                height: 320,
                type: 'area',
                dropShadow: {
                    enabled: true,
                    top: 10,
                    left: 0,
                    blur: 3,
                    color: '#720f1e',
                    opacity: 0.15,
                },
                toolbar: {
                    show: false,
                },
                zoom: {
                    enabled: false,
                },
            },
            markers: {
                strokeWidth: 4,
                strokeColors: '#ffffff',
                hover: {
                    size: 9,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: 'smooth',
                lineCap: 'butt',
                width: 4,
            },
            legend: {
                show: false,
            },
            colors: ['#0da487'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.6,
                    stops: [0, 90, 100],
                },
            },
            grid: {
                xaxis: {
                    lines: {
                        borderColor: 'transparent',
                        show: true,
                    },
                },
                yaxis: {
                    lines: {
                        borderColor: 'transparent',
                        show: false,
                    },
                },
                padding: {
                    right: -112,
                    bottom: 0,
                    left: 15,
                },
            },
            responsive: [{
                breakpoint: 1200,
                options: {
                    grid: {
                        padding: {
                            right: -95,
                        },
                    },
                },
            }, {
                breakpoint: 992,
                options: {
                    grid: {
                        padding: {
                            right: -69,
                        },
                    },
                },
            }, {
                breakpoint: 767,
                options: {
                    chart: {
                        height: 200,
                    },
                },
            }, {
                breakpoint: 576,
                options: {
                    yaxis: {
                        labels: {
                            show: false,
                        },
                    },
                },
            }],
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return 'Q ' + Number(value).toLocaleString('es-GT', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0,
                        });
                    },
                },
                crosshairs: {
                    show: true,
                    position: 'back',
                    stroke: {
                        color: '#b6b6b6',
                        width: 1,
                        dashArray: 5,
                    },
                },
                tooltip: {
                    enabled: true,
                },
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return 'Q ' + Number(value).toLocaleString('es-GT', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        });
                    },
                },
            },
            xaxis: {
                categories: data.labels || [],
                axisBorder: {
                    low: 0,
                    offsetX: 0,
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
        };

        var chart = new ApexCharts(el, options);
        chart.render();
        window.__adminReportChartInstance = chart;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderRevenueChart);
    } else {
        renderRevenueChart();
    }
})();
