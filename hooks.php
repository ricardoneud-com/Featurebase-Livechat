<?php

use WHMCS\Database\Capsule;
use WHMCS\Session;
use Firebase\JWT\JWT;

$enabled = Capsule::table('tbladdonmodules')
    ->where('module', 'Featurebase')
    ->where('setting', 'FeaturebaseLiveChat-enable')
    ->where('value', 'on')
    ->count();

if (empty($enabled)) {
    return;
}

add_hook('ClientAreaFooterOutput', -1, function () {
    $token = Capsule::table('tbladdonmodules')
        ->where('module', 'Featurebase')
        ->where('setting', 'FeaturebaseLiveChat-token')
        ->value('value');

    $language = Capsule::table('tbladdonmodules')
        ->where('module', 'Featurebase')
        ->where('setting', 'FeaturebaseLiveChat-language')
        ->value('value') ?? 'en';

    $theme = Capsule::table('tbladdonmodules')
        ->where('module', 'Featurebase')
        ->where('setting', 'FeaturebaseLiveChat-theme')
        ->value('value') ?? 'dark';

    $dataSync = Capsule::table('tbladdonmodules')
        ->where('module', 'Featurebase')
        ->where('setting', 'FeaturebaseLiveChat-datasync')
        ->where('value', 'on')
        ->count();

    $identityEnabled = Capsule::table('tbladdonmodules')
        ->where('module', 'Featurebase')
        ->where('setting', 'FeaturebaseLiveChat-identity_verification')
        ->where('value', 'on')
        ->count();

    $identitySecret = Capsule::table('tbladdonmodules')
        ->where('module', 'Featurebase')
        ->where('setting', 'FeaturebaseLiveChat-identity_verification_secret')
        ->value('value');

    if (empty($token)) {
        return;
    }

    $baseConfig = [
        "appId" => $token,
        "theme" => $theme,
        "language" => $language
    ];

    if (!empty($dataSync) && Session::get("uid")) {
        $user = Capsule::table('tblclients')->where('id', Session::get("uid"))->first();
        if ($user) {
            $baseConfig["userId"] = (string)$user->id;
            $baseConfig["name"] = addslashes($user->firstname);
            $baseConfig["email"] = addslashes($user->email);

            if (!empty($identityEnabled) && !empty($identitySecret)) {
                $payload = [
                    "name" => $user->firstname,
                    "email" => $user->email,
                    "userId" => (string)$user->id
                ];
                $jwt = JWT::encode($payload, $identitySecret, 'HS256');
                $baseConfig["featurebaseJwt"] = $jwt;
            }
        }
    }

    $featurebaseConfig = json_encode($baseConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    return <<<HTML
<script>!(function(e,t){var a="featurebase-sdk";function n(){if(!t.getElementById(a)){var e=t.createElement("script");e.id=a,e.src="https://do.featurebase.app/js/sdk.js",e.defer=!0,e.async=!0,t.getElementsByTagName("script")[0].parentNode.insertBefore(e,t.getElementsByTagName("script")[0])}};"function"!=typeof e.Featurebase&&(e.Featurebase=function(){(e.Featurebase.q=e.Featurebase.q||[]).push(arguments)}),"complete"===t.readyState||"interactive"===t.readyState?n():t.addEventListener("DOMContentLoaded",n)})(window,document);</script>
<script>
  Featurebase("boot", {$featurebaseConfig});
</script>
HTML;
});
