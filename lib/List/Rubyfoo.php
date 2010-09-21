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
        'collect'   => 'map',
        'collect_'  => 'map_',
        'find'      => 'detect',
        'find_all'  => 'select'
    );

    /**
     * Constructor.
     * alias => delegated
     *
     * <code>
     * // Following is the same in meaning.
     * $list = new List_Rubyfoo(array(1, 2, 3, 4, 5));
     * $list = new List_Rubyfoo(1, 2, 3, 4, 5);
     * </code>
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
     * Property overloading.
     * Properties defined in self::$_overloadProps calls method.
     *
     * @param  string $key
     * @return mixed
     * @see    self::$_overloadedProps
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
     * Method alias.
     * Some methods have alias.
     *
     * @param  string $method name of called method
     * @param  array  $args   arguments
     * @return mixed
     * @see    self::$_aliases
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

    /**
     * First element.
     *
     * <code>
     * echo $list->first();
     * echo $list->first; // Calling as property is also available.
     * </code>
     */
    public function first()
    {
        return $this->_list[0];
    }

    /**
     * Last element.
     *
     * <code>
     * echo $list->last();
     * echo $list->last; // Calling as property is also available.
     * </code>
     */
    public function last()
    {
        return $this->_list[count($this->_list) - 1];
    }

    public function max()
    {
        foreach ($this as $val)
        {
            if (empty($max) || $val > $max) {
                $max = $val;
            }
        }
        return $max;
    }

    public function min()
    {
        foreach ($this as $val)
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

    /**
     * Method as an internal iterator.
     * If false is returned, it means the same as break.
     *
     * <code>
     * $list->each(function ($val)
     * {
     *     echo $val;
     * });
     * </code>
     *
     * @param  callback $func
     * @return List_Rubyfoo
     */
    public function each($func)
    {
        foreach ($this as $val)
        {
            if ($func($val) === false) {
                break;
            }
        }
        return $this;
    }

    public function each_index($func)
    {
        foreach ($this as $key => $val)
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
        return $this->_clone()->map_($func);
    }

    public function map_($func)
    {
        foreach ($this as $key => $val)
        {
            $this[$key] = $func($val);
        }
        return $this;
    }

    public function grep($func)
    {
        $result = new List_Rubyfoo;
        foreach ($this as $val)
        {
            if ($func($val)) {
                $result->push($val);
            }
        }
        return $result;
    }

    public function detect($func)
    {
        foreach ($this as $val)
        {
            if ($func($val)) {
                return $val;
            }
        }
    }

    public function select($func)
    {
        $result = new List_Rubyfoo;
        foreach ($this as $val)
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
        foreach ($this as $val)
        {
            if (!$func($val)) {
                return false;
            }
        }
        return true;
    }

    public function any($func)
    {
        foreach ($this as $val)
        {
            if ($func($val)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sorts elements.
     * It's non-destructive.
     *
     * @return List_Rubyfoo
     */
    public function sort()
    {
        return $this->_clone()->sort_();
    }

    /**
     * Sorts elements.
     * It's destructive.
     *
     * @return List_Rubyfoo
     */
    public function sort_()
    {
        sort($this->_list);
        return $this;
    }

    public function sort_by() {}

    /**
     * Rewinds the iterator.
     *
     * @return void
     * @see    Iterator::rewind()
     */
    public function rewind()
    {
        $this->_pointer = 0;
    }

    /**
     * Current element.
     *
     * @return mixed
     * @see    Iterator::current()
     */
    public function current()
    {
        return $this->_list[$this->key()];
    }

    /**
     * Pointer of the current element.
     *
     * @return int
     * @see    Iterator::key()
     */
    public function key()
    {
        return $this->_pointer;
    }

    /**
     * Whether iterator has current element or not.
     *
     * @return bool
     * @see    Iterator::valid()
     */
    public function valid()
    {
        return $this->key() < $this->count();
    }

    /**
     * Moves forward to next
     *
     * @return void
     * @see    Iterator::next()
     */
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

    /**
     * Creates clone of the object itself.
     *
     * @return List_Rubyfoo
     */
    protected function _clone()
    {
        return clone $this;
    }
}
