<?php
/**
 * Shared helper functions for the SMS Grade System
 */

/**
 * Get the appropriate avatar path based on available image files
 * @return string The path to the avatar image
 */
function get_avatar_path() {
    $png = __DIR__ . '/../assets/sir-greg.png';
    $jpg = __DIR__ . '/../assets/sir-greg.jpg';
    
    if (file_exists($png)) {
        return '../assets/sir-greg.png';
    } elseif (file_exists($jpg)) {
        return '../assets/sir-greg.jpg';
    } else {
        return '../assets/ctu-logo.png';
    }
}
?>
