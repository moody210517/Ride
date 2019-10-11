<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
* 
* Billing related functions
* @author Casperon
*
**/
 
class Billing extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','email'));
		$this->load->library(array('encrypt','form_validation'));
		$this->load->model('billing_model');
		$this->load->model('revenue_model');
    }
	
	public function generate_billing(){
		$hasBIlling = $this->billing_model->check_billing_cycle($this->data['billing_cycle'],$this->data['last_billing_date']); 
		
		/*  #Testing Lines 
		$billingInfo = array('bill_generated'=> MongoDATE(strtotime(date("Y-m-d 00:00:00"))));
		$this->billing_model->commonDelete(TRANSACTION,$billingInfo);
		$billingInfoz = array('bill_date'=> MongoDATE(strtotime(date("Y-m-d 00:00:00"))));
		$this->billing_model->commonDelete(BILLINGS,$billingInfoz); 
		$hasBIlling = TRUE; 

		*/  
		
		
		if($hasBIlling=== TRUE){		
			$billing_from = date("Y-m-d",strtotime("+1 day",strtotime($this->data['last_billing_date']))).' 00:00:00';
			$billing_to = date("Y-m-d",strtotime("-1 day")).' 23:59:59';

		
			$fdate=strtotime($billing_from);
			$tdate=strtotime($billing_to);
			
			
			
			$selectFields= array('email','image','driver_name','vehicle_number','vehicle_model','no_of_rides','cancelled_rides','mobile_number','dail_code');
			$driverDetails = $this->revenue_model->get_selected_fields(DRIVERS,array(),$selectFields);
			$totalRides=0;$totalRevenue=0;$siteRevenue=0;$driverRevenue=0;	
			if($driverDetails->num_rows()>0){
				$bill_id=time();				
				$rideSummary = $this->revenue_model->get_ride_summary($fdate,$tdate);
				if(!empty($rideSummary['result'])){
					$totalRides = $rideSummary['result'][0]['totalTrips'];
					$siteRevenue = $rideSummary['result'][0]['site_earnings'];
					$driverRevenue = $rideSummary['result'][0]['driver_earnings'];
					$totalRevenue = $siteRevenue+$driverRevenue;
				}
				
				$this->data['billing_summary']['totalRides'] = $totalRides;
				$this->data['billing_summary']['totalRevenue'] = $totalRevenue;
				$this->data['billing_summary']['siteRevenue'] = $siteRevenue;
				$this->data['billing_summary']['driverRevenue'] = $driverRevenue;
				
				
				$billingInfo = array('bill_id'=> (string)$bill_id,
												'bill_period_from'=> MongoDATE($fdate),
												'bill_period_to'=> MongoDATE($tdate),
												'bill_generated'=> MongoDATE(strtotime(date("Y-m-d 00:00:00"))),
												'totalRides'=> floatval($this->data['billing_summary']['totalRides']),
												'totalRevenue'=> floatval(round($this->data['billing_summary']['totalRevenue'],2)),
												'siteRevenue'=> floatval(round($this->data['billing_summary']['siteRevenue'],2)),
												'driverRevenue'=> floatval(round($this->data['billing_summary']['driverRevenue'],2))
											);
				#echo '<pre>'; print_r($billingInfo); 
				$config = '<?php $config["last_billing_date"] = "'.date("Y-m-d",$tdate).'";  ?>';
				$file = 'commonsettings/dectar_billing.php';
				file_put_contents($file, $config);
				$this->billing_model->simple_insert(TRANSACTION,$billingInfo);
				
				$drid=0;
				foreach ($driverDetails->result() as $driver){
					$drid++;
					$total_rides=0;$cancelled_rides=0;$successfull_rides=0;$total_revenue=0;$in_site=0;$couponAmount=0;$in_driver=0;$total_due=0;	
					$site_earnings=0;$driver_earnings=0;	$tips_amount = 0; $tips_in_driver = 0; $tips_in_site = 0;
					
					$driver_id=(string)$driver->_id;
					$driver_name=(string)$driver->driver_name;
					$driver_email=(string)$driver->email;
					$driver_phone=(string)$driver->dail_code.$driver->mobile_number;
					$driver_image=USER_PROFILE_IMAGE_DEFAULT;
					if(isset($driver->image)){
						if($driver->image!=''){
							$driver_image=USER_PROFILE_IMAGE.$driver->image;
						}
					}										
					$rideDetails = $this->revenue_model->get_ride_details($driver_id,$fdate,$tdate);
					
					
					if(!empty($rideDetails['result'])){
						$total_rides=$rideDetails['result'][0]['totalTrips'];
						$total_revenue=$rideDetails['result'][0]['totalAmount'];						
						$couponAmount=$rideDetails['result'][0]['couponAmount'];
						$site_earnings=$rideDetails['result'][0]['site_earnings'];
						$driver_earnings=$rideDetails['result'][0]['driver_earnings'];
						
						if(isset($rideDetails['result'][0]['tipsAmount'])){
							$tips_amount = $rideDetails['result'][0]['tipsAmount'];
						}
                        if (isset($rideDetails['result'][0]['amount_in_site'])) {
                            $in_site = $rideDetails['result'][0]['amount_in_site'];
                        }
                        if (isset($rideDetails['result'][0]['amount_in_driver'])) {
                            $in_driver = $rideDetails['result'][0]['amount_in_driver'];
                        }
					}
					/*  tips was not included in total revenue so driver tips amount need to be added with driver earnings */
					$driver_earnings = $driver_earnings + $tips_amount;
					
					
					$this->data['driversList'][$driver_id]['id'] = $driver_id;
					$this->data['driversList'][$driver_id]['driver_name'] = $driver_name;
					$this->data['driversList'][$driver_id]['driver_email'] = $driver_email;
					$this->data['driversList'][$driver_id]['driver_image'] = base_url().$driver_image;
					$this->data['driversList'][$driver_id]['driver_phone'] = $driver_phone;
					
					
					
					
					$this->data['driversList'][$driver_id]['total_rides'] = $total_rides;
					$this->data['driversList'][$driver_id]['total_revenue'] = $total_revenue;
					$this->data['driversList'][$driver_id]['in_site'] = $in_site;
					$this->data['driversList'][$driver_id]['couponAmount'] = $couponAmount;
					$this->data['driversList'][$driver_id]['in_driver'] = $in_driver;
					$this->data['driversList'][$driver_id]['total_due'] = $total_due;
					
					$this->data['driversList'][$driver_id]['site_earnings'] = $site_earnings;
					$this->data['driversList'][$driver_id]['driver_earnings'] = $driver_earnings;
					$this->data['driversList'][$driver_id]['tips_amount'] = $tips_amount;
					
					$total_amount = $total_revenue + $couponAmount;
					$this->data['driversList'][$driver_id]['bill_from'] = date('d F Y',strtotime($billing_from));
					$this->data['driversList'][$driver_id]['bill_to'] = date('d F Y',strtotime($billing_to));
					
					$this->data['driversList'][$driver_id]['billing_cycle'] = $this->data['billing_cycle'];
					
					$invoice_id = $bill_id+$drid;
					$this->data['driversList'][$driver_id]['invoice_id'] = $invoice_id;
					
					$this->data['driversList'][$driver_id]['bill_date'] = date('d F Y');
					$this->data['driversList'][$driver_id]['bill_id'] = $bill_id;
					
					$site_need_to_pay = 'No';
					$driver_need_to_payment = 'No';
					$site_pay_amount=0.00;
					$driver_pay_amount=0.00;
					
					if(($in_site+$couponAmount)>0){
						if($site_earnings<($in_site+$couponAmount)){
							$site_need_to_pay = 'Yes';
							$site_pay_amount = ($in_site+$couponAmount)-$site_earnings;
							if($site_pay_amount<=0){
								$site_pay_amount = 0;
							}
						}
					}
					if($in_driver>0){
						if($driver_earnings<$in_driver){
							$driver_need_to_payment = 'Yes';
							$driver_pay_amount = $in_driver-$driver_earnings;
							if($driver_pay_amount<=0){
								$driver_pay_amount = 0;
							}
						}
					}
					$s_paid='Yes';
					$d_paid='Yes';
					if($site_need_to_pay=='Yes'){
						$s_paid='No';
					}
					if($driver_need_to_payment=='Yes'){
						$d_paid='No';
					}
					
					
					$invoiceDetails = array('bill_id'=> (string)$bill_id,
						'bill_date'=> MongoDATE(strtotime(date("Y-m-d 00:00:00"))),
						'invoice_id'=> $invoice_id,
						'bill_from'=> MongoDATE(strtotime($billing_from)),
						'bill_to'=> MongoDATE(strtotime($billing_to)),
						'driver_id'=> $driver_id,
						'driver_email'=> $driver_email,
						'driver_name'=> $driver_name,
						'total_rides'=> floatval(round($total_rides,2)),
						'total_revenue'=> floatval(round($total_revenue,2)),
						'couponAmount'=> floatval(round($couponAmount,2)),
						'in_site'=> floatval(round($in_site,2)),
						'in_driver'=> floatval(round($in_driver,2)),
						'site_earnings'=> floatval(round($site_earnings,2)),
						'driver_earnings'=> floatval(round($driver_earnings,2)),
						'total_tips'=> floatval(round($tips_amount,2)),
						'tips_in_site'=> floatval(round($tips_in_site,2)),
						'tips_in_driver'=> floatval(round($tips_in_driver,2)),
						'site_need_to_pay'=> $site_need_to_pay,
						'driver_need_to_payment'=> $driver_need_to_payment,
						'site_pay_amount'=> floatval(round($site_pay_amount,2)),
						'driver_pay_amount'=> floatval(round($driver_pay_amount,2)),
						'site_paid'=> $s_paid,
						'driver_paid'=> $d_paid
					);
					#echo '<pre>'; print_r($invoiceDetails); 
					$this->billing_model->simple_insert(BILLINGS,$invoiceDetails);
					
					if($this->data['billing_cycle']>1){
						$binsf = $this->data['driversList'][$driver_id]['bill_from'].' through '.$this->data['driversList'][$driver_id]['bill_to'];
					}else{
						$binsf = $this->data['driversList'][$driver_id]['bill_from'];
					}
					$subject=$this->config->item('email_title').' - billing for '.$binsf;
					
					if($this->data['driversList'][$driver_id]['tips_amount'] > 0){
							$tips_details =  number_format($this->data['driversList'][$driver_id]['tips_amount'],2);
					} else {
						$tips_details =0;
					}
					$newsid = '14';        
					$template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);        
					$ridernewstemplateArr = array('email_title' => $this->config->item('email_title'),
					'mail_emailTitle' => $this->config->item('email_title'),
					'mail_logo' => $this->config->item('logo_image'),
					'mail_footerContent' => $this->config->item('footer_content'),
					'mail_metaTitle' => $this->config->item('meta_title'),
					'mail_contactMail' => $this->config->item('site_contact_mail'),
					'invoice_id' => $invoice_id,
					'bill_date' => $this->data['driversList'][$driver_id]['bill_date'],
					'driver_name' => $driver_name,
					'binsf' => $binsf,
					'total_rides' => $total_rides,
					'dcurrencySymbol' => $this->data['dcurrencySymbol'],
					'total_revenue' => number_format($total_amount,2),
					'site_earnings' => number_format($site_earnings,2),
					'driver_earnings' => number_format($driver_earnings,2),
					'tips_details' => $tips_details,
					);
					extract($ridernewstemplateArr);
					$message = '<!DOCTYPE HTML>
					<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
							<meta name="viewport" content="width=device-width"/>
							<title>' . $template_values['subject'] . '</title>
						<body>';
							include($template_values['templateurl']);
							$message .= '</body>
					</html>';
					//$mm = $this->generate_content($this->data['driversList'][$driver_id]);
					$email_values = array('mail_type'=>'html',
						'from_mail_id'=>$this->config->item('site_contact_mail'),
						'mail_name'=>$this->config->item('email_title'),
						'to_mail_id'=>$driver_email,
						'subject_message'=>$subject,
						'body_messages'=>$message
					);		
					
					if($total_rides>0){
						$email_send_to_common = $this->revenue_model->common_email_send($email_values);
					}
				}
			}
		}
	}
	
	public function generate_content($mailValues=array()){
		$mailContent='';
		
		$mailContent='
		<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>Dector</title>
			</head>
			<body style="font-family: sans-serif; margin:0;padding:0;">
				<div style="width:680px; margin:0 auto;border:1px solid #ccc;">
					<table width="100%" style="margin:0;padding:0;border-spacing: 0;border:0;border-collapse: initial;">
						<tr style="background:#000;width:100%;">
							<td style="width:50%;padding-left: 10px;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;">
							<img src="'.base_url().'images/logo/'.$this->config->item('logo_image').'"  style="width: 30%;">
							</td>
							<td style="text-align:right;width:50%;padding-left: 10px;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;">
								<p style="margin:0;color:#fff;font-size:14px;">INVOICE NO:'.$mailValues['invoice_id'].'</p>
								<span style="margin:0;color:#fff;font-size:12px;">'.$mailValues['bill_date'].'</span>
							</td>
						</tr>
						<tr style="width:100%;">
							<td colspan="2" style="width:100%;padding-left: 10px;padding-top: 12px;padding-right: 10px;padding-bottom: 12px;text-align:left;">
								<h2 style="color:#000;font-size:18px;margin:0;">Thanks for using '.$this->config->item('email_title').'! Here\'s your invoice.</h2>
							</td>
						</tr>
						<tr style="width:100%;">
							<td colspan="2" style="width:100%;padding-left: 10px;padding-top: 12px;padding-right: 10px;padding-bottom: 12px;text-align:left;">
								<span style="color:#000;font-size:15px;margin:0;">Hi '.$mailValues['driver_name'].',</span>
							</td>
						</tr>';
						if($mailValues['billing_cycle']>1){
						$mailContent .='<tr style="width:100%;">
							<td colspan="2" style="width:100%;padding-left: 10px;padding-top: 12px;padding-right: 10px;padding-bottom: 12px;text-align:left;">
								<span style="color:#000;font-size:15px;margin:0;">Your '.$this->config->item('email_title').' invoice for the period from '.$mailValues['bill_from'].' through '.$mailValues['bill_to'].' is now available to view in your account.</span>
							</td>
						</tr>';
						}else{						
						$mailContent .='<tr style="width:100%;">
							<td colspan="2" style="width:100%;padding-left: 10px;padding-top: 12px;padding-right: 10px;padding-bottom: 12px;text-align:left;">
								<span style="color:#000;font-size:15px;margin:0;">Your '.$this->config->item('email_title').' invoice for '.$mailValues['bill_from'].' is now available to view in your account.</span>
							</td>
						</tr>';
						}
						$mailContent .='<tr style="width:100%;">
							<td colspan="2" style="width:100%;padding-left: 10px;padding-top: 12px;padding-right: 10px;padding-bottom: 12px;text-align:left;border-bottom:5px solid #ccc;">
								<span style="color:#000;font-size:15px;margin:0;"></span>
							</td>
						</tr>
					</table>
					<table cellspacing="10" style="width:100%;background:#fff;border-bottom:1px solid #ccc;padding-bottom:10px;">
						<tr>
							<td style="font-size:14px;color:#333;width:50%;font-weight:bold;">
								Number of trips
							</td>
							<td style="font-size:14px;color:#333;">
								: '.$mailValues['total_rides'].'
							</td>
						</tr>
						
						<tr>
							<td style="font-size:14px;color:#333;width:50%;font-weight:bold;">
								Grand Fare
							</td>
							<td style="font-size:14px;color:#333;">
								: <font style="font-family : DejaVu Sans, Helvetica, sans-serif;">'.$this->data['dcurrencySymbol'].'</font> '.number_format($mailValues['total_revenue']+$mailValues['couponAmount'],2).'
							</td>
						</tr>';
						
						
						
						$mailContent .='<tr>
							<td style="font-size:14px;color:#333;width:50%;font-weight:bold;">
								Commission
							</td>
							<td style="font-size:14px;color:#333;">
								: <font style="font-family : DejaVu Sans, Helvetica, sans-serif;">'.$this->data['dcurrencySymbol'].'</font> '.number_format($mailValues['site_earnings'],2).'
							</td>
						</tr>
						<tr>
							<td style="font-size:14px;color:#333;width:50%;font-weight:bold;">
								Total Amount
							</td>
							<td style="font-size:14px;color:#333;">
								: <font style="font-family : DejaVu Sans, Helvetica, sans-serif;">'.$this->data['dcurrencySymbol'].'</font> '.number_format($mailValues['driver_earnings'],2).'
							</td>
						</tr>';
						
						if($mailValues['tips_amount'] > 0){
							$mailContent .='<tr>
								<td style="font-size:14px;color:#333;width:50%;font-weight:bold;">
									Total Tips
								</td>
								<td style="font-size:14px;color:#333;">
									: <font style="font-family : DejaVu Sans, Helvetica, sans-serif;">'.$this->data['dcurrencySymbol'].'</font> '.number_format($mailValues['tips_amount'],2).'
								</td>
							</tr>';
						}
						
						$mailContent .='<tr>
							<td style="font-size:14px;color:#333;width:50%;">
								Click <a href="'.base_url().'driver/billing-summary/'.$mailValues['invoice_id'].'" target="_blank">here</a> to view the billing summary
							</td>
						</tr>
					</table>
					<table style="width:100%;background:#000;text-align:center;">
						<tr>
							<td style="width:100%;padding-left: 10px;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;">
								<h6 style="color:#27CAF8;font-size:13px;margin:0;">'.$this->config->item('footer_content').'</h6>
							</td>
						</tr>
					</table>
				</div>
			</body>
		</html>';
		
		return $mailContent;
	}
	
	
	
}

/* End of file billing.php */
/* Location: ./application/controllers/cron/billing.php */