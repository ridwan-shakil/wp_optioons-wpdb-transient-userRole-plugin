; (function ($) {
    $(document).ready(function () {

        $('.user-role-btn').click(function (e) {
            e.preventDefault();
            var action = $(this).data('action');
            $.post(rs_user_roles_obj.ajax_url, {      //POST request
                nonce: rs_user_roles_obj.nonce, //nonce
                action: "rs_user_role_actions",           //action
                data: action                 //data
            }, function (data) {                //callback
                $('#user-role-result').text(data);
                // $('.wp-block-template-part').text(data);
            }
            );
        });

    });
})(jQuery);