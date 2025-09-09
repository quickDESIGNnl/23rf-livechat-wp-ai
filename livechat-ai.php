<?php
/**
 * Plugin Name: LiveChat AI
 * Description: Live chat waarmee bezoekers via een webhook met een AI-assistent praten.
 * Version:     1.0.0
 * Author:      Jouw Naam
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class LiveChatAI {
    const WEBHOOK_URL = 'https://example.com/webhook'; // vervang met jouw webhook

    public function __construct() {
        add_shortcode( 'livechat_ai', [ $this, 'render_chat' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_livechatai_send', [ $this, 'handle_message' ] );
        add_action( 'wp_ajax_nopriv_livechatai_send', [ $this, 'handle_message' ] );
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
        wp_enqueue_style( 'livechat-ai', $base . 'assets/chat.css', [], '1.0.0' );
        wp_enqueue_script( 'livechat-ai', $base . 'assets/chat.js', [ 'jquery' ], '1.0.0', true );
        wp_localize_script( 'livechat-ai', 'LiveChatAI', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        ] );
    }

    public function handle_message() {
        $message = sanitize_text_field( $_POST['message'] ?? '' );
        if ( empty( $message ) ) {
            wp_send_json_error( [ 'error' => 'Leeg bericht' ] );
        }

        $response = wp_remote_post( self::WEBHOOK_URL, [
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
}

new LiveChatAI();
