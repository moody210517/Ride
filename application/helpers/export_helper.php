<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
if (!function_exists('export_rides_list')){
	function export_rides_list($rideDetails = array(), $ride_actions = ''){
		$limit = 500;
		$ci =& get_instance();
		$ci->load->library(array('excel'));
		#echo "<pre>";print_r($rideDetails->result());die;
		$rideArray = $rideDetails->result_array();
		$no_of_rows = count($rideDetails->result_array());
		$no_of_sheets = floor($no_of_rows/$limit);
		if(($no_of_rows%$limit) > 0){
			$no_of_sheets++;
		}
		$ride_dis_mi = 'Ride Distance ('.$ci->data['d_distance_unit'].')';
        
        $fareAction = array('Finished','Completed'); 
        
		$headers_array = array('Ride ID','Type','Booking Date','Ride Date','Ride Status','Username','User Email','Driver Name','Driver Email','Car Type','Pickup Location','Drop Location','Total Fare (USD)','Coupon Used (USD)','Wallet Used (USD)','Total Fare Paid (USD)','Service Tax (USD)','Tips Amount (USD)','Pay Status',$ride_dis_mi,'Ride Duration (mins)','Paid By','Amount in Site','Amount in Driver','Site Revenue','Driver Revenue');
        
        if(!in_array($ride_actions,$fareAction)){
            $headers_array = array_slice($headers_array,0,12);
        }
       
		if($ride_actions == 'Cancelled' ){
			array_push($headers_array,"Cancelled By","Cancellation Reason");
		}
		
        
		#$ci->excel->getActiveSheet()->fromArray($headers_array);
		
		$next_limit = 0;
		for($i=0; $i<$no_of_sheets; $i++){
			$ci->excel->setActiveSheetIndex($i);
			$current_limit = $next_limit;
			/* Setting Header Name */
			$headerLetter = 'A';
			foreach($headers_array as $key => $val){
			$ci->excel->getActiveSheet()->setCellValue($headerLetter++."1", $val);
			}
			$ci->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setBold(true);
			$ci->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setSize(12);
			
			/* Setting Header Name --- Ends here */
			
			$m = $i+1;
			$next_limit = $m*$limit;
			$row = 2;
			foreach($rideArray as $key => $val){
				if($key >= $current_limit && $key < $next_limit){
					$contentLetter = 'A';
					$ride_id = (string)$rideArray[$key]['ride_id'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $ride_id);
					$contentLetter++;
					
					$type = (string)$rideArray[$key]['type'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $type);
					$contentLetter++;
					
					if(isset($rideArray[$key]['booking_information']['booking_date']) && $rideArray[$key]['booking_information']['booking_date'] != ''){
						$booking_date = date('Y-m-d H:i:s',MongoEPOCH($rideArray[$key]['booking_information']['booking_date']));
					}else{
						$booking_date = 'NA';
					}
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $booking_date);
					$contentLetter++;
					
					if(isset($rideArray[$key]['booking_information']['pickup_date']) && $rideArray[$key]['booking_information']['pickup_date'] != ''){
						$pickup_date = date('Y-m-d H:i:s',MongoEPOCH($rideArray[$key]['booking_information']['pickup_date']));
					}else{
						$pickup_date = 'NA';
					}
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $pickup_date);
					$contentLetter++;
					
					$ride_status = $rideArray[$key]['ride_status'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $ride_status);
					$contentLetter++;
					
					$user_name = $rideArray[$key]['user']['name'];
					if(!ctype_alpha($user_name)){
						$user_name = preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $user_name);;
					}
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $user_name);
					$contentLetter++;
					
					$useremail = $rideArray[$key]['user']['email'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $useremail);
					$contentLetter++;
					
					if(isset($rideArray[$key]['driver']['name']) && $rideArray[$key]['driver']['name'] != ''){
						$driver_name = $rideArray[$key]['driver']['name'];
						if(!ctype_alpha($driver_name)){
							$driver_name = preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $driver_name);;
						}
					}else{
						$driver_name = 'NA';
					}
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $driver_name);
					$contentLetter++;
					
					if(isset($rideArray[$key]['driver']['email']) && $rideArray[$key]['driver']['email'] != ''){
						$driver_email = $rideArray[$key]['driver']['email'];
					}else{
						$driver_email = 'NA';
					}
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $driver_email);
					$contentLetter++;
					
					$service_type = $rideArray[$key]['booking_information']['service_type'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $service_type);
					$contentLetter++;
					
					if(isset($rideArray[$key]['booking_information']['pickup']['location']) && $rideArray[$key]['booking_information']['pickup']['location'] != ''){
						$pickup_location = $rideArray[$key]['booking_information']['pickup']['location'];
					}else{
						$pickup_location = 'NA';
					}
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $pickup_location);
					$contentLetter++;
					
					if(isset($rideArray[$key]['booking_information']['drop']['location']) && $rideArray[$key]['booking_information']['drop']['location'] != ''){
						$drop_location = $rideArray[$key]['booking_information']['drop']['location'];
					}else{
						$drop_location = 'NA';
					}
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $drop_location);
					$contentLetter++;
					
					
                    if(in_array($ride_actions,$fareAction)){
                    
                        if(isset($rideArray[$key]['total']['grand_fare']) && $rideArray[$key]['total']['grand_fare'] != ''){
                            $grand_fare = $rideArray[$key]['total']['grand_fare'];
                        }else{
                            $grand_fare = 0;
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $grand_fare);
                        $contentLetter++;
                        
                        if(isset($rideArray[$key]['total']['coupon_discount']) && $rideArray[$key]['total']['coupon_discount'] != ''){
                            $coupon_discount = $rideArray[$key]['total']['coupon_discount'];
                        }else{
                            $coupon_discount = 0;
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $coupon_discount);
                        $contentLetter++;
                        
                        if(isset($rideArray[$key]['total']['wallet_usage']) && $rideArray[$key]['total']['wallet_usage'] != ''){
                            $wallet_usage = $rideArray[$key]['total']['wallet_usage'];
                        }else{
                            $wallet_usage = 0;
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $wallet_usage);
                        $contentLetter++;
                        
                        
                        if(isset($rideArray[$key]['total']['paid_amount']) && $rideArray[$key]['total']['paid_amount'] != ''){
                            $paid_amount = $rideArray[$key]['total']['paid_amount'];
                        }else{
                            $paid_amount = 0;
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $paid_amount);
                        $contentLetter++;
                        
                        if(isset($rideArray[$key]['total']['service_tax']) && $rideArray[$key]['total']['service_tax'] != ''){
                            $service_tax = $rideArray[$key]['total']['service_tax'];
                        }else{
                            $service_tax = 0;
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $service_tax);
                        $contentLetter++;
                        
                        if(isset($rideArray[$key]['total']['tips_amount']) && $rideArray[$key]['total']['tips_amount'] != ''){
                            $tips_amount = $rideArray[$key]['total']['tips_amount'];
                        }else{
                            $tips_amount = 0;
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $tips_amount);
                        $contentLetter++;
                        
                        
                        if(isset($rideArray[$key]['pay_status']) && $rideArray[$key]['pay_status'] != ''){
                            $pay_status = $rideArray[$key]['pay_status'];
                        }else{
                            $pay_status = 'NA';
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $pay_status);
                        $contentLetter++;
                        
                        if(isset($rideArray[$key]['summary']['ride_distance']) && $rideArray[$key]['summary']['ride_distance'] != ''){
                            $ride_distance = $rideArray[$key]['summary']['ride_distance'];
                        }else{
                            $ride_distance = '0';
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $ride_distance);
                        $contentLetter++;
                        
                        if(isset($rideArray[$key]['summary']['ride_duration']) && $rideArray[$key]['summary']['ride_duration'] != ''){
                            $ride_duration = $rideArray[$key]['summary']['ride_duration'];
                        }else{
                            $ride_duration = '0';
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $ride_duration);
                        $contentLetter++;
                        
                        if(isset($rideArray[$key]['pay_summary']['type']) && $rideArray[$key]['pay_summary']['type'] != ''){
                            $pay_summary = $rideArray[$key]['pay_summary']['type'];
                        }else{
                            $pay_summary = 'NA';
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $pay_summary);
                        $contentLetter++;
                        
                        if(isset($rideArray[$key]['amount_detail']['amount_in_site']) && $rideArray[$key]['amount_detail']['amount_in_site'] != ''){
                            $amount_in_site = $rideArray[$key]['amount_detail']['amount_in_site'];
                        }else{
                            $amount_in_site = 0;
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $amount_in_site);
                        $contentLetter++;
                        
                        if(isset($rideArray[$key]['amount_detail']['amount_in_driver']) && $rideArray[$key]['amount_detail']['amount_in_driver'] != ''){
                            $amount_in_driver = $rideArray[$key]['amount_detail']['amount_in_driver'];
                        }else{
                            $amount_in_driver = 0;
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $amount_in_driver);
                        $contentLetter++;
                        
                        if(isset($rideArray[$key]['amount_commission']) && $rideArray[$key]['amount_commission'] != ''){
                            $amount_commission = $rideArray[$key]['amount_commission'];
                        }else{
                            $amount_commission = 0;
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $amount_commission);
                        $contentLetter++;
                        
                        if(isset($rideArray[$key]['driver_revenue']) && $rideArray[$key]['driver_revenue'] != ''){
                            $driver_revenue = $rideArray[$key]['driver_revenue'];
                        }else{
                            $driver_revenue = 0;
                        }
                        $ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $driver_revenue);
                        $contentLetter++;
                    }
					
					if($ride_actions == 'Cancelled' ){
						if(isset($rideArray[$key]['cancelled']['primary']['by']) && $rideArray[$key]['cancelled']['primary']['by'] != ''){
							$cancelled_by = $rideArray[$key]['cancelled']['primary']['by'];
						}else{
							$cancelled_by = 0;
						}
						$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $cancelled_by);
						$contentLetter++;
						
						if(isset($rideArray[$key]['cancelled']['primary']['text']) && $rideArray[$key]['cancelled']['primary']['text'] != ''){
							$cancelled_reason = $rideArray[$key]['cancelled']['primary']['text'];
						}else{
							$cancelled_reason = 0;
						}
						$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $cancelled_reason);
						$contentLetter++;
						
					}
					
					$row = $row +1;;
				}
				
			} 
			
		/* Creating Multiple Sheets*/
		$sheet_index = $i+1;
		$ci->excel->getActiveSheet()->setTitle('sheet'.$sheet_index);
		$ci->excel->createSheet();
		
		}
        
        if($ride_actions == 'OnRide') $ride_actions = 'On';
        if($ride_actions == 'Booked') $ride_actions = 'Just Booked';
		
		$filename= $ride_actions.' Ride Report '.date("Y-m-d").'.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
					 
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($ci->excel, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
		exit;
	}
}

if (!function_exists('export_drivers_list')){
	function export_drivers_list($driversList = array(),$data = array(),$file_name=""){
		$limit = 10000;
		$ci =& get_instance();
		$ci->load->library(array('excel'));
		$no_of_rows = count($driversList);
		$no_of_sheets = floor($no_of_rows/$limit);
		if($no_of_rows%$limit > 0){
			$no_of_sheets++;
		}
        
		$headers_array = array('Display Name','Mobile Number','EMail Id','Commission','Address','City','State','Postcode','Category','Vehicle Maker','Vehicle Model','Vehicle Number','Date Of Joining','Verification Status','Average Review');
        
        
		$next_limit = 0;  
		for($i=0; $i<$no_of_sheets; $i++){
			$ci->excel->setActiveSheetIndex($i);
			$current_limit = $next_limit;
			/* Setting Header Name */
			$headerLetter = 'A';  
			foreach($headers_array as $key => $val){
				$headLet = $headerLetter++;
                $ci->excel->getActiveSheet()->setCellValue($headLet."1", $val);
				$ci->excel->getActiveSheet()->getColumnDimension($headLet)->setWidth(50);
			}
			$ci->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setBold(true);
			$ci->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setSize(12);
			
			/* Setting Header Name --- Ends here */
			
			$m = $i+1;
			$next_limit = $m*$limit;
			$row = 2;
			foreach($driversList as $key => $driver){            
				if($key >= $current_limit && $key < $next_limit){
					$contentLetter = 'A';
                    
                    $driver_name = (string)$driver->driver_name;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $driver_name);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(50);
					$contentLetter++;   #Display Name

                    $mobile = (string)$driver->dail_code. '  ' .$driver->mobile_number;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $mobile);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #Mobile
                    
                    $email = (string)$driver->email;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $email);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #EMail
                    
					$driver_commission = 0;
					if(isset($driver->driver_commission)) $driver_commission = (string)$driver->driver_commission;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $driver_commission);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(40);
					$contentLetter++;   #Commission
                    					
					$address = (string)$driver->address['address'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $address);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;   #Address
					
					$city = (string)$driver->address['city'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $city);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;  #City
					
					$state = (string)$driver->address['state'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $state);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;	 #Town
					
					$postal_code = (string)$driver->address['postal_code'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $postal_code);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;   #Postcode	
					
					
					$cTy='N/A';
					if(isset($driver->category)){
						$catsId = (string)$driver->category; 
						if(array_key_exists($catsId,$data['cabCats'])){
							$cTy = $data['cabCats'][$catsId]->name;
						}
					}
					
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $cTy);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;   #Category
                  
                    $vehicle_maker = 'N/A';
					if(isset($driver->vehicle_maker)){
						$catsId = (string)$driver->vehicle_maker;                         
						if(array_key_exists($catsId,$data['brand'])){ 
							$vehicle_maker = $data['brand'][$catsId]->brand_name;
						}
					}
                  
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row,$vehicle_maker);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #Vehicle Maker
                    
                    
                    $vehicle_model = 'N/A';
					if(isset($driver->vehicle_model)){
						$catsId = (string)$driver->vehicle_model;                         
						if(array_key_exists($catsId,$data['model'])){ 
							$vehicle_model = $data['model'][$catsId]->name;
						}
					}
                   
                   
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row,$vehicle_model);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;  #Vehicle Model	

                    $vehicle_number = $driver->vehicle_number;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, (string)$vehicle_number);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #Vehicle Reg

					$doj = 'N/A';
                    if(isset($driver->created))  $doj = date('Y-m-d', strtotime($driver->created));
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, (string)$doj);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #Date Of Joining
					
					$verify_status = 'No';
					if(isset($driver->verify_status) && $driver->verify_status == 'Yes')  $verify_status = 'Yes';
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, (string)$verify_status);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #Verification Status
                    
                    $avg = '0';
                    if(isset($driver->avg_review))  $avg = $driver->avg_review;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, (string)$avg);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #Average Review
                    
					$row = $row +1;
				}
				
				
			}
			
		/* Creating Multiple Sheets*/
		$ci->excel->getActiveSheet()->setTitle('sheet'.$i);
		$ci->excel->createSheet();
		
		} 
        
		
		if($file_name=="") $file_name= 'Drivers_Report_'.date("Y-m-d");
		$file_name = $file_name.'.csv';	//save our workbook as this file name
				
		header("Content-type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$file_name.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
					 
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($ci->excel, 'CSV');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output'); die;
	}
}

if (!function_exists('export_operators_list')){  
	function export_operators_list($operatorsList = array(),$data = array(),$file_name=""){
		$limit = 10000;
		$ci =& get_instance();
		$ci->load->library(array('excel'));
		$no_of_rows = count($operatorsList);
		$no_of_sheets = floor($no_of_rows/$limit);
		if($no_of_rows%$limit > 0){
			$no_of_sheets++;
		}
        $headers_array = array('Display Name','Mobile Number','EMail Id','Date of Joining','Operator Location','Address','City','State','Postal Code');
        
		$next_limit = 0;  
		for($i=0; $i<$no_of_sheets; $i++){
			$ci->excel->setActiveSheetIndex($i);
			$current_limit = $next_limit;
			/* Setting Header Name */
			$headerLetter = 'A';  
			foreach($headers_array as $key => $val){
				$headLet = $headerLetter++;
                $ci->excel->getActiveSheet()->setCellValue($headLet."1", $val);
				$ci->excel->getActiveSheet()->getColumnDimension($headLet)->setWidth(50);
			}
			$ci->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setBold(true);
			$ci->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setSize(12);
			
			/* Setting Header Name --- Ends here */
			
			$m = $i+1;
			$next_limit = $m*$limit;
			$row = 2;
            
            $loce = array();
            foreach($data['locationList']->result() as $key => $loc){
                $loce[$loc->city] = (string)$loc->_id;
            }  
            
			foreach($operatorsList as $key => $operator){ 
				if($key >= $current_limit && $key < $next_limit){
					$contentLetter = 'A';
					
                    $operator_name = (string)$operator->operator_name;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $operator_name);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(50);
					$contentLetter++;   #Display Name

                    $mobile = (string)$operator->dail_code. '  ' .$operator->mobile_number;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $mobile);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #Mobile Number
                    
                    $email = (string)$operator->email;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $email);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #EMail    
                    
                    $doj = 'N/A';
                    if(isset($operator->created))  $doj = date('Y-m-d', strtotime($operator->created));
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, (string)$doj);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #Date Of Joining
                    
                    $Loc = 'Not Available';
                    if(isset($operator->operator_location) && $operator->operator_location != ''){
                        $Loc = array_search($operator->operator_location, $loce);
                    }
                        if($Loc ==""){
                            $Loc = 'Not Available';
                        }
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $Loc);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;   #Operator Location 
                    					
					$address = (string)$operator->address['address'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $address);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;   #Address
					
					$city = (string)$operator->address['city'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $city);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;  #City
					
					$state = (string)$operator->address['state'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $state);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;	 #Town
					
					$postal_code = (string)$operator->address['postal_code'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $postal_code);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;   #Postcode	
                    
					$row = $row +1;
				}
			}
            
		/* Creating Multiple Sheets*/
		$ci->excel->getActiveSheet()->setTitle('sheet'.$i);
		$ci->excel->createSheet();
		
		} 
        
		
		if($file_name=="") $file_name= 'Operators_Report_'.date("Y-m-d");
		$file_name = $file_name.'.csv';	//save our workbook as this file name
				
		header("Content-type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$file_name.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
					 
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($ci->excel, 'CSV');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output'); die;
	}
}

if (!function_exists('export_companys_list')){  
	function export_companys_list($companylist = array(),$data = array(),$file_name=""){
		$limit = 10000;
		$ci =& get_instance();
		$ci->load->library(array('excel'));
		$no_of_rows = count($companylist);
		$no_of_sheets = floor($no_of_rows/$limit);
		if($no_of_rows%$limit > 0){
			$no_of_sheets++;
		}
        $headers_array = array('Company Name','Mobile Number','EMail Id','Date of Joining','Address','City','State','Postal Code');
        
		$next_limit = 0;  
		for($i=0; $i<$no_of_sheets; $i++){
			$ci->excel->setActiveSheetIndex($i);
			$current_limit = $next_limit;
			/* Setting Header Name */
			$headerLetter = 'A';  
			foreach($headers_array as $key => $val){
				$headLet = $headerLetter++;
                $ci->excel->getActiveSheet()->setCellValue($headLet."1", $val);
				$ci->excel->getActiveSheet()->getColumnDimension($headLet)->setWidth(50);
			}
			$ci->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setBold(true);
			$ci->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setSize(12);
			
			/* Setting Header Name --- Ends here */
			
			$m = $i+1;
			$next_limit = $m*$limit;
			$row = 2;
            
			foreach($companylist as $key => $company){ 
				if($key >= $current_limit && $key < $next_limit){
					$contentLetter = 'A';
					
                    $company_name = $company->company_name;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $company_name);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(50);
					$contentLetter++;   #Company Name

                    $mobile = (string)$company->dail_code. '  ' .$company->phonenumber;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $mobile);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #Mobile Number
                    
                    $email = (string)$company->email;
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $email);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #EMail    
                    
                    $doj = 'N/A';
                    if(isset($company->created))  $doj = date('Y-m-d', strtotime($company->created));
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, (string)$doj);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;  #Date Of Joining
                    					
					$address = (string)$company->locality['address'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $address);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;   #Address
					
					$city = (string)$company->locality['city'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $city);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;  #City
					
					$state = (string)$company->locality['state'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $state);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;	 #Town
					
					$postal_code = (string)$company->locality['zipcode'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $postal_code);
                    $ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(30);
					$contentLetter++;   #Postcode	
                    
					$row = $row +1;
				}
			}
            
		/* Creating Multiple Sheets*/
		$ci->excel->getActiveSheet()->setTitle('sheet'.$i);
		$ci->excel->createSheet();
		
		} 
        
		
		if($file_name=="") $file_name= 'Companys_Report_'.date("Y-m-d");
		$file_name = $file_name.'.csv';	//save our workbook as this file name
				
		header("Content-type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$file_name.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
					 
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($ci->excel, 'CSV');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output'); die;
	}
}

	
/* End of file export_helper.php */
/* Location: ./application/helpers/export_helper.php */