<?php
require_once '../test_helper.php';
require_once 'List/Rubyfoo.php';

class List_RubyfooTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var List_Rubyfoo
     */
    protected $_list;

    protected function setUp()
    {
        $this->_list = List_Rubyfoo::new_(1, 2, 3, 4, 5);
    }

    public function testNew_()
    {
        $expected = clone $this->_list;
        $this->assertEquals($expected, $this->_list->new_(1, 2, 3, 4, 5));
        $this->assertEquals($expected, new List_Rubyfoo(1, 2, 3, 4, 5));
    }

    public function testNew_static()
    {
        $this->assertEquals($this->_list, List_Rubyfoo::new_(1, 2, 3, 4, 5));
    }

    public function testPush()
    {
        $expected = List_Rubyfoo::new_(1, 2, 3, 4, 5, 6);
        $this->assertEquals($expected, $this->_list->push(6), 'push() adds an element on end of list.');
    }

    public function testPushWithArrayAccess()
    {
        $expected = List_Rubyfoo::new_(1, 2, 3, 4, 5, 6);
        $this->_list[] = 6;
        $this->assertEquals($expected, $this->_list);
    }

    public function testPop()
    {
        $this->assertEquals(5, $this->_list->pop(), 'pop() gets the last element.');
    }

    public function testPop2()
    {
        $expected = List_Rubyfoo::new_(1, 2, 3, 4);
        $this->_list->pop();
        $this->assertEquals($expected, $this->_list);
    }

    public function testShift()
    {
        $this->assertEquals(1, $this->_list->shift());
    }

    public function testShift_2()
    {
        $expected = List_Rubyfoo::new_(2, 3, 4, 5);
        $this->_list->shift();
        $this->assertEquals($expected, $this->_list);
    }

    public function testUnshift()
    {
        $expected = List_Rubyfoo::new_(0, 1, 2, 3, 4, 5);
        $this->assertEquals($expected, $this->_list->unshift(0));
    }

    public function testJoin()
    {
        $this->assertEquals('1,2,3,4,5', $this->_list->join(','), 'join() joins each elements with glue.');
    }

    public function testFirst()
    {
        $this->assertEquals(1, $this->_list->first());
        $this->assertEquals(1, $this->_list->first);
    }

    public function testLast()
    {
        $this->assertEquals(5, $this->_list->last());
        $this->assertEquals(5, $this->_list->last);
    }

    public function testMax()
    {
        $this->assertEquals(5, $this->_list->max());
        $this->assertEquals(5, $this->_list->max);
    }

    public function testMin()
    {
        $this->assertEquals(1, $this->_list->min());
        $this->assertEquals(1, $this->_list->min);
    }

    public function testSlice()
    {
        $expected = List_Rubyfoo::new_(2, 3);
        $this->assertEquals($expected, $this->_list->slice(1, 2));
    }

    public function testEach()
    {
        $result = '';
        $this->_list->each(function ($n) use (&$result)
        {
            $result .= "{$n},";
        });
        $this->assertEquals('1,2,3,4,5,', $result);
    }

    public function testEachBreaksWhenFalseIsReturned()
    {
        $result = '';
        $this->_list->each(function ($n) use (&$result)
        {
            if ($n > 3) {
                return false;
            }
            $result .= "{$n},";
        });
        $this->assertEquals('1,2,3,', $result);
    }

    public function testEach_index()
    {
        $result = '';
        $this->_list->each_index(function ($key) use (&$result)
        {
            $result .= $key . ',';
        });
        $this->assertEquals('0,1,2,3,4,', $result);
    }

    public function testEach_indexBreaksWhenFalseIsReturned()
    {
        $result = '';
        $this->_list->each_index(function ($key) use (&$result)
        {
            if ($key > 3) {
                return false;
            }
            $result .= $key . ',';
        });
        $this->assertEquals('0,1,2,3,', $result);
    }

    public function testCount()
    {
        $this->assertEquals(5, $this->_list->count());
        $this->assertEquals(5, $this->_list->size);
        $this->assertEquals(5, $this->_list->length);
        $this->assertEquals(5, count($this->_list));
    }

    public function testMap()
    {
        $expected = List_Rubyfoo::new_(1, 4, 9, 16, 25);
        $func     = function ($x) { return $x * $x; };
        $this->assertEquals($expected->to_a(), $this->_list->map($func)->to_a());
        $this->assertEquals($expected->to_a(), $this->_list->collect($func)->to_a());
        $this->assertNotSame($this->_list, $this->_list->map($func));
    }

    public function testMap_()
    {
        $func    = function ($x) { return $x * $x; };
        $sortedA = $this->_list->map_($func);
        $sortedB = $this->_list->collect_($func);
        $this->assertSame($this->_list, $sortedA);
        $this->assertSame($this->_list, $sortedB);
    }

    public function testGrep()
    {
        $expected = List_Rubyfoo::new_(4, 5);
        $this->assertEquals($expected, $this->_list->grep(function ($x) { return $x > 3; }));
    }

    public function testFind()
    {
        $this->assertEquals(2, $this->_list->find(function ($x) { return $x % 2 === 0; }));
        $this->assertEquals(2, $this->_list->detect(function ($x) { return $x % 2 === 0; }));
    }

    public function testSelect()
    {
        $expected = List_Rubyfoo::new_(2, 4);
        $this->assertEquals($expected, $this->_list->select(function ($x) { return $x % 2 === 0; }));
        $this->assertEquals($expected, $this->_list->find_all(function ($x) { return $x % 2 === 0; }));
    }

    public function testReduce()
    {
        $this->assertEquals(15, $this->_list->reduce(function ($a, $b) { return $a + $b; }));
    }

    public function testSum()
    {
        $this->assertEquals(15, $this->_list->sum());
    }

    public function testDump()
    {
        $dump = $this->_list->dump(true);
        eval("\$clone = {$dump};");
        $this->assertEquals($this->_list, $clone);
    }

    public function testDumpIteratorPointer()
    {
        $this->_list->next();
        $dump = $this->_list->dump(true);
        eval("\$clone = {$dump};");
        $this->assertEquals($this->_list, $clone);
    }

    public function testTo_a()
    {
        $this->assertEquals(array(1, 2, 3, 4, 5), $this->_list->to_a());
    }

    public function testAll()
    {
        $this->assertTrue($this->_list->all(function ($x) { return $x < 6; }));
    }

    public function testAll_fail()
    {
        $this->assertFalse($this->_list->all(function ($x) { return $x < 5; }));
    }

    public function testAny()
    {
        $this->assertTrue($this->_list->any(function ($x) { return $x === 5; }));
    }

    public function testAny_fail()
    {
        $this->assertFalse($this->_list->any(function ($x) { return $x === 6; }));
    }

    public function testSort()
    {
        $unsorted = List_Rubyfoo::new_(5, 1, 4, 2, 3);
        $sorted   = $unsorted->sort();
        $this->assertEquals($this->_list, $sorted);
        $this->assertNotSame($sorted, $unsorted);
    }

    public function testSort_()
    {
        $expected = clone $this->_list;
        $unsorted = List_Rubyfoo::new_(5, 1, 4, 2, 3);
        $actual   = $unsorted->sort_();
        $this->assertEquals($expected, $actual);
        $this->assertSame($unsorted, $actual);
    }

    /**
     * @todo Implement testSort_by().
     */
    public function testSort_by()
    {
        $this->markTestIncomplete();
        $expected = List_Rubyfoo::new_('z', 'cc', 'bbb', 'xxxx', 'aaaaa');
        $sorted   = List_Rubyfoo::new_('aaaaa', 'cc', 'bbb', 'xxxx', 'z')->sort_by(function ($val)
        {
            return strlen($val);
        });
        $this->assertEquals($expected, $sorted);
    }

    public function testRewind()
    {
        $this->_list->next();
        $this->_list->rewind();
        $this->assertEquals(1, $this->_list->current());
    }

    public function testCurrent()
    {
        $this->assertEquals(1, $this->_list->current());
        $this->_list->next();
        $this->assertEquals(2, $this->_list->current());
    }

    public function testKey()
    {
        $this->assertEquals(0, $this->_list->key());
        $this->_list->next();
        $this->assertEquals(1, $this->_list->key());
    }

    public function testValid()
    {
        $this->assertTrue($this->_list->valid());
        $this->_list->next();
        $this->_list->next();
        $this->_list->next();
        $this->_list->next();
        $this->assertTrue($this->_list->valid());
        $this->_list->next();
        $this->assertFalse($this->_list->valid());
    }

    public function testOffsetGet()
    {
        $this->assertEquals(5, $this->_list[4]);
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testOffsetGet_exception()
    {
        $this->_list[5];
    }

    public function testOffsetSet()
    {
        $expected = List_Rubyfoo::new_(5, 2, 3, 4, 5);
        $this->_list[0] = 5;
        $this->assertEquals($expected, $this->_list);
    }

    /**
     * @todo Implement testOffsetExists().
     */
    public function testOffsetExists()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOffsetUnset().
     */
    public function testOffsetUnset()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
