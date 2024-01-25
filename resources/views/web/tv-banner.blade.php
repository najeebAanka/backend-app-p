	<!DOCTYPE html>
	<html>
	​
	<head>
		<meta charset="UTF-8">
		<title></title>
		<style>
			@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@10..48,500&display=swap');
			body{
				font-family: 'Bricolage Grotesque', sans-serif;
			}
		</style>
	</head>
	​
	<body style="display: flex;  align-items: flex-end; justify-content: center; min-height: 100vh; flex-wrap: wrap; background-color:{{ $auction->live_bids_main_bg ?? '#ffffff' }}">
		<!-- Start Main Section -->
		<section style="position: relative; overflow: hidden; z-index: 11; width: 1200px; height: 200px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
			<!--Start  background Image -->
			<img class="" style="width: 100%; position: absolute; z-index: -1; opacity: 1;top: -30%;filter: blur(2px) brightness(1.1) opacity(0.6);" src="{{ $auction->tv_banner_bg }}" alt="">
			<!-- End background Image -->
		
			<!--Start Number -->
			<div style="width: 200px; height: 200px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
				<span style="font-size: 5rem;" id="lot-id">0</span>
			</div>
			<!--End Number -->
			<!-- Start Main Data -->
			<div style="width: 700px; height: 200px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; flex-wrap: wrap;">
				<!--Start Horse Name & price -->
				<div style="width: 700px; height: 100px; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
					<!--Start Horse Name -->
					<div style="width: 800px; height: 100px; border-right: 1px solid #ddd; display: flex; align-items: center; justify-content: left; padding-inline: 1rem;">
						<span style="font-size: 3rem;" id="name">--</span>
					</div>
					<!--End Horse Name --> 
				</div>
				<!--End Horse Name & price -->
				
				<!-- Start Hourse Details -->
				<div style="width: 1000px; height: 100; padding: 0.5rem 1rem;">
					<!--Start mother Name && Father-->
						<p style="width: 100%;font-size: 1.8rem; margin: 0;" id="parents">--  x  --</p>  <!-- Names -->
						<p style="width: 100%;font-size: 1.3rem; margin: 0; display: flex; align-items: center; justify-content: left;flex-wrap: wrap; gap: 2rem; margin-top: 1rem;">
							<span id="gender" style="text-transform: capitalize">--</span> <!-- Gender -->
							<span id="dob">--</span> <!-- date of birth -->
							<span id="owner">--</span> <!-- Owner -->
						</p>
					<!--End mother Name && Father -->
				</div>
				<!-- End Hourse Details -->
			</div>
			<!-- End Main Data -->
			<!--Start Number -->
			<div style="width: 300px; height: 200px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
				<span style="font-size: 3rem;" id="max-bid">00 AED</span>
			</div>
			<!--End Number -->
		</section>
		<!-- End Main Section -->

		<script>
			window.laravel_echo_port = '6001';
		</script>
		<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
		<script src="https://test.com:6001/socket.io/socket.io.js"></script>
		<script src="{{ url('/js/laravel-echo-setup.js') }}" type="text/javascript"></script>
		<script>
			$(function() {
				$.get("/api/v1/get-active-lot/{{ $auction->id }}", function(data, status){
					console.log(data);
					var lot = data.data.lot;
					document.getElementById('lot-id').innerHTML = lot.order_sn + 1;
					document.getElementById('name').innerHTML = lot.horse.name_en;
					document.getElementById('parents').innerHTML = data.data.sire + ' <span style="font-size: 20px;opacity: .6;">X</span> ' + data.data.dam;
					document.getElementById('gender').innerHTML = lot.horse.gender;
					document.getElementById('dob').innerHTML = lot.horse.dob;
					document.getElementById('owner').innerHTML = lot.horse.owner_name;
					document.getElementById('max-bid').innerHTML = data.data.max_bid;
				});
			});

			function updateLotData(lot){
				document.getElementById('lot-id').innerHTML = lot.order_sn + 1;
				document.getElementById('name').innerHTML = lot.horse.name_en;
				document.getElementById('parents').innerHTML = data.data.sire + ' <span style="font-size: 20px;opacity: .6;">X</span> ' + data.data.dam;
				document.getElementById('gender').innerHTML = lot.horse.gender;
				document.getElementById('dob').innerHTML = lot.horse.dob;
				document.getElementById('owner').innerHTML = lot.horse.owner_name;
				document.getElementById('max-bid').innerHTML = lot.max_bid;
			}
			window.Echo.channel('parent-auction-{{ $auction->id }}').listen('.AuctionLotsUpdated', (data) => {
				console.log(data);
				for(var i = 0; i < data.payload.lots.length; i++){
					if(data.payload.lots[i].status_string == "started"){
						updateLotData(data.payload.lots[i]);
					}
				}
			});
		</script>
	</body>
	​
	</html>