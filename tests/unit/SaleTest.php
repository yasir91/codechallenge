<?php

class SaleTest extends \PHPUnit\Framework\TestCase
{
    //@todo autoloading was not working, will see later.
    protected function setUp(): void
    {
        include_once 'config/init.php';
        include_once 'app/lib/Sale.php';
        include_once 'app/lib/Database.php';
    }

    public function testCompareVersionAndReturnDateInUTC()
    {
        $sale = new Sale;

        //check if version is above or equal 1.0.17+60 return same date
        $this->assertEquals("2019-07-01 15:01:13", $sale->compareVersion("1.0.17+65", "2019-07-01 15:01:13"));

        //check if version is above or equal 1.0.17+60 return same date
        $this->assertEquals("2019-08-07 19:08:56", $sale->compareVersion("1.1.3", "2019-08-07 19:08:56"));

        //check if version is below 1.0.17+60 return UTC converted date
        $this->assertEquals("2019-05-06 12:26:14", $sale->compareVersion("1.0.15+83", "2019-05-06 14:26:14"));

        //check if version is below 1.0.17+60 return UTC converted date
        $this->assertEquals("2019-05-01 09:07:18", $sale->compareVersion("1.0.17+59", "2019-05-01 11:07:18"));
    }
}
