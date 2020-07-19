<?php


namespace Oxygen\DI\Test\Extraction;

use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Extraction\ExtractionChain;
use Oxygen\DI\Test\BaseTestCase;

class ExtractionChainTest extends BaseTestCase
{
    private function makeExtractionChain(): ExtractionChain
    {
        return new ExtractionChain();
    }

    /**
     * @throws CircularDependencyException
     */
    public function testAppend()
    {
        $extractionChain = $this->makeExtractionChain();
        $extractionChain->append("foo");
        $extractionChain->append("bar");
        $extractionChain->append("baz");
        $this->assertEquals(["foo", "bar", "baz"], $extractionChain->getChain());
    }

    /**
     * @throws CircularDependencyException
     */
    public function testItThrowsIfYouAppendTheSameValueMultipleTime()
    {
        $chain = $this->makeExtractionChain();
        $chain->append("foo");
        $chain->append("bar");
        $this->expectException(CircularDependencyException::class);
        $chain->append("foo");
    }

    /**
     * @throws CircularDependencyException
     */
    public function testContains()
    {
        $chain = $this->makeExtractionChain();
        $chain->append("foo");
        $this->assertTrue($chain->contains("foo"));
        $this->assertFalse($chain->contains("baz"));
    }

    /**
     * @throws CircularDependencyException
     */
    public function testToString()
    {
        $extractionChain = $this->makeExtractionChain();
        $extractionChain->append("foo");
        $extractionChain->append("bar");
        $extractionChain->append("baz");
        $this->assertEquals("foo => bar => baz", (string)$extractionChain);
    }

    /**
     * @throws CircularDependencyException
     */
    public function testClear()
    {
        $extractionChain = $this->makeExtractionChain();
        $extractionChain->append("foo");
        $extractionChain->append("bar");
        $extractionChain->append("baz");
        $this->assertNotEmpty($extractionChain->getChain());
        $extractionChain->clear();
        $this->assertEmpty($extractionChain->getChain());
    }
}
