/**
 *  Function to clear input fields
 * @param caller
 * @constructor
 */
var ClearInputs = function (caller) {
	$(caller).closest('form').find("input.dropdown").select2('val', '');
	$(caller).find("select").select2('val', '');
	//Clear Form
	$(caller).closest('form').find("input[type=text],input[type=number],input[type=month], textarea").val("");
};

var CrudTable = function () {
	"use strict";
	//function to initiate DataTable
	//DataTable is a highly flexible tool, based upon the foundations of progressive enhancement,
	//which will add advanced interaction controls to any HTML table
	//For more information, please visit https://datatables.net/
	// Notification Toastr Options
/*	toastr.options = {
		"closeButton": true, "positionClass": "toast-top-full-width", "timeOut": "5000", "showMethod": "slideDown"
	};
	var runSetDefaultValidation = function () {
		$.validator.setDefaults({
			errorElement: "span", // contain the error msg in a small tag
			errorClass: 'help-block', errorPlacement: function (error, element) { // render error placement for each input type
				if (element.attr("type") == "radio" || element.attr("type") == "checkbox") { // for chosen elements, need to insert the error after the chosen container
					error.insertAfter($(element).closest('.form-group').children('div').children().last());
				} else {
					error.insertAfter(element);
					// for other inputs, just perform default behavior
				}
			}, ignore: ':hidden', success: function (label, element) {
				label.addClass('help-block valid');
				// mark the current input as valid and display OK icon
				$(element).closest('.form-group').removeClass('has-error');
			}, highlight: function (element) {
				$(element).closest('.help-block').removeClass('valid');
				// display OK icon
				$(element).closest('.form-group').addClass('has-error');
				// add the Bootstrap error class to the control group
			}, unhighlight: function (element) { // revert the change done by hightlight
				$(element).closest('.form-group').removeClass('has-error');
				// set error class to the control group
			}
		});
	};*/
	/**
	 * Crud Datatable
	 * @returns {undefined}
	 * 
	 */
	var runDataTable_crud = function () {
		$('#crud_table, .panel-tools').on('click', '.crud-action', function (e) {
			e.preventDefault();
			var url = $(this).attr('href');
			var postData = {
				id: $(this).attr('row-id')
			};
			// clear div content
			$('#simplecrud').html();
			$.ajax({
				url: url, type: "POST", data: postData, success: function (data) {
					$('#simplecrud').html(data);
					$.subview({
						content: "#simplecrud",
						onShow: function () {
							//Save Data
							$('#simplecrud form').validate({
								errorPlacement: function (error, element) {
									if (element.attr("type") == "file" || element.attr("type") == "radio" || element.attr("type") == "checkbox") { // for chosen elements, need to insert the error after the chosen container
										error.insertAfter($(element).closest('.form-group').children('div').children().last());
									} else {
										$(element).addClass('tooltips');
										$(element).attr('data-placement', 'top');
										$(element).attr('data-rel', 'tooltip');
										$(element).attr('data-original-title', error.text());
										$(element).tooltip();
									}
									//error.appendTo('#invalid-' + element.attr('id'));
								},
								submitHandler: function (form) {
									e.preventDefault();
									/* Ajax Save Data After Validation*/
									$.blockUI({
										message: '<i class="fa fa-spinner fa-spin"></i> Saving data entered...'
									});
									var postUrl = $('#simplecrud form').attr('data-url');
									// Initiate postData variable
									var postData = new FormData();
									// Manage Text | Number Inputs
									$.each($('#simplecrud input[type="text"],#simplecrud input[type="number"], #simplecrud input[type="password"]'), function () {
										if (typeof $(this).attr('name') !== "undefined") {
											postData.append($(this).attr('name'), $(this).val());
										}
									});
									// Manage Textarea used with CKEDITOR
									$.each($('#simplecrud textarea'), function () {
										if (typeof $(this).attr('name') !== "undefined") {
											var $name = $(this).attr('name');
											var $value = $().CKEditorValFor($name);
											postData.append($name, $value);
										}
									});
									// Manage Hidden Inputs
									$.each($('#simplecrud input[type="hidden"]'), function () {
										if (typeof $(this).attr('name') !== "undefined") {
											postData.append($(this).attr('name'), $(this).val());
										}
									});
									// Manage Date | Datetime | Month Inputs
									$.each($('#simplecrud input[type="date"], #simplecrud input[type="datetime"], #simplecrud input[type="month"]'), function () {
										if (typeof $(this).attr('name') !== "undefined") {
											postData.append($(this).attr('name'), $(this).val());
										}
									});
									// Manage File Inputs
									$.each($('input[type="file"]'), function () {
										if (typeof $(this).attr('name') !== "undefined") {
											postData.append($(this).attr('name'), $(this)[0].files[0]);
										}
									});
									// Manage File Select
									$.each($('#simplecrud select').not('#simplecrud select[name^="crud_table"]'), function () {
										if (typeof $(this).attr('name') !== "undefined") {
											postData.append($(this).attr('name'), $(this).val());
										}
									});
									// Call Ajax Post
									$.ajax({
										type: 'POST',
										url: postUrl,
										data: postData,
										cache: false,
										processData: false,
										contentType: false,
										dataType: "json",
										success: function (response) {
											$.unblockUI();
											if (response.type == "error") {
												var errormessage = '<div class="alert alert-block">';
												if ($.isArray(response.message)) {
													$.each(response.message, function (index) {
														errormessage += '<p>' + Number(index + 1) + '.' + response.message[index] + '</p>';
													});
												} else {
													errormessage += '<p>' + response.message + '</p>';
												}
												errormessage += '</div>';
												toastr.options.timeOut = 5000;
												toastr.error('<strong class="uppercase">' + lang.danger + '</strong>. ' + lang.check_data + errormessage);
												return false;
											} else {
												toastr.success('<strong class="uppercase">' + lang.success + '</strong> ' + lang.save_success);
												setTimeout(function () {
													$.hideSubview();
												}, 1000);
											}
										}
									});
								}
							});
							$('#simplecrud  span.symbol.required').each(function () {
								var element = $('#' + $(this).attr('data-field-id'));
								element.rules('add', {
									required: true, messages: {
										required: lang.required
									}
								});
							});
							if ($('textarea').length > 0) {
								var ckElement = $('textarea').attr('name');
								if (CKEDITOR.instances.ckElement) {
									var editor = CKEDITOR.instances[ckElement];
									if (editor) {
										editor.destroy(true);
									}
								}
								CKEDITOR.replace(ckElement);
							}

							$('select').select2();

							// Reset Clicked
							$(document).on('click', 'button[type="reset"]', function(){
								ClearInputs('#simplecrud');
							});
						}, onClose: function () {
							window.location.reload();
						}, onHide: function () {
							window.location.reload();
						}
					});
				}
			});
		});
		/**
		 * Crud Operation Delete
		 * @param  {row-id} Element Id to be deleted
		 *
		 */
		$('#crud_table').on('click', '.delete-row', function (e) {
			e.preventDefault();
			var url = $(this).attr('href');
			var postData = {
				method: 'delete', id: $(this).attr('row-id')
			};
			var nRow = $(this).parents('tr')[0];
			var actualEditingRow = nRow;
			bootbox.confirm(lang.confirm_delete, function (result) {
				if (result) {
					// Process to delete action
					$.blockUI({
						message: '<i class="fa fa-spinner fa-spin"></i> ' + lang.deleting
					});
					$.ajax({
						url: url, type: "POST", dataType: 'json', data: postData, success: function (response) {
							$.unblockUI();
							if (response.type == "error") {
								var errormessage = '<div class="alert alert-block">';
								if ($.isArray(response.message)) {
									$.each(response.message, function (index) {
										errormessage += '<p>' + Number(index + 1) + '.' + response.message[index] + '</p>';
									});
								} else {
									errormessage += '<p>' + response.message + '</p>';
								}
								errormessage += '</div>';
								toastr.options.timeOut = 5000;
								toastr.error('<strong class="uppercase">' + lang.danger + '</strong>. ' + lang.check_data + errormessage);
								return false;
							} else {
								toastr.success('<strong class="uppercase">' + lang.success + '</strong> ' + lang.process_success);
								oTable.fnDeleteRow(actualEditingRow);
								//if (oTable.fnSettings()._iDisplayStart > 0) {
								//	oTable.fnPageChange('last');
								//}
							}
						}
					});
				}
			});
		});
		/**
		 * Crud Operation Restore
		 * @param  {row-id} Element Id to be deleted
		 *
		 */
		$('#crud_table').on('click', '.restore-row', function (e) {
			e.preventDefault();
			var url = $(this).attr('href');
			var postData = {
				method: 'restore', id: $(this).attr('row-id')
			};
			var nRow = $(this).parents('tr')[0];
			bootbox.confirm(lang.confirm_restore, function (result) {
				if (result) {
					// Process to delete action
					$.blockUI({
						message: '<i class="fa fa-spinner fa-spin"></i> ' + lang.restoring
					});
					$.ajax({
						url: url, type: "POST", dataType: 'json', data: postData, success: function (response) {
							$.unblockUI();
							if (response.type == "error") {
								var errormessage = '<div class="alert alert-block">';
								if ($.isArray(response.message)) {
									$.each(response.message, function (index) {
										errormessage += '<p>' + Number(index + 1) + '.' + response.message[index] + '</p>';
									});
								} else {
									errormessage += '<p>' + response.message + '</p>';
								}
								errormessage += '</div>';
								toastr.options.timeOut = 5000;
								toastr.error('<strong class="uppercase">' + lang.danger + '</strong>. ' + lang.check_data + errormessage);
								return false;
							} else {
								toastr.success('<strong class="uppercase">' + lang.success + '</strong> ' + lang.process_success);
							}
						}
					});
				}
			});
		});
		/**
		 * Convert Table to DataTable using plugin jquery.DataTable
		 * @type {[type]}
		 *
		 */
		var oTable = $('#crud_table').dataTable({
			"bProcessing": false,
			"autoWidth": true,
			dom: 'T<"clear">lfrtip',
			tableTools: {
				"sSwfPath": "/rapido/assets/plugins/DataTables/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
				"aButtons": [{
					"sExtends": "xls", "sButtonText": "Excel", "sFileName": "*.xls", "oSelectorOpts": {page: "current"}
				},
				"pdf",
				"copy",
				"print"
				]
			},
			"aoColumnDefs": [{
				"aTargets": [0]
			}],
			"oLanguage": lang.oLanguage,
			"aaSorting": [],
			"aLengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, lang.all] // change per page values here
			], // set the initial value
			"iDisplayLength": 5
		});
		$('#crud_table_wrapper .dataTables_filter label').attr('style', 'padding-right: 1%;');
		//$('#crud_table_wrapper .dataTables_filter input').addClass("form-control input-sm").attr("placeholder", "Search");
		// modify table search input
		$('#crud_table_wrapper .dataTables_length label').attr("style", 'width: 20%;');
		$('#crud_table_wrapper .dataTables_length select').attr("style", 'width: 50%;');
		// modify table per page dropdown
		//$('#crud_table_wrapper .dataTables_length select').select2();
		// initialzie select2 dropdown
		$('#crud_table_column_toggler input[type="checkbox"]').change(function () {
			/* Get the DataTables object again - this is not a recreation, just a get of the object */
			var iCol = parseInt($(this).attr("data-column"));
			var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
			oTable.fnSetColumnVis(iCol, (bVis ? false : true));
		});
	};
	// Execute Functions
	return {
		//main function to initiate template pages
		init: function () {
			//runSetDefaultValidation();
			runDataTable_crud();
		}
	};
}();
