Tester
======


## Example

Сomparing the speed of the `file_get_contents` and `fopen`

```php
include 'Tester.php';

$tester = new Tester(200);

$tester->addTest(function() {
  $content = '';
	$content = file_get_contents('http://ya.ru');
}, 'file_get_contents');

$tester->addTest(function() {
	$content = '';
	$fp = fopen('http://ya.ru', 'r');
	while (!feof($fp)) {
		$content .= fread($fp, 1024);
	}
	fclose($fp);
}, 'fopen');


$result1 = $tester->execute();
```
