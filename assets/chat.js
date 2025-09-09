(function ($) {
    $(function () {
        var $chat = $('#livechat-ai .chat-window');
        var $form = $('#livechat-ai .chat-form');

        $form.on('submit', function (e) {
            e.preventDefault();
            var msg = $form.find('input[name="message"]').val();
            if (!msg) return;

            $chat.append('<div class="user">' + $('<div>').text(msg).html() + '</div>');
            $form.find('input').val('');

            var $wait = $('<div class="waiting">Even wachten…</div>');
            $chat.append($wait);

            $.post(LiveChatAI.ajaxUrl, {
                action: 'livechatai_send',
                message: msg
            }).done(function (res) {
                $wait.remove();
                if (res.success) {
                    $chat.append('<div class="bot">' + $('<div>').text(res.data.reply).html() + '</div>');
                } else {
                    $chat.append('<div class="error">' + $('<div>').text(res.data.error || "Er ging iets mis").html() + '</div>');
                }
            }).fail(function () {
                $wait.remove();
                $chat.append('<div class="error">Netwerkfout</div>');
            });
        });
    });
})(jQuery);
