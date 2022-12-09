<?php

namespace PersonalNotebook\Test\Controller\Site;

use PersonalNotebook\Test\TestCase;

class NoteControllerTest extends TestCase
{
    protected $site;
    protected $user;
    protected $item1;
    protected $item2;

    public function setUp(): void
    {
        parent::setUp();

        $response = $this->api()->create('sites', [
            'o:title' => 'Test site',
            'o:slug' => 'test',
            'o:theme' => 'default',
        ]);
        $this->site = $response->getContent();

        $this->item1 = $this->api()->create('items')->getContent();
        $this->item2 = $this->api()->create('items')->getContent();

        $response = $this->api()->create('users', [
            'o:email' => 'user1234@example.org',
            'o:name' => 'User 1234',
            'o:role' => 'researcher',
            'o:is_active' => true,
        ]);
        $this->user = $response->getContent();
        $this->user->getEntity()->setPassword('user1234');
        $this->getEntityManager()->flush();

        $this->logout();
        $this->login('user1234@example.org', 'user1234');
            $identity = $this->getServiceLocator()
                ->get('Omeka\AuthenticationService')->getIdentity();
    }

    public function tearDown(): void
    {
        $this->loginAsAdmin();
        $this->getEntityManager()->clear();
        $this->api()->delete('users', $this->user->id());
        $this->api()->delete('items', $this->item1->id());
        $this->api()->delete('items', $this->item2->id());
        $this->api()->delete('sites', $this->site->id());

        parent::tearDown();
    }

    public function testExportAsCsv()
    {
        $note1 = $this->api()->create('personalnotebook_notes', [
            'o-module-personal-notebook:resource' => [
                'o:id' => $this->item1->id(),
            ],
            'o-module-personal-notebook:content' => 'A note on item 1',
        ])->getContent();
        $note2 = $this->api()->create('personalnotebook_notes', [
            'o-module-personal-notebook:resource' => [
                'o:id' => $this->item2->id(),
            ],
            'o-module-personal-notebook:content' => 'A note on item 2',
        ])->getContent();

        $this->dispatch('/s/test/personal-notebook/notes.csv');
        $this->assertResponseStatusCode(200);

        $response = $this->getResponse();
        $contentType = $response->getHeaders()->get('Content-Type')->getFieldValue();
        $this->assertEquals('text/csv; charset=utf-8', $contentType);

        $csv = $response->getContent();
        $this->assertStringEqualsFile(__DIR__ . '/data/notes.csv', $csv);
    }
}
