<?php

if (!class_exists("WP_List_Table")) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class AI_Chat_Message_Table extends WP_List_Table
{

    function __construct()
    {
        parent::__construct(array(
            'singular' => 'record',
            'plural' => 'records',
            'ajax' => false
        ));
    }

    // Định nghĩa các cột của bảng
    public function get_columns()
    {
        return [
            "role" => "Vai trò",
            "content" => "Nội dung",
            "created_at" => "Thời gian"
        ];
    }

    // Các cột có thể sắp xếp
    protected function get_sortable_columns()
    {
        return [
            'created_at' => ['created_at', false],
        ];
    }


    // Cột hiển thị dữ liệu mặc định
    public function column_default($item, $column_name)
    {
        return esc_html($item->$column_name);
    }

    // Tùy biến hiển thị cho cột Checkbox
    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item->id);
    }


    // Chuẩn bị dữ liệu cho bảng
    public function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ai_chat_messages';

        // Xử lý Sắp xếp
        $orderby = !empty($_GET['orderby']) ? sanitize_sql_orderby($_GET['orderby']) : 'id';
        $order = !empty($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';

        // Xử lý Phân trang
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        $session_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Truy vấn dữ liệu
        $total_items = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $table_name WHERE session_id = %d", $session_id));
        $this->items = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE session_id = %d ORDER BY $orderby $order LIMIT %d OFFSET %d",
            $session_id,
            $per_page,
            $offset
        ));

        // Thiết lập tham số phân trang
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);


        $columns = $this->get_columns();
        $hidden = array(); // Các cột muốn ẩn
        $sortable = $this->get_sortable_columns();


        // 2. QUAN TRỌNG: Gán vào biến hệ thống của class
        $this->_column_headers = array($columns, $hidden, $sortable);
    }
}