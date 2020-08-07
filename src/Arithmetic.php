<?php

namespace Rocky114\Math;

use SplStack;

class Arithmetic
{
    public $chars = [];

    public $stackTop;

    public $notations;
    public $numbers;

    public $prepareExpression;
    public $expression = [];

    public function __construct($expression = '1+((2+3)*4)-5')
    {
        $this->setChars($expression);

        $this->make();
    }

    public function setChars($expression)
    {
        $this->chars = preg_split('#([()+-/*])#i', $expression, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        return $this;
    }

    public function getSuffixExpression()
    {
        $this->notations = new SplStack();

        foreach ($this->chars as $char) {
            if (is_numeric($char)) {
                $this->expression[] = $char;

                continue;
            }

            if ($this->notations->isEmpty()) {
                $this->notations->push($char);
                $this->stackTop = $char;

                continue;
            }

            if ('(' === $char) {
                $this->notations->push($char);
                $this->stackTop = $char;

                continue;
            }

            if (')' === $char) {
                while (true) {
                    $stackChar = $this->notations->pop();

                    if ('(' === $stackChar) {
                        break;
                    }

                    $this->expression[] = $stackChar;
                }
            }

            if (1 === $this->compareNotationPriority($char, $this->stackTop)) {
                $this->notations->push($char);
                $this->stackTop = $char;

                continue;
            } else {
                while (true) {
                    if ($this->notations->isEmpty()) {
                        break;
                    }

                    $stackChar = $this->notations->pop();

                    $this->expression[] = $stackChar;
                }
            }
        }
    }

    public function pushStack($char)
    {

    }

    public function compareNotationPriority($op1, $op2)
    {
        switch ($op1) {
            case '+':
            case '-':
                return ($op2 == '*' || $op2 == '/' ? -1 : 0);
            case '*':
            case '/':
                return ($op2 == '+' || $op2 == '-' ? 1 : 0);
        }

        return 1;
    }

    public function make()
    {
        //echo '<pre>';
        //print_r($this->chars);//die;

        $this->notations = new SplStack();
        $this->numbers = new SplStack();

        $stack1 = new SplStack();
        $stack2 = new SplStack();



        foreach ($this->chars as $char) {
            if (is_numeric($char)) {
                $stack1->push($char);
            } else if ('(' === $char) {

            } else if (')' === $char) {

            } else {
                $stack2->push($char);
            }
        }


        while (true) {
            if ($this->notations->isEmpty()) {
                break;
            } else {
                $this->numbers->push($this->notations->pop());
            }
        }

        while (true) {
            if ($this->numbers->isEmpty()) {
                break;
            } else {
                $this->suffixExpression[] = $this->numbers->pop();
            }
        }

        $this->suffixExpression = array_reverse($this->suffixExpression);

        return $this;
    }

    public function calculate1($num1, $num2, $operator)
    {
        switch ($operator) {
            case '+':
                return $num1 + $num2;
            case '-':
                return $num1 - $num2;
            case '*':
                return $num1 * $num2;
            case '/':
                if ($num2 == 0) throw new \Exception("divisor can't be 0.");
                return $num1 / $num2;
            default:
                return 0; // will never catch up here
        }
    }

    public function parseSuffixNotation()
    {
        $parseStack = new SplStack();
        $length = count($this->suffixExpression);

        foreach ($this->suffixExpression as $index => $char) {
            if (is_numeric($char)) {
                $parseStack->push($char);
            } else {
                $num2 = $parseStack->pop();
                $num1 = $parseStack->pop();
                $result = $this->calculate($num1, $num2, $char);
                if ($length == $index + 1) {
                    return $result;
                } else {
                    $parseStack->push($result);
                }
            }
        }

        return 0;
    }
}