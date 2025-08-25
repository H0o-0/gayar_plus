<?php
echo "<h1>Session Test</h1>";

echo "<p>Session status before: " . session_status() . "</p>";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "<p>Session started</p>";
} else {
    echo "<p>Session already started</p>";
}

echo "<p>Session status after: " . session_status() . "</p>";

echo "<p>Session ID: " . session_id() . "</p>";

echo "<p>Session data: " . print_r($_SESSION, true) . "</p>";
?>