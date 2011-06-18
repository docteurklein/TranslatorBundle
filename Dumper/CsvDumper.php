<?php

namespace Knplabs\Bundle\TranslatorBundle\Dumper;

use Knplabs\Bundle\TranslatorBundle\Dumper\DumperInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;
use Knplabs\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException;

class CsvDumper implements DumperInterface
{
    public function supports(FileResource $resource)
    {
        return 'csv' === pathinfo($resource->getResource(), PATHINFO_EXTENSION);
    }

    /**
     *
     * Updates the content of a csv file with value for the matched trans id
     */
    public function update(FileResource $resource, $id, $value)
    {
        if ('' === $id) {
            throw new InvalidTranslationKeyException(
                sprintf('An empty key can not be used in "%s"', $resource->getResource())
            );
        }

        $lines = $this->all($resource);

        if(false === $fd = fopen($resource->getResource(), 'r+b')) {
            throw new \InvalidArgumentException(sprintf('Error opening file "%s" for writing.', $resource->getResource()));
        }
        // empty the file
        ftruncate($fd, 0);

        $updated = false;
        foreach ($lines as $data) {
            if (is_array($data)) {
                // it's a csv's line parsed array
                if ($id === $data[0]) {
                    // this line is the one we want to update
                    $data[1] = $value;
                    $updated = false !== fputcsv($fd, $data, ';');
                }
                else {
                    fputcsv($fd, $data, ';', '"');
                }
            }
            else {
                // it's a commented line, put it rawly
                fputs($fd, $data);
            }
        }
        fclose($fd);

        if (false === $updated) {
            throw new InvalidTranslationKeyException(
                sprintf('The key "%s" can not be found in "%s"', $id, $resource->getResource())
            );
        }

        return $updated;
    }

    /**
     *
     * @return array
     */
    private function all($resource)
    {
        try {
            $file = new \SplFileObject($resource->getResource(), 'rb');
        } catch(\RuntimeException $e) {
            throw new \InvalidArgumentException(sprintf('Error opening file "%s".', $resource->getResource()));
        }

        $file->setFlags(\SplFileObject::SKIP_EMPTY);

        $lines = array();
        // iterate over the file's rows
        // fgets increments file descriptor to next line
        while($data = $file->fgets()) {
            if (substr($data, 0, 1) === '#') {
                // # is first char, it's a comment
                $lines[] = $data;
            }
            else {
                $line = str_getcsv($data, ';');
                if (2 === count($line)) {
                    // only use parsed csv line if valid translation line ( 2 columns )
                    $lines[] = $line;
                }
                else {
                    $lines[] = $data;
                }
            }
        }

        return $lines;
    }
}
