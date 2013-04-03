<?php
/**
 * Tester package
 * 
 * @package Tester
 * @author  Peter Gribanov <gribanov@professionali.ru>
 */


/**
 * Класс для тестирования производительности
 *
 * @package Tester
 * @author  Peter Gribanov <gribanov@professionali.ru>
 */
class Tester {

	/**
	 * Количесвто итераций для тестов
	 *
	 * @var integer
	 */
	private $iterations;

	/**
	 * Список тестов
	 *
	 * @var array
	 */
	private $tests = array();


	/**
	 * Конструктор
	 *
	 * @param integer|null $iterations Количесвто итераций для тестов
	 */
	public function __construct($iterations = 0) {
		$this->setIterations($iterations);
	}

	/**
	 * Устанавливает количесвто итераций для тестов
	 *
	 * @param integer $iterations Количесвто итераций для тестов
	 *
	 * @return Tester
	 */
	public function setIterations($iterations) {
		$this->iterations = (int)$iterations;
		return $this;
	}

	/**
	 * Добавить тест
	 *
	 * @param Closure     $test Тест
	 * @param string|null $name Название теста
	 *
	 * @return Tester
	 */
	public function addTest(Closure $test, $name = null) {
		if ($name && is_string($name)) {
			$this->tests[$name] = $test;
		} else {
			$this->tests[] = $test;
		}
		return $this;
	}

	/**
	 * Выполнение тестов
	 *
	 * @return array
	 */
	public function execute() {
		$results = array();
		
		if ($this->tests) {
			foreach ($this->tests as $name => $test) {
				echo 'Run '.$name."\n";

				// считаем затраы по памяти
				ob_start();
				$use_memory = 0;
				$use_memory = memory_get_usage();
				$test(); // вызов теста
				$use_memory = memory_get_usage()-$use_memory;
				ob_end_clean();

				// считаем среднее время выполнения
				$use_time = $time = 0;
				$ratio = $this->iterations >= 100 ? ceil($this->iterations/50) : $this->iterations;
				$part  = $this->iterations >= 100 ? '.' : str_repeat('.', ceil(50/$this->iterations));
				for ($i = 0; $i < $this->iterations; $i++) {
					// прогресс тестирования
					if ($ratio == $this->iterations || $i%$ratio == 0) {
						echo $part;
					}
					ob_start();
					$time = microtime(true);
					$test(); // вызов теста
					$time = microtime(true)-$time;
					$use_time += $time;
					ob_end_clean();
				}
				
				$results[$name] = array($use_time/$this->iterations, $use_memory);
				echo "\n";
			}
			// получение длинн для форматирования отчета
			$length_name = $length_time = $length_mem = 0;
			foreach ($results as $name => $result) {
				$length_name = max($length_name, strlen($name));
				$length_time = max($length_time, strlen($result[0]));
				$length_mem = max($length_mem, strlen($result[1]));
			}

			// вывод отчета
			echo "\n\nCompletely";
			foreach($results as $name => $result) {
				echo "\n".
					str_pad($name, $length_name).' '.
					str_pad($result[0], $length_time, '0').' '.
					str_pad($result[1], $length_mem, ' ', STR_PAD_LEFT);
			}
		}
		return $results;
	}

}
