(function () {
    'use strict';

    function renderVisitorsChart() {
        var el = document.querySelector('#pie-chart-visitors');
        var data = window.__adminVisitorsChart;

        if (!el || typeof ApexCharts === 'undefined' || !data) {
            return;
        }

        el.innerHTML = '';

        var options = {
            series: data.series || [],
            labels: data.labels || [],
            chart: {
                width: '100%',
                height: 275,
                type: 'donut',
            },
            legend: {
                fontSize: '12px',
                position: 'bottom',
                offsetX: 1,
                offsetY: -1,
                markers: {
                    width: 10,
                    height: 10,
                },
                itemMargin: {
                    vertical: 2,
                },
            },
            colors: ['#28c870', '#ffa044', '#9e65c2', '#6670bd', '#0da487', '#2483e2', '#FF9800'],
            plotOptions: {
                pie: {
                    startAngle: -90,
                    endAngle: 270,
                },
            },
            dataLabels: {
                enabled: false,
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return Number(value).toLocaleString('es-GT') + ' pedido(s)';
                    },
                },
            },
            responsive: [{
                breakpoint: 1835,
                options: {
                    chart: {
                        height: 245,
                    },
                    legend: {
                        position: 'bottom',
                        itemMargin: {
                            horizontal: 5,
                            vertical: 1,
                        },
                    },
                },
            }, {
                breakpoint: 1388,
                options: {
                    chart: {
                        height: 330,
                    },
                    legend: {
                        position: 'bottom',
                    },
                },
            }, {
                breakpoint: 1275,
                options: {
                    chart: {
                        height: 300,
                    },
                    legend: {
                        position: 'bottom',
                    },
                },
            }, {
                breakpoint: 1158,
                options: {
                    chart: {
                        height: 280,
                    },
                    legend: {
                        fontSize: '10px',
                        position: 'bottom',
                        offsetY: 10,
                    },
                },
            }, {
                breakpoint: 598,
                options: {
                    chart: {
                        height: 280,
                    },
                    legend: {
                        fontSize: '12px',
                        position: 'bottom',
                        offsetX: 5,
                        offsetY: -5,
                        markers: {
                            width: 10,
                            height: 10,
                        },
                        itemMargin: {
                            vertical: 1,
                        },
                    },
                },
            }],
        };

        var chart = new ApexCharts(el, options);
        chart.render();
        window.__adminVisitorsChartInstance = chart;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderVisitorsChart);
    } else {
        renderVisitorsChart();
    }
})();
