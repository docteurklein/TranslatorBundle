<?php

namespace spec\Knp\Bundle\TranslatorBundle\Dumper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class YamlDumperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\Bundle\TranslatorBundle\Dumper\YamlDumper');
    }

    function it_supports_yaml_only()
    {
        $this->supports('test.txt')->shouldBe(false);
        $this->supports('test.yml')->shouldBe(true);
    }

    function it_updates_the_file()
    {
        $this->update('php://memory', 'id', 'value');
    }
}
