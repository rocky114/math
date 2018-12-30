<?php

namespace notation;

use SplStack;

class SuffixNotation
{
    public $chars = [];
    public $operatorStack;
    public $resultStack;
    public $suffixExpression = [];

    public function __construct($expression = '1+((2+3)*4)-5')
    {
        $this->operatorStack = new SplStack();
        $this->resultStack = new SplStack();

        $this->chars = preg_split('#([()+-/*])#i', $expression, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $this->polishNotation();
    }

    public function polishNotation()
    {
        foreach ($this->chars as $char) {
            if (is_numeric($char)) {
                $this->resultStack->push($char);
            } else {
                if ($char == '(') {
                    $this->operatorStack->push($char);
                } else if ($char == ')') {
                    do {
                        $stackTopElement = $this->operatorStack->pop();
                        if ($stackTopElement == '(') {
                            break;
                        } else {
                            $this->resultStack->push($stackTopElement);
                        }
                    } while (true);
                } else {
                    loop:
                    if ($this->operatorStack->isEmpty() || $this->operatorStack->top() == '(') {
                        $this->operatorStack->push($char);
                    } else {
                        $result = $this->priorityCompare($char, $this->operatorStack->top());
                        if ($result === 1) {
                            $this->operatorStack->push($char);
                        } else {
                            $this->resultStack->push($this->operatorStack->pop());
                            goto loop;
                        }
                    }
                }
            }
        }

        while (true) {
            if ($this->operatorStack->isEmpty()) {
                break;
            } else {
                $this->resultStack->push($this->operatorStack->pop());
            }
        }

        while (true) {
            if ($this->resultStack->isEmpty()) {
                break;
            } else {
                $this->suffixExpression[] = $this->resultStack->pop();
            }
        }

        $this->suffixExpression = array_reverse($this->suffixExpression);
    }

    public function priorityCompare($op1, $op2)
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

    public function calculate($num1, $num2, $operator)
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


$result = (new SuffixNotation())->parseSuffixNotation();
var_dump($result);