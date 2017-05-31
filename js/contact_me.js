/* =============================================================
 * contact_me.js v1.0.0
 * ETCHamac.com
 * =============================================================
 * Copyright 2017 ETCHamac
 * Code modified from http://startbootstrap.com/template-overviews/creative
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */

var reader, formdata = new FormData();

$(function() {

    // Set up the CSRF token value
    csrf = function(){
      $.ajax({url:"csrf/csrf.php",
        success: function(token){
          $('input[name="csrf_token"]').val(token);
        }
      });
    }
    csrf();

    // Clear form and reset labels
    resetForm = function(){
      $('#contactForm').trigger("reset");
      $('#contactForm .floating-label-form-group label').css({'top':'2em','opacity':'0'})
      $('input[name=project_size]').val(0);
      $('.range-slider__value').html(0);

      // Reset the file upload
      formdata = new FormData();
      $('#inputFile').attr('data-title', 'Drag and drop a file')
      $('.btn.upload').html('send').removeClass('disabled')
    };

    // Bind Validation script to form
    $("#contactForm input,#contactForm textarea").jqBootstrapValidation({
        preventSubmit: true,
        submitError: function($form, event, errors) {
            // additional error messages or events
        },

        submitSuccess: function($form, event) {
            // Disabled send button
            $('.btn.upload').html('sending...').addClass('disabled')

            event.preventDefault(); // prevent default submit behaviour
            // get values from FORM
            var name         = $("input#name").val();
            var email        = $("input#email").val();
            var phone        = $("input#phone").val();
            var procurement  = $('input[name=procurement]').val();
            var project_size = $('input[name=project_size]').val() || "N/A";
            var product_type = $('input[name=product_type]').val() || "N/A";
            var message      = $("textarea#message").val() || "No message supplied!";
            var csrf_token   = $('input[name="csrf_token"]').val();

            // Create formdata object with file upload to send
            formdata.append('name',name);
            formdata.append('phone',phone);
            formdata.append('email',email);
            formdata.append('message',message);
            formdata.append('csrf_token',csrf_token);
            formdata.append('procurement',procurement);
            formdata.append('project_size',project_size);
            formdata.append('product_type',product_type);

            // Send request
            $.ajax({
                url: "mail/mailer.php",
                type: "POST",
                data: formdata,
                cache: false,
                processData: false,
                contentType: false,
                success: function(data) {
                    response = JSON.parse(data);

                    if ( response.code == "200" ){
                      // Success message
                      $('#success')
                          .html("<div class='alert alert-success alert-dismissable fade show'>");

                      $('#success > .alert-success')
                          .html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                          .append("</button>");

                      $('#success > .alert-success')
                          .append(response.msg); //"Your message has been sent."

                      $('#success > .alert-success')
                          .append('</div>');

                      //clear all fields
                      resetForm()

                      // Set new CSFR token
                      csrf();
                    }else{
                      // Fail message
                      $('#success')
                          .html("<div class='alert alert-danger alert-dismissable fade show'>");

                      $('#success > .alert-danger')
                          .html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                          .append("</button>");

                      $('#success > .alert-danger')
                          .append(response.msg);

                      $('#success > .alert-danger')
                          .append('</div>');

                      //clear all fields
                      resetForm()
                    }
                },
                error: function(msg) {
                    // Fail message mail script not found or server error
                    $('#success')
                        .html("<div class='alert alert-danger alert-dismissable fade show'>");

                    $('#success > .alert-danger')
                        .html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                        .append("</button>");

                    $('#success > .alert-danger')
                        .append("Sorry, it seems that my mail server is not responding. Please try again later!");

                    $('#success > .alert-danger')
                        .append('</div>');

                    //clear all fields
                    resetForm()
                },
            });
        },
        filter: function() {
            return $(this).is(":visible");
        },
    });

    $("a[data-toggle=\"tab\"]").click(function(e) {
        e.preventDefault();
        $(this).tab("show");
    });


});



/*When clicking on Full hide fail/success boxes */
$('#name').focus(function() {
    $('#success').html('');
});
