<?php
$ch = curl_init("https://www.google.com");
$content = curl_exec($ch);
if (curl_error($ch)) {
    echo(curl_error($ch) . "<br>");
}
echo($content);