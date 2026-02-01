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


        // 3. Cấu hình Headers cho Stream
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Tắt buffer cho Nginx
        header("X-Chat-Session-Id: $session_id"); // Gửi Session ID qua header
        header('Access-Control-Expose-Headers: X-Chat-Session-Id');

        // 4. Lấy cấu hình FastAPI
        $settings = get_option('ai_chat_settings');
        $api_url = rtrim($settings['ai_service_url'], '/') . '/api/chat';
        $token = $settings['permanent_access_token'];

        // 5. Gọi FastAPI bằng cURL Stream
        $full_ai_response = "";
        $ch = curl_init($api_url);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'session_id' => strval($session_id),
            'input' => $message
        ]));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);

        // Hàm callback xử lý từng chunk dữ liệu trả về từ FastAPI
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) use (&$full_ai_response) {
            $full_ai_response .= $chunk;

            // Gửi chunk này về trình duyệt ngay lập tức
            echo $chunk;

            if (ob_get_level() > 0)
                ob_flush();
            flush();

            return strlen($chunk);
        });

        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // 6. Lưu câu trả lời hoàn chỉnh của AI vào DB
        if ($http_code === 200 && !empty($full_ai_response)) {
            save_ai_chat_message($session_id, 'assistant', $full_ai_response);
        }

        exit;
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