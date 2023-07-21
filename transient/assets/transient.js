; (function ($) {
    $(document).ready(function () {
        $(".transient-btn").click(function (e) {
            e.preventDefault();
            var action = $(this).data('action');
            $.post(rs_transient_obj.ajax_url, {      //POST request
                nonce: rs_transient_obj.nonce, //nonce
                action: "rs_transient_actions",                //action
                data: action                          //data
            }, function (data) {                 //callback
                $('.transient-result').text(data);
            }
            );
        });


    });
})(jQuery);