<?php
require 'vendor/autoload.php';
foreach (glob('config/*.php') as \) {
    try {
        \ = require \;
        if (is_array(\)) {
            echo "OK: " . basename(\) . "\n";
        } else {
            echo "ERROR: " . basename(\) . " returns " . gettype(\) . "\n";
        }
    } catch (Throwable \) {
        echo "EXCEPTION in " . basename(\) . ": " . \->getMessage() . "\n";
    }
}
