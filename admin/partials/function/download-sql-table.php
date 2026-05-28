<?php
//下载指定数据库表内容
if (!class_exists('MaBox_Download_SQL_Table')) {
    class MaBox_Download_SQL_Table
    {
        /**
         * 敏感字段列表（用于脱敏）
         */
        private static $sensitive_fields = array(
            'user_pass', 'user_email', 'user_activation_key',
            'comment_author_email', 'comment_author_IP',
            'option_value', // 可能包含 API key 等敏感信息
            'meta_value',   // 可能包含 token、secret 等
        );

        /**
         * 默认允许的表名前缀（仅插件自有表）
         */
        private static $allowed_prefixes = array(
            'mabox_', 'wp_mabox_',
        );

        public static function run()
        {
        }

        /**
         * 检查表名是否在允许列表中
         */
        private static function is_table_allowed($table_name) {
            global $wpdb;

            // 插件自有表始终允许
            foreach (self::$allowed_prefixes as $prefix) {
                if (strpos($table_name, $prefix) === 0) {
                    return true;
                }
            }

            // WordPress 核心表允许部分
            $allowed_core_tables = array(
                $wpdb->posts, $wpdb->postmeta, $wpdb->terms, $wpdb->term_taxonomy,
                $wpdb->term_relationships, $wpdb->termmeta, $wpdb->comments,
                $wpdb->commentmeta,
            );

            return in_array($table_name, $allowed_core_tables, true);
        }

        /**
         * 检查字段是否敏感
         */
        private static function is_field_sensitive($field_name) {
            $sensitive_patterns = array(
                'pass', 'password', 'secret', 'token', 'key', 'api_key',
                'api_secret', 'access_token', 'refresh_token',
                'email', 'ip',
            );

            foreach ($sensitive_patterns as $pattern) {
                if (stripos($field_name, $pattern) !== false) {
                    return true;
                }
            }

            return false;
        }

        /**
         * 对敏感字段值进行脱敏
         */
        private static function mask_value($field_name, $value) {
            if (!self::is_field_sensitive($field_name)) {
                return $value;
            }

            if (empty($value)) {
                return $value;
            }

            // 邮箱脱敏
            if (stripos($field_name, 'email') !== false && is_email($value)) {
                $parts = explode('@', $value);
                return substr($parts[0], 0, 2) . '***@' . $parts[1];
            }

            // IP 地址脱敏
            if (stripos($field_name, 'ip') !== false) {
                return '***.***.***.' . substr($value, strrpos($value, '.') + 1);
            }

            // 密码/密钥类直接隐藏
            if (stripos($field_name, 'pass') !== false || stripos($field_name, 'secret') !== false || stripos($field_name, 'key') !== false) {
                return '***masked***';
            }

            // 其他敏感字段截断显示
            if (is_string($value) && strlen($value) > 8) {
                return substr($value, 0, 4) . '***' . substr($value, -4);
            }

            return '***masked***';
        }

        //获取所有的数据库表名
        public static function get_all_table_names()
        {
            global $wpdb;

            //管理员权限
            if (!current_user_can('manage_options')) {
                return wp_send_json_error(['error' => '非管理员，无权获取此内容', 'data' => []], 404);
            }

            // Nonce 验证
            check_ajax_referer('mabox_save_nonce', 'nonce');

            //获取所有表名
            $results = $wpdb->get_results("SHOW TABLES", ARRAY_N);

            $table_names = array();
            $allowed_tables = array();

            foreach ($results as $result) {
                $table_names[] = $result[0];
                if (self::is_table_allowed($result[0])) {
                    $allowed_tables[] = $result[0];
                }
            }

            // 如果 $table_names 是空数组，则返回空数据
            if (empty($table_names)) {
                wp_send_json_error(['error' => '获取数据库表名失败', 'data' => []], 404);
            } else {
                wp_send_json_success([
                    'msg' => '成功获取数据库表名',
                    'data' => $allowed_tables,
                    'all_tables' => $table_names,
                    'notice' => '仅允许导出插件自有表和 WordPress 核心内容表',
                ]);
            }
        }

        //获取表格数据
        public static function get_table_data()
        {
            global $wpdb;

            //管理员权限
            if (!current_user_can('manage_options')) {
                return  wp_send_json_error(['error' => '非管理员，无权获取此内容', 'data' => []], 404);
            }

            // Nonce 验证
            check_ajax_referer('mabox_save_nonce', 'nonce');

            // 检查是否传递了数据库名
            if (empty($_POST['databaseName'])) {
                return wp_send_json_error(['error' => '没有拿到表名',], 400);
            }

            $databaseName = sanitize_text_field(wp_unslash($_POST['databaseName']));

            // 白名单验证：表名只能包含字母、数字、下划线
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $databaseName)) {
                return wp_send_json_error(['error' => '非法表名'], 400);
            }

            // 表名权限检查
            if (!self::is_table_allowed($databaseName)) {
                return wp_send_json_error([
                    'error' => '该表不在允许导出列表中',
                    'notice' => '仅允许导出插件自有表和 WordPress 核心内容表',
                ], 403);
            }

            // 检查数据库表是否存在
            $existingTableName = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $databaseName));
            if ($existingTableName !== $databaseName) {
                return wp_send_json_error([
                    'error' => '该表不存在',
                ], 404);
            }

            // 限制导出行数（防止大表导致内存溢出）
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 1000;
            $limit = min($limit, 1000); // 最大 1000 行
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

            // NOTE: $wpdb->prepare() cannot parameterize table names (identifiers, not values).
            // Safety is ensured by: (1) sanitize_text_field, (2) regex whitelist /^[a-zA-Z0-9_]+$/,
            // (3) SHOW TABLES LIKE %s existence check above.
            $query = $wpdb->prepare("SELECT * FROM `{$databaseName}` LIMIT %d OFFSET %d", $limit, $offset);
            $results = $wpdb->get_results($query, ARRAY_A);

            // 检查查询结果是否为空
            if (!$results) {
                return wp_send_json_error(['error' => '没有查到表格的数据，可能该表为空',], 404);
            }

            // 对敏感字段进行脱敏
            foreach ($results as &$row) {
                foreach ($row as $field => &$value) {
                    $value = self::mask_value($field, $value);
                }
            }
            unset($row, $value);

            // 使用内存流代替临时文件，避免竞态条件
            $stream = fopen('php://temp', 'r+');
            $header = array_keys((array) $results[0]);
            fputcsv($stream, $header);

            foreach ($results as $row) {
                fputcsv($stream, (array) $row);
            }

            rewind($stream);
            $file_content = stream_get_contents($stream);
            fclose($stream);

            // 记录导出日志
            if (class_exists('MaBox_Audit_Logger')) {
                MaBox_Audit_Logger::database('数据库表导出: ' . $databaseName, array(
                    'table' => $databaseName,
                    'user_id' => get_current_user_id(),
                    'rows' => count($results),
                ));
            }
            error_log('[MaBox] 数据库表导出: ' . $databaseName . ' by user ' . get_current_user_id());

            if ($file_content !== false) {
                wp_send_json_success([
                    'data' => $file_content,
                    'message' => '下载成功（已脱敏，最多 ' . $limit . ' 行）',
                    'total_exported' => count($results),
                ]);
            } else {
                wp_send_json_error(['error' => '无法读取文件内容',], 400);
            }
        }
    }
}
