<?php
$this->load->view('site/templates/profile_header');

$startdate = get_time_to_string('Y-m-d',strtotime($rider_info->row()->created));
$enddate = get_time_to_string('Y-m-d',time());



?> 

<section class="profile_pic_sec row myrides">
   <div  class="profile_login_cont">
     
		<!--------------  Load Profile Side Bar ------------------------>
		<?php    
			$this->load->view('site/templates/profile_sidebar'); 
		?>
	 
      <div class="share_detail">
         <div class="share_det_title">
            <h2><?php echo $heading; ?></h2>
         </div>
         <div class="profile_ac_inner_det">
            <div class="profile_ac_details profile_ac_title myride_part">
               <p class="search_title_top"><?php if ($this->lang->line('dash_user_search_rides_by_date') != '') echo stripslashes($this->lang->line('dash_user_search_rides_by_date')); else echo 'Search rides by date'; ?>...</p>
               <span class="from">
               <input type="text" id="searchFrom" value="<?php echo $startdate; ?>"  class="date" maxlength="25" size="25"/ placeholder="<?php if ($this->lang->line('dash_user_rides_from_date') != '') echo stripslashes($this->lang->line('dash_user_rides_from_date')); else echo 'From Date'; ?>" readonly />
               <img src="images/site/date_icon.png" />
               </span>
               <span class="to">		
               <input type="text" id="searchTo" value="<?php echo $enddate; ?>" class="date" maxlength="25" size="25"/ placeholder="<?php if ($this->lang->line('dash_user_rides_to_date') != '') echo stripslashes($this->lang->line('dash_user_rides_to_date')); else echo 'To Date'; ?>" readonly />
               <img src="images/site/date_icon.png"/>
               </span>	
               <button class="rd_btn rdd_btn" onclick="datechange();"><?php if ($this->lang->line('admin_rides_search') != '') echo stripslashes($this->lang->line('admin_rides_search')); else echo 'Search'; ?></button>
               <button class="rd_btn rdd_btn" onclick="resetDate('<?php echo $startdate; ?>','<?php echo $enddate; ?>');"><?php if ($this->lang->line('dash_user_rides_reset') != '') echo stripslashes($this->lang->line('dash_user_rides_reset')); else echo 'Reset'; ?></button>
            </div>
            <div class="profile_ac_form myride">
               <table id="display_rides_tbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                     <tr>
                        <th><?php if ($this->lang->line('dash_ride_id') != '') echo stripslashes($this->lang->line('dash_ride_id')); else echo 'Ride ID'; ?></th>
                        <th><?php if ($this->lang->line('dash_driver') != '') echo stripslashes($this->lang->line('dash_driver')); else echo 'Driver'; ?></th>
                        <th><?php if ($this->lang->line('dash_user_rides_trip_date') != '') echo stripslashes($this->lang->line('dash_user_rides_trip_date')); else echo 'Trip Date'; ?></th>
                        <th><?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?></th>
                        <th><?php if ($this->lang->line('dash_user_rides_car_ucfirst') != '') echo stripslashes($this->lang->line('dash_user_rides_car_ucfirst')); else echo 'Car'; ?></th>
                        <th><?php if ($this->lang->line('dash_user_rides_invoice_ucfirst') != '') echo stripslashes($this->lang->line('dash_user_rides_invoice_ucfirst')); else echo 'Invoice'; ?></th>
                     </tr>
                  </thead>
                  <tbody>
									<?php 
									 foreach ($ridesList->result() as $rides) {
																$bookinTime = MongoEPOCH($rides->booking_information['booking_date']);
									?>
                     <tr>
                        <td><?php echo $rides->ride_id; ?></td>
                        <td><?php if(isset($rides->driver['name']) && $rides->driver['name'] != '') echo $rides->driver['name'];  else echo '--';?></td>
                        <td><span style="display:none;"><?php echo $bookinTime."|"; ?></span><?php echo get_time_to_string('Y-m-d', $bookinTime); ?></td>
                        <td><?php if (isset($rides->ride_status)) echo get_language_value_for_keyword($rides->ride_status,$this->data['langCode']); ?></td>
                        
                         
                        <td>
                        <?php if(isset($rides->booking_information['service_type'])){

                        $lng_cat_name =  get_category_name_by_lang($rides->booking_information['service_id'],$this->data['langCode']);  
													if($lng_cat_name != '') $disp_cat = $lng_cat_name; else $disp_cat = $rides->booking_information['service_type'];
																	echo $disp_cat;
																	} else { echo '--'; }?>
												</td>
                        <td><a href="rider/view-ride/<?php echo $rides->ride_id; ?>"><?php if ($this->lang->line('admin_subadmin_view') != '') echo stripslashes($this->lang->line('admin_subadmin_view')); else echo 'View'; ?></a></td>
                     </tr>
                    <?php } ?> 
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</section>

<?php $startTimeStr = strtotime($rider_info->row()->created); ?>

<script>
	$.fn.dataTableExt.afnFiltering.push(function(oSettings, aData, iDataIndex){
		var dateStart = Date.parse($("#searchFrom").val());
		var dateEnd = Date.parse($("#searchTo").val());
		var dateCol = splitString(aData[2]);
		var evalDate= Date.parse(dateCol);
		
		if (evalDate >= dateStart && evalDate <= dateEnd) {
			return true;
		}else {
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


  
	  

	var $dTable = $('#display_rides_tbl').dataTable( {
	  "language": {
		"paginate": {
		  "previous": "<i class='fa fa-angle-left' aria-hidden='true'></i>",
		  "next": "<i class='fa fa-angle-right' aria-hidden='true'></i>"
		}
	  },
	   "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, admin_menu_all]],
        "order": [[ 2, "desc" ]]
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
			format: 'yyyy-mm-dd',
			autoclose: true,
            startDate: '<?php echo date('Y-m-d',$startTimeStr); ?>'
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
			format: 'yyyy-mm-dd',
			autoclose: true,
            startDate: '<?php echo date('Y-m-d',$startTimeStr); ?>'
		}).on('changeDate', function (selected) {
            if($("#searchTo").val() != '') $("#searchTo").css('border-color','#e8e8e8');
        });
	});

  </script>

<?php
$this->load->view('site/templates/footer');
?> 