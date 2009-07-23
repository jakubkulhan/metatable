# metatable

## What is *metatable*?

*metatable* aims to provide simple interface to store and retrieve data. It is written entirely in PHP and uses only basic PHP functions, so no extensions are needed. But still *metatable* should be fast enough to be used as storage backend in some sort of applications. Data are stored in binary file in *metatable*'s own format.

It is big hashmap, which associates `(row : string, column : string)` pair to value. Currently supported value types are: `string`, `integer` (32bit) and `boolean`. `string` is uninterpreted, so other types may be stored in their binary form as `string`. Maximal lengths of `row` and `col` are 124 bytes.


## Basic usage

To open up *metatable* file use `metatable::open()`:

    require_once 'metatable.php';
    
    $table = metatable::open('foo');

Either `FALSE` or newly created instance of `metatable` class is returned. `metatable` object has only a few public methods – `get()`, `set()`, `index()`, `unindex()` and `close()`. `get()` and `set()` are used to retrieve and store values. When passing `NULL` to `set()` as value, record is deleted:

    $table->set('foo', 'bar', 5);
    
    $table->get('foo', 'bar');
    // => array('foo' => array('bar' => 5))
    
    $table->set('foo', 'bar', NULL);
    
    $table->get('foo', 'bar');
    // => array()

`index()` and `unindex()` creates and drops indexes. Index is precomputed saved value, where data are stored in file. Indexes can be created only if there are some values to index.

The last method `close()` saves structure informations and closes *metatable* file. *metatable* is constructed in way that each instance is „transaction“ – all operations are performed or none of them – data integrity is assured.


## Advanced usage

### `metatable::open()` flags

`metatable::open()` takes two arguments – `$filename` and `$flags`, where `$flags` is integer – `or`-ed list of flags defined in `metatable` class constants. Possible flags are:

* `READONLY` – *metatable* is opened readonly, cannot do `set()` and is not saved on closing (defautly turned off)
* `READWRITE` – all operations are possible (defaulty on)
* `STRINGS_GC` – strings are garbage collected (defaultly on)
* `AUTOCLOSE` – automatically saved on instance destruction (defaultly off)

The most common option is to open *metatable* readonly, which increases efficiency, when you do not need to write to *metatable*:

    $readonly = metatable::open('foo', metatable::READONLY);

Do not ever open more *metatable*s, when one of them would be opened in `READWRITE` mode – this will cause race condition, each *metatable* will wait for the other one to aquire file lock.

## Support

*metatable* should definitely work perfectly with PHP 5.2.0 or higher on any UNIX – this cover the most servers you can encounter. Lower versions of PHP 5 are probably without problems, too – please run tests and report if something does not work. With Windows is the problem that they does not give any support for atomic file handling. It is fixed with workaround, so *metatable* can be used without guaranted atomicity, but it is not as reliable as if running on any UNIX. *metatable* should be used on Windows only for development purposes.


## File format

*metatable* file is composed from frames and structure of frames written at the end of file. Frames are named, name of frame consists from 4 bytes (there is possibility to have at most 2^32 frames if ever needed). Data itself are stored as sorted set of fixed-size records in `data` frame. Values of type `string` are stored in another frame called `strs`. The last basic frame is `indx` where „indexes“ are stored.

All integers are stored in big-endian format.

### Structure of frames (end of file)

Zero or more frame triples `(name : string4, size : integer, used : integer)` followed by count of frames `frame_count : integer`. Then version of *metatable* format `format_version : integer` and at the end of string `"metatable"`:

    (name : string4, size : integer, used : integer)*
    frame_count : integer
    format_version : integer
    "metatable"

### `data` frame

Fixed-size records, each record 256 bytes long:

* `row` (124 bytes) – NUL right padded string; row name
* `col` (124 bytes) – NUL right padded string; col name
* `type` (4 bytes) – integer:
    * `1xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` – string, other bits are string size (string can be at most 2^31 bytes long); offset in `strs` table is in `value` field
    * `01000000000000000000000000000000` – integer; value in `value` field
    * `00100000000000000000000000000000` – true; `value` field is 0
    * `00010000000000000000000000000000` – false; `value` field is 0
* `value` (4 bytes) – depends on `type`

Records are stored in ascending order by row and col.

### `strs` frame

Raw value of strings stacked up one after another. Have to be referenced from `data` records.

### `indx` frame

Fixed-size records, each record 128 bytes long:

* `index` (120 bytes) – NUL right padded string; start of row name
* `lower` (4 bytes) – integer; lower bound position of row (`offset_in_data_frame = lower * 256`)
* `upper` (4 bytes) – integer; upper bound position of row (`end_in_data_frame = upper * 256`)

## License

*metatable* is licensed under the MIT License.

Copyright (c) 2009 Jakub Kulhan <jakub.kulhan@gmail.com>

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
