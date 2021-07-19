<?php

class Arithmetic
{
    public $is_delimiter = false;

    public function getSplitChars($expression): array
    {
        $chars = preg_split("#([{}()+-/*])#i", $expression, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        $symbolStack = new SplStack();
        $stack = new SplStack();
        foreach ($chars as $char) {
            if ($char == '{') {
                $this->is_delimiter = true;
            }

            if ($char == '}') {
                if ($symbolStack->top() == '{') {
                    $symbolStack->pop();
                } else {
                    $symbols = [];
                    while (!$symbolStack->isEmpty()) {
                        $symbol = $symbolStack->pop();
                        if ($symbol == '{') {
                            array_unshift($symbols, $symbol);
                            break;
                        } else {
                            array_unshift($symbols, $symbol);
                        }
                    }
                    $symbols[] = $char;

                    foreach ($symbols as $item) {
                        $stack->push($item);
                    }
                }

                $this->is_delimiter = false;
                continue;
            }

            if ($this->is_delimiter) {
                $symbolStack->push($char);
            } else {
                $stack->push($char);
            }
        }

        $data = [];
        while (!$stack->isEmpty()) {
            array_unshift($data, $stack->pop());
        }

        return $data;
    }

    public function isSample(string $expression1, string $expression2): bool
    {
        $diff = array_diff($this->getSplitChars($expression1), $this->getSplitChars($expression2));

        return empty($diff);
    }
}

try {
    // 例【\rm】显示字体，可以用str_replace过滤一下
    $arithmetic = new Arithmetic();
    $result = $arithmetic->isSample("1\\frac{2}{21}", "1\\frac{2}{{21}}");
    var_dump($result);//相同

    $result = $arithmetic->isSample("1\\frac{2}{{231}}", "1\\frac{2}{{21}}");
    var_dump($result);//不相同
} catch (\Exception $exception) {
    var_dump($exception->getMessage(), $exception->getLine());
}
