/* global Chart:false */

$(function () {
  'use strict'

  var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }

  var mode = 'index'
  var intersect = true

  

  var $visitorsChart = $('#visitors-chart')
  // eslint-disable-next-line no-unused-vars
  var visitorsChart = new Chart($visitorsChart, {
    data: {
      labels: ['0', '25', '50', '75', '100', '125', '150'],
      datasets: [{
        type: 'bubble',
        data: [100, 120, 170, 167, 180, 177, 160],
        backgroundColor: '#007bff',
        // borderColor: '#007bff',
        // pointBorderColor: '#007bff',
        // pointBackgroundColor: '#007bff',
        fill: true
        // pointHoverBackgroundColor: '#007bff',
        // pointHoverBorderColor    : '#007bff'
      },
      {
        type: 'bubble',
        data: [60, 80, 70, 67, 80, 77, 100],
        backgroundColor: '#0F0FFF',
        // borderColor: '#007bff',
        // pointBorderColor: '#007bff',
        // pointBackgroundColor: '#007bff',
        fill: true
        // pointHoverBackgroundColor: '#ced4da',
        // pointHoverBorderColor    : '#ced4da'
      },{
        type: 'bubble',
        data: [100, 67, 80, 77, 67, 80, 77],
        backgroundColor: '#6F11F7',
        // borderColor: '#007bff',
        // pointBorderColor: '#007bff',
        // pointBackgroundColor: '#007bff',
        fill: true
        // pointHoverBackgroundColor: '#ced4da',
        // pointHoverBorderColor    : '#ced4da'
      }]
    },
    options: {
      maintainAspectRatio: true,
      tooltips: {
        mode: mode,
        intersect: intersect
      },
      hover: {
        mode: mode,
        intersect: intersect
      },
      legend: {
        display: false
      },
      scales: {
        yAxes: [{
          gridLines: {
            display: true,
            lineWidth: '4px',
            color: 'rgba(0, 0, 0, .2)',
            zeroLineColor: '#00000'
          },
          ticks: $.extend({
            beginAtZero: true,
            suggestedMax: 140
          }, ticksStyle)
        }],
        xAxes: [{
          // display: true,
          gridLines: {
            display: true,
            lineWidth: '4px',
            color: 'rgba(0, 0, .2, .2)',
            zeroLineColor: '#00000'
          },
          ticks: ticksStyle,
        }]
      }
    }
  })
})

// lgtm [js/unused-local-variable]
