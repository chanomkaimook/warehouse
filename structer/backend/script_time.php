<script>
	function countTimingFull(dateStart){
		// var dateSet = Math.floor(new Date('2020-08-28 12:50:20').getTime());
		var dateSet = Math.floor(new Date(dateStart).getTime());
		var dateNow = Math.floor(new Date().getTime());

		var timestamp =  ((dateNow - dateSet))/1000;
		var setHours = Math.floor((timestamp / 60) /60);
		var setMinutes = Math.floor(timestamp / 60) % 60;
		var setSeconds = Math.floor(timestamp % 60);
		setMinutes = ( setMinutes < 10 ? "0" : "" ) + setMinutes;
		setSeconds = ( setSeconds < 10 ? "0" : "" ) + setSeconds;
		
		result = setHours+":"+setMinutes+":"+setSeconds;
		return result;
	}
	function countTimingHM(dateStart){
		// var dateSet = Math.floor(new Date('2020-08-28 12:50:20').getTime());
		var dateSet = Math.floor(new Date(dateStart).getTime());
		var dateNow = Math.floor(new Date().getTime());

		var timestamp =  ((dateNow - dateSet))/1000;
		var setHours = Math.floor((timestamp / 60) /60);
		var setMinutes = Math.floor(timestamp / 60) % 60;
		var setSeconds = Math.floor(timestamp % 60);
		setMinutes = ( setMinutes < 10 ? "0" : "" ) + setMinutes;
		setSeconds = ( setSeconds < 10 ? "0" : "" ) + setSeconds;
		
		result = setHours+":"+setMinutes;
		return result;
	}
</script>