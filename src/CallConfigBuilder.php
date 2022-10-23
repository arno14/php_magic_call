<?php

namespace Arno14\MagicCall;

use Exception;

class CallConfigBuilder
{
    private CallConfig $config;

    /**
     * @var string[][]
     */
    private ?array $extractedPhpDoc=null;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->config = new CallConfig($className);
    }

    /**
     * @param string|boolean $methodName name of the class method to redirect the magic call, if value is false, the property read is disabled
     */
    public function addPropertyRead(string $propertyName, string|bool $methodName=true): self
    {
        if (false===$methodName) {
            unset($this->config->property_read[$propertyName]);
            return $this;
        }
        if (is_string($methodName)) {
            if (!$this->config->reflection->hasMethod($methodName)) {
                throw new Exception(sprintf('unsupported read method [%s], for property [%s]', $methodName, $propertyName));
            }
        }

        $this->config->property_read[$propertyName]=$methodName;

        return $this;
    }

    /**
     * @param string|boolean $methodName name of the class method to redirect the magic call, if value is false, the property write is disabled
     */
    public function addPropertyWrite(string $propertyName, string|bool $methodName=true): self
    {
        if (false===$methodName) {
            unset($this->config->property_write[$propertyName]);
            return $this;
        }
        if (is_string($methodName)) {
            if (!$this->config->reflection->hasMethod($methodName)) {
                throw new Exception(sprintf('unsupported write method [%s], for property [%s]', $methodName, $propertyName));
            }
        }

        $this->config->property_write[$propertyName]=$methodName;

        return $this;
    }

    public function getConfig(): CallConfig
    {
        return $this->config;
    }

    public function guessFromPhpDoc(): self
    {
        return $this
            ->guessPropertyReadFromPhpDoc()
            ->guessPropertyWriteFromPhpDoc();
    }

    public function guessPropertyReadFromPhpDoc(): self
    {
        foreach ($this->extractPhpDoc(['@property','@property-read']) as $d) {
            $propertyName=$d['tag_value'];
            if ($this->config->reflection->hasProperty($propertyName)) {
                $this->addPropertyRead($propertyName);
                continue;
            }
            $this->config->debug_logs['guessPropertyReadFromPhpDoc_unknown_property='.$propertyName]=$d['line'];
        }

        return $this;
    }

    public function guessPropertyWriteFromPhpDoc(): self
    {
        foreach ($this->extractPhpDoc(['@property','@property-write']) as $d) {
            $propertyName=$d['tag_value'];
            if ($this->config->reflection->hasProperty($propertyName)) {
                $this->addPropertyWrite($propertyName);
                continue;
            }
            $this->config->debug_logs['guessPropertyWriteFromPhpDoc_unknown_property='.$propertyName]=$d['line'];
        }

        return $this;
    }

    /**
     * @param string[]|string $searchedTagNames
     * @return iterable<string[]>
     */
    private function extractPhpDoc(array|string $searchedTagNames): iterable
    {
        if (null===$this->extractedPhpDoc) {
            $this->extractedPhpDoc=[];
            $searchedTagNames=(array)$searchedTagNames;
            $phpdoc=(string)$this->config->reflection->getDocComment();

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
            if (in_array((string)$phpDoc['tag_name'], (array)$searchedTagNames)) {
                yield $phpDoc;
            }
        }
    }
}
