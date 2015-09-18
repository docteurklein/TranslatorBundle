<?php

namespace spec\Knp\Bundle\TranslatorBundle\Dumper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class XliffDumperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\Bundle\TranslatorBundle\Dumper\XliffDumper');
    }

    function it_supports_xliff_only()
    {
        $this->supports('test.txt')->shouldBe(false);
        $this->supports('test.xlf')->shouldBe(true);
    }

    function it_updates_the_file()
    {
        $this->update(__DIR__.'/tmp.xlf', 'id', 'value');
    }
}
