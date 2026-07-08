<?php
$html = @file_get_contents('http://127.0.0.1:8000/dashboard');
if ($html === false) {
    echo "Could not fetch from 127.0.0.1:8000\n";
    exit(1);
}
preg_match_all('/<link[^>]+href="([^"]+admin[^"]+\.css)"[^>]*>/i', $html, $matches);
print_r($matches[1]);
echo "Vite script tags:\n";
preg_match_all('/<script[^>]+src="([^"]+)"[^>]*>/i', $html, $matches);
print_r($matches[1]);
