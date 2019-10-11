<?php
$this->load->view('site/templates/profile_header');
?>


<section class="moneytrancsaction profile_pic_sec row">
   <div  class="profile_login_cont">

    <?php 
    if (!empty($wallet_history[0]['transactions'])) {
        $wallet_txns = array_reverse($wallet_history[0]['transactions']);
    } else {
        $wallet_txns = array();
    }
    ?> 
    
    <?php
    $this->load->view('site/templates/profile_sidebar');
    
    $org_startdate = time();
    
    if(count($wallet_txns) > 0){
      $walletnew = array_reverse($wallet_txns);
      $startdate = get_time_to_string('m-d-Y', MongoEPOCH($walletnew[0]['trans_date']));
      $org_startdate = MongoEPOCH($walletnew[0]['trans_date']);
    }else{
      $startdate = get_time_to_string('01-01-Y',time());
    }
    
    $enddate = get_time_to_string('m-d-Y',time());

    ?>

    <div class="share_detail">
       <div class="share_det_title">
          <h2><span><?php echo $siteTitle; ?></span><span><?php if ($this->lang->line('admin_common_money') != '') echo stripslashes($this->lang->line('admin_common_money')); else echo 'Money'; ?></span><span><?php if ($this->lang->line('site_user_cab_transactions_upper') != '') echo stripslashes($this->lang->line('site_user_cab_transactions_upper')); else echo 'TRANSACTIONS'; ?></span></h2>
       </div>
       <div class="profile_ac_inner_det">
          <div class="profile_ac_details profile_ac_title moneytransaction">
             <p class="search_title_top"><?php if ($this->lang->line('site_user_search_transactions_by_date') != '') echo stripslashes($this->lang->line('site_user_search_transactions_by_date')); else echo 'Search transactions by date'; ?>...</p>
             <span class="from">
             <input type="text" value="<?php echo $startdate; ?>" id="searchFrom" class="date" maxlength="25" size="25"/ placeholder="<?php if ($this->lang->line('dash_user_rides_from_date') != '') echo stripslashes($this->lang->line('dash_user_rides_from_date')); else echo 'From Date'; ?>" readonly />
             <img src="images/site/date_icon.png"/>
             </span>
             <span class="to">       
             <input type="text" value="<?php echo $enddate; ?>" id="searchTo" class="date" maxlength="25" size="25"/ placeholder="<?php if ($this->lang->line('dash_user_rides_to_date') != '') echo stripslashes($this->lang->line('dash_user_rides_to_date')); else echo 'To Date'; ?>" readonly />
             <img src="images/site/date_icon.png" />
             </span>     
             <button class="rd_btn rdd_btn"  onclick="datechange();"><?php if ($this->lang->line('admin_rides_search') != '') echo stripslashes($this->lang->line('admin_rides_search')); else echo 'Search'; ?></button>
             <button class="rd_btn rdd_btn" onclick="resetDate('<?php echo $startdate; ?>','<?php echo $enddate; ?>');"><?php if ($this->lang->line('dash_user_rides_reset') != '') echo stripslashes($this->lang->line('dash_user_rides_reset')); else echo 'Reset'; ?></button>
          </div>
          <div class="profile_ac_form many_trans">
             <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                   <tr>
                      <th><?php if ($this->lang->line('admin_cms_description') != '') echo stripslashes($this->lang->line('admin_cms_description')); else echo 'Description'; ?></th>
                      <th><?php if ($this->lang->line('admin_reports_date') != '') echo stripslashes($this->lang->line('admin_reports_date')); else echo 'Date'; ?></th>
                      <th><?php if ($this->lang->line('admin_promocode_amount') != '') echo stripslashes($this->lang->line('admin_promocode_amount')); else echo 'Amount'; ?>(<?php echo $dcurrencySymbol; ?>)</th>
                      <th><?php if ($this->lang->line('admin_promocode_type') != '') echo stripslashes($this->lang->line('admin_promocode_type')); else echo 'Type'; ?></th>
                      <th><?php if ($this->lang->line('user_balance') != '') echo stripslashes($this->lang->line('user_balance')); else echo 'Balance'; ?>(<?php echo $dcurrencySymbol; ?>)</th>
                   </tr>
                </thead>
                <tbody>
                   <?php if (!empty($wallet_txns)) {
							
							$sitename =  $this->config->item('email_title');
                    
                            foreach ($wallet_txns as $txns) { 
                                $txns_description = '';
                                $txn_amount = 0;
                                $avail_balance = 0;
                                $txn_date = '';
                                $trans_mode = '';
                                if ((isset($txns['credit_type']) || isset($txns['debit_type'])) && isset($txns['type'])) {
                                    if (isset($txns['credit_type']) && $txns['credit_type'] == 'welcome') {
                                        if ($this->lang->line('user_welcome_bonus') != '') $var = stripslashes($this->lang->line('user_welcome_bonus')); else $var = ' Welcome Bonus';
                                        $txns_description = $sitename . $var;
                                    } else if (isset($txns['credit_type']) && $txns['credit_type'] == 'recharge') {
                                        if ($this->lang->line('user_wallet_recharge_txn') != '') $var = stripslashes($this->lang->line('user_wallet_recharge_txn')); else $var = 'Wallet Recharge TxnId : ';
                                        $txns_description = $var . $txns['trans_id'];
                                    } else if ($txns['debit_type'] == 'payment') {
                                        if ($this->lang->line('user_booking_for_crn') != '') $var = stripslashes($this->lang->line('user_booking_for_crn')); else $var = 'Booking for CRN:';
                                        $txns_description = $var ." ". $txns['ref_id'];
                                    } else if ($txns['credit_type'] == 'referral') {
                                        if ($this->lang->line('user_referral_reward') != '') $var = stripslashes($this->lang->line('user_referral_reward')); else $var = 'Referral reward:';
                                          $name = get_referer_name($txns['ref_id']);
                                          if($name==''){
                                            if ($this->lang->line('rides_na') != '') $name = stripslashes($this->lang->line('rides_na')); else $name = 'N/A';
                                          }
                                          $txns_description = $var ." ". $name;
                                    } else {
                                        $txns_description = $txns['credit_type'];
                                    }
                                }
                                
                                if(isset($txns['debit_type'])) {
                                    $var = $txns_description ." ". $txns['ref_id'];
                                }

                                ?>
                   <tr>
                      <td><?php echo $txns_description; ?></td>
                      <td><span style="display:none;"><?php echo MongoEPOCH($txns['trans_date'])."|"; ?></span><?php echo get_time_to_string('m-d-Y', MongoEPOCH($txns['trans_date'])); ?></td>
                      <td><?php echo number_format($txns['trans_amount'],0); ?></td>
                      <td><?php 
						
						if(isset($txns['type'])&&$txns['type']=='CREDIT'){
							if ($this->lang->line('user_credit') != '') $txns_type = stripslashes($this->lang->line('user_credit')); else $txns_type = 'CREDIT';
						}else{
							if ($this->lang->line('user_debit') != '') $txns_type = stripslashes($this->lang->line('user_debit')); else $txns_type ='DEBIT';
						}
						echo $txns_type; 
					  
					  
					  ?></td>
                      <td><?php echo number_format($txns['avail_amount'],2); ?></td>
                   </tr>
                    <?php   }
                    }
                    ?>
                </tbody>
             </table>
          </div>
       </div>
    </div>

    </div>
</section>            


<?php $startTimeStr = strtotime($rider_info->row()->created); ?>

<script>
  $(document).ready(function(){
  new WOW().init();
  });

  $.fn.dataTableExt.afnFiltering.push(
    function(oSettings, aData, iDataIndex){
      var searchFrom = parseDateValue($("#searchFrom").val());
      var searchTo = parseDateValue($("#searchTo").val());
      // aData represents the table structure as an array of columns, so the script access the date value 
      // in the first column of the table via aData[0]
      var dateCol = splitString(aData[1]);
      var evalDate= parseDateValue(dateCol);
      
      if (evalDate >= searchFrom && evalDate <= searchTo) {
        return true;
      }
      else {
        return false;
      }
      
    });


    function splitString(rawString) {
      var dateArray= rawString.split("|");
      var exactDate= dateArray[1];
      return exactDate;
    }

    function parseDateValue(rawDate) {
      var dateArray= rawDate.split("-");
      var parsedDate= dateArray[2] + dateArray[0] + dateArray[1];
      return parsedDate;
    }


    var $dTable= $('#example').dataTable( {
              "language": {
                "paginate": {
                  "previous": "<i class='fa fa-angle-left' aria-hidden='true'></i>",
                  "next": "<i class='fa fa-angle-right' aria-hidden='true'></i>"
                }
              },
                "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, admin_menu_all]],
                "order": [[ 1, "desc" ]]
        } );
    function datechange() {
       var  searchFrom = $("#searchFrom").val();
        var  searchTo = $("#searchTo").val();
        
        if(searchFrom != '' && searchTo != ''){
            $dTable.fnDraw();  
        } else {
            if(searchTo == '')  
            $("#searchTo").css('border-color','red'); else $("#searchTo").css('border-color','#e8e8e8');
            if(searchFrom == '') 
            $("#searchFrom").css('border-color','red'); else $("#searchFrom").css('border-color','#e8e8e8');
        }
    }

    function resetDate(start,end){
      $("#searchFrom").val(start);
      $("#searchTo").val(end);
      $dTable.fnDraw(); 
    }
	
	$(function () {		
		$("#searchFrom").datetimepicker({
			minView: 2,
			format: 'mm-dd-yyyy',
			autoclose: true,
            startDate: '<?php echo date('Y-m-d',$org_startdate); ?>'
		}).on('changeDate', function (selected) {
            if($("#searchFrom").val() != ''){
                var minDate = new Date(selected.date.valueOf()); 
                $('#searchTo').val('');
                $('#searchTo').datetimepicker('setStartDate', minDate);
                $("#searchFrom").css('border-color','#e8e8e8');
            }
        });
		
		$("#searchTo").datetimepicker({
			minView: 2,
			format: 'mm-dd-yyyy',
			autoclose: true,
            startDate: '<?php echo date('Y-m-d',$org_startdate); ?>'
		}).on('changeDate', function (selected) {
            if($("#searchTo").val() != '') $("#searchTo").css('border-color','#e8e8e8');
        });
	});
	
</script>

<style>
    .wallet-trans-tab-head li{
        width:30%;
    }
	.profile_ac_form table.table-bordered.dataTable thead th{
		padding: 20px 18px !important;
	}
</style>
<?php
$this->load->view('site/templates/footer');
?>