<?php
// require some basic definitions
require_once dirname(__FILE__) . '/squares.php';

// open in readwrite mode (default)
$table = metatable::open(TABLE_FILE);

if (!$table) {
    die("cannot open table file " . TABLE_FILE . "! :-(\n");
}

// store data
for ($i = 1; $i <= N; $i++) {
    if (!$table->set(sprintf(ROW_PRINTF, $i), 'value', $i * $i)) {
        die("cannot store data into table!\n");
    }
}

// save
if (!$table->close()) {
    die("cannot save table file\n");
}
