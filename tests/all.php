<?php
require_once dirname(__FILE__) . '/test-more.php';
require_once dirname(__FILE__) . '/../metatable.php';

plan(18);

{
    $table = metatable::open('nonexistent');
    ok($table, 'open non-existent');
}

{
    $table = metatable::open('nonexistent', metatable::READONLY);
    ok(!$table, 'open non-existent readonly');
}

{
    $table = metatable::open('table');
    ok($table, 'open new');
    ok($table->close(), 'save new');
}

{
    $table = metatable::open('table');
    ok($table, 'open empty');
    ok($table->close(), 'close empty');
}

{
    $table = metatable::open('table', metatable::READONLY);
    ok($table, 'open empty readonly');
    ok($table->close(), 'close empty readonly');
}

{
    if (($table = metatable::open('table')) !== FALSE) {
        ok($table->set('x', 'int', 1), 'set integer');
        ok($table->set('x', 'string', 'abc'), 'set string');
        ok($table->set('x', 'emptystring', ''), 'set empty string');
        ok($table->set('x', 'true', TRUE), 'set true');
        ok($table->set('x', 'false', FALSE), 'set false');
        ok($table->close(), 'save');
    } else {
        fail('set integer');
        fail('set string');
        fail('set empty string');
        fail('set true');
        fail('set false');
        fail('save');
    }
}

{
    if (($table = metatable::open('table')) !== FALSE) {
        is($table->get('*', '*'), 
            array('x' => array(
                'int' => 1,
                'string' => 'abc',
                'emptystring' => '',
                'true' => TRUE,
                'false' => FALSE
            )), 'get');
        ok($table->close(), 'close');
    } else {
        fail('get');
        fail('close');
    }
}

{
    if (($table = metatable::open('table', metatable::READONLY)) !== FALSE) {
        is($table->get('*', '*'), 
            array('x' => array(
                'int' => 1,
                'string' => 'abc',
                'emptystring' => '',
                'true' => TRUE,
                'false' => FALSE
            )), 'get readonly');
        ok($table->close(), 'close readonly');
    } else {
        fail('get readonly');
        fail('close readonly');
    }
}

unlink('table');
