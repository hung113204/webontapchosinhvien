<?php
$p = 'app/Http/Controllers/Admin/UsersController.php';
$c = file_get_contents($p);
if (substr($c, 0, 3) === "\xef\xbb\xbf") {
    file_put_contents($p, substr($c, 3));
    echo "BOM removed\n";
} else {
    echo "No BOM\n";
}
