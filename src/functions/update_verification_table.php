<?php
require_once __DIR__ . '/config.php';

try {
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/update_verification_table.sql');
    
    // Execute the SQL
    if ($mysqli->multi_query($sql)) {
        do {
            // Store first result set
            if ($result = $mysqli->store_result()) {
                $result->free();
            }
        } while ($mysqli->more_results() && $mysqli->next_result());
    }
    
    echo "Table updated successfully!\n";
} catch (Exception $e) {
    echo "Error updating table: " . $e->getMessage() . "\n";
} 