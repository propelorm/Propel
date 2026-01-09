<?php

class CustomPlatformTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var GeneratorConfig
     */
    protected $generatorConfig;

    public function setUp(): void
    {
        $projectDir = realpath(__DIR__ . '/../../../fixtures/generator/platform/');
        $platformClass = str_replace('/', '.', $projectDir) . '.CustomPlatform';
        $props = array(
            "propel.project" => "kfw-propel",
            "propel.database" => "pgsql", // Or anything else
            "propel.projectDir" => $projectDir,
            "propel.platform.class" => $platformClass,
            "propel.buildtime.conf.file" => "buildtime-conf.xml"

        );

        $this->generatorConfig = new GeneratorConfig($props);
    }

    public function testGetPLatform()
    {
        $this->assertInstanceOf('CustomPlatform', $this->generatorConfig->getConfiguredPlatform());
        $this->assertInstanceOf('CustomPlatform', $this->generatorConfig->getConfiguredPlatform(null, 'default'));
    }
}
