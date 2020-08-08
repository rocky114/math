<?php

include "../src/Arithmetic.php";

try {
    $arithmetic = new \Rocky114\Math\Arithmetic('1+2-3+4');
    $result = $arithmetic->calculate();

    var_dump(1, $result);
} catch (\Exception $exception) {
    var_dump($exception->getMessage());
}
