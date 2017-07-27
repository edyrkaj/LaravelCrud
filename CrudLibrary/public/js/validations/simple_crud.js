var SimpleCrud = function () {
    "use strict";
    var runSetDefaultValidation = function () {
        $.validator.setDefaults({
            errorElement: "span", // contain the error msg in a small tag
            errorClass: 'help-block',
            errorPlacement: function (error, element) {// render error placement for each input type
                var element_type = $(element).prop('nodeName');

                if (element.attr("type") == "radio" || element.attr("type") == "checkbox") {// for chosen elements, need to insert the error after the chosen container
                    error.insertAfter($(element).closest('.form-group').children('div').children().last());
                } else if (element.attr("name") == "card_expiry_mm" || element.attr("name") == "card_expiry_yyyy") {
                    error.appendTo($(element).closest('.form-group').children('div'));
                } else if(element_type == "SELECT") {
                    error.appendTo($(element).closest('.form-group').children('span'));
                } else {
                    error.insertAfter(element);
                    // for other inputs, just perform default behavior
                }
            },
            ignore: [],
            success: function (label, element) {
                label.addClass('help-block valid');
                // mark the current input as valid and display OK icon
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
            },
            highlight: function (element) {
                $(element).closest('.help-block')
                    .removeClass('valid');
                // display add error class icon
                $(element).closest('.form-group')
                    .removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
            },
            unhighlight: function (element) {// revert the change done by hightlight
                $(element).closest('.form-group').removeClass('has-error');
                // set error class to the control group
            }
        });
    };

    var runCrudValidation = function () {
        var form = $('form.simple_crud');
        var errorHandler = $('.errorHandler', form);
        var successHandler = $('.successHandler', form);

        form.validate({
            submitHandler: function (form, event) {
                event.preventDefault();
                errorHandler.hide();

                // Add Loading
                $('form.simple_crud').addClass('load1').addClass('csspinner');

                // Process with submit ...
                form.submit();

                setTimeout(function(){
                    return true;
                }, 10000);
            },
            invalidHandler: function (event, validator) {//display error alert on form submit
                successHandler.hide();
                errorHandler.show();
            }
        });

        // ON Reset Click Reset fields
        $('button[type="reset"]').click(function(){
            form.validate().resetForm();
            // Remove classes if form group has errors or success
            form.find('.form-group')
                .removeClass('has-error')
                .removeClass('has-success').find('.symbol').removeClass('ok').addClass('required');
        });

        $('span.symbol.required').each(function () {
            var element = $('#' + $(this).attr('data-field-id'));

            var validations = {
                required: true,
                messages: {
                    required: lang.required,
                }
            };

            element.rules('add', validations);

            // Check for extra VALIDATIONS
            if(element.data('validation')) {
                element.rules('add', element.data('validation'));
            }
        });
    };

    return {
        init: function () {
            runSetDefaultValidation();
            runCrudValidation();
        }
    };
}();