$(function () {

    /*
     * -----------------------------------------------------------------------
     * All Item Statistics
     * -----------------------------------------------------------------------
     * -----------------------------------------------------------------------
     * Total revenue chart
     * -----------------------------------------------------------------------
     */

    var options_Total_Revenue = {
        series: [
            {
                name: '2020',
                data: [
                    800000,
                    1200000,
                    1400000,
                    1300000,
                    1200000,
                    1400000,
                    1300000,
                    1300000,
                    1200000
                ]
            },
            {
                name: '2016',
                data: [
                    200000,
                    400000,
                    500000,
                    300000,
                    400000,
                    500000,
                    300000,
                    300000,
                    400000
                ]
            },
            {
                name: '2015',
                data: [
                    100000,
                    200000,
                    400000,
                    600000,
                    200000,
                    400000,
                    600000,
                    600000,
                    200000
                ]
            }
        ],
        chart: {
            fontFamily: 'Poppins,sans-serif',
            type: 'bar',
            height: 360,
            stacked: true,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: true
            }
        },
        grid: {
            borderColor: 'rgba(0,0,0,0.1)',
            strokeDashArray: 3
        },
        colors: [
            '#0f8edd',
            '#11a0f8',
            '#51bdff'
        ],
        responsive: [
            {
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }
        ],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '45%'
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            categories: [
                'Jan',
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sept"
            ],
            labels: {
                style: {
                    colors: '#a1aab2'
                }
            }
        },
        yaxis: {
            tickAmount: 10,
            labels: {
                style: {
                    colors: '#a1aab2'
                }
            }
        },
        tooltip: {
            theme: 'dark'
        },
        legend: {
            show: false
        },
        fill: {
            opacity: 1
        }
    }

    var chart_column_stacked = new ApexCharts(
        document.querySelector('#item-total-revenue'),
        options_Total_Revenue
    )

    chart_column_stacked.render()

    /*
     * -----------------------------------------------------------------------
     * Item Details Statistics
     * -----------------------------------------------------------------------
     */

    var Revenue_Statistics = {
        series: [
            {
                name: 'Total Revenue ',
                data: [
                    1,
                    2,
                    3,
                    0,
                    13,
                    1,
                    14,
                    22
                ]
            },
            {
                name: 'Total Invoice ',
                data: [
                    2,
                    4,
                    0,
                    4,
                    18,
                    4,
                    10,
                    29
                ]
            },
            {
                name: 'Total Quantity ',
                data: [
                    3,
                    6,
                    2,
                    8,
                    0,
                    2,
                    15,
                    35
                ]
            }
        ],
        chart: {
            fontFamily: 'Rubik,sans-serif',
            height: 350,
            type: 'area',
            toolbar: {
                show: false
            }
        },
        fill: {
            type: 'solid',
            opacity: 0.2,
            colors: [
                '#009efb',
                "#39c449"
            ]
        },
        grid: {
            show: true,
            borderColor: 'rgba(0,0,0,0.1)',
            strokeDashArray: 3,
            xaxis: {
                lines: {
                    show: true
                }
            }
        },
        colors: [
            '#39c449',
            "#f62d51",
            "#7460ee"
        ],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 1,
            colors: [
                '#39c449',
                "#f62d51",
                "#7460ee"
            ]
        },
        markers: {
            size: 3,
            colors: [
                '#39c449',
                "#f62d51",
                "#7460ee"
            ],
            strokeColors: 'transparent'
        },
        xaxis: {
            axisBorder: {
                show: true
            },
            axisTicks: {
                show: true
            },
            categories: [
                '0',
                '4',
                '8',
                '12',
                '16',
                '20',
                '24',
                '30'
            ],
            labels: {
                style: {
                    colors: '#39c449'
                }
            }
        },
        yaxis: {
            tickAmount: 9,
            labels: {
                style: {
                    colors: '#a1aab2'
                }
            }
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
            theme: 'dark'
        },
        legend: {
            show: false
        }
    };

    var chart_area_spline = new ApexCharts(
        document.querySelector('#item-details-statistics'),
        Revenue_Statistics
    )

    chart_area_spline.render()

    /*
     * -----------------------------------------------------------------------
     * Sales Prediction
     * -----------------------------------------------------------------------
     */
})
