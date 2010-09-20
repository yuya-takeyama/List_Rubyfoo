<?php
/**
 * List_Rubyfoo
 * Array iterator like Ruby for PHP.
 *
 * @author Yuya Takeyama <sign.of.the.wolf.pentagram@gmail.com>
 */

class List_Rubyfoo implements Iterator, ArrayAccess, Countable
{
    /**
     * Array storage
     *
     * @var array
     */
    protected $_list;

    /**
      * Pointer of iterator
      *
      * @var int
      */
    protected $_pointer;

	/**
     * Overloaded properties definition
     * property => method
     *
     * @var array
     */
    protected static $_overloadedProps = array(
        'count' => 'count',
        'size'  => 'count',
        'length'=> 'count',
        'first' => 'first',
        'last'  => 'last',
        'max'   => 'max',
        'min'   => 'min'
    );

    /**
     * Method aliases
     *
     * @var array
     */
    protected static $_aliases = array(
        'collect'  => 'map',
        'find'     => 'detect',
        'find_all' => 'select'
    );

    /**
     * Constructor
     *
     * @param  array $list variable argument is also available.
     */
    public function __construct($list = array())
    {
        if (is_array($list)) {
            $this->_list = $list;
        } else {
            $this->_list = func_get_args();
        }
        $this->rewind();
    }

    /**
     * Property overloading
     * Properties defined in self::$_overloadProps calls method.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, self::$_overloadedProps)) {
            $method = self::$_overloadedProps[$key];
            return $this->$method();
        }
        throw new Exception("Undefined property '{$key}' is called.");
    }

    /**
     * Method alias
     *
     * @param  string $method name of called method
     * @param  array  $args   arguments
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (array_key_exists($method, self::$_aliases)) {
            $method = self::$_aliases[$method];
            return call_user_func_array(array($this, $method), $args);
        }
        throw new BadMethodCallException("Undefined method '{$method}' is called.");
    }

    public function new_($list = array())
    {
        if (is_array($list)) {
            return new self($list);
        } else {
            return new self(func_get_args());
        }
    }

    public function push()
    {
        foreach (func_get_args() as $v)
        {
            $this->_list[] = $v;
        }
        return $this;
    }

    public function pop()
    {
        return array_pop($this->_list);
    }

    public function unshift()
    {
        $args = array(&$this->_list);
        foreach (func_get_args() as $val)
        {
            $args[] = $val;
        }
        call_user_func_array('array_unshift', $args);
        return $this;
    }

    public function shift()
    {
        return array_shift($this->_list);
    }

    public function join($glue)
    {
        return join($glue, $this->_list);
    }

    public function first()
    {
        return $this->_list[0];
    }

    public function last()
    {
        return $this->_list[count($this->_list) - 1];
    }

    public function max()
    {
        foreach ($this->to_a() as $val)
        {
            if (empty($max) || $val > $max) {
                $max = $val;
            }
        }
        return $max;
    }

    public function min()
    {
        foreach ($this->to_a() as $val)
        {
            if (empty($min) || $val < $min) {
                $min = $val;
            }
        }
        return $min;
    }

    public function slice($start, $len)
    {
        $list = $this->new_();
        for ($i = $start; $i < $start + $len; $i++)
        {
            $list->push($this[$i]);
        }
        return $list;
    }

    public function each($func)
    {
        foreach ($this->_list as $val)
        {
            if ($func($val) === false) {
                break;
            }
        }
        return $this;
    }

    public function each_index($func)
    {
        foreach ($this->to_a() as $key => $val)
        {
            if ($func($key) === false) {
                break;
            }
        }
        return $this;
    }

    public function count()
    {
        return count($this->_list);
    }

    public function map($func)
    {
        return new self(array_map($func, $this->_list));
    }

    public function grep($func)
    {
        $result = new List_Rubyfoo;
        foreach ($this->_list as $val)
        {
            if ($func($val)) {
                $result->push($val);
            }
        }
        return $result;
    }

    public function detect($func)
    {
        foreach ($this->_list as $val)
        {
            if ($func($val)) {
                return $val;
            }
        }
    }

    public function select($func)
    {
        $result = new List_Rubyfoo;
        foreach ($this->_list as $val)
        {
            if ($func($val)) {
                $result->push($val);
            }
        }
        return $result;
    }

    public function reduce($func)
    {
        return array_reduce($this->_list, $func);
    }

    public function sum()
    {
        return $this->reduce(function ($a, $b) { return $a + $b; });
    }

    public function dump() {}

    public function to_a()
    {
        return $this->_list;
    }

    public function all($func)
    {
        foreach ($this->_list as $val)
        {
            if (!$func($val)) {
                return false;
            }
        }
        return true;
    }

    public function any($func)
    {
        foreach ($this->_list as $val)
        {
            if ($func($val)) {
                return true;
            }
        }
        return false;
    }

    public function sort()
    {
        sort($this->_list);
        return $this;
    }

    public function sort_by() {}

    public function rewind()
    {
        $this->_pointer = 0;
    }

    public function current()
    {
        return $this->_list[$this->key()];
    }

    public function key()
    {
        return $this->_pointer;
    }

    public function valid()
    {
        return $this->key() < $this->count();
    }

    public function next()
    {
        $this->_pointer++;
    }

    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->_list)) {
            return $this->_list[$key];
        }
        throw new OutOfBoundsException;
    }

    public function offsetSet($key, $val)
    {
        $this->_list[$key] = $val;
        return $this;
    }

    public function offsetExists($key) {}

    public function offsetUnset($key) {}
}
