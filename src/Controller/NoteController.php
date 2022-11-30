<?php

namespace PersonalNotebook\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;
use Omeka\View\Model\ApiJsonModel;
use PersonalNotebook\Form\NoteForm;

class NoteController extends AbstractRestfulController
{
    public function create($data)
    {
        $form = $this->getForm(NoteForm::class);
        $form->setData($data);
        if (!$form->isValid()) {
            $messages = $form->getMessages();
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(['errors' => $messages]);
        }

        $response = $this->api()->create('personalnotebook_notes', $data);

        return new ApiJsonModel($response);
    }

    public function update($id, $data)
    {
        $form = $this->getForm(NoteForm::class);
        $form->setData($data);
        if (!$form->isValid()) {
            $messages = $form->getMessages();
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(['errors' => $messages]);
        }

        $response = $this->api()->update('personalnotebook_notes', $id, $data);

        return new ApiJsonModel($response);
    }
}
