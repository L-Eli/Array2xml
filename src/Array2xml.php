<?php

namespace Eli2n\Array2xml;

/**
 * Class Array2xml
 *
 * @package Eli2n\Array2xml
 */
class Array2xml
{
    /**
     * Version
     *
     * @var string
     */
    private $version = '1.0';

    /**
     * Encoding
     *
     * @var string
     */
    private $encoding = 'UTF-8';

    /**
     * Name
     *
     * @var string
     */
    private $name = 'root';

    /**
     * Data
     *
     * @var array
     */
    private $data = [];

    /**
     * Convert array $data to XML document
     *
     * @codeCoverageIgnore
     *
     * @param array $data
     * @param string $tag_name
     * @param \SimpleXMLElement|null $simple_xml_element
     *
     * @return \SimpleXMLElement
     */
    private function parse(array $data, string $tag_name = '', \SimpleXMLElement $simple_xml_element = null)
    {
        if (!(is_object($simple_xml_element) and $simple_xml_element instanceof \SimpleXMLElement)) {
            $simple_xml_element = new \SimpleXMLElement($this->getTemplate());
        }

        foreach ($data as $name => $value) {
            if (is_string($value)) {
                if (is_numeric($name)) {
                    if ($tag_name) {
                        $simple_xml_element->addChild($tag_name, $value);
                        continue;
                    }
                    $simple_xml_element[0] = $value;
                    continue;
                }
                $simple_xml_element->addChild($name, $value);
                continue;
            }

            if ('@attributes' === $name) {
                $this->parseAttributes($value, $simple_xml_element);
                continue;
            }

            if (array_values($value) === $value) {
                $this->parse($value, $name, $simple_xml_element);
                continue;
            }

            if (is_numeric($name)) {
                $this->parse($value, '', $simple_xml_element->addChild($tag_name));
                continue;
            }

            $this->parse($value, $name, $simple_xml_element->addChild($name));
        }

        return $simple_xml_element;
    }

    /**
     * Convert array $attributes to XML attributes
     *
     * @param array $attributes
     * @param \SimpleXMLElement $simple_xml_element
     */
    private function parseAttributes(array $attributes, \SimpleXMLElement $simple_xml_element)
    {
        foreach ($attributes as $name => $value) {
            $simple_xml_element->addAttribute($name, $value);
        }
    }

    /**
     * Set version option
     *
     * @param string $version
     * @return $this
     */
    public function setVersion(string $version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version option
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set encoding option
     *
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding(string $encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * Get encoding option
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set name option
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name option
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set data expect to convert
     *
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data expect to convert
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Array2xml constructor.
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = [], array $options = [])
    {
        $this->data = $data;
        $this->setOptions($options);
    }

    /**
     * Set xml document options
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{ 'set' . ucwords($key) }($value);
            }
        }
    }

    /**
     * Get XML template by options
     *
     * @return string
     */
    public function getTemplate()
    {
        return sprintf('<?xml version="%s" encoding="%s"?><%s></%s>',
            $this->version,
            $this->encoding,
            $this->name,
            $this->name
        );
    }

    /**
     * Get parsed XML document
     *
     * @param array $data
     *
     * @return mixed
     */
    public function get(array $data = [])
    {
        return $this->parse($data ?: $this->data)->asXML();
    }
}
