<?php

namespace Knp\Bundle\TranslatorBundle\Dumper;

use Knp\Bundle\TranslatorBundle\Dumper\Dumper;
use Symfony\Component\Yaml\Yaml;
use Knp\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException;

class CsvDumper implements Dumper
{
    public function supports($resource)
    {
        return 'csv' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    /**
     *
     * Updates the content of a csv file with value for the matched trans id
     */
    public function update($resource, $id, $value)
    {
        if ('' === $id) {
            throw new InvalidTranslationKeyException(
                sprintf('An empty key can not be used in "%s"', $resource)
            );
        }

        $lines = $this->all($resource);

        if(false === $fd = fopen($resource, 'r+b')) {
            throw new \InvalidArgumentException(sprintf('Error opening file "%s" for writing.', $resource));
        }
        // empty the file
        ftruncate($fd, 0);

        $updated = false;
        foreach ($lines as $data) {
            if(0 === strpos($data[0], '#')) {
                continue;
            }
            if ($id === $data[0]) {
                // this line is the one we want to update
                $data[1] = $value;
                $updated = true;
            }
            fputcsv($fd, $data, ';', '"');
        }

        if (false === $updated) {
            $updated = false !== fputcsv($fd, array($id, $value), ';', '"');
        }
        fclose($fd);

        return $updated;
    }

    /**
     *
     * @return array
     */
    private function all($resource)
    {
        try {
            $file = new \SplFileObject($resource, 'rb');
        } catch(\RuntimeException $e) {
            throw new \InvalidArgumentException(sprintf('Error opening file "%s".', $resource));
        }

        $file->setFlags(\SplFileObject::SKIP_EMPTY | \SplFileObject::READ_CSV);
        $file->setCsvControl(';');

        $lines = array();
        // iterate over the file's rows
        // fgets increments file descriptor to next line
        while($data = $file->fgetcsv()) {
            $lines[] = $data;
        }

        return $lines;
    }
}
