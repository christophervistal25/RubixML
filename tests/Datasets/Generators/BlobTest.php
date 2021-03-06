<?php

namespace Rubix\ML\Tests\Datasets\Generators;

use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Datasets\Generators\Generator;
use PHPUnit\Framework\TestCase;

class BlobTest extends TestCase
{
    protected $generator;

    public function setUp()
    {
        $this->generator = new Blob([0.0, 0.0], 1.0);
    }

    public function test_build_generator()
    {
        $this->assertInstanceOf(Blob::class, $this->generator);
        $this->assertInstanceOf(Generator::class, $this->generator);

        $this->assertEquals(2, $this->generator->dimensions());
    }

    public function test_generate_dataset()
    {
        $dataset = $this->generator->generate(30);

        $this->assertInstanceOf(Unlabeled::class, $dataset);
        $this->assertInstanceOf(Dataset::class, $dataset);

        $this->assertCount(30, $dataset);
    }
}
