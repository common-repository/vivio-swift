jQuery(document).ready(function($){
	// message fade
	$( ".fade" ).delay( 3000 ).fadeOut( 800 );

	// general admin form change events
	var frm = document.getElementById("vivio_swift_dashboard_debug_settings");
	if(frm){
		frm.addEventListener("change", function () {
			$(this).find("input[type=submit]").addClass("pure-button-primary");
			// if debug is active, enable level drop-down
			var chk_debug = document.getElementById("vivio_swift_enable_debug");
			var sel_loglevel = document.getElementById("vivio_swift_log_level");
			if(chk_debug.checked){
				sel_loglevel.disabled = false;
			} else {
				sel_loglevel.disabled = true;
			}
		});
	}
	var frm = document.getElementById("vivio_swift_cache_dashboard");
	if(frm){
		frm.addEventListener("change", function () {
			$(this).find("input[id=vivio_swift_cache_dashboard_submit]").addClass("pure-button-primary");
		});
	}

	var frm = document.getElementById("vivio_swift_dashboard_logs");
	if(frm){
		frm.addEventListener("change", function () {
			$(this).find("input[type=submit]").addClass("pure-button-primary");
		});
	}

	var frm = document.getElementById("vivio_swift_cache_simple_settings");
	if(frm){
		frm.addEventListener("change", function () {
			$(this).find("input[type=submit]").addClass("pure-button-primary");
		});
	}

	var frm = document.getElementById("vivio_swift_cache_preload_settings");
	if(frm){
		frm.addEventListener("change", function () {
			$(this).find("input[id=vivio_swift_save_preload_cache_settings]").addClass("pure-button-primary");
		});
	}

	var frm = document.getElementById("vivio_swift_cache_extra_settings");
	if(frm){
		frm.addEventListener("change", function () {
			$(this).find("input[type=submit]").addClass("pure-button-primary");
		});
	}

	var frm = document.getElementById("vivio_swift_exclusion_settings");
	if(frm){
		frm.addEventListener("change", function () {
			$(this).find("input[type=submit]").addClass("pure-button-primary");
		});
	}

	var frm = document.getElementById("vivio_swift_refresh_events_settings");
	if(frm){
		frm.addEventListener("change", function () {
			$(this).find("input[type=submit]").addClass("pure-button-primary");
		});
	}

	var frm = document.getElementById("vivio_swift_compression_settings");
	if(frm){
		frm.addEventListener("change", function () {
			$(this).find("input[type=submit]").addClass("pure-button-primary");
		});
	}

	// browser cache control form events
	var frm = document.getElementById("vivio_swift_control_group_form");
	if(frm){
		frm.addEventListener("change", function () {
			$(this).find("button[type=submit][name='vivio_swift_cache_control_save']").addClass("pure-button-primary");
		});
	}
	var btn = document.getElementById("vivio_swift_control_group_id");
	if(btn){
		btn.addEventListener("change", function () {
			frm.submit();
		});
	}
	var btn = document.getElementById("vivio_swift_remove_control_group_submit");
	if(btn){
		btn.addEventListener("click", function () {
			var r = confirm("Are you sure you want to delete this group?\nThis action cannot be undone.");
	        if (r==true){
	        	document.getElementById("vivio_swift_control_group_delete").value="1";
	            frm.submit();
	        }
		});
	}

	var icn = document.getElementById("log_level_info");
	var tr = document.getElementById("log_level_info_row");
	if(tr){
		tr.style.visibility = 'collapse';
	}
	if(icn){
		icn.addEventListener("click", function () {
			if (tr.style.visibility === 'collapse') {
				tr.style.visibility = 'visible';
			} else {
				tr.style.visibility = 'collapse';
			}
		});
	}
});