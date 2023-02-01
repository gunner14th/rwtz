( function( $ ) {
    $( document ).ready(function() {

        $('.mob_menu_btn').click(function (event) {
            if ($(".main_wrapper").hasClass("nav-open")) {
                $(".main_wrapper").removeClass('nav-open');
                $("body").removeClass("hidden_body");
                $('.js_nav_open').hide();
            } else {
                $(".main_wrapper").addClass('nav-open');
                $("body").addClass("hidden_body");
                $('.js_nav_open').show();
            }
        });

        $(".js_nav_open").click(function (event) {
            $('.main_wrapper').removeClass('nav-open');
            $('body').removeClass('hidden_body');
            $(this).hide();
        })

        $('header nav ul li a').click(function (event) {
            $(".main_wrapper").removeClass('nav-open');
            $("body").removeClass('hidden_body');
            $('.js_nav_open').hide();
        });

        $('header nav .close_btn').click(function (event) {
            $(".main_wrapper").removeClass('nav-open');
            $("body").removeClass('hidden_body');
            $('.js_nav_open').hide();
        });

        // $('select.select2').select2();

        $('.anchor[href*="#"]:not([href="#"])').click(function () {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top
                    }, 1000)
                    return false
                }
            }
        });

        function apply_form_display (uuid) {
            Swal.fire({
                html:
                    '<form action="" method="" id="applyForm" class="form swal_form">' +
                    '<p class="title">Send your CV</p>' +
                    '<input id="full_name" type="text" name="name" placeholder="Name" required>'+
                    '<input id="phone_number" type="tel" name="tel" class="phone_mask" placeholder="Phone" required>'+
                    '<input id="email_field" type="email" name="email" placeholder="Email" required>'+
                    ' <div class="file_input_group">\n' +
                    '   <span class="default_label">Add your CV file</span>' +
                    '   <span class="file_name"></span>' +
                    '   <input type="file" id="file-input" name="file">\n' +
                    '   <label for="file-input">' +
                    '       <span class="label_ico"></span>' +
                    '   </label>\n' +
                    '   <span class="remove"></span>' +
                    ' </div>' +
                    '<button class="button" type="submit">Send</button>' +
                    '<input type="hidden" name="job_uuid" value="' + uuid + '">' +
                    '</form>',
                showConfirmButton: false,
                showCloseButton: true,
            });

            var $file = $('#file-input'),
                $label = $file.next('label'),
                $labelIcon = $label.find('.label_ico'),
                $labelRemove = $file.parent().find('.remove'),
                $fileName = $file.parent().find('.file_name'),
                $labelDefault = $file.parent().find('.default_label');

            // on file change
            $file.on('change', function(){
                var fileName = $file.val().split( '\\' ).pop();
                var fileInput = document.getElementById('file-input');
                var filePath = fileInput.value;
                // Allowing file type
                var allowedExtensions = /(\.pdf)$/i;
                if (!allowedExtensions.exec(filePath)) {
                    Swal.showValidationMessage('Please add a PDF file.');
                    fileInput.value = '';
                }
                else {
                    $('.swal2-validation-message').hide();
                    if (fileName) {
                        $fileName.text(fileName);
                        $labelRemove.show();
                        $labelIcon.hide();
                        $labelDefault.hide();
                    } else {
                        $fileName.text("");
                        $labelRemove.hide();
                        $labelIcon.show();
                        $labelDefault.show();
                    }
                }
            });

            // Remove file
            $labelRemove.on('click', function(event){
                $file.val("");
                $fileName.text("");
                $labelRemove.hide();
                $labelIcon.show();
                $labelDefault.show();
            });

            $('#full_name').on('change', function () {
                var regName = /^[a-zA-Z]+ [a-zA-Z]+$/;
                var name_val = $(this).val();
                if( !regName.test(name_val) ) {
                    Swal.showValidationMessage('Please enter your full name (first & last name).');
                    $(this).focus();
                }
                else {
                    $('.swal2-validation-message').hide();
                }
            });

            $('#phone_number').on('change', function () {
                var regName = /^\d{9,12}$/;
                var phone_val = $(this).val();
                if( !regName.test(phone_val) ) {
                    Swal.showValidationMessage('Please enter correct phone number.');
                    $(this).focus();
                }
                else {
                    $('.swal2-validation-message').hide();
                }
            });

            $('#email_field').on('change', function () {
                var regName = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                var email_val = $(this).val();
                if( !regName.test(email_val) ) {
                    Swal.showValidationMessage('Please enter correct email.');
                    $(this).focus();
                }
                else {
                    $('.swal2-validation-message').hide();
                }
            });
        }

        function refer_redirection (uuid) {
            var refURL = 'https://api.smartrecruiters.com/v1/companies/Flink3/postings/' + uuid;
            fetch(refURL)
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data.uuid === uuid && data.referralUrl) window.location.href = data.referralUrl;
                });
        }

        $('body').on('submit', 'form#applyForm', function (event) {
            event.preventDefault();
            var full_name = $('#full_name').val().split(' ');
            var first_name = full_name[0];
            var last_name = full_name[1];
            var phone_number = $('#phone_number').val();
            var email_field = $('#email_field').val();
            var job_uuid = $(this).find('input[name=job_uuid]').val();
            var fileInput = document.getElementById('file-input');
            if ( fileInput.files && fileInput.files[0] ) {
                var fileName = fileInput.value.split( '\\' ).pop();
                var reader = new FileReader();
                reader.onload = function () {
                    var file_base64 = reader.result
                        .replace('data:', '')
                        .replace(/^.+,/, '');
                    if (file_base64 && job_uuid) {
                        var url = 'https://api.smartrecruiters.com/postings/' + job_uuid + '/candidates';
                        var data = {
                            "firstName": first_name,
                            "lastName": last_name,
                            "email": email_field,
                            "phoneNumber": phone_number,
                            "resume": {
                                "fileName": fileName,
                                "mimeType": "application/pdf",
                                "fileContent": file_base64
                            }
                        };
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'accept': 'application/json',
                                'x-smarttoken': 'DCRA1-f3af1ed8fa474d599c316c4c561ca061',
                                'content-type': 'application/json'
                            },
                            body: JSON.stringify(data)
                        }).then(function (response) {
                            return response.json();
                        })
                        .then(function (data) {
                            if ( data.id && data.createdOn && data.candidatePortalUrl ) {
                                Swal.fire({
                                    html:
                                        '<div class="swal-message-wrap">' +
                                        '<p class="message-title">Your CV sended!</p>' +
                                        '</div>',
                                    showConfirmButton: false,
                                    showCloseButton: true,
                                });
                            }
                            else {
                                Swal.fire({
                                    html:
                                        '<div class="message-wrap">' +
                                        '<p class="message-title error">Something went wrong, please try again.</p>' +
                                        '</div>',
                                    showConfirmButton: false,
                                    showCloseButton: true,
                                });
                            }
                        });
                    }
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
            else {
                Swal.showValidationMessage('Please add your CV.');
            }
        });

        $('table.position_table').on('click', 'tr', function (event){
            if ( $(event.target).hasClass('button apply_btn') ) {
                const uuid = $(event.currentTarget).data('uuid');
                if ( uuid ) apply_form_display(uuid);
            }
            else if ( $(event.target).hasClass('button referral_btn') ) {
                const uuid = $(event.currentTarget).data('uuid');
                if ( uuid ) refer_redirection(uuid);
            }
            else if ( $(event.target).hasClass('link_chain_btn') ) {
                navigator.clipboard.writeText($(event.target).data('url')).then(function () {
                    $(event.target).find('span.share-notation').text('Copied');
                }, function(err) {
                    console.error('Async: Could not copy text: ', err);
                });
            }
            else {
                window.location.href = $(event.currentTarget).data('url');
            }
        });

        $('.single-position button.apply_btn').click(function () {
            const uuid = $(this).data('uuid');
            apply_form_display(uuid);
        });

        $('.single-position button.referral_btn').click(function () {
            const uuid = $(this).data('uuid');
            refer_form_display(uuid);
        });

        $('.position_detail span.link_chain_btn').click(function (event) {
            navigator.clipboard.writeText($(event.target).data('url')).then(function () {
                $(event.target).find('span.share-notation').text('Copied');
            }, function(err) {
                console.error('Async: Could not copy text: ', err);
            });
        });

        // Show more for items loop
        $('.page-template-jobs .show_more_btn').click(function (e) {
            e.preventDefault();
            $(".page-template-jobs .position_table tbody tr:hidden").slice(0, 5).fadeIn();
            if ( $(this).parent().find(".position_table tbody tr:hidden").length < 1 ) $(this).fadeOut();
        });

        // Show more for Home Page -> Life section
        $('body.home .blog_section .btn_center_wrap a.show_more_btn').click(function (e) {
            e.preventDefault();
            $(this).parents(".blog_section").find(".blog_list_item:hidden").slice(0, 3).fadeIn();
            if ( $(this).parents('.blog_section').find(".blog_list_item:hidden").length < 1 ) $(this).fadeOut();
        });

        // Home Page -> Life section -> Show item text
        $('.blog_list .blog_list_item a.show_more_btn').click(function (e) {
            e.preventDefault();
            $(this).parents('.blog_list_item').find('.item-text').slideToggle();
        });

        // AJAX Filter posts by category
        $('.page-template-jobs .categories_list .categories_list_item').click( function () {
            const term_slug = $(this).data('term_slug');
            $('.page-template-jobs .categories_list .categories_list_item').removeClass('chosen');
            $(this).addClass('chosen');
            const ajax_data = {
                action: 'filter_jobs_by_cat',
                nonce:	front_data.nonce,
                term_slug: term_slug
            };
            $.ajax({
                url: front_data.ajaxurl,
                type: 'post',
                dataType: 'json',
                data: ajax_data,
                success: function(response) {
                    var jobs_list = $('.page-template-jobs .position_table tbody');
                    if ( response.status === 200 ) {
                        jobs_list.fadeOut(function () {
                            jobs_list.find('tr').remove();
                            jobs_list.html(response.posts).fadeIn();
                            if ( $('.page-template-jobs .position_table tbody tr').length <= 5 )
                                $('.page-template-jobs .show_more_btn').fadeOut();
                            else $('.page-template-jobs .show_more_btn').fadeIn();
                        });
                    }
                    else if (response.status === 201) {
                        jobs_list.html(response.message);
                    }
                    else {
                        jobs_list.html(response.message);
                    }
                }
            });
        });

    });
}( jQuery ) );