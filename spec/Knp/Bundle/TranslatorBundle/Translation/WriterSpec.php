<?php

namespace spec\Knp\Bundle\TranslatorBundle\Translation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Knp\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException;
use Knp\Bundle\TranslatorBundle\Dumper\Dumper;

class WriterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([['messages.fr.csv']]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\Bundle\TranslatorBundle\Translation\Writer');
    }

    function it_throws_if_invalid_key()
    {
        $this->shouldThrow(new InvalidTranslationKeyException('Empty key not allowed'))->during('write', ['', '', '', '']);
    }

    function it_uses_supported_dumpers(Dumper $csv, Dumper $xlf)
    {
        $csv->supports('messages.fr.csv')->willReturn(true);
        $csv->update(Argument::cetera())->shouldBeCalled()->willReturn(true);
        $this->addDumper($csv);

        $xlf->supports('messages.fr.csv')->willReturn(false);
        $xlf->update(Argument::cetera())->shouldNotBeCalled();
        $this->addDumper($xlf);

        $this->write('test', '', 'messages', 'fr');
    }
}
