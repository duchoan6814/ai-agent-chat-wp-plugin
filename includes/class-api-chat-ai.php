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

        register_rest_route('ai-chat/v1', '/get-current-session', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'handle_get_current_session'),
            'permission_callback' => '__return_true',
        ));
    }

    public static function handle_send_message($request)
    {
        $params = $request->get_params();
        $visitor_id = sanitize_text_field($request->get_header('x-visitor-id') ?? '');
        if (empty($visitor_id)) {
            return new WP_Error('no_visitor_id', 'Thiếu visitor_id', ['status' => 400]);
        }

        $message = sanitize_text_field($params['message'] ?? '');
        $session_id = intval($params['session_id'] ?? 0);

        if (empty($message)) {
            return new WP_Error('no_message', 'Tin nhắn không được để trống', ['status' => 400]);
        }

        if ($session_id <= 0) {
            $session_id = create_ai_chat_session(visitor_id: $visitor_id, title: sanitize_message($message));
        }

        // *: Save user message to DB
        save_ai_chat_message($session_id, 'user', $message);


        // TODO: Thực hiện stream gọi AI ở đây và nhận phản hồi

        // TODO: Save AI response to DB
        save_ai_chat_message($session_id, 'assistant', 'AI trả lời: ' . $message);

        $response = new WP_REST_Response([
            'status' => 'success',
            'data' => 'AI trả lời: ' . $message,
        ], 200);

        // CHÈN SESSION ID VÀO HEADER
        $response->header('X-Chat-Session-Id', $session_id);

        // Lưu ý quan trọng: Phải cho phép trình duyệt đọc Header này (CORS)
        $response->header('Access-Control-Expose-Headers', 'X-Chat-Session-Id');

        return $response;
    }


    public static function handle_get_current_session($request)
    {
        try {
            $visitor_id = sanitize_text_field($request->get_header('x-visitor-id') ?? '');

            if (empty($visitor_id)) {
                return new WP_Error('no_visitor_id', 'Thiếu visitor_id', ['status' => 400]);
            }


            global $wpdb;
            $table_sessions = $wpdb->prefix . 'ai_chat_sessions';
            $session = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_sessions WHERE visitor_fingerprint = %s AND is_active = 1 ORDER BY created_at DESC LIMIT 1",
                $visitor_id
            ));

            if (!$session) {
                return new WP_REST_Response([
                    'status' => 'success',
                    'data' => null,
                ], 200);
            }

            return new WP_REST_Response([
                'status' => 'success',
                'data' => $session->id ?? null,
            ], 200);

        } catch (\Throwable $th) {
            //throw $th;
            return new WP_Error('session_error', 'Lỗi khi lấy phiên hiện tại', ['status' => 500]);
        }
    }
}