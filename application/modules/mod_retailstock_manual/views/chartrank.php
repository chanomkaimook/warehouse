<div class="chartrank main w-100">
	<canvas id="myChart"></canvas>
</div>

<script>
	var ctx = document.getElementById('myChart').getContext('2d');

	function getRandomColor() {
		var letters = '0123456789ABCDEF'.split('');
		var color = '#';
		for (var i = 0; i < 6; i++) {
			color += letters[Math.floor(Math.random() * 16)];
		}
		return color;
	}

	$(document).on('click', 'a[href="#home"]', function(event) {
		event.stopPropagation();
		// chartjs-size-monitor

		if (!$('.main .chartjs-size-monitor').length) {

			displayChart();
		}
	})

	function getRandomColorEachEmployee(count) {
		var data = [];
		for (var i = 0; i < count; i++) {
			data.push(getRandomColor());
		}
		return data;
	}

	function creatGraph(array) {
		const labels = array.label;
		const datavalue = array.data;
		const colours = datavalue.map((value) => value < 0 ? 'red' : 'rgba(201, 203, 207, 1)');
		const data = {
			labels: labels,
			datasets: [{
				label: 'จำนวนคงเหลือ',
				data: datavalue,
				backgroundColor: colours,
				/* backgroundColor: [
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
				], */
				borderWidth: 1
			}]
		};

		const config = {
			type: 'bar',
			data: data,
			options: {
				responsive: true,
				maintainAspectRatio: false,
				events: ['click'],
				scales: {
					yAxes: [{
						display: true,
						ticks: {
							beginAtZero: true
						}
					}]
				}
			}
		};

		new Chart(ctx, config);
	}

	function displayChart() {
		const eChartrank = $(".chartrank");
		let queryString = decodeURIComponent(window.location.search);
		let params = new URLSearchParams(queryString);
		let getDate = params.get("date");

		$.ajax({
				method: "get",
				beforeSend: function() {
					// eChartrank.html('Loading...');
				},
				data: {
					date: getDate
				},
				url: "chartrank",
				success: function(result) {
					// eChartrank.html(result);
					if (result) {
						let obj = jQuery.parseJSON(result);

						creatGraph(obj);
					} else {
						$('#myChart').hide();
					}

				},
				error: function(error) {
					alert("error occured: " + error.status + " " + error.statusText);
				}
			})
			.fail(function(xhr, status, error) {
				// error handling
				alert('error');
				// window.location.reload();
			});
	}
</script>