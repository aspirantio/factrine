<?php

namespace Aspirant\Factrine\Tests\Factrine;

use Aspirant\Factrine\Factory;
use Aspirant\Factrine\FactoryRegistry;
use Aspirant\Factrine\Factrine;
use Aspirant\Factrine\Tests\TestCase;
use Aspirant\Factrine\Tests\TestEntity\Post;
use Faker\Generator;
use Tests\Factrine\ArticleFactory;

class FactrineSimpleFactoryTest extends TestCase
{
    /**
     * @var Factrine
     */
    protected $factrine;

    public function setUp()
    {
        parent::setUp();

        $factrineFactory = new Factory($this->em, new FactoryRegistry());

        $factrineFactory->addFactoryFor(Post::class, function (Generator $faker) {
            return [
                'title'     => $faker->sentence,
                'content'   => join('\n\n', $faker->paragraphs(5)),
                'createdAt' => $faker->dateTimeBetween(),
                'author'    => function(Factrine $factrine) {
                    return $factrine->
                },
            ];
        });

        $this->factrine = $factrineFactory->create();
    }

    /** @test */
    public function it_provides_values_for_a_given_factory()
    {
        $values = $this->factrine->values(Post::class);

        $this->assertTrue(is_array($values));
        $this->assertArrayHasKey('title', $values);
        $this->assertTrue(is_string($values['title']));
        $this->assertArrayHasKey('content', $values);
        $this->assertTrue(is_string($values['content']));
        $this->assertArrayHasKey('createdAt', $values);
        $this->assertTrue($values['createdAt'] instanceof \DateTime);
    }

    /** @test */
    public function it_provides_multiple_values()
    {
        $values = $this->factrine->times(5)->values(Post::class);

        $this->assertCount(5, $values);
        $this->assertArrayHasKey('title', $values[0]);
        $this->assertArrayHasKey('title', $values[4]);
    }

    /** @test */
    public function multiple_fake_values_are_different()
    {
        $values = $this->factrine->times(2)->values(Post::class);

        $this->assertNotEquals($values[0]['title'], $values[1]['title']);
        $this->assertNotEquals($values[0]['content'], $values[1]['content']);
    }

    /** @test */
    public function fake_values_can_be_overridden()
    {
        $values = $this->factrine->values(Post::class, [
            'title' => 'My Post',
        ]);

        $this->assertEquals('My Post', $values['title']);
    }

    /** @test */
    public function fake_values_can_be_overridden_for_multiple_values()
    {
        $values = $this->factrine->times(2)->values(Post::class, [
            'title' => 'My Post',
        ]);

        $this->assertEquals('My Post', $values[0]['title']);
        $this->assertEquals('My Post', $values[1]['title']);
    }

    /** @test */
    public function it_instantiates_a_new_entity()
    {
        /** @var Post $post */
        $post = $this->factrine->make(Post::class);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertTrue(is_string($post->getTitle()));
        $this->assertTrue(is_string($post->getContent()));
        $this->assertInstanceOf(\DateTime::class, $post->getCreatedAt());
    }

    /** @test */
    public function it_instantiates_multiple_new_entity()
    {
        /** @var Post[] $post */
        $posts = $this->factrine->times(2)->make(Post::class);

        $this->assertCount(2, $posts);
        $this->assertInstanceOf(Post::class, $posts[0]);
        $this->assertInstanceOf(Post::class, $posts[1]);
    }

    /** @test */
    public function it_creates_a_new_persisted_entity()
    {
        /** @var Post $post */
        $post = $this->factrine->create(Post::class);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals(1, $post->getId());
    }

    /** @test */
    public function it_creates_multiple_new_persisted_entities()
    {
        /** @var Post[] $post */
        $posts = $this->factrine->times(2)->create(Post::class);

        $this->assertEquals(1, $posts[0]->getId());
        $this->assertEquals(2, $posts[1]->getId());
    }
}
