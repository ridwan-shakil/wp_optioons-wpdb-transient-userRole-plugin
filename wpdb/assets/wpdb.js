; (function ($) {
    $(document).ready(function () {

        $(".wpdb-btn").click(function (e) {
            e.preventDefault();
            var wpdb_task = $(this).data('action');
            console.log(wpdb_task);
            $.post(wpdb_ajax_obj.ajax_url, {      //POST request
                nonce: wpdb_ajax_obj.nonce, //nonce
                action: "rs_wpdb_actions",           //action
                data: wpdb_task                      //data
            }, function (response) {                //callback
                $('.wpdb-result').text(response);
            }
            );
        });

    });
})(jQuery);