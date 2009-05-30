<?php
// require some basic definitions
require_once dirname(__FILE__) . '/squares.php';

// open readonly (default)
$table = metatable::open(TABLE_FILE, metatable::READONLY);

if (!$table) {
    die("cannot open table file " . TABLE_FILE . "! :-(\n");
}

// retrieve data -- * can be used as wild card
foreach ($table->get('*', '*') as $row => $data) {
    list($n) = sscanf($row, ROW_PRINTF);
    $value = $data['value'];

    printf("% 4d * %4d = % 8d\n", $n, $n, $value);
}

// no need to close() if do not want to save -- file is closed automatically
