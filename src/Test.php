<?php
/**
 * Test.php
 *
 * @author  shuixuqiang <email>
 * @since   2021/9/3 12:10 下午
 * @version 0.1
 */
namespace func;

class Test
{

    public function test1($a, $b)
    {
        return ($a + $b);
    }

    public function test2($a)
    {
        return printf("我们%s中国式", $a);
    }
}