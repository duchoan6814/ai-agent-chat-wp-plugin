<?php

if (!class_exists("WP_List_Table")) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class AI_Chat_Session_Table extends WP_List_Table
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
            'cb' => '<input type="checkbox" />',
            "title" => "Tiêu đề",
            "visitor_fingerprint" => "Fingerprint",
            "is_active" => "Trạng thái",
            "created_at" => "Thời gian"
        ];
    }

    // Các cột có thể sắp xếp
    protected function get_sortable_columns()
    {
        return [
            'id' => ['id', true],
            'created_at' => ['created_at', false],
        ];
    }

    // Định nghĩa các bulk actions
    protected function get_bulk_actions()
    {
        return [
            // 'bulk-delete' => 'Xóa',
            // 'bulk-activate' => 'Kích hoạt',
            'bulk-deactivate' => 'Vô hiệu hóa',
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

    // Tùy biến hiển thị cho cột Tiêu đề (Click để xem chi tiết)
    public function column_title($item)
    {
        $url = admin_url('admin.php?page=ai-chat-detail&id=' . $item->id);
        return sprintf('<strong><a href="%s">%s</a></strong>', esc_url($url), esc_html($item->title));
    }

    // Tùy biến hiển thị cho cột Trạng thái (Checkbox/Switch)
    public function column_is_active($item)
    {
        $checked = $item->is_active ? 'checked' : '';
        return sprintf(
            '<input type="checkbox" onclick="return false;" class="toggle-active" data-id="%d" %s>',
            $item->id,
            $checked
        );
    }

    // Chuẩn bị dữ liệu cho bảng
    public function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ai_chat_sessions';

        // Xử lý Sắp xếp
        $orderby = !empty($_GET['orderby']) ? sanitize_sql_orderby($_GET['orderby']) : 'id';
        $order = !empty($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';

        // Xử lý Phân trang
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        // Truy vấn dữ liệu
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
        $this->items = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d",
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