<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use Assert\Assertion;

final class Text implements ValueObject
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new static($value);
    }

    public static function fromInt(int $value): self
    {
        return new static((string) $value);
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
        ];
    }

    public static function fromArray(array $data)
    {
        Assertion::keyExists($data, 'value');

        return new static($data['value']);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function closeHtmlTags(string $value = null): ?string
    {
        $xhtml = ($value === null) ? $this->value : $value;

        $tags = [];
        for ($i = 0; preg_match('`<(/?)([a-z]+)(?:\s+[a-z]+="[^"]*")*>`i', $xhtml, $tag, PREG_OFFSET_CAPTURE, $i); $i = strlen($tag[0][0]) + $tag[0][1]) {
            if ($tag[1][0] != '/') {
                $tags[] = $tag[2][0];
            } elseif ($tag[2][0] == end($tags)) {
                array_pop($tags);
            } else {
                $xhtml = substr_replace($xhtml, '', $tag[0][1], strlen($tag[0][0]));
            }
        }

        $xhtml = preg_replace('`<[^>]*$`', '', $xhtml);
        while ($tag = array_pop($tags)) {
            $xhtml .= '</' . $tag . '>';
        }

        return $xhtml;
    }

    public function breakTextAt(int $maxSize, string $endChar = '</p>'): ?string
    {
        $str = $this->value;
        if (strlen($str) >= $maxSize) {
            $str = substr($str, 0, $maxSize);
            $lastSpace = strrpos($str, $endChar);
            if (is_bool($lastSpace)) {
                $lastSpace = 0;
            }
            $str = substr($str, 0, $lastSpace) . '...';
        }

        return $this->closeHtmlTags($str);
    }

    public function toTimeEspied(): string
    {
        $time = (int) $this->value;

        if ($time < 60) {
            $value = $time;
            $type = 'second(s)';
        } elseif ($time < (60 * 60)) {
            $value = round($time / 60);
            $type = 'minute(s)';
        } elseif ($time < (60 * 60 * 24)) {
            $value = round($time / (60 * 60));
            $type = 'hour(s)';
        } elseif ($time < (60 * 60 * 24 * 30)) {
            $value = round($time / (60 * 60 * 24));
            $type = 'day(s)';
        } elseif ($time < (60 * 60 * 24 * 30 * 12)) {
            $value = round($time / (60 * 60 * 24 * 30));
            $type = 'month(s)';
        } else {
            $value = round($time / (60 * 60 * 24 * 365));
            $type = 'year(s)';
        }

        return sprintf('Since %d %s ago', $value, $type);
    }
}
