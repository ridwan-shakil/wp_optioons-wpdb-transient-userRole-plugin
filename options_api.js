; (function ($) {
    $(document).ready(function () {
        $('.action-btn').on('click', function (e) {
            e.preventDefault();
            var task = $(this).data('task');    // get data from that button 
            $.post(my_ajax_obj.ajax_url, {      //POST request
                _ajax_nonce: my_ajax_obj.nonce, //nonce
                action: "rs_actions",           //action
                data: task                      //data
            }, function (data) {                //callback
                $('.result').text(data);
            }
            );



        });
    });
})(jQuery);




