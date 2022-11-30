<?php

namespace PersonalNotebook\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use PersonalNotebook\Api\Representation\NoteRepresentation;
use PersonalNotebook\Form\NoteForm;

class PersonalNotebook extends AbstractHelper
{
    public function form($noteOrResource)
    {
        $view = $this->getView();
        $user = $view->identity();
        if (!$user) {
            return '';
        }

        if ($noteOrResource instanceof NoteRepresentation) {
            $note = $noteOrResource;
            $resource = $note->resource();
        } elseif ($noteOrResource instanceof AbstractResourceEntityRepresentation) {
            $resource = $noteOrResource;
            $note = $this->getNote($resource);
        } else {
            throw new \InvalidArgumentException(sprintf('Argument $noteOrResource must be an instance of %s or %s', NoteRepresentation::class, AbstractResourceEntityRepresentation::class));
        }

        $form = $this->getFormElementManager()->get(NoteForm::class);
        $form->setAttribute('data-url', $view->url('personal-notebook/notes', $note ? ['id' => $note->id()] : []));
        $form->setAttribute('data-method', $note ? 'PUT' : 'POST');

        $data = [];
        if ($resource) {
            $data['o-module-personal-notebook:resource[o:id]'] = $resource->id();
        }
        if ($note) {
            $data['o-module-personal-notebook:content'] = $note->content();
        }
        $form->populateValues($data);

        $values = [
            'note' => $note,
            'resource' => $resource,
            'form' => $form,
        ];

        return $view->partial('personal-notebook/helper/personal-notebook/form', $values);
    }

    public function getNote(AbstractResourceEntityRepresentation $resource): ?NoteRepresentation
    {
        $view = $this->getView();
        $user = $view->identity();
        if (!$user) {
            return null;
        }

        $note = $view->api()->searchOne('personalnotebook_notes', [
            'owner_id' => $user->getId(),
            'resource_id' => $resource->id(),
        ])->getContent();

        return $note;
    }

    public function setFormElementManager($formElementManager)
    {
        $this->formElementManager = $formElementManager;
    }

    public function getFormElementManager()
    {
        return $this->formElementManager;
    }
}
