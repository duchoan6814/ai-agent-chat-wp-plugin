<?php

class API_Chat_AI
{
    public static function register_routes()
    {
        register_rest_route('ai-chat/v1', '/send-message', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'handle_send_message'),
            'permission_callback' => '__return_true',
        ));
    }

    public static function handle_send_message($request): WP_Error
    {
        $params = $request->get_params();

        $message = sanitize_text_field($params['message'] ?? '');
        $session_id = intval($params['session_id'] ?? 0);

        if (empty($message)) {
            return new WP_Error('no_message', 'Tin nhắn không được để trống', ['status' => 400]);
        }

        // Thực hiện logic lưu DB và gọi AI ở đây...
        // $reply = MyAIHelper::get_response($message);

        return new WP_Error('no_message', 'Tin nhắn không được để trống', ['status' => 400]);

        return new WP_REST_Response([
            'status' => 'error',
            'data' => 'AI trả lời: ' . $message,
        ], 200);
    }
}