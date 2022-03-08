<canvas id="myChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  var ctx = document.getElementById('myChart').getContext('2d');
  const labels = ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange', 'Gray'];
  const data = {
    labels: labels,
    datasets: [{
      label: 'My First Dataset',
      data: [65, 59, 80, 81, 56, 55, 40],
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(255, 205, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(201, 203, 207, 0.2)'
      ],
      borderColor: [
        'rgb(255, 99, 132)',
        'rgb(255, 159, 64)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(54, 162, 235)',
        'rgb(153, 102, 255)',
        'rgb(201, 203, 207)'
      ],
      borderWidth: 1
    }]
  };

  //  original 1 
  const config = {
    type: 'bar',
    data: data,
    options: {
    
      scales: {
        y: {
          beginAtZero: true
        }
      },
  	
  	plugins: {
        tooltip: {
          // Tooltip will only receive click events
          events: ['click']
        }
      }
  	
    }
  };

  /* const config = {
    type: 'bar',
    data: {
      datasets: [{
        data: [{
          id: 'Sales',
          nested: {
            value: 1500
          }
        }, {
          id: 'Purchases',
          nested: {
            value: 500
          }
        }]
      }]
    },
    options: {
      parsing: {
        xAxisKey: 'id',
        yAxisKey: 'nested.value'
      }
    }
  }; */


  var myChart = new Chart(ctx, config);
</script>