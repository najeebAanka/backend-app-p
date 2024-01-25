<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- Favicons -->
    <link href="<?= url('') ?>/dashboard/assets/img/favicon.png" rel="icon">
    <link href="<?= url('') ?>/dashboard/assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <?php $lot = \App\Models\AuctionHorseReg::find(Route::input('id')); ?>
    <?php $horse = App\Models\Horse::find($lot->horse_id); ?>
    <title>{{ $horse->name_en }} Lot {{ $lot->order_sn + 1 }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: tahoma;
        }

        section {
            position: relative;
            width: 100%;
            height: 100vh;
            background: #8a5232;

            overflow: hidden;
        }

        section .air {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url({{ url('dist/assets/img/wave.png') }});
            background-size: 1000px 100px
        }

        section .air.air1 {
            animation: wave 30s linear infinite;
            z-index: 1000;
            opacity: 1;
            animation-delay: 0s;
            bottom: 0;
        }

        section .air.air2 {
            animation: wave2 15s linear infinite;
            z-index: 999;
            opacity: 0.5;
            animation-delay: -5s;
            bottom: 10px;
        }

        section .air.air3 {
            animation: wave 30s linear infinite;
            z-index: 998;
            opacity: 0.2;
            animation-delay: -2s;
            bottom: 15px;
        }

        section .air.air4 {
            animation: wave2 5s linear infinite;
            z-index: 997;
            opacity: 0.7;
            animation-delay: -5s;
            bottom: 20px;
        }

        @keyframes wave {
            0% {
                background-position-x: 0px;
            }

            100% {
                background-position-x: 1000px;
            }
        }

        @keyframes wave2 {
            0% {
                background-position-x: 0px;
            }

            100% {
                background-position-x: -1000px;
            }
        }

        .center-container {
            position: absolute;
            width: 100%;
            max-width: 800px;
            top: 2rem;
            left: 0;
            z-index: 9999;
            color: #fff;
            right: 0;
            margin-left: auto;
            margin-right: auto;
            background-color: #fff;
            padding: 1rem;
            border-radius: 1rem;
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
            background-color: #f1f1f1;
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

        @keyframes slide {
            0% {
                left: -100;
                top: 0;
            }

            50% {
                left: 120px;
                top: 0px;
            }

            100% {
                left: 290px;
                top: 0;
            }
        }
    </style>
</head>

<body>

    <iframe id="video" src="//www.youtube.com/embed/xhX5m1j8oTc?rel=0&autoplay=1" frameborder="0"
        style="width: 100%
            ;height: 100vh;"></iframe>
    <!--  <section>
  <div class='air air1'></div>
  <div class='air air2'></div>
  <div class='air air3'></div>
  <div class='air air4'></div>
</section>  -->


    <div class="center-container">




        <table class="bgt">
            <tr>
                <td colspan="100%" style="position: relative">
                    <div id="logo"> <img src="{{ url('dist/assets/img/test-logo-hi-res.png') }}" /></div>

                    <h2 style="color: #676767">00:00</h2>
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
                        <tr>

                            <th colspan="2">Bidder</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Source</th>
                            <th>Amount</th>


                        </tr>
                        <tbody id="bidders-table">


                        </tbody>
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
                            "' /></td><td>\n\
                ";
                        str += (data[i].source == 'Online' ? "" + data[i].name + "" : data[i].name);
                        str += "</td>\n\
                      <td>" + data[i].date + "</td><td>" + data[i].time + "</td><td>" + data[i].source + "</td><td>" + data[i]
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
