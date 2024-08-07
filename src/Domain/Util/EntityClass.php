<?php

declare(strict_types=1);

namespace App\Domain\Util;

class EntityClass
{
    /** @var array<string> */
    private array $implements;

    /** @var array<string> */
    private array $extends;

    /**
     * Undocumented function.
     *
     * @param array<string>|string  $implements
     * @param array<string>|string  $extends
     * @param array<string, string> $attributes
     */
    public function __construct(
        public readonly string $template,
        array|string $implements = [],
        array|string $extends = [],
        private array $attributes = [],
        public readonly string $type = 'class'
    ) {
        $this->implements = array_map(fn (string $implement) => '\\'.$implement, is_string($implements) ? [$implements] : $implements);
        $this->extends = array_map(fn (string $extend) => '\\'.$extend, is_string($extends) ? [$extends] : $extends);
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
            $attrName = '$'.lcfirst($attrClassBaseName);
            $returnValArr[] = "{$attrMod} \\{$attrClass} {$attrName}";
        }

        return implode(','.PHP_EOL, $returnValArr);
    }

    /**
     * @param array<string> $val
     */
    private function getVal(string $type, array $val): string
    {
        return $type.' '.implode(', ', $val);
    }
}
