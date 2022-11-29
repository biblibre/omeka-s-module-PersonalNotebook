<?php

namespace PersonalNotebook\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use PersonalNotebook\Api\Representation\NoteRepresentation;

class PersonalNotebook extends AbstractHelper
{
    public function form(AbstractResourceEntityRepresentation $resource)
    {
        $view = $this->getView();
        $user = $view->identity();
        if (!$user) {
            return '';
        }

        return $view->partial('personal-notebook/helper/personal-notebook/form', ['resource' => $resource]);
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
}
