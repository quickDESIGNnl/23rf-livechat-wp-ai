<?php
/**
 * Plugin Name: LiveChat AI
 * Description: Live chat waarmee bezoekers via een webhook met een AI-assistent praten.
 * Version:     1.0.1
 * Author:      Jouw Naam
 * Update URI: https://github.com/<gebruikersnaam>/23rf-livechat-wp-ai
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class LiveChatAI {

    public function __construct() {
        add_shortcode( 'livechat_ai', [ $this, 'render_chat' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_livechatai_send', [ $this, 'handle_message' ] );
        add_action( 'wp_ajax_nopriv_livechatai_send', [ $this, 'handle_message' ] );
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function render_chat() {
        ob_start(); ?>
        <div id="livechat-ai">
            <div class="chat-window"></div>
            <form class="chat-form">
                <input type="text" name="message" placeholder="Stel je vraag…" autocomplete="off" />
                <button type="submit">Verstuur</button>
            </form>
        </div>
        <?php return ob_get_clean();
    }

    public function enqueue_assets() {
        $base = plugin_dir_url( __FILE__ );
        wp_enqueue_style( 'livechat-ai', $base . 'assets/chat.css', [], '1.0.1' );
        wp_enqueue_script( 'livechat-ai', $base . 'assets/chat.js', [ 'jquery' ], '1.0.1', true );
        wp_localize_script( 'livechat-ai', 'LiveChatAI', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        ] );
    }

    public function handle_message() {
        $message = sanitize_text_field( $_POST['message'] ?? '' );
        if ( empty( $message ) ) {
            wp_send_json_error( [ 'error' => 'Leeg bericht' ] );
        }

        $response = wp_remote_post( $this->get_webhook_url(), [
            'headers' => [ 'Content-Type' => 'application/json' ],
            'body'    => wp_json_encode( [ 'question' => $message ] ),
            'timeout' => 60,
        ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( [ 'error' => $response->get_error_message() ] );
        }

        $body = wp_remote_retrieve_body( $response );
        wp_send_json_success( [ 'reply' => $body ] );
    }

    private function get_webhook_url() {
        return get_option( 'livechatai_webhook', 'https://example.com/webhook' );
    }

    public function add_settings_page() {
        add_options_page(
            'LiveChat AI',
            'LiveChat AI',
            'manage_options',
            'livechatai',
            [ $this, 'settings_page_html' ]
        );
    }

    public function register_settings() {
        register_setting( 'livechatai', 'livechatai_webhook', [ 'sanitize_callback' => 'esc_url_raw' ] );

        add_settings_section(
            'livechatai_section',
            '',
            null,
            'livechatai'
        );

        add_settings_field(
            'livechatai_webhook',
            'Webhook URL',
            [ $this, 'webhook_field_html' ],
            'livechatai',
            'livechatai_section'
        );
    }

    public function webhook_field_html() {
        $value = get_option( 'livechatai_webhook', 'https://example.com/webhook' );
        echo '<input type="text" name="livechatai_webhook" value="' . esc_attr( $value ) . '" class="regular-text" />';
    }

    public function settings_page_html() {
        ?>
        <div class="wrap">
            <h1>LiveChat AI</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'livechatai' );
                do_settings_sections( 'livechatai' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

if ( ! class_exists( 'Puc_v5_Factory', false ) ) {
    require __DIR__ . '/vendor/plugin-update-checker/plugin-update-checker.php';
}
Puc_v5_Factory::buildUpdateChecker(
    'https://github.com/<username>/23rf-livechat-wp-ai',
    __FILE__,
    'livechat-ai'
);

new LiveChatAI();
