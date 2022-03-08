<div class="row mt-2">
	<div class="col-md-12">
		<div class="form-group col-md-3 col-sm-12">
			<select class="form-control form-control-sm" style="width:150px" name="" id="selectpullcut">
				<option value="chartpullcut">ปกติ</option>
				<option value="chartpullcut_week">เทียบ 7 วันล่าสุด</option>
			</select>
		</div>

	</div>
</div>


<div class="chartrank pullcut w-100">
	<canvas id="chartpullcut"></canvas>
</div>

<script>
	var chartpullcut = document.getElementById('chartpullcut').getContext('2d');
	var mychart;

	$(document).on('click', 'a[href="#pullcut"]', function(event) {
		event.stopPropagation();
		event.preventDefault;

		if (!$('.pullcut .chartjs-size-monitor').length) {

			displayChart_pullcut();
		}
	})

	$(document).on('change', 'select#selectpullcut', function(event) {
		event.stopPropagation();
		event.preventDefault;

		mychart.destroy(); //	for destroy graph before
		displayChart_pullcut();
	})

	function creatGraph_pullcut(array, ctrName) {
		let labels = "";
		let datavalue = ""
		// const colours = datavalue.map((value) => value < 0 ? 'rgba(99, 203, 207, 1)' : 'rgba(54, 162, 235, 1)');
		let data = {};
		let config = "";

		switch (ctrName) {

			//	normal
			case 'chartpullcut':
				labels = array.label;
				datavalue = array.data;

				data = {
					labels: ['ข้อมูลรับเข้า-ขาย'],
					datasets: [{
						label: 'ข้อมูลรับเข้า (' + labels[0] + ')',
						data: [datavalue[0]],
						backgroundColor: [
							'rgba(255, 205, 86, 0.2)'

						],
						borderColor: [
							'rgb(255, 205, 86)'
						],
						borderWidth: 1
					}, {
						label: 'ข้อมูลขาย (' + labels[1] + ')',
						data: [datavalue[1]],
						backgroundColor: [
							'rgba(75, 192, 192, 0.2)'
						],
						borderColor: [
							'rgb(75, 192, 192)'
						],
						borderWidth: 1
					}]
				}

				config = {
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

				break;

				//	week	
			case 'chartpullcut_week':
				labels = array.label;

				let data_pull = array.data_pull;
				let data_cut = array.data_cut;

				// console.log(datavalue);
				data = {
					labels: labels,
					datasets: [{
						label: 'ข้อมูลรับเข้า',
						data: data_pull,
						fill: false,
						borderColor: 'rgba(255, 205, 86, 1)',
						tension: 0.1
					}, {
						label: 'ข้อมูลขาย',
						data: data_cut,
						fill: false,
						borderColor: 'rgba(75, 192, 192, 1)',
						tension: 0.1
					}]
				}

				config = {
					type: 'line',
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

				break;
		}

		mychart = new Chart(chartpullcut, config);
	}

	function displayChart_pullcut() {

		let queryString = decodeURIComponent(window.location.search);
		let params = new URLSearchParams(queryString);
		let getDate = params.get("date");

		let selectmain = $('#selectpullcut');
		// console.log(selectmain.val());
		let ctrName = selectmain.val();

		$.ajax({
				method: "get",
				beforeSend: function() {
					// eChartrank.html('Loading...');
				},
				data: {
					date: getDate
				},
				url: ctrName,
				success: function(result) {
					if (result) {
						let obj = jQuery.parseJSON(result);
						// console.log(result);
						creatGraph_pullcut(obj, ctrName);
					} else {
						$('#chartpullcut').hide();
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