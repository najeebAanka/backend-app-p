<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Banners</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@10..48,500&display=swap');

        body {
            font-family: 'Bricolage Grotesque', sans-serif;
        }
    </style>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: {{ $auction->live_bids_main_bg ?? '#ffffff' }};
        }

        section {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        section .air {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background-size: 1000px 100px
        }

        .center-container {
            position: absolute;
            width: 500px;
            top: 2rem;
            z-index: 9999;
            color: #fff;
            background-color: #fff;
            max-height: 78vh;
            overflow-y: auto;
            right: 1rem
        }

        .bgt {
            width: 100%;
        }

        .bgt tr td {
            text-align: center;
            color: #000;
            padding: 0.5rem;
            background-color: {{ $auction->live_bids_bg ?? '#f7aaaa' }};
        }

        img.flag-img {
            width: 51px;
        }

        .bid-status--1 td {
            text-decoration: line-through;
            background-color: #ffe5ea !important;
        }

        /* width */
        ::-webkit-scrollbar {
            width: 0px;
            transition: all 0.5s ease-in-out;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #e9e9e9;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #fff;
            border-radius: 5px;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #ccc;
        }

        .biko {
            border-spacing: 0;
        }

        .biko tr td,
        .biko tr th {
            background-color: #fff;
            padding: 0.5rem;
        }

        #logo {
            position: absolute;
            width: 6rem;
            top: 0.4rem;
            overflow: hidden;
            right: 0;
        }

        #logo img {
            width: 100%;
        }

        #logo:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100px;
            width: 70px;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
            transform: skewX(-30deg);
            animation-name: slide;
            animation-duration: 7s;
            animation-timing-function: ease-in-out;
            animation-delay: .3s;
            animation-iteration-count: infinite;
            animation-direction: alternate;
            background: linear-gradient(to right,
                    rgba(255, 255, 255, 0.13) 0%,
                    rgba(255, 255, 255, 0.13) 77%,
                    rgba(255, 255, 255, 0.5) 92%,
                    rgba(255, 255, 255, 0.0) 100%);
        }
    </style>
</head>
​
<body style="background-color:'{{ $auction->live_bids_main_bg ?? '#ffffff' }}'">
    <div style="height: 90vh;">
        <div style="display: flex;justify-content:flex-end;margin-bottom:20px;">
            <div class="center-container" id='mydiv'>
                <table class="bgt">
                    <tr>
                        <td colspan="100%" style="position: relative">
                            <div id="logo"> <img src="{{ url('dist/assets/img/test-logo-hi-res.png') }}" />
                            </div>
                            <h3 style="margin: 1rem;color: #8a5232">
                                <small id="results-target"></small>
                                <br /><span id="results-horse-name"></span>

                                <span id="is-pregnant" style="display: none">
                                    , In Foal , From <span id="pregnant-from"></span>
                                    , Due date <span id="pregnant-due-date"></span>
                                </span>
                            </h3>
                            <p id="results-owner-name"></p>
                            <p id="results-gender-dob"></p>

                        </td>
                    </tr>
                    <tr style="font-weight: bold">
                        <td id="bids-count">-/-</td>
                        <td id='max-amount'>-/-</td>
                        <td id="results-status"></td>
                    </tr>
                    <tr>
                        <td colspan="100%">
                            <table style="width: 100%;" class="biko">
                                <tbody id="bidders-table"></tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div
            style="position: fixed; bottom:20px; left:50%; margin-left:-600px; overflow: hidden; z-index: 11; width: 1200px; height: 200px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;border-right:0;border-left:0;">
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
                        <div style="width: 800px; height: 100px; display: flex; align-items: center; justify-content: left; padding-inline: 1rem;">
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
        </div>
    </div>

    <script>
        window.laravel_echo_port = '6001';
    </script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://test.com:6001/socket.io/socket.io.js"></script>
    <script src="{{ url('/js/laravel-echo-setup.js') }}" type="text/javascript"></script>
    <script>
        function updateLotData(lot) {
            document.getElementById('lot-id').innerHTML = lot.order_sn + 1;
            document.getElementById('name').innerHTML = lot.horse.name_en;
            document.getElementById('parents').innerHTML = data.data.sire + ' <span style="font-size: 20px;opacity: .6;">X</span> ' + data.data.dam;
            document.getElementById('gender').innerHTML = lot.horse.gender;
            document.getElementById('dob').innerHTML = lot.horse.dob;
            document.getElementById('owner').innerHTML = lot.horse.owner_name;
            document.getElementById('max-bid').innerHTML = lot.max_bid;

            //update the data in the bids banner
            if(lot.target_type == 'breeding-right'){
                if(lot.horse.gender == 'mare'){
                    document.getElementById('results-target').innerHTML = 'Embryo of : ';
                }else{
                    document.getElementById('results-target').innerHTML = 'Breeding right from : ';
                }
            }
            document.getElementById('results-horse-name').innerHTML = lot.horse.name_en;
            if(lot.is_pregnant){
                document.getElementById('is-pregnant').style.display = 'inline';
            }else{
                document.getElementById('is-pregnant').style.display = 'none';
            }
            document.getElementById('pregnant-from').innerHTML = lot.pregnant_from;
            document.getElementById('pregnant-due-date').innerHTML = lot.pregnant_due_date;
            document.getElementById('results-owner-name').innerHTML = lot.horse.owner_name;
            document.getElementById('results-gender-dob').innerHTML = lot.horse.gender.toUpperCase() + ' | ' + lot.horse.dob;
            if(lot.status_string == 'started'){
                document.getElementById('results-status').innerHTML = 'Live now';
            }else{
                document.getElementById('results-status').innerHTML = lot.status_string;
            }
        }
        window.Echo.channel('parent-auction-{{ $auction->id }}').listen('.AuctionLotsUpdated', (data) => {
            for (var i = 0; i < data.payload.lots.length; i++) {
                if (data.payload.lots[i].status_string == "started") {
                    updateLotData(data.payload.lots[i]);
                }
            }
        });
    </script>

    <script>
        function renderBids(data) {
            console.log(data);
            let str = "";
            let max_set = false;
            if (data.length > 0) {
                $('#bids-count').html(data.length + " Bids")
                for (var i = 0; i < data.length; i++) {
                    if (!max_set && data[i].status == 1) {
                        $('#max-amount').html(data[i].amount +
                            " {{ App\Models\Auction::find($lot->auction_id)->currency }}");
                        max_set = true;
                    }
                    str += "<tr class='bid-status-" + data[i].status + "'><td><img class='flag-img' src='" + data[i]
                        .country_flag +
                        "' /></td>\n\<td>" + data[i].time + "</td><td>" + data[i].source + "</td><td>" + data[i]
                        .amount + " {{ App\Models\Auction::find($lot->auction_id)->currency }}" + "</td>";
                    str += "</tr>";
                }
            } else {
                str =
                    "<tr><td style='text-align: center;font-weight: bold;color: gray;font-size : 1.7rem' colspan='100%'><p>No bidders so far</p></td></tr>";
            }
            $('#bidders-table').html(str);
        }
    </script>

    <script>
        @php
            $bids = \App\Models\Bid::where('lot_id', $lot->id)
                ->orderBy('id', 'desc')
                ->get();
            $list = [];
            foreach ($bids as $b) {
                $s = $b->buildObject();
                $list[] = $s;
            }
        @endphp
        let initalData = JSON.parse('{{ json_encode($list) }}');
        renderBids(initalData);

        var i = 0;
        window.Echo.channel('auction-rooms-{{ $lot->id }}').listen('.NewBidPlaced', (data) => {
            i++;
            renderBids(data.payload.bids_list);
            console.log(data.payload)
        }).listen('.LotTimeExtended', (data) => {
            console.log("data lot extedned")
            console.log(data)
        });
    </script>

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

                //update the data in the bids banner
                if(lot.target_type == 'breeding-right'){
                    if(lot.horse.gender == 'mare'){
                        document.getElementById('results-target').innerHTML = 'Embryo of : ';
                    }else{
                        document.getElementById('results-target').innerHTML = 'Breeding right from : ';
                    }
                }
                document.getElementById('results-horse-name').innerHTML = lot.horse.name_en;
                if(lot.is_pregnant){
                    document.getElementById('is-pregnant').style.display = 'inline';
                }else{
                    document.getElementById('is-pregnant').style.display = 'none';
                }
                document.getElementById('pregnant-from').innerHTML = lot.pregnant_from;
                document.getElementById('pregnant-due-date').innerHTML = lot.pregnant_due_date;
                document.getElementById('results-owner-name').innerHTML = lot.horse.owner_name;
                document.getElementById('results-gender-dob').innerHTML = lot.horse.gender.toUpperCase() + ' | ' + lot.horse.dob;
                if(lot.status_string == 'started'){
                    document.getElementById('results-status').innerHTML = 'Live now';
                }else{
                    document.getElementById('results-status').innerHTML = lot.status_string;
                }
			});
		});
    </script>
</body>
​

</html>
