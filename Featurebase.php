<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function Featurebase_config() {
    $authorLink = "https://ricardoneud.com";
    return array(
        "name" => "Featurebase",
        "description" => "The next-gen support & feedback platform",
        "version" => "1.0",
        "author" => '<a href="' . $authorLink . '" style="text-decoration: none; display: inline-flex; align-items: center;">Ricardoneud.com</a>',
        "fields" => array(
            "FeaturebaseLiveChat-enable" => array(
                "FriendlyName" => "Enable livechat?", 
                "Type" => "yesno", 
                "Size" => "55", 
                "Description" => "A quick way to enable or disable the chat on your website", 
                "Default" => "", 
            ),
            "FeaturebaseLiveChat-token" => array(
                "FriendlyName" => "App ID",
                "Type" => "text",
                "Size" => "90",
                "Description" => "Enter your Featurebase App ID",
                "Default" => "",
            ),
            "FeaturebaseLiveChat-language" => array(
                "FriendlyName" => "Language",
                "Type" => "text",
                "Size" => "90",
                "Description" => "Short code (e.g. en, de, etc.)",
                "Default" => "en",
            ),
            "FeaturebaseLiveChat-theme" => array(
                "FriendlyName" => "Theme Mode",
                "Type" => "dropdown",
                "Options" => "light,dark",
                "Description" => "Choose the widget theme mode (light, or dark)",
                "Default" => "dark",
            ),
            "FeaturebaseLiveChat-datasync" => array(
                "FriendlyName" => "Enable Data Sync",
                "Type" => "yesno",
                "Size" => "55",
                "Description" => "If enabled, WHMCS client data (first name, email, user ID, signup date) will be automatically synced with Featurebase. "
                    . "This ensures personalization in the live chat (e.g., showing 'Hi John' instead of 'Hi there') and enables automatic authorization for Featurebase services.",
                "Default" => "",
            ),
        )
    );
}

function Featurebase_activate() {
    try {
        return ['status' => 'success', 'description' => 'Featurebase add-on has been activated.'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'description' => 'Error creating tables: ' . $e->getMessage()];
    }
}

function Featurebase_deactivate() {
    try {
        return ['status' => 'success', 'description' => 'Featurebase add-on has been deactivated.'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'description' => 'Error dropping tables: ' . $e->getMessage()];
    }
}

?>