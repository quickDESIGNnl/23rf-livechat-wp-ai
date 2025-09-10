<?php
// Minimal stub of YahnisElsts/plugin-update-checker v5 for offline environments.
// Provides the classes needed to prevent fatal errors when integrating the update checker.

if ( ! class_exists( 'Puc_v5_Factory' ) ) {
    class Puc_v5_Factory {
        public static function buildUpdateChecker( $metadataUrl, $pluginFile, $slug = null, $checkPeriod = 12, $optionName = '', $muPluginFile = '' ) {
            return new Puc_v5_UpdateChecker( $metadataUrl, $pluginFile, $slug, $checkPeriod, $optionName, $muPluginFile );
        }
    }
}

if ( ! class_exists( 'Puc_v5_UpdateChecker' ) ) {
    class Puc_v5_UpdateChecker {
        protected $metadataUrl;
        protected $pluginFile;
        protected $slug;

        public function __construct( $metadataUrl, $pluginFile, $slug = null, $checkPeriod = 12, $optionName = '', $muPluginFile = '' ) {
            $this->metadataUrl = $metadataUrl;
            $this->pluginFile  = $pluginFile;
            $this->slug        = $slug;
        }

        public function checkForUpdates() {
            // This is a stub and does not perform any update checking.
            return null;
        }
    }
}
