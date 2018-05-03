<?php

use Rubix\Engine\Datasets\Dataset;
use Rubix\Engine\Transformers\Transformer;
use Rubix\Engine\Transformers\TokenCountVectorizer;
use PHPUnit\Framework\TestCase;

class TokenCountVectorizerTest extends TestCase
{
    protected $transformer;

    public function setUp()
    {
        $data = new Dataset([
            ['the quick brown fox jumped over the lazy man sitting at a bus stop drinking a can of coke'],
        ]);

        $this->transformer = new TokenCountVectorizer();

        $this->transformer->fit($data);
    }

    public function test_build_count_vectorizer()
    {
        $this->assertInstanceOf(TokenCountVectorizer::class, $this->transformer);
        $this->assertInstanceOf(Transformer::class, $this->transformer);
    }

    public function test_transform_dataset()
    {
        $data = [
            ['a quick bus jumped the lazy fox'],
            ['where are my friends'],
        ];

        $this->transformer->transform($data);

        $this->assertEquals([
            [1, 1, 0, 1, 1, 0, 1, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0],
            [0, 0, 0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        ], $data);
    }

    public function test_vectorize_string()
    {
        $vector = $this->transformer->vectorize('stop drinking coke stop');

        $this->assertEquals([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 1, 0, 0, 1], $vector);
    }
}