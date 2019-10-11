<div id="collect_admin_email" style="display:none;">
	<h3><?php if ($this->lang->line('security_purpose') != '') echo stripslashes($this->lang->line('security_purpose')); else echo 'For Security Purpose, Please Enter Email Id'; ?></h3>
	<ul>
	  <li>
			<div class="form_grid_12">
				<div class="form_input">
					 <input name="security_alertBox" id="security_alertBox" type="text"   class="large tipTop security_alertBox" />
					 <input type="hidden" id="admin_bulkAction">
					 <input type="hidden" id="admin_bulkSecurityEmail">
				</div>
			</div>
		</li>
		<li>
			<div class="form_grid_12">
				<div class="form_input">
					<button type="submit" class="btn_small btn_blue security_alertSubmit"  onclick="validate_admin_security_email();"><span><?php if ($this->lang->line('admin_subadmin_submit') != '') echo stripslashes($this->lang->line('admin_subadmin_submit')); else echo 'Submit'; ?></span></button>
				</div>
			</div>
		</li>
	</ul>
</div>
<div class="foot-copyright-outer">
<p class="foot-copyright"><span style="float:left"><?php echo $footer;?></span> <span style="float:right">V.6.0</span></p>
</div>

</body>
</html>