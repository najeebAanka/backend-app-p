<!DOCTYPE html>

<html lang="en" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
    <head>
        <title>{{$data->name}} Catalog </title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/><!--[if mso]><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch><o:AllowPNG/></o:OfficeDocumentSettings></xml><![endif]-->
        <style>
            
            
            body{
            
    font-family: system-ui;
    background-color: #f2f2f2;
}
        </style>
    </head>
    <body >

                  
                  <p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;"><img src="https://secure.test.com/dist/assets/img/logo.png" alt="" /></p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;"><span style="font-size: x-large;"><strong>Welcome to test auction</strong></span></p>
<p style="text-align: center;"><span style="font-size: x-large;"><strong>Auction : {{$data->name}}</strong></span></p>
<p style="text-align: center;"><span style="font-size: x-large;"><strong>Starting date : {{$data->start_date}}</strong></span></p>
<p style="text-align: center;"><span style="font-size: medium;"><strong>Number of lots : {{count($data->lots)}}</strong></span></p>
<p style="text-align: center;"><span style="font-size: medium;"><strong>Auction type : mixed</strong></span></p>

                   
<div>
    <div >
        <?php foreach ($data->lots as $lot){ ?> 
        <div style="background-color: #ffffff;margin: 10px;padding: 10px;border-radius: 10px;" >
            <table class="petra" style="width: 100%;text-align: left;">
                <td style="width: 30%"><img style="width: 200px;border-radius: 10px;" src="{{$lot->lot_poster}}" /></td>
            <td style="width: 50%">
                <b>{{$lot->horse->name_en}}</b>
                <p>{{$lot->horse->gender}}</p>
                <p><?= Carbon\Carbon::parse($lot->horse->dob)->format('d-m-Y')?></p>
                <p>Seller  : <?= App\Models\User::find($lot->horse->seller_id)->name?></p>
                    <p>Sire : {{$lot->horse->sire_name_en}}</p>
                <p>Dam : {{$lot->horse->dam_en}}</p>
            </td>
        
            <td>Lot type : {{$lot->lot_type}}
            <?php if($lot->lot_type == 'online'){ ?>
                <p>Starts at : {{$lot->lot_start_date}}</p>
                <p>Ends at : {{$lot->lot_end_date}}</p>
            <?php } ?>
            
            </td>
       
     
            </table>
        </div> 
        <div class="page-break"></div>
        <hr />
          <?php } ?>
    </div>
</div>
    <div style="background-color: #ffffff;margin: 10px;padding: 10px;border-radius: 10px;" >
    <h2>Auction terms and conditions</h2>
    <hr />
    {!!$data->terms!!}
    
    
</div>

                  
     <div style="background-color: #ffffff;margin: 10px;padding: 10px;border-radius: 10px;" >
    <h2>About test</h2>
    <hr />
    <?= App\Models\StaticContent::where('static_key' ,'privacy-policy')->first()->static_content ?>
    
    
</div>         
               
                        
              
    </body>
</html>