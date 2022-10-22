<?php

namespace Arno14\MagicCall;

use Exception;

class CallConfigBuilder
{
    private CallConfig $configuration;

    private ?array $extractedPhpDoc=null;

    public function __construct(private string $className)
    {
        $this->configuration = new CallConfig($className);
    }

    public function addPropertyRead(string $propertyName, string|bool $methodName=true): self
    {
        if (false===$methodName) {
            unset($this->configuration->property_read[$propertyName]);
            return $this;
        }
        if (is_string($methodName)) {
            if (!$this->configuration->reflection->hasMethod($methodName)) {
                throw new Exception(sprintf('unsupported read method [%s], for property [%s]', $methodName, $propertyName));
            }
        }

        $this->configuration->property_read[$propertyName]=$methodName;

        return $this;
    }

    public function addPropertyWrite(string $propertyName, string|bool $methodName=true): self
    {
        if (false===$methodName) {
            unset($this->configuration->property_write[$propertyName]);
            return $this;
        }
        if (is_string($methodName)) {
            if (!$this->configuration->reflection->hasMethod($methodName)) {
                throw new Exception(sprintf('unsupported write method [%s], for property [%s]', $methodName, $propertyName));
            }
        }

        $this->configuration->property_write[$propertyName]=$methodName;

        return $this;
    }

    public function getConfiguration(): CallConfig
    {
        return $this->configuration;
    }

    public function guessPropertyReadFromPhpDoc(): self
    {
        foreach ($this->extractPhpDoc(['@property','@property-read']) as $d) {
            $propertyName=$d['tag_value'];
            if ($this->configuration->reflection->hasProperty($propertyName)) {
                $this->addPropertyRead($propertyName);
                continue;
            }
            $this->configuration->debug_logs['guessPropertyReadFromPhpDoc_unknown_property='.$propertyName]=$d['line'];
        }

        return $this;
    }

    public function guessPropertyWriteFromPhpDoc(): self
    {
        foreach ($this->extractPhpDoc(['@property','@property-write']) as $d) {
            $propertyName=$d['tag_value'];
            if ($this->configuration->reflection->hasProperty($propertyName)) {
                $this->addPropertyWrite($propertyName);
                continue;
            }
            $this->configuration->debug_logs['guessPropertyWriteFromPhpDoc_unknown_property='.$propertyName]=$d['line'];
        }

        return $this;
    }

    private function extractPhpDoc(array|string $searchedTagNames): iterable
    {
        if (null===$this->extractedPhpDoc) {
            $searchedTagNames=(array)$searchedTagNames;
            $phpdoc=(string)$this->configuration->reflection->getDocComment();

            foreach (explode(PHP_EOL, $phpdoc) as $line) {
                $line = str_replace(['*'], '', $line);

                $explodedLine=explode(' ', $line);
                $explodedLine=array_filter($explodedLine);
                if (count($explodedLine)<3) {
                    continue;
                }

                $tagName=null;
                $tagValue=null;

                $iterated=0;
                foreach ($explodedLine as $word) {
                    if (null===$tagName) {
                        $tagName=$word;
                        continue;
                    }
                    if ('static'===$word) {
                        continue;
                    }
                    $iterated++;
                    if ($iterated===2) {
                        $tagValue=$word;
                    }
                }
                $tagValue=(string)$tagValue;
                $tagValue=explode('(', $tagValue)[0];
                $tagValue=str_replace('$', '', $tagValue);

                $this->extractedPhpDoc[]=[
                    'tag_name'=>$tagName,
                    'tag_value'=>$tagValue,
                    'line'=>$line
                ];
            }
        }

        foreach ($this->extractedPhpDoc as $phpDoc) {
            if (in_array($phpDoc['tag_name'], $searchedTagNames)) {
                yield $phpDoc;
            }
        }
    }
}
