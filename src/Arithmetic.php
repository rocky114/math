<?php

namespace Rocky114\Math;

use SplStack;

class Arithmetic
{
    public $chars = [];
    public $symbols;
    public $expression = [];

    public function __construct($expression = '1+((2+3)*4)-5')
    {
        $this->setChars($expression);
    }

    public function setChars($expression)
    {
        $this->chars = preg_split('#([()+-/*])#i', $expression, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        return $this;
    }

    public function getSuffixExpression()
    {
        $this->symbols = new SplStack();

        $length = count($this->chars) - 1;

        foreach ($this->chars as $index => $char) {
            if (is_numeric($char)) {
                $this->expression[] = $char;
                if ($length === $index) {
                    while (!$this->symbols->isEmpty()) {
                        $this->expression[] = $this->symbols->pop();
                    }
                }

                continue;
            }

            if ($this->symbols->isEmpty() || '(' === $this->symbols->top()) {
                $this->symbols->push($char);

                continue;
            }

            if (')' === $char) {
                while (true) {
                    $popChar = $this->symbols->pop();
                    if ('(' === $popChar) {
                        break;
                    }

                    $this->expression[] = $popChar;
                }

                continue;
            }

            if (1 === $this->compareNotationPriority($char, $this->symbols->top())) {
                $this->symbols->push($char);

                continue;
            } else {
                $this->expression[] = $this->symbols->pop();

                do {
                    if ($this->symbols->isEmpty()) {
                        $this->symbols->push($char);
                        break;
                    }

                    $popChar = $this->symbols->pop();
                    if ('(' === $popChar) {
                        $this->symbols->push($char);

                        break;
                    }

                    if (1 === $this->compareNotationPriority($char, $popChar)) {
                        $this->symbols->push($char);

                        break;
                    } else {
                        $this->expression[] = $popChar;
                    }
                } while (true);
            }
        }
    }

    public function compareNotationPriority($symbol1, $symbol2)
    {
        switch ($symbol1) {
            case '+':
            case '-':
                return ($symbol2 === '*' || $symbol2 === '/' ? -1 : 0);
            case '*':
            case '/':
                return ($symbol2 === '+' || $symbol2 === '-' ? 1 : 0);
        }

        return 1;
    }

    public function calculate()
    {
        $this->getSuffixExpression();

        $numberStack = new SplStack();

        $result = 0;
        foreach ($this->expression as $index => $item) {
            if (is_numeric($item)) {
                $numberStack->push($item);
                continue;
            }

            $number2 = $numberStack->pop();
            $number1 = $numberStack->pop();

            switch ($item) {
                case '+':
                    $result = $number1 + $number2;
                    break;
                case '-':
                    $result = $number1 - $number2;
                    break;
                case '*':
                    $result = $number1 * $number2;
                    break;
                case '/':
                    if ($number2 == 0) throw new \Exception("divisor can't be 0.");
                    $result = $number1 / $number2;
                    break;
            }

            $numberStack->push($result);
        }

        return $result;
    }
}