<?php

function getQuintypeAPIHost($request) {
    $currentHost = $request->getHost();

    if(config("quintype.api-host-remove-web") && strpos($currentHost, '-web.') !== false) {
        return "https://" . str_replace("-web", "", $currentHost);
    }

    foreach((array) config("quintype.api-host-remove-string") as $str) {
        if(strpos($currentHost, $str) !== false) {
            return "https://" . str_replace($str, "", $currentHost);
        }
    }

    if(config("quintype.host-to-api-host")) {
        $hosts = config("quintype.host-to-api-host");
        if(isset($hosts[$currentHost]))
            return $hosts[$currentHost];
    }

    return config('quintype.api-host');
}
