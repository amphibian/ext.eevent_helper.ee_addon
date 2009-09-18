<?php

class Eevent_helper
{
	var $settings        = array();
	var $name            = 'EEvent Helper';
	var $version         = '1.0.0';
	var $description     = 'Automatically sets the expiration date for event entries, and more.';
	var $settings_exist  = 'y';
	var $docs_url        = '';

	
	// -------------------------------
	//   Constructor - Extensions use this for settings
	// -------------------------------
	
	function Eevent_helper($settings='')
	{
	    $this->settings = $settings;
	}
	// END
	
	
	// --------------------------------
	//  Settings
	// --------------------------------  
	
	function settings()
	{	    
	    global $DB;
	
		$weblogs = array('' => '--');
		$query = $DB->query("SELECT blog_title, weblog_id FROM exp_weblogs ORDER BY blog_title ASC");
		if($query->num_rows > 0) {
			foreach($query->result as $value) {
				$weblogs[$value['weblog_id']] = $value['blog_title'];
			}
		}
		
		$fields = array();
		$query = $DB->query("SELECT w.field_group, w.blog_title, f.field_id, f.field_label FROM exp_weblogs as w, exp_weblog_fields as f WHERE w.field_group = f.group_id AND f.field_type = 'date'ORDER BY w.blog_title ASC,f.field_order ASC");
		if($query->num_rows > 0) {
			foreach($query->result as $value) {
				$fields['field_id_' . $value['field_id']] = $value['blog_title'] . ': ' . $value['field_label'];
			}
		}
		
		$start_field_begin = array('' => 'Use Entry Date');
		$start_field = array_merge($start_field_begin, $fields);
		
		$end_field_begin = array('' => 'None');
		$end_field = array_merge($end_field_begin, $fields);
		
		$settings = array();
	    $settings['event_weblog'] = array('s', $weblogs, NULL);
	    $settings['start_date_field'] = array('s', $start_field, NULL);
	    $settings['end_date_field'] = array('s', $end_field, NULL);
	    $settings['clone_date'] = array('r', array('yes' => 'yes', 'no' => 'no'), 'no');
	    $settings['midnight'] = array('r', array('yes' => 'yes', 'no' => 'no'), 'no');
	    
	    return $settings;
	}
	// END
	
	
	// --------------------------------
	//  Do the stuff
	// --------------------------------  	
	
	function set_dates() {
		
		// Check to see if we're in our events weblog
		if($_POST['weblog_id'] == $this->settings['event_weblog']) {
		
			// Are we zeroing the time?
			if($this->settings['midnight'] == 'yes') {
			
				// Zero the appropriate start date
				if($this->settings['start_date_field'] && $_POST[$this->settings['start_date_field']]) {
					$_POST[$this->settings['start_date_field']] = 
					substr($_POST[$this->settings['start_date_field']], 0, 10) . ' 00:00:00';
				} else {
					$_POST['entry_date'] = substr($_POST['entry_date'], 0, 10) . ' 00:00:00';
				}
				
				// Zero the end date if applicable
				if($this->settings['end_date_field'] && $_POST[$this->settings['end_date_field']]) {
					$_POST[$this->settings['end_date_field']] = 
					substr($_POST[$this->settings['end_date_field']], 0, 10) . ' 00:00:00';
				}
			}
		
			// Set the expiration date
			if($this->settings['end_date_field'] && 
			$_POST[$this->settings['end_date_field']]) // We're using an end date
			{ 
				$_POST['expiration_date'] = 
				substr($_POST[$this->settings['end_date_field']], 0, 10) . ' 23:59:59';
			}
			else
			{ 
				if($this->settings['start_date_field']) // We're using a custom start date
				{
					$_POST['expiration_date'] = 
					substr($_POST[$this->settings['start_date_field']], 0, 10) . ' 23:59:59';
				}
				else // We're using the entry_date
				{
				$_POST['expiration_date'] = substr($_POST['entry_date'], 0, 10) . ' 23:59:59';
				}
			}
			
			// Clone start date to entry date
			if($this->settings['clone_date'] && $this->settings['start_date_field'] && $_POST[$this->settings['start_date_field']])
			{
				$_POST['entry_date'] = $_POST[$this->settings['start_date_field']];
			}
		}	
	}	
    // END
   
   
	// --------------------------------
	//  Activate Extension
	// --------------------------------
	
	function activate_extension()
	{
	    global $DB;
	    
	    $DB->query($DB->insert_string('exp_extensions',
	    	array(
				'extension_id' => '',
		        'class'        => "Eevent_helper",
		        'method'       => "set_dates",
		        'hook'         => "submit_new_entry_start",
		        'settings'     => "",
		        'priority'     => 10,
		        'version'      => $this->version,
		        'enabled'      => "y"
				)
			)
		);
	}
	// END


	// --------------------------------
	//  Update Extension
	// --------------------------------  
	
	function update_extension($current='')
	{
	    global $DB;
	    
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
	    
	    if ($current < '1.0.1')
	    {
	        // Update to next version 1.0.1
	    }
	    
	    if ($current < '1.0.2')
	    {
	        // Update to next version 1.0.2
	    }
	    
	    $DB->query("UPDATE exp_extensions 
	                SET version = '".$DB->escape_str($this->version)."' 
	                WHERE class = 'Eevent_helper'");
	}
	// END
	
	
	// --------------------------------
	//  Disable Extension
	// --------------------------------
	
	function disable_extension()
	{
	    global $DB;
	    
	    $DB->query("DELETE FROM exp_extensions WHERE class = 'Eevent_helper'");
	}
	// END


}
// END CLASS