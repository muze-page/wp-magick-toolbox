<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OptimizeOutputEscapingTest extends TestCase
{
    public function test_widget_theme_wrappers_preserve_the_widgets_api_contract(): void
    {
        $source = $this->source('widget/index.php');

        $this->assertSame(2, substr_count($source, "echo \$args['before_widget'];"));
        $this->assertSame(
            2,
            substr_count(
                $source,
                "echo \$args['before_title'] . esc_html(\$instance['title']) . \$args['after_title'];"
            )
        );
        $this->assertSame(2, substr_count($source, "echo \$args['after_widget'];"));
        $this->assertSame(
            7,
            substr_count($source, 'phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped')
        );
        $this->assertStringNotContainsString("wp_kses_post(\$args[", $source);
    }

    public function test_thumbnail_switcher_button_labels_are_html_escaped(): void
    {
        $source = $this->source('admin/thumbnail_switcher/easy-thumbnail-switcher.php');

        $this->assertSame(2, substr_count($source, 'esc_html( $this->change_str )'));
        $this->assertSame(2, substr_count($source, 'esc_html( $this->remove_str )'));
        $this->assertSame(2, substr_count($source, 'esc_html( $this->add_new_str )'));
    }

    public function test_list_id_output_is_html_escaped(): void
    {
        $source = $this->source('admin/single_show_id.php');

        $this->assertStringContainsString('echo esc_html($id);', $source);
        $this->assertStringNotContainsString('echo $id;', $source);
    }

    private function source(string $relative_path): string
    {
        $source = file_get_contents(
            dirname(__DIR__, 2) . '/admin/partials/optimize/' . $relative_path
        );
        $this->assertIsString($source);

        return $source;
    }
}
