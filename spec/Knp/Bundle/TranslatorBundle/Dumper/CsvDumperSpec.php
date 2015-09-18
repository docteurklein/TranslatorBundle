<?php

namespace spec\Knp\Bundle\TranslatorBundle\Dumper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CsvDumperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\Bundle\TranslatorBundle\Dumper\CsvDumper');
    }

    function it_supports_csv_only()
    {
        $this->supports('test.txt')->shouldBe(false);
        $this->supports('test.csv')->shouldBe(true);
    }

    function it_updates_the_file()
    {
        $this->update('php://memory', 'id', 'value')->shouldReturn(true);
    }
}
