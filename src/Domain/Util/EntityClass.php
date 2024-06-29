<?php

declare(strict_types=1);

namespace App\Domain\Util;

class EntityClass
{
    private array $implements;
    private array $extends;

    public function __construct(
        public readonly string $template,
        array|string $implements = [],
        array|string $extends = [],
        private array $attributes = [],
        public readonly string $type = 'class'
    ) {
        $this->implements = is_string($implements) ? [$implements] : $implements;
        $this->extends = is_string($extends) ? [$extends] : $extends;
    }

    public function getExtends(): string
    {
        if (empty($this->extends)) {
            return '';
        }

        return $this->getVal('extends', $this->extends);
    }

    public function getImplements(): string
    {
        if (empty($this->implements)) {
            return '';
        }

        return $this->getVal('implements', $this->implements);
    }

    public function getAttributes(): string
    {
        $returnValArr = [];
        foreach ($this->attributes as $attrClass => $attrMod) {

            $attrClassBaseName = substr($attrClass, strrpos($attrClass, '\\') + 1);
            $attrName = '$' . lcfirst($attrClassBaseName);
            $returnValArr[] = "{$attrMod} \\{$attrClass} {$attrName}";
        }

        return implode(','.PHP_EOL, $returnValArr);
    }

    private function getVal(string $type, array $val): string
    {
        return $type.' '.implode(', ', $this->implements);
    }
}