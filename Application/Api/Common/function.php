<?php

function formUrlEncoded($arr) {
    $ret = array();
    foreach ($arr as $key => $value) {
        $ret[] = $key . '=' . $value;
    }
    return implode($ret, '&');
}

function curl_post_contents($url, $data, $referer) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}