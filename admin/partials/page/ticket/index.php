<?php
/**
 * 轻量级工单系统
 *
 * 功能：
 * - 自定义工单类型
 * - 前端提交工单
 * - 后台管理工单状态
 * - 工单回复
 */
if (!class_exists('MaBox_Ticket_System')) {
    class MaBox_Ticket_System {

        private static $config;
        private static $post_type = 'mabox_ticket';

        public static function run($config) {
            self::$config = $config;
            add_action('init', array(__CLASS__, 'register_post_type'));
            add_action('rest_api_init', array(__CLASS__, 'register_rest_routes'));
            add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_frontend_assets'));
            add_shortcode('mabox_ticket_form', array(__CLASS__, 'render_ticket_form'));
            add_filter('the_content', array(__CLASS__, 'add_ticket_status_to_content'));
        }

        public static function register_post_type() {
            register_post_type(self::$post_type, array(
                'labels' => array(
                    'name'          => '工单',
                    'singular_name' => '工单',
                    'add_new'       => '新建工单',
                    'edit_item'     => '编辑工单',
                    'view_item'     => '查看工单',
                    'search_items'  => '搜索工单',
                    'not_found'     => '未找到工单',
                ),
                'public'             => false,
                'show_ui'            => true,
                'show_in_menu'       => 'tools.php',
                'capability_type'    => 'post',
                'supports'           => array('title', 'editor', 'author', 'custom-fields'),
                'menu_icon'          => 'dashicons-tickets',
                'show_in_rest'       => true,
            ));

            register_post_meta(self::$post_type, 'mabox_ticket_status', array(
                'show_in_rest' => true,
                'single'       => true,
                'type'         => 'string',
                'default'      => 'open',
            ));

            register_post_meta(self::$post_type, 'mabox_ticket_email', array(
                'show_in_rest' => true,
                'single'       => true,
                'type'         => 'string',
            ));

            register_post_meta(self::$post_type, 'mabox_ticket_type', array(
                'show_in_rest' => true,
                'single'       => true,
                'type'         => 'string',
            ));
        }

        public static function register_rest_routes() {
            register_rest_route('mabox/v1', '/ticket/submit', array(
                'methods'             => 'POST',
                'callback'            => array(__CLASS__, 'rest_submit_ticket'),
                'permission_callback' => array(__CLASS__, 'verify_ticket_nonce'),
            ));

            register_rest_route('mabox/v1', '/ticket/(?P<id>\d+)/reply', array(
                'methods'             => 'POST',
                'callback'            => array(__CLASS__, 'rest_reply_ticket'),
                'permission_callback' => array(__CLASS__, 'check_admin_permission'),
            ));

            register_rest_route('mabox/v1', '/ticket/(?P<id>\d+)/status', array(
                'methods'             => 'POST',
                'callback'            => array(__CLASS__, 'rest_update_status'),
                'permission_callback' => array(__CLASS__, 'check_admin_permission'),
            ));
        }

        public static function rest_submit_ticket($request) {
            $title   = sanitize_text_field($request->get_param('title'));
            $content = sanitize_textarea_field($request->get_param('content'));
            $email   = sanitize_email($request->get_param('email'));
            $type    = sanitize_text_field($request->get_param('type'));

            if (empty($title) || empty($content)) {
                return new WP_REST_Response(array('success' => false, 'error' => '标题和内容不能为空'), 400);
            }

            $post_id = wp_insert_post(array(
                'post_title'   => $title,
                'post_content' => $content,
                'post_type'    => self::$post_type,
                'post_status'  => 'publish',
            ));

            if (is_wp_error($post_id)) {
                return new WP_REST_Response(array('success' => false, 'error' => $post_id->get_error_message()), 500);
            }

            update_post_meta($post_id, 'mabox_ticket_status', 'open');
            update_post_meta($post_id, 'mabox_ticket_email', $email);
            update_post_meta($post_id, 'mabox_ticket_type', $type);

            return new WP_REST_Response(array('success' => true, 'ticket_id' => $post_id), 200);
        }

        public static function rest_reply_ticket($request) {
            $ticket_id = intval($request->get_param('id'));
            $content   = sanitize_textarea_field($request->get_param('content'));

            if (empty($content)) {
                return array('success' => false, 'error' => '回复内容不能为空');
            }

            $post = get_post($ticket_id);
            if (!$post || $post->post_type !== self::$post_type) {
                return array('success' => false, 'error' => '工单不存在');
            }

            $reply_id = wp_insert_post(array(
                'post_title'   => '回复：' . $post->post_title,
                'post_content' => $content,
                'post_type'    => self::$post_type,
                'post_parent'  => $ticket_id,
                'post_status'  => 'publish',
            ));

            if (is_wp_error($reply_id)) {
                return array('success' => false, 'error' => $reply_id->get_error_message());
            }

            return array('success' => true, 'reply_id' => $reply_id);
        }

        public static function rest_update_status($request) {
            $ticket_id = intval($request->get_param('id'));
            $status    = sanitize_text_field($request->get_param('status'));

            $valid_statuses = array('open', 'processing', 'resolved', 'closed');
            if (!in_array($status, $valid_statuses)) {
                return array('success' => false, 'error' => '无效的状态值');
            }

            update_post_meta($ticket_id, 'mabox_ticket_status', $status);
            return array('success' => true);
        }

        public static function render_ticket_form($atts) {
            $atts = shortcode_atts(array(
                'show' => 'form',
            ), $atts);

            ob_start();
            ?>
            <div class="mabox-ticket-form">
                <h3>提交工单</h3>
                <form id="mabox-ticket-submit">
                    <p>
                        <label>工单类型</label>
                        <select name="type" required>
                            <option value="bug">Bug 报告</option>
                            <option value="feature">功能建议</option>
                            <option value="support">技术支持</option>
                            <option value="other">其他</option>
                        </select>
                    </p>
                    <p>
                        <label>标题</label>
                        <input type="text" name="title" required placeholder="简要描述问题">
                    </p>
                    <p>
                        <label>邮箱</label>
                        <input type="email" name="email" placeholder="用于接收回复通知">
                    </p>
                    <p>
                        <label>详细内容</label>
                        <textarea name="content" rows="5" required placeholder="请详细描述您遇到的问题..."></textarea>
                    </p>
                    <p>
                        <button type="submit" class="button button-primary">提交工单</button>
                    </p>
                </form>
                <div id="mabox-ticket-result"></div>
            </div>
                <script>
            document.getElementById('mabox-ticket-submit').addEventListener('submit', function(e) {
                e.preventDefault();
                var form = e.target;
                var data = new FormData(form);
                var obj = {};
                data.forEach(function(value, key) { obj[key] = value; });

                fetch('/wp-json/mabox/v1/ticket/submit', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-Mabox-Ticket-Nonce': '<?php echo esc_js(wp_create_nonce("mabox_ticket_submit")); ?>'
                    },
                    body: JSON.stringify(obj)
                })
                .then(function(res) { return res.json(); })
                .then(function(result) {
                    var resultDiv = document.getElementById('mabox-ticket-result');
                    if (result.success) {
                        resultDiv.innerHTML = '<p style="color:green;">工单已提交，工单号：' + result.ticket_id + '</p>';
                        form.reset();
                    } else {
                        resultDiv.innerHTML = '<p style="color:red;">提交失败：' + result.error + '</p>';
                    }
                });
            });
            </script>
            <?php
            return ob_get_clean();
        }

        public static function add_ticket_status_to_content($content) {
            if (is_singular(self::$post_type) && current_user_can('manage_options')) {
                $status = get_post_meta(get_the_ID(), 'mabox_ticket_status', true);
                $status_labels = array(
                    'open'       => '<span style="background:#e6f4ff;color:#1677ff;padding:2px 8px;border-radius:4px;">待处理</span>',
                    'processing' => '<span style="background:#fff7e6;color:#fa8c16;padding:2px 8px;border-radius:4px;">处理中</span>',
                    'resolved'   => '<span style="background:#f6ffed;color:#52c41a;padding:2px 8px;border-radius:4px;">已解决</span>',
                    'closed'     => '<span style="background:#f5f5f5;color:#999;padding:2px 8px;border-radius:4px;">已关闭</span>',
                );
                $label = !empty($status_labels[$status]) ? $status_labels[$status] : $status;
                $content = '<div style="margin-bottom:16px;">工单状态：' . $label . '</div>' . $content;
            }
            return $content;
        }

        public static function enqueue_frontend_assets() {
            if (is_singular(self::$post_type) || has_shortcode(get_post()->post_content, 'mabox_ticket_form')) {
                wp_add_inline_style('mabox-ticket-style', '.mabox-ticket-form{max-width:600px;margin:20px auto;padding:20px;background:#fff;border:1px solid #ddd;border-radius:8px;}.mabox-ticket-form p{margin-bottom:15px;}.mabox-ticket-form label{display:block;margin-bottom:5px;font-weight:bold;}.mabox-ticket-form input,.mabox-ticket-form select,.mabox-ticket-form textarea{width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;}');
                wp_register_style('mabox-ticket-style', false);
                wp_enqueue_style('mabox-ticket-style');
            }
        }

        public static function check_admin_permission() {
            return current_user_can('manage_options');
        }

        /**
         * 验证工单提交的 nonce（公开端点但需防 CSRF）
         */
        public static function verify_ticket_nonce($request) {
            $nonce = $request->get_header('x-mabox-ticket-nonce');
            if (empty($nonce)) {
                $nonce = $request->get_param('nonce');
            }
            return wp_verify_nonce($nonce, 'mabox_ticket_submit') !== false;
        }
    }
}
