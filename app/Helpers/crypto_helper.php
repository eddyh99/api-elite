<?php

if (!function_exists('encode_private_key')) {
    function encode_private_key(string $privateKey): string
    {
        return base64_encode($privateKey); // simple encode
    }
}

if (!function_exists('decode_private_key')) {
    function decode_private_key(string $encoded): string
    {
        return base64_decode($encoded); // simple decode
    }
}
