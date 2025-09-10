<?php
// Minimal stub of YahnisElsts/plugin-update-checker for offline environments.
// This is not the full library; it only defines the classes required
// to prevent fatal errors when integrating the update checker.

if (!class_exists('Puc_v4_Factory')) {
    class Puc_v4_Factory {
        public static function buildUpdateChecker($metadataUrl, $pluginFile, $slug = null, $checkPeriod = 12, $optionName = '', $muPluginFile = '') {
            return new Puc_v4_UpdateChecker($metadataUrl, $pluginFile, $slug, $checkPeriod, $optionName, $muPluginFile);
        }
    }
}

if (!class_exists('Puc_v4_UpdateChecker')) {
    class Puc_v4_UpdateChecker {
        protected $metadataUrl;
        protected $pluginFile;
        protected $slug;

        public function __construct($metadataUrl, $pluginFile, $slug = null, $checkPeriod = 12, $optionName = '', $muPluginFile = '') {
            $this->metadataUrl = $metadataUrl;
            $this->pluginFile = $pluginFile;
            $this->slug = $slug ?: plugin_basename($pluginFile);
        }

        public function checkForUpdates() {
            // Update checking is not implemented in this stub.
        }
    }
}
