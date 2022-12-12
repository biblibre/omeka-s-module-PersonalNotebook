<?php

namespace PersonalNotebook\Test\Controller;

use PersonalNotebook\Test\TestCase;
use PersonalNotebook\Form\NoteForm;

class NoteControllerTest extends TestCase
{
    protected $site;
    protected $user;
    protected $item;

    public function setUp(): void
    {
        parent::setUp();

        $response = $this->api()->create('sites', [
            'o:title' => 'Test site',
            'o:slug' => 'test',
            'o:theme' => 'default',
        ]);
        $this->site = $response->getContent();

        $this->item = $this->api()->create('items')->getContent();

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
        $this->api()->delete('items', $this->item->id());
        $this->api()->delete('sites', $this->site->id());

        parent::tearDown();
    }

    public function testCreate()
    {
        $form = $this->getServiceLocator()->get('FormElementManager')->get(NoteForm::class);
        $csrf = $form->get('noteform_csrf')->getValue();
        $data = [
            'o-module-personal-notebook:resource' => [
                'o:id' => $this->item->id(),
            ],
            'o-module-personal-notebook:content' => 'A note on item 1',
            'noteform_csrf' => $csrf,
        ];

        $this->dispatch('/personal-notebook/notes', 'POST', $data, true);
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');

        $response = $this->getResponse();
        $note = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('o-module-personal-notebook:content', $note);
        $this->assertEquals('A note on item 1', $note['o-module-personal-notebook:content']);
    }
}
