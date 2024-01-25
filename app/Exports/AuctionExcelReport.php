<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;


class AuctionExcelReport implements  WithHeadings ,ShouldAutoSize ,WithStyles , \Maatwebsite\Excel\Concerns\WithProperties ,
 \Maatwebsite\Excel\Concerns\FromCollection

{
  
private $auction_id;

public function __construct($auction_id) {
    $this->auction_id = $auction_id;
}


    public function headings(): array {
        
      $arr =  ['Bid ID', 'User name','User phone','Last IP','Lot' ,'Type' ,'Bidding Amount','Current amount' ,'Date and time'   ,'Status' ];   

      return $arr;
    }

    public function styles(Worksheet $sheet) {
     
                
        return [
            // Style the first row as bold text.
            1    =>[
    'font' => [
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'top' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
        'rotation' => 90,
        'startColor' => [
            'argb' => 'FFA0A0A0',
        ],
        'endColor' => [
            'argb' => 'FFFFFFFF',
        ],
    ],
],

        
         // Styling an entire column.
            'B'  => ['font' => ['bold' => true] ,  'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ]],
            
            
                     'C'  => [  'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ]],
          
          
          
          
          
                            'D'  => [  'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ]],
          
                            'E'  => [ 'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ]],
          
                            'F'  =>  [ 'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ]],
          

   
            
            
        ];
        
    }

    public function properties(): array {
          return [
            'creator'        => 'test',
            'lastModifiedBy' => 'test',
            'title'          => 'test auction log template',
            'description'    => 'Bidding log',
            'subject'        => 'test',
            'keywords'       => 'test,export,spreadsheet',
            'category'       => 'test',
            'manager'        => 'test',
            'company'        => 'test',
        ]; 
    }

    public function collection(): \Illuminate\Support\Collection {
        $items = DB::select('SELECT bids.id ,users.name ,users.phone ,users.last_login_ip ,auction_horse_regs.order_sn , 
            auction_horse_regs.lot_type ,bids.inc_amount
            ,bids.curr_amount,bids.created_at ,bids.status from bids join users on users.id = bids.user_id
            join auction_horse_regs on auction_horse_regs.id = bids.lot_id WHERE bids.auction_id = ?' ,[ $this->auction_id]);
        $currency = \App\Models\Auction::find($this->auction_id)->currency;
        foreach ($items as $i){
            $i->order_sn =  $i->order_sn  + 1;
            $i->status =  $i->status == 1  ? "Accepted" : "Cancelled by admin";
             $i->phone = "(".$i->phone.")";
             $i->inc_amount=  $i->inc_amount." ".$currency;
             $i->curr_amount=  $i->curr_amount." ".$currency;
        }

        return collect($items);
    }

}