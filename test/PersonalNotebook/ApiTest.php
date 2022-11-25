<?php

namespace PersonalNotebook\Test;

use DateTimeInterface;

class ApiTest extends TestCase
{
    public function testCreateEmpty()
    {
        $response = $this->api()->create('personalnotebook_notes', []);
        $note = $response->getContent();

        $owner = $note->owner();
        $this->assertEquals(1, $owner->id(), 'owner is the authenticated user');
        $this->assertEquals('', $note->content(), 'content is empty');
        $this->assertNull($note->resource(), 'resource is null');
        $this->assertInstanceOf(DateTimeInterface::class, $note->created());
        $this->assertInstanceOf(DateTimeInterface::class, $note->modified());
    }

    public function testCreateWithResource()
    {
        $item = $this->api()->create('items')->getContent();
        $note = $this->api()->create('personalnotebook_notes', [
            'o-module-personal-notebook:resource' => [
                'o:id' => $item->id(),
            ],
            'o-module-personal-notebook:content' => 'A boring note',
        ])->getContent();

        $owner = $note->owner();
        $this->assertEquals(1, $owner->id(), 'owner is the authenticated user');
        $this->assertEquals('A boring note', $note->content(), 'content is boring');
        $this->assertEquals($item->id(), $note->resource()->id(), 'resource is as expected');
    }

    public function testCreateMultipleNotesOnSameResource()
    {
        $item = $this->api()->create('items')->getContent();
        $note = $this->api()->create('personalnotebook_notes', [
            'o-module-personal-notebook:resource' => [
                'o:id' => $item->id(),
            ],
            'o-module-personal-notebook:content' => 'A boring note',
        ])->getContent();

        $this->expectException(\Doctrine\DBAL\Exception\UniqueConstraintViolationException::class);
        $this->api()->create('personalnotebook_notes', [
            'o-module-personal-notebook:resource' => [
                'o:id' => $item->id(),
            ],
            'o-module-personal-notebook:content' => 'Another boring note',
        ])->getContent();
    }

    public function testPartialUpdate()
    {
        $item = $this->api()->create('items')->getContent();
        $note = $this->api()->create('personalnotebook_notes', [
            'o-module-personal-notebook:resource' => [
                'o:id' => $item->id(),
            ],
            'o-module-personal-notebook:content' => 'A boring note',
        ])->getContent();

        $this->api()->update('personalnotebook_notes', $note->id(), [
            'o-module-personal-notebook:content' => 'A modified boring note',
        ], [], ['isPartial' => true]);

        $note = $this->api()->read('personalnotebook_notes', $note->id())->getContent();
        $this->assertEquals('A modified boring note', $note->content());
        $this->assertEquals($item->id(), $note->resource()->id());
    }
}
