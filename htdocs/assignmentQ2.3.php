<?php
// Function to calculate the area of a convex polygon using Shoelace formula
function calculate_polygon_area($vertices) {
    $n = count($vertices); // Count the number of vertices
    $area = 0;

    // Apply Shoelace formula
    for ($i = 0; $i < $n; $i++) {
        // Extract coordinates of two consecutive vertices
        $x1 = $vertices[$i][0];
        $y1 = $vertices[$i][1];
        $x2 = $vertices[($i + 1) % $n][0]; // Ensure circular index for the last vertex
        $y2 = $vertices[($i + 1) % $n][1];
        
        // Apply Shoelace formula for each pair of consecutive vertices
        $area += ($x1 * $y2 - $x2 * $y1);
    }

    // Return the absolute value of area divided by 2
    return abs($area) / 2;
}

// Input vertices as strings
$input = "1.0, 0.0 0.0, 0.0 1.0, 1.0 2.0, 0.0 -1.0, 1.0";

// Split input into individual vertices
$vertices_str = explode(" ", $input);

// Convert vertices from strings to arrays of floats
$vertices = [];
foreach ($vertices_str as $vertex_str) {
    // Split each vertex string into x and y coordinates
    $vertex = explode(",", $vertex_str);
    // Convert string coordinates to float values and add to vertices array
    $vertices[] = array_map('floatval', $vertex);
}

// Calculate area of the polygon
$polygon_area = calculate_polygon_area($vertices);

// Output the result
echo "Area of the polygon: " . $polygon_area;
?>
