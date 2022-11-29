<?php

namespace PersonalNotebook\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Omeka\View\Model\ApiJsonModel;

class NoteController extends AbstractRestfulController
{
    public function create($data)
    {
        $response = $this->api()->create('personalnotebook_notes', $data);

        return new ApiJsonModel($response);
    }

    public function update($id, $data)
    {
        $response = $this->api()->update('personalnotebook_notes', $id, $data);

        return new ApiJsonModel($response);
    }
}
