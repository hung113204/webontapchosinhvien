<?php
$text = file_get_contents('resources/views/Client/AI/index.blade.php');
file_put_contents('resources/views/Client/AI/index_fixed.blade.php', utf8_decode($text));
$script = file_get_contents('resources/views/Client/AI/ai_scripts.blade.php');
file_put_contents('resources/views/Client/AI/ai_scripts_fixed.blade.php', utf8_decode($script));
echo 'Done';
