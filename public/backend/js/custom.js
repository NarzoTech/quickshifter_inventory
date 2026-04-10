(function ($) {
    "use strict";
    $(document).ready(function () {
        tinymce.init({
            selector: ".summernote",
            plugins:
                "anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount ",
            toolbar:
                "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat",
            tinycomments_mode: "embedded",
            tinycomments_author: "Author name",
            mergetags_list: [
                {
                    value: "First.Name",
                    title: "First Name",
                },
                {
                    value: "Email",
                    title: "Email",
                },
            ],
        });
        $(".select2").select2();

        $(".tags").tagify();


        $(".datepicker").datepicker({
            format: "dd-mm-yyyy",
            startDate: -Infinity,
            todayHighlight: true,
            todayBtn: "linked"
        }).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });
        $(".clockpicker").clockpicker();

    });

    $("#setLanguageHeader").on("change", function (e) {
        this.submit();
    });


    //======NICE SELECT=======
    $('select:not(.select2)').niceSelect();


    //======STICKY SIDEBAR=======
    $(".sticky_sidebar").stickit({
        // top: 90,
    })


})(jQuery);


// tostr options
const options = {
    "closeButton": true,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-bottom-center",
    "preventDuplicates": true,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}

// Throttle countdown toastr
function showThrottleCountdown(seconds) {
    var remaining = Math.ceil(seconds);
    var $toast = toastr.error(
        "Please wait " + remaining + " seconds before submitting again.",
        '',
        $.extend({}, options, {
            timeOut: remaining * 1000,
            extendedTimeOut: 0,
            progressBar: true,
            preventDuplicates: true,
            onCloseClick: function () { clearInterval(interval); }
        })
    );

    var interval = setInterval(function () {
        remaining--;
        if (remaining <= 0) {
            clearInterval(interval);
            toastr.clear($toast);
        } else {
            $toast.find('.toast-message').text(
                "Please wait " + remaining + " seconds before submitting again."
            );
        }
    }, 1000);
}

// Global AJAX handler for 429 throttle responses
$(document).ajaxError(function (event, jqXHR) {
    if (jqXHR.status === 429 && jqXHR.responseJSON && jqXHR.responseJSON.throttled) {
        showThrottleCountdown(jqXHR.responseJSON.remaining_seconds);
    }
});

// Global AJAX handler for successful write responses — show countdown
$(document).ajaxSuccess(function (event, jqXHR) {
    var data = jqXHR.responseJSON;
    if (data && data.throttle_cooldown) {
        showThrottleCountdown(data.throttle_cooldown);
    }
});
