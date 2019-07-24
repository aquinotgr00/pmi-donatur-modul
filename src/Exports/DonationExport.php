<?php

namespace BajakLautMalaka\PmiDonatur\Exports;

use BajakLautMalaka\PmiDonatur\Donation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

if ( interface_exists('\Maatwebsite\Excel\Concerns\FromView') ) {
	
	class DonationExport implements FromView
	{
		
		

    	public function __construct(array $ids)
    	{
        	$this->ids = $ids;
    	}

    	public function view(): View
    	{
    		$data['donations'] 	=  Donation::all();
    		if (is_array($this->ids) && $this->ids) {
    			$donations 			= new Donation;
    			$data['donations'] 	= $donations->whereIn('id',$this->ids)->get(); 
    		}
			return view('donation::table-donations', $data);
    	}
	}

}
