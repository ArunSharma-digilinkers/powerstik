<?php

namespace App\Support\Admin;

/**
 * Describes one form field on a generic admin edit screen. The type decides
 * the input partial (resources/views/admin/resource/fields/{type}) and the
 * default validation; see ResourceController::save() for persistence.
 */
class Field
{
    public array $rules = [];

    public ?string $help = null;

    public array $options = [];

    public bool $full = false;

    public string $dir = 'misc';

    public bool $allowSvg = false;

    public mixed $default = null;

    public function __construct(
        public string $type,
        public string $name,
        public string $label,
    ) {}

    public static function text(string $name, string $label): static
    {
        return new static('text', $name, $label);
    }

    public static function slug(string $name = 'slug', string $label = 'URL slug'): static
    {
        return (new static('slug', $name, $label))->help('Leave empty to generate from the name.');
    }

    public static function textarea(string $name, string $label): static
    {
        return (new static('textarea', $name, $label))->full();
    }

    public static function richtext(string $name, string $label): static
    {
        return (new static('richtext', $name, $label))->full();
    }

    public static function number(string $name, string $label): static
    {
        return new static('number', $name, $label);
    }

    public static function email(string $name, string $label): static
    {
        return new static('email', $name, $label);
    }

    public static function password(string $name, string $label): static
    {
        return new static('password', $name, $label);
    }

    public static function url(string $name, string $label): static
    {
        return new static('url', $name, $label);
    }

    public static function datetime(string $name, string $label): static
    {
        return new static('datetime', $name, $label);
    }

    public static function toggle(string $name, string $label): static
    {
        return new static('toggle', $name, $label);
    }

    public static function select(string $name, string $label, array $options): static
    {
        return (new static('select', $name, $label))->options($options);
    }

    /** Many-to-many checkboxes; $name is the relation method. */
    public static function checkboxes(string $name, string $label, array $options): static
    {
        return (new static('checkboxes', $name, $label))->options($options)->full();
    }

    public static function image(string $name, string $label, string $dir): static
    {
        return (new static('image', $name, $label))->dir($dir);
    }

    public static function gallery(string $name, string $label, string $dir): static
    {
        return (new static('gallery', $name, $label))->dir($dir)->full();
    }

    public static function file(string $name, string $label, string $dir): static
    {
        return (new static('file', $name, $label))->dir($dir);
    }

    /** JSON list of {label, value} rows, e.g. machine specs. */
    public static function keyvalue(string $name, string $label): static
    {
        return (new static('keyvalue', $name, $label))->full();
    }

    public function rules(string|array ...$rules): static
    {
        $this->rules = array_merge($this->rules, $rules);

        return $this;
    }

    public function required(): static
    {
        return $this->rules('required');
    }

    public function help(string $help): static
    {
        $this->help = $help;

        return $this;
    }

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function full(bool $full = true): static
    {
        $this->full = $full;

        return $this;
    }

    public function dir(string $dir): static
    {
        $this->dir = $dir;

        return $this;
    }

    public function allowSvg(): static
    {
        $this->allowSvg = true;

        return $this;
    }

    public function default(mixed $value): static
    {
        $this->default = $value;

        return $this;
    }
}
