<?php

if(!defined('EXT'))
{
	exit('Invalid file request');
}

class Eevent_helper
{
	var $settings        = array();
	var $name            = 'EEvent Helper';
	var $version         = '1.2.2';
	var $description     = 'Automatically sets the expiration date for event entries, and more.';
	var $settings_exist  = 'y';
	var $docs_url        = 'http://github.com/amphibian/ext.eevent_helper.ee_addon';
	var $debug			 = 'n';

	
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
	
	function settings_form($current)
	{	    
		global $DB, $DSP, $IN, $LANG, $PREFS;
		
		if($this->debug == 'y')
		{
			print '<pre>';
			print_r($current);
			print '</pre>';
		}
		
		$site = $PREFS->ini('site_id');
						
		// Get a list of weblogs for the current site
		$weblogs = $DB->query("SELECT blog_title, weblog_id 
			FROM exp_weblogs 
			WHERE site_id = '".$DB->escape_str($site)."' 
			ORDER BY blog_title ASC");
		
		// Get a list of date fields for the current site
		$fields = $DB->query("SELECT w.blog_title, f.field_id, f.field_label 
			FROM exp_weblogs as w, exp_weblog_fields as f 
			WHERE w.field_group = f.group_id 
			AND w.site_id = '".$DB->escape_str($site)."' 
			AND f.field_type = 'date' 
			ORDER BY w.blog_title ASC,f.field_order ASC");
		
		// Only grab settings for the current site (if they exist)
		$current = (array_key_exists($site, $current)) ? $current[$site] : array();
				
		// Start building the page
		$DSP->crumbline = TRUE;
		
		$DSP->title  = $LANG->line('extension_settings');
		$DSP->crumb  = $DSP->anchor(BASE.AMP.'C=admin'.AMP.'area=utilities', $LANG->line('utilities')).
		$DSP->crumb_item($DSP->anchor(BASE.AMP.'C=admin'.AMP.'M=utilities'.AMP.'P=extensions_manager', $LANG->line('extensions_manager')));
		$DSP->crumb .= $DSP->crumb_item($this->name);
		
		$DSP->right_crumb($LANG->line('disable_extension'), BASE.AMP.'C=admin'.AMP.'M=utilities'.AMP.'P=toggle_extension_confirm'.AMP.'which=disable'.AMP.'name='.$IN->GBL('name'));
		
		$DSP->body = $DSP->heading($this->name.NBS.$DSP->qspan('defaultLight', $this->version), 1);		
		
		$DSP->body .= $DSP->form_open(
			array(
				'action' => 'C=admin'.AMP.'M=utilities'.AMP.'P=save_extension_settings',
				'name'   => 'eevent_helper',
				'id'     => 'eevent_helper'
			),
			array('name' => get_class($this))
		);
		
		// Open the table
		$DSP->body .=   $DSP->table('tableBorder', '0', '', '100%');
		$DSP->body .=   '<tbody>'.$DSP->tr();
		$DSP->body .=   $DSP->td('tableHeading', '', '2');
		$DSP->body .=   $LANG->line('events').' '.$PREFS->core_ini['weblog_nomenclature'].'s';
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->tr_c().'</tbody>';
		
		// How many event weblogs do we have settings for?
		$total = ( array_key_exists('event_weblog', $current) && !empty($current['event_weblog']) ) ? count($current['event_weblog']) : 1;
		// Start at 1
		$count = 1;
		// Settings array starts at 0
		$i = $count-1;
		// Build a settings panel for each events weblog
		while($count <= $total)
		{
			// Choose event weblog
			$DSP->body .=   '<tbody>'.$DSP->tr();
			$DSP->body .=   $DSP->td('tableCellOne', '45%');
			$DSP->body .=   $DSP->qdiv('defaultBold', $LANG->line('event_weblog'));
			$DSP->body .=   $DSP->td_c();
			
			$DSP->body .=   $DSP->td('tableCellOne');
			$DSP->body .=   $DSP->input_select_header('event_weblog[]');
			$DSP->body .=	$DSP->input_select_option('', '--');
			foreach($weblogs->result as $value)
			{
				extract($value);
				$DSP->body .= $DSP->input_select_option($weblog_id, $blog_title, 
				( isset($current['event_weblog'][$i]) && $current['event_weblog'][$i] == $weblog_id ) ? 1 : '');
			}
			$DSP->body .=   $DSP->input_select_footer();
			$DSP->body .=   $DSP->td_c();
			$DSP->body .=   $DSP->tr_c();
	
			// Choose Start Date Field
			$DSP->body .=   $DSP->tr();
			$DSP->body .=   $DSP->td('tableCellTwo', '45%');
			$DSP->body .=   $DSP->qdiv('defaultBold', $LANG->line('start_date_field'));
			$DSP->body .=   $DSP->td_c();
			
			$DSP->body .=   $DSP->td('tableCellTwo');
			$DSP->body .=   $DSP->input_select_header('start_date_field[]');
			$DSP->body .=	$DSP->input_select_option('', $LANG->line('use_entry_date'));
			foreach($fields->result as $value)
			{
				extract($value);
				$DSP->body .= $DSP->input_select_option($field_id, $blog_title.': '.$field_label, 
					( isset($current['start_date_field'][$i]) && $current['start_date_field'][$i] == $field_id ) ? 1 : '');
			}
			$DSP->body .=   $DSP->input_select_footer();
			$DSP->body .=   $DSP->td_c();
			$DSP->body .=   $DSP->tr_c();
	
			// Choose End Date Field
			$DSP->body .=   $DSP->tr();
			$DSP->body .=   $DSP->td('tableCellOne', '45%');
			$DSP->body .=   $DSP->qdiv('defaultBold', $LANG->line('end_date_field'));
			$DSP->body .=   $DSP->td_c();
			
			$DSP->body .=   $DSP->td('tableCellOne');
			$DSP->body .=   $DSP->input_select_header('end_date_field[]');
			$DSP->body .=	$DSP->input_select_option('', $LANG->line('none'));
			foreach($fields->result as $value)
			{
				extract($value);
				$DSP->body .= $DSP->input_select_option($field_id, $blog_title.': '.$field_label, 
					( isset($current['end_date_field'][$i]) && $current['end_date_field'][$i] == $field_id ) ? 1 : '');
			}
			$DSP->body .=   $DSP->input_select_footer();
			$DSP->body .=   $DSP->td_c();
			$DSP->body .=   $DSP->tr_c();
	
			// Clone dates?
			$DSP->body .=   $DSP->tr();
			$DSP->body .=   $DSP->td('tableCellTwo', '45%');
			$DSP->body .=   $DSP->qdiv('defaultBold', $LANG->line('clone_date'));
			$DSP->body .=   $DSP->td_c();
			
			$DSP->body .=   $DSP->td('tableCellTwo');
			$DSP->body .=   $DSP->input_select_header('clone_date[]');			
			$DSP->body .= 	$DSP->input_select_option('yes', $LANG->line('yes'), 
				( isset($current['clone_date'][$i]) && $current['clone_date'][$i] == 'yes' ) ? 1 : '');
			$DSP->body .= 	$DSP->input_select_option('no', $LANG->line('no'), 
				( isset($current['clone_date'][$i]) && $current['clone_date'][$i] == 'no' ) ? 1 : '');
			$DSP->body .=   $DSP->input_select_footer();
			$DSP->body .=   $DSP->td_c();
			$DSP->body .=   $DSP->tr_c();
	
			// Remove time?
			$DSP->body .=   $DSP->tr();
			$DSP->body .=   $DSP->td('tableCellOne', '45%');
			$DSP->body .=   $DSP->qdiv('defaultBold', $LANG->line('midnight'));
			$DSP->body .=   $DSP->td_c();
			
			$DSP->body .=   $DSP->td('tableCellOne');
			$DSP->body .=   $DSP->input_select_header('midnight[]');			
			$DSP->body .= 	$DSP->input_select_option('yes', $LANG->line('yes'), 
				( isset($current['midnight'][$i]) && $current['midnight'][$i] == 'yes' ) ? 1 : '');
			$DSP->body .= 	$DSP->input_select_option('no', $LANG->line('no'), 
				( isset($current['midnight'][$i]) && $current['midnight'][$i] == 'no' ) ? 1 : '');
			$DSP->body .=   $DSP->input_select_footer();
			$DSP->body .=   $DSP->td_c();
			$DSP->body .=   $DSP->tr_c();
			
			// Remove localization?
			$DSP->body .=   $DSP->tr();
			$DSP->body .=   $DSP->td('tableCellTwo', '45%');
			$DSP->body .=   $DSP->qdiv('defaultBold', $LANG->line('remove_localization'));
			$DSP->body .=   $DSP->td_c();
			
			$DSP->body .=   $DSP->td('tableCellTwo');
			$DSP->body .=   $DSP->input_select_header('remove_localization[]');			
			$DSP->body .= 	$DSP->input_select_option('yes', $LANG->line('yes'), 
				( isset($current['remove_localization'][$i]) && $current['remove_localization'][$i] == 'yes' ) ? 1 : '');
			$DSP->body .= 	$DSP->input_select_option('no', $LANG->line('no'), 
				( isset($current['remove_localization'][$i]) && $current['remove_localization'][$i] == 'no' ) ? 1 : '');
			$DSP->body .=   $DSP->input_select_footer();
			$DSP->body .=   $DSP->td_c();
			$DSP->body .=   $DSP->tr_c();				
			
			// Default localization
			$DSP->body .=   $DSP->tr();
			$DSP->body .=   $DSP->td('tableCellOne', '45%');
			$DSP->body .=   $DSP->qdiv('defaultBold', $LANG->line('default_localization'));
			$DSP->body .=   $DSP->td_c();
			
			$DSP->body .=   $DSP->td('tableCellOne');
			$DSP->body .=   $DSP->input_select_header('default_localization[]');			
			$DSP->body .= 	$DSP->input_select_option('n', $LANG->line('Fixed'), 
				( isset($current['default_localization'][$i]) && $current['default_localization'][$i] == 'n' ) ? 1 : '');
			$DSP->body .= 	$DSP->input_select_option('y', $LANG->line('Localized'), 
				( isset($current['default_localization'][$i]) && $current['default_localization'][$i] == 'y' ) ? 1 : '');
			$DSP->body .=   $DSP->input_select_footer();
			$DSP->body .=   $DSP->td_c();
			$DSP->body .=   $DSP->tr_c();
			
			// Spacer row
			$DSP->body .=	$DSP->table_row(
				array(
					'cell_one' => array('class' => 'tableCellTwo', 'style' => 'background: #B8C6CE; height: 10px;'),
					'cell_two' => array('class' => 'tableCellTwo', 'style' => 'background: #B8C6CE; height: 10px;')
				)
			).'</tbody>';
			
			// Increment
	   		$count++; $i++;
	   }
	   
	   	// Add/remove links
		$DSP->body .=	'<tbody>'.$DSP->tr().$DSP->td('tableCellTwo defaultBold');
		$DSP->body .=	'<a href="#" id="clone">'.$LANG->line('clone').'</a>';
		$DSP->body .=	$DSP->td_c().$DSP->td('tableCellTwo defaultRightBold');
		$DSP->body .=	'<a href="#" id="remove">'.$LANG->line('remove').'</a>';		
		$DSP->body .=	$DSP->td_c().$DSP->tr_c().'</tbody>';
		
		// Wrap it up
		$DSP->body .=   $DSP->table_c();
		$DSP->body .=   $DSP->qdiv('itemWrapperTop', $DSP->input_submit());
		$DSP->body .=   $DSP->form_c();	    
	}
	// END
	
	
	function save_settings()
	{
		global $DB, $PREFS;

		$site = $PREFS->ini('site_id');		
		
		$settings = $this->get_settings(TRUE);
		
		$settings[$site] = array(
			'event_weblog' => $_POST['event_weblog'],
			'start_date_field' => $_POST['start_date_field'],
			'end_date_field' => $_POST['end_date_field'],
			'clone_date' => $_POST['clone_date'],
			'midnight' => $_POST['midnight'],
			'remove_localization' => $_POST['remove_localization'],
			'default_localization' => $_POST['default_localization']
		);
		
		$data = array('settings' => addslashes(serialize($settings)));
		$update = $DB->update_string('exp_extensions', $data, "class = 'Eevent_helper'");
		$DB->query($update);
	}

	
	function get_settings($all_sites = FALSE)
	{
		global $DB, $PREFS, $REGX;
		$site = $PREFS->ini('site_id');

		$get_settings = $DB->query("SELECT settings FROM exp_extensions WHERE class = 'Eevent_helper' LIMIT 1");
		if ($get_settings->num_rows > 0 && $get_settings->row['settings'] != '')
        {
        	$settings = $REGX->array_stripslashes(unserialize($get_settings->row['settings']));
        	$settings = ($all_sites == TRUE) ? $settings : $settings[$site];
        }
        else
        {
        	$settings = array();
        }
        return $settings;		
	}	
	
	
	function is_event_weblog($type = '')
	{
		global $IN, $PREFS;
		$site = $PREFS->ini('site_id');

		// Have we saved our settings for this site?
		if(array_key_exists($site, $this->settings))
		{
			// Find which index in the array we want to take our settings from
			$key = array_search($IN->GBL('weblog_id'), $this->settings[$site]['event_weblog']);
			
			// If we only want to do stuff on the publish screen, we need some additional checks
			if($type == 'display')
			{
				if($IN->GBL('M') != 'entry_form' && $IN->GBL('M') != 'new_entry' && $IN->GBL('M') != 'edit_entry')
				{
					$key = FALSE;	
				}
			}
			return $key;
		}
		else
		{
			return FALSE;
		}
	}	
	
	
	function submit_new_entry_start() {
	
		global $PREFS;
		
		$key = $this->is_event_weblog();

		if($key !== FALSE)
		{
			$settings = $this->get_settings();
			
			$midnight = $settings['midnight'][$key];
			$start_date = (isset($settings['start_date_field'][$key]) && !empty($settings['start_date_field'][$key])) ? 
				'field_id_'.$settings['start_date_field'][$key] : '';
			$end_date = (isset($settings['end_date_field'][$key]) && !empty($settings['end_date_field'][$key])) ? 
				'field_id_'.$settings['end_date_field'][$key] : '';
			$clone = $settings['clone_date'][$key];
			
			// Are we zeroing the time?
			if($midnight == 'yes')
			{
				// Zero the appropriate start date
				if($start_date && $_POST[$start_date])
				{
					// We submitted a custom start date
					$_POST[$start_date] = substr($_POST[$start_date], 0, 10) . ' 00:00:00';
				}
				else
				{
					// Use the entry date
					$_POST['entry_date'] = substr($_POST['entry_date'], 0, 10) . ' 00:00:00';
				}
				
				// Zero the end date if applicable
				if($end_date && $_POST[$end_date])
				{
					$_POST[$end_date] = substr($_POST[$end_date], 0, 10) . ' 00:00:00';
				}
			}
		
			// Set the expiration date
			if($end_date && $_POST[$end_date]) // We're using an end date
			{ 
				$_POST['expiration_date'] = substr($_POST[$end_date], 0, 10) . ' 23:59:59';
			}
			else
			{ 
				if($start_date) // We're using a custom start date
				{
					if($_POST[$start_date]) // Make sure we have a date
					{
						$_POST['expiration_date'] = substr($_POST[$start_date], 0, 10) . ' 23:59:59';
					}
				}
				else // We're using the entry_date
				{
					$_POST['expiration_date'] = substr($_POST['entry_date'], 0, 10) . ' 23:59:59';
				}
			}
			
			// Clone start date to entry date
			if($clone == 'yes' && $start_date && $_POST[$start_date])
			{
				$_POST['entry_date'] = $_POST[$start_date];
			}
		}	
	}	
    // END


	// --------------------------------
	//  Control panel changes
	// -------------------------------- 
	    
	function show_full_control_panel_end($out)
	{

		global $EXT, $IN, $PREFS;
		
		$out = ($EXT->last_call !== FALSE) ? $EXT->last_call : $out;
		
		$key = $this->is_event_weblog('display');

		if($key !== FALSE)
		{
			$settings = $this->get_settings();
			
			$start_date = (isset($settings['start_date_field'][$key]) && !empty($settings['start_date_field'][$key])) ? 
				'field_id_'.$settings['start_date_field'][$key] : '';
			$end_date = (isset($settings['end_date_field'][$key]) && !empty($settings['end_date_field'][$key])) ? 
				'field_id_'.$settings['end_date_field'][$key] : '';
			$midnight = $settings['midnight'][$key];
			$remove = $settings['remove_localization'][$key];
			$default = $settings['default_localization'][$key];
			
			// Remove the localization toggle
			if($remove == 'yes')
			{
				// Regex courtesy of Lodewijk Schutte from his Low CP extension
				$out = preg_replace('/<select name=\'(field_offset_\d+)\'.*?<\/select>/is', '<input type="hidden" name="$1" value="' . $default . '" />', $out);
			}
			
			
			// JavaScript for hiding the time
			if($midnight == 'yes')
			{
				$target = "</head>";
				$js = '
				<script type="text/javascript">
				<!-- Added by EEvent Helper -->
				$(document).ready(function()
					{
					';
				if($start_date)
				{
					$js .= '$("input[name='.$start_date.']").attr("maxlength", "10")
                    $("input[name='.$start_date.']").val($("input[name='.$start_date.']").val().substr(0,10));
					';
				}
				else
				{
					$js .= '$("input[name=entry_date]").attr("maxlength", "10")
                    $("input[name=entry_date]").val($("input[name=entry_date]").val().substr(0,10));
					';
				}
				if($end_date)
				{
					$js .= '$("input[name='.$end_date.']").attr("maxlength", "10")
                    $("input[name='.$end_date.']").val($("input[name='.$end_date.']").val().substr(0,10));
					';
				}
				$js .= '}
				);
				</script>
				</head>
				';			
	
				$out = str_replace($target, $js, $out);
			}
		}
		
		// Javascript for Extension settings page
		if( $IN->GBL('P') == 'extension_settings' && $IN->GBL('name') == 'eevent_helper' )
		{
			$js = '
				<script type="text/javascript">
				<!-- Added by EEvent Helper -->
				$(document).ready(function()
					{
					
					function countPanels()
					{
						if($("form#eevent_helper tbody").size() == 3) {
							$("a#remove").hide();	
						} else {
							$("a#remove").show();
						}					
					}
					
					countPanels();
					
					$("a#clone").click(function(){
						var el = $(this).parents("tbody").prev("tbody").clone();
						$("select option:first-child", el).attr("selected", "selected");
						$(this).parents("tbody").prev("tbody").after(el);
						$(el).hide().fadeIn();
						countPanels();
						return false;
					});
					
					$("a#remove").click(function(){
						$(this).parents("tbody").prev("tbody").fadeOut("", function(){
							$(this).remove();
							countPanels();
						});
						return false;
					});
						
					}
				);
				</script>
				</head>
				';
			$out = str_replace('</head>', $js, $out);
		}
				
		return $out;
	}   
	// END 
	
   
	// --------------------------------
	//  Activate Extension
	// --------------------------------
	
	function activate_extension()
	{
	    global $DB;
	    
	    $hooks = array(
	    	'submit_new_entry_start' => 'submit_new_entry_start',
	    	'weblog_standalone_insert_entry' => 'submit_new_entry_start',
	    	'show_full_control_panel_end' => 'show_full_control_panel_end'
	    );
	    
	    foreach($hooks as $hook => $method)
	    {
		    $DB->query($DB->insert_string('exp_extensions',
		    	array(
					'extension_id' => '',
			        'class'        => "Eevent_helper",
			        'method'       => $method,
			        'hook'         => $hook,
			        'settings'     => serialize(array(0)),
			        'priority'     => 10,
			        'version'      => $this->version,
			        'enabled'      => "y"
					)
				)
			);
	    }	
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
	    
	    if($current < '1.2')
	    {
	    	// Settings format has changed, so zero it out.  Sorry!
	    	$DB->query("UPDATE exp_extensions 
	    		SET settings = '".serialize(array(0))."' 
	    		WHERE class = 'Eevent_helper'");
	    }

	    if($current < '1.2.2')
	    {
	    	$settings = $this->get_settings(TRUE);
	    	
	    	// Add compatibility with SAEFs
	    	$DB->query($DB->insert_string('exp_extensions',
		    	array(
					'extension_id' => '',
			        'class'        => "Eevent_helper",
			        'method'       => 'submit_new_entry_start',
			        'hook'         => 'weblog_standalone_insert_entry',
			        'settings'     => serialize($settings),
			        'priority'     => 10,
			        'version'      => $this->version,
			        'enabled'      => "y"
					)
				)
			);
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