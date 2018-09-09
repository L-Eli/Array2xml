<?php

use Eli2n\Array2xml\Array2xml;
use PHPUnit\Framework\TestCase;

class Array2xmlTest extends TestCase
{
    /**
     * Test accessor version with wrong type
     *
     * @expectedException TypeError
     */
    public function testSetVersionWrongType()
    {
        $array2xml = new Array2xml;
        $array2xml->setVersion([]);
    }

    /**
     * Test accessor version
     *
     * @param $version
     *
     * @dataProvider versionProvider
     */
    public function testAccessorVersion($version)
    {
        $array2xml = new Array2xml;
        $array2xml->setVersion($version);

        $this->assertSame($version, $array2xml->getVersion());
    }

    /**
     * Provide "version" data
     *
     * @return array
     */
    public function versionProvider()
    {
        return [
            ['1.0'],
            ['2.0'],
        ];
    }

    /**
     * Test accessor encoding with wrong type
     *
     * @expectedException TypeError
     */
    public function testSetEncodingWrongType()
    {
        $array2xml = new Array2xml;
        $array2xml->setVersion([]);
    }

    /**
     * Test accessor encoding
     *
     * @param $encoding
     *
     * @dataProvider encodingProvider
     */
    public function testAccessorEncoding($encoding)
    {
        $array2xml = new Array2xml;
        $array2xml->setEncoding($encoding);

        $this->assertSame($encoding, $array2xml->getEncoding());
    }

    /**
     * Provide "encoding" data
     *
     * @return array
     */
    public function encodingProvider()
    {
        return [
            ['UTF-8'],
            ['UTF-16'],
        ];
    }

    /**
     * Test accessor name with wrong type
     *
     * @expectedException TypeError
     */
    public function testSetNameWrongType()
    {
        $array2xml = new Array2xml;
        $array2xml->setName([]);
    }

    /**
     * Test accessor name
     *
     * @param $name
     *
     * @dataProvider nameProvider
     */
    public function testAccessorName($name)
    {
        $array2xml = new Array2xml;
        $array2xml->setName($name);

        $this->assertSame($name, $array2xml->getName());
    }

    /**
     * Provide "name" data
     *
     * @return array
     */
    public function nameProvider()
    {
        return [
            ['name1'],
            ['name2'],
        ];
    }

    /**
     * Test accessor data with wrong type
     *
     * @expectedException TypeError
     */
    public function testSetDataWrongType()
    {
        $array2xml = new Array2xml;
        $array2xml->setData('');
    }

    /**
     * Test accessor data
     *
     * @param $data
     *
     * @dataProvider dataProvider
     */
    public function testAccessorData($data)
    {
        $array2xml = new Array2xml;
        $array2xml->setData($data);

        $this->assertSame($data, $array2xml->getData());
    }

    /**
     * Provide "data" data
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            [[]],
            [['data']],
        ];
    }

    /**
     * Test setOptions()
     *
     * @param $options
     *
     * @dataProvider optionsProvider
     */
    public function testSetOptions($options)
    {
        $array2xml = new Array2xml;
        $array2xml->setOptions($options);

        $this->assertSame($options, [
            'version' => $array2xml->getVersion(),
            'encoding' => $array2xml->getEncoding(),
            'name' => $array2xml->getName(),
            'data' => $array2xml->getData(),
        ]);
    }

    /**
     * Test getTemplate()
     *
     * @dataProvider optionsProvider
     *
     * @param $options
     */
    public function testGetTemplate($options)
    {
        $array2xml = new Array2xml;
        $array2xml->setOptions($options);

        $this->assertSame(sprintf('<?xml version="%s" encoding="%s"?><%s></%s>',
            $options['version'],
            $options['encoding'],
            $options['name'],
            $options['name']
        ), $array2xml->getTemplate());
    }

    /**
     * Provide options data
     *
     * @return array
     */
    public function optionsProvider()
    {
        return [
            [
                [
                    'version' => '1.0',
                    'encoding' => 'UTF-8',
                    'name' => 'name1',
                    'data' => [],
                ],
            ],
            [
                [
                    'version' => '2.0',
                    'encoding' => 'UTF-16',
                    'name' => 'name2',
                    'data' => ['data'],
                ]
            ],
        ];
    }

    /**
     * Test get()
     *
     * @dataProvider xmlStringProvider
     *
     * @param $xml_string
     */
    public function testGet($xml_string)
    {
        $simple_xml = simplexml_load_string($xml_string);

        $name = $simple_xml->getName();
        $data = json_decode(json_encode($simple_xml), true);

        $array2xml = new Array2xml($data, compact('name'));

        $expected_string = preg_replace('/\s+/S', '', $xml_string);
        $result_string = preg_replace('/\s+/S', '', $array2xml->get());

        $this->assertSame($expected_string, $result_string);
    }

    /**
     * Test get() by passing data
     *
     * @dataProvider xmlStringProvider
     *
     * @param $xml_string
     */
    public function testGetByPassingData($xml_string)
    {
        $simple_xml = simplexml_load_string($xml_string);

        $name = $simple_xml->getName();
        $data = json_decode(json_encode($simple_xml), true);

        $array2xml = new Array2xml;
        $array2xml->setOptions(compact('name'));

        $expected_string = preg_replace('/\s+/S', '', $xml_string);
        $result_string = preg_replace('/\s+/S', '', $array2xml->get($data));

        $this->assertSame($expected_string, $result_string);
    }

    /**
     * Provide "xml string" data
     *
     * @return array
     */
    public function xmlStringProvider()
    {
        $paths = glob(__DIR__ . '/xml/*.xml');

        $data = [];
        foreach ($paths as $path) {
            $data[] = [
                file_get_contents($path),
            ];
        }

        return $data;
    }
}
