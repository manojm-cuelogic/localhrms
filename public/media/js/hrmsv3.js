function changestatus(controllername,objid,flag) {
	var flagAr = flag.split("@#$"); 
	var i;
	var msgdta = ' ';
	for(i=0;i<flagAr.length;i++)
	{
		msgdta += flagAr[i]+' ';
	}	
	
	mdgdta = $.trim(msgdta);
	if(controllername == 'bgscreeningtype') 
		var messageAlert = 'Are you sure you want to delete the selected screening type?';
	else  if(controllername == 'businessunits') 
		 var messageAlert = 'Are you sure you want to delete the selected business unit?';
	else  if(controllername == 'agencylist') 
		var messageAlert = 'You are trying to delete the selected agency. The background check processes assigned to this agency will become invalid. Please confirm.';
	//else if(controllername == 'pendingleaves')	
	 //   var messageAlert = 'Are you sure you want to cancel the selected leave request?';
	/**/
	else if(controllername == 'approvedleaves' || controllername == 'pendingleaves')  {	
		var messageAlert = 'Are you sure you want to cancel the selected leave request?';
		$("#cancelleaverequest").dialog({
									open: function(event, ui) {
														
										 //checkerrorvalidation();
										 // datepickerappend();
										 $("#objectId").val(objid);
										 $("#controllername").val(controllername);
										  
										 },

									draggable:false, 
									resizable: false,
									height:'auto',
									title: "Cancel: Leave request",
									modal: true, 
									close: function( event, ui ) {
										  //closedialog('');
										  $("#cancelleaverequest").hide();
									}
								});
		return;
		
	}
	else if(controllername == 'roles')	
		var messageAlert = 'Are you sure you want to delete the selected role name?';
	else if(controllername == 'categories')
		var messageAlert = 'Documents added to the selected category will also be deleted. Are you sure you want to delete the category?';
	else
	{
		
		if($.inArray(controllername,configurationsArr) != -1)
		{
			var messageAlert = 'If the selected '+mdgdta+' is used in the system, it will be unset. Are you sure you want to delete this '+mdgdta+'?';
		}
		else
		{
			var messageAlert = 'Are you sure you want to delete the selected '+mdgdta+'? ';
		}
	}
	
	jConfirm(messageAlert, "Delete "+msgdta, function(r) {

		if(r==true)
		{
			if(controllername == 'candidatedetails')
			{
				$.post(base_url+"/candidatedetails/chkcandidate",{id:objid},function(data){
					if(data.status == 'no')
					{
						successmessage_changestatus('Candidate cannot be deleted.','error',controllername);
					}
					else 
					{
						$.ajax({
							url: base_url+"/"+controllername+"/delete",   
							type : 'POST',
							data: 'objid='+objid,
							dataType: 'json',
									success : function(response)
									{	
										successmessage_changestatus(response['message'],response['msgtype'],controllername);
										
										if(response['flagtype']=='process')
											location.reload();                                                                                
										else
											getAjaxgridData(controllername);		    	        	
									}
								});
					}
				},'json');
			}
			else
			{
				//console.log( base_url+"/"+controllername+"/delete"); return;
				$.ajax({
					url: base_url+"/"+controllername+"/delete",   
					type : 'POST',
					data: 'objid='+objid,
					beforeSend: function () {
						$.blockUI({ width:'50px',message: $("#spinner").html() });
					},
					dataType: 'json',
					success : function(response)
					{	
						successmessage_changestatus(response['message'],response['msgtype'],controllername);
						if(response['flagtype']=='process')
						{
							if(response['redirect']=='no')
								return false;
							else
								location.reload();
						}
						else if($.trim(response['flagtype']) == 'sd_request')
						{
							window.location = base_url+"/"+controllername;
						}
						else{
							getAjaxgridData(controllername);		    	        	
						}
					}
				});
			}
		}
		   else 
		   {

		   }
		});
	 
		 
}


function cancelLeaveWithComment () {
	var comment = $.trim($('#leave_comment').val());
	if(comment == "")
	{
		$('#leave_comment_err').html("Comment is required");
		return;
	}
	var objid = $('#objectId').val();
	var controllername = $('#controllername').val();
	
	$.ajax({
					url: base_url+"/"+controllername+"/delete",   
					type : 'POST',
					data: 'objid='+objid+'&comment='+comment,
					beforeSend: function () {
						$.blockUI({ width:'50px',message: $("#spinner").html() });
					},
					dataType: 'json',
					success : function(response)
					{   
						successmessage_changestatus(response['message'],response['msgtype'],controllername);
						if(response['flagtype']=='process')
						{
							if(response['redirect']=='no')
								return false;
							else
								location.reload();
						}
						else if($.trim(response['flagtype']) == 'sd_request')
						{
							window.location = base_url+"/"+controllername;
						}
						else{
							getAjaxgridData(controllername);
						}
						$('#cancelleaverequest').dialog('close');
					}
				});
}

function changeManager(url,objid){
	var controllername = "myemployees";
	var messageAlert = 'Are you sure you want to change the reporting manager?';
	$("#changemanagerrequest").dialog({
								open: function(event, ui) {
									 $("#objectId").val(objid);
									 $("#controllername").val(controllername);
									 $("#url").val(url);
									  
									 },
								draggable:false, 
								resizable: false,
								height:'auto',
								title: "Change: Reporting Manager",
								modal: true, 
								close: function( event, ui ) {
									  //closedialog('');
									  $("#changemanagerrequest").hide();
								}
							});
}

function submitChangeManager(){
	var url = $('#url').val();
	objectId
	var id = $('#objectId').val();
	var newRMid = $('#reporting_manager').val();
	var controllername = "myemployees";
	if(newRMid == ""){
		$('#new_rm_err').html("Please select new reporting manager");
		setTimeout(function(){$('#new_rm_err').html("");}, 5000);
	 	return;
	}
	$.ajax({
					url: url,   
					type : 'POST',
					data: 'id='+id+'&newRM='+newRMid,
					beforeSend: function () {
						$.blockUI({ width:'50px',message: $("#spinner").html() });
					},
					dataType: 'json',
					success : function(response)
					{   console.log(response); //return;
						if(response.success == 1 || response.success == '1' ) {
							location.reload();
						}
						else {
							alert("Invalid request!! Please reload the page.");
						}
						// //successmessage_changestatus(response['message'],response['msgtype'],controllername);
						// if(response['flagtype']=='process')
						// {
						// 	if(response['redirect']=='no')
						// 		return false;
						// 	else
						// 		location.reload();
						// }
						// else if($.trim(response['flagtype']) == 'sd_request')
						// {
						// 	window.location = url;
						// }
						// else{
						// 	getAjaxgridData(controllername);
						// }
						// $('#cancelleaverequest').dialog('close');
					}
				});
}