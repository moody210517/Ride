<?php
$this->load->view('driver/templates/profile_header.php');
?>

<section class="profile_pic_sec row myrides">
   <div  class="profile_login_cont">
     
		<!--------------  Load Profile Side Bar ------------------------>
		<?php    
			$this->load->view('driver/templates/profile_sidebar'); 
		?>
	 
      <div class="share_detail">
         <div class="share_det_title">
            <h2><span> <?php
                                    if ($this->lang->line('driver_earn') != '')
                                        echo stripslashes($this->lang->line('driver_earn'));
                                    else
                                        echo 'Earnings';
                                    ?>  </span></h2>
         </div>
         <div class="profile_ac_inner_det">
            <div class="rate_title earn-head">
               <h2> <?php
                                    if ($this->lang->line('driver_my_earnings') != '')
                                        echo stripslashes($this->lang->line('driver_my_earnings'));
                                    else
                                        echo 'My Earnings';
                                    ?>  </h2>
            </div>
            <div class="profile_ac_form">
               <table id="display_payments_tbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                     <tr>
                        <th><?php
                                    if ($this->lang->line('driver_earnings_invoice_ID') != '')
                                        echo stripslashes($this->lang->line('driver_earnings_invoice_ID'));
                                    else
                                        echo 'Invoice ID';
                                    ?>  </th>
                        <!--<th>Bill Date</th>-->
                        <th><?php
                                    if ($this->lang->line('driver_earnings_bill_date') != '')
                                        echo stripslashes($this->lang->line('driver_earnings_bill_date'));
                                    else
                                        echo 'Bill From';
                                    ?> </th>
                        <th><?php
                                    if ($this->lang->line('driver_earnings_bill_to') != '')
                                        echo stripslashes($this->lang->line('driver_earnings_bill_to'));
                                    else
                                        echo 'Bill To';
                                    ?>  </th>
						<th><?php
                                    if ($this->lang->line('driver_earnings_trips') != '')
                                        echo stripslashes($this->lang->line('driver_earnings_trips'));
                                    else
                                        echo 'Trips';
                                    ?>  </th>
						<th><?php
                                    if ($this->lang->line('driver_earnings_total') != '')
                                        echo stripslashes($this->lang->line('driver_earnings_total'));
                                    else
                                        echo 'Total Amt';
                                    ?>  </th>
						<th><?php
                                    if ($this->lang->line('driver_earnings_tips') != '')
                                        echo stripslashes($this->lang->line('driver_earnings_tips'));
                                    else
                                        echo 'Tips';
                                    ?>  </th>
						<th><?php
                                    if ($this->lang->line('driver_earnings_site_earnings') != '')
                                        echo stripslashes($this->lang->line('driver_earnings_site_earnings'));
                                    else
                                        echo 'Site Earnings';
                                    ?>  </th>
						<th><?php
                                    if ($this->lang->line('driver_earnings_your_earnings') != '')
                                        echo stripslashes($this->lang->line('driver_earnings_your_earnings'));
                                    else
                                        echo 'Your Earnings';
                                    ?>  </th>
						<th><?php
                                    if ($this->lang->line('driver_earnings_action') != '')
                                        echo stripslashes($this->lang->line('driver_earnings_action'));
                                    else
                                        echo 'Action';
                                    ?>  </th>
                     </tr>
                  </thead>
                  <tbody>
                     
						<?php 
						if ($billings->num_rows() > 0){ 
							foreach ($billings->result() as $row){
						?>
					 
					<tr>
							<td>
								<?php  if(isset($row->invoice_id)) echo $row->invoice_id;?>
							</td>
							<!--<td>
								<?php  if(isset($row->bill_date)) echo get_time_to_string("M j,Y",MongoEPOCH($row->bill_date));?>
							</td>-->
							<td><span style="display:none;"><?php echo MongoEPOCH($row->bill_from); ?></span>
								<?php  if(isset($row->bill_from)) echo get_time_to_string("M j,Y",MongoEPOCH($row->bill_from));?>
							</td>
							<td><span style="display:none;"><?php echo MongoEPOCH($row->bill_to); ?></span>
								<?php  if(isset($row->bill_to)) echo get_time_to_string("M j,Y",MongoEPOCH($row->bill_to));?>
							</td>
							<td>
								<?php  if(isset($row->total_rides)) echo $row->total_rides;?>
							</td>
							<td>
								<?php  echo number_format(($row->couponamount+$row->total_revenue),2);?>
							</td>
							
							<td>
								<?php  if(isset($row->total_tips))  echo number_format($row->total_tips,2); else echo '0.00';  ?>
							</td>
							
							<td>
								<?php  echo number_format($row->site_earnings,2);?>
							</td>
							<td>
								<?php  echo number_format($row->driver_earnings,2);?>
							</td>							
							<td>
								<a  href="driver/payments/payment_summary/<?php echo $row->invoice_id;?>"><?php
                                    if ($this->lang->line('driver_view') != '')
                                        echo stripslashes($this->lang->line('driver_view'));
                                    else
                                        echo 'View';
                                    ?>   </a>
							</td>
						</tr>
						<?php 
							}
						}
						?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
  $(document).ready(function() {
    $('#display_payments_tbl').dataTable( {
	  "language": {
		"paginate": {
		  "previous": "<i class='fa fa-angle-left' aria-hidden='true'></i>",
		  "next": "<i class='fa fa-angle-right' aria-hidden='true'></i>"
		}
	  },
	  pageLength:10,
	   "lengthMenu": [[05, 10, 30,40,50, -1], [05, 10, 30,40,50, admin_menu_all]],
	   "order": [[ 0, 'desc' ]],
	   "aoColumnDefs": [
            {"bSortable": false, "aTargets": [8]}
        ],
	} );
} );
  </script>

<?php 
$this->load->view('driver/templates/footer.php');
?>