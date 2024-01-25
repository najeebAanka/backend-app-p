<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- Favicons -->
    <link href="<?= url('') ?>/dashboard/assets/img/favicon.png" rel="icon">
    <link href="<?= url('') ?>/dashboard/assets/img/apple-touch-icon.png" rel="apple-touch-icon">
	@php
		$lot = \App\Models\AuctionHorseReg::find(Route::input('id'));
		$auction = \App\Models\Auction::find($lot->auction_id);
		$horse = App\Models\Horse::find($lot->horse_id);
	@endphp
    <title>{{ $horse->name_en }} Lot {{ $lot->order_sn + 1 }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: tahoma;
        }

		body{
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
            width: 100%;
            max-width: 800px;
            top: 2rem;
            z-index: 9999;
            color: #fff;
            background-color: #fff;
            max-height: 78vh;
            overflow-y: auto;
        }

        .bgt {
            width: 100%;
        }

        .bgt tr td {
            text-align: center;
            color: #000;
            padding: 0.5rem;
            background-color: {{ $auction->live_bids_bg ?? '#f7aaaa'}};
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

		.biko{
			border-spacing: 0; 
		}

        .biko tr td,
        .biko tr th {
            background-color: #fff;
            padding: 0.5rem;
        }

        #logo {
            position: absolute;
            width: 7rem;
            top: 1rem;
            overflow: hidden;
            right: 1rem;
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

<body>
    <div class="center-container" id='mydiv'>
        <table class="bgt">
            <tr>
                <td colspan="100%" style="position: relative">
                    <div id="logo"> <img src="{{ url('dist/assets/img/test-logo-hi-res.png') }}" /></div>
                    <h3 style="
                        margin: 1rem;
                        color: #8a5232">

                        <small><?= $lot->target_type == 'breeding-right' ? ($horse->gender == 'mare' ? 'Embryo of : ' : 'Breeding right from : ') : '' ?>
                        </small><br /><?= $horse->name_en ?>

                        <?php if($lot->is_pregnant){ ?>
                        , In Foal , From {{ $lot->pregnant_from }}
                        , Due date {{ $lot->pregnant_due_date }}
                        <?php } ?>

                    </h3>
                    <p>{{ $horse->owner_name }}</p>
                    <p>{{ strtoupper($horse->gender) }} | {{ $horse->dob }}</p>

                </td>
            </tr>
            <tr style="font-weight: bold">
                <td id="bids-count">-/-</td>
                <td id='max-amount'>-/-</td>
                <td>
                    @if ($lot->status_string == 'started')
                        Live now
                    @else
                        {{ $lot->status_string }}
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="100%">
                    <table style="width: 100%;" class="biko">
                        <tbody id="bidders-table"></tbody>
                    </table>
                </td>
            </tr>
        </table>

        <script src="https://code.jquery.com/jquery-3.7.0.min.js"
            integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
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
                                " <?= App\Models\Auction::find($lot->auction_id)->currency ?>");
                            max_set = true;
                        }
                        str += "<tr class='bid-status-" + data[i].status + "'><td><img class='flag-img' src='" + data[i]
                            .country_flag +
                            "' /></td>\n\<td>" + data[i].time + "</td><td>" + data[i].source + "</td><td>" + data[i]
                            .amount + " <?= App\Models\Auction::find($lot->auction_id)->currency ?>" + "</td>";
                        str += "</tr>";
                    }
                } else {
                    str =
                        "<tr><td style='text-align: center;font-weight: bold;color: gray;font-size : 1.7rem' colspan='100%'><hr /><p>No bidders so far</p></td></tr>";
                }
                $('#bidders-table').html(str);
            }
        </script>

        <script>
            <?php
            $bids = \App\Models\Bid::where('lot_id', $lot->id)
                ->orderBy('id', 'desc')
                ->get();
            $list = [];
            foreach ($bids as $b) {
                $s = $b->buildObject();
                $list[] = $s;
            }
            ?>
            let initalData = JSON.parse('<?= json_encode($list) ?>');
            renderBids(initalData);
        </script>
        @if ($lot->status_string == 'started')
            <script>
                window.laravel_echo_port = '6001';
            </script>
            <script src="https://test.com:6001/socket.io/socket.io.js"></script>
            <script src="{{ url('/js/laravel-echo-setup.js') }}" type="text/javascript"></script>
            <script type="text/javascript">
                var i = 0;
                window.Echo.channel('auction-rooms-<?= $lot->id ?>')
                    .listen('.NewBidPlaced', (data) => {
                        i++;
                        renderBids(data.payload.bids_list);
                        console.log(data.payload)

                    })

                    .listen('.LotTimeExtended', (data) => {

                        console.log("data lot extedned")
                        console.log(data)
                    });
            </script>
        @endif
    </div>
</body>

</html>
