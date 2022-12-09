<?php

namespace PersonalNotebook\Controller\Site;

use Laminas\Form\Form;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Omeka\Mvc\Exception\PermissionDeniedException;

class NoteController extends AbstractActionController
{
    public function exportAsCsvAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            throw new PermissionDeniedException();
        }

        $response = $this->api()->search('personalnotebook_notes', ['owner_id' => $identity->getId()]);
        $notes = $response->getContent();

        $fh = fopen('php://temp', 'r+');
        $headerRow = [
            $this->translate('Resource title'),
            $this->translate('Resource URL'),
            $this->translate('Notes'),
        ];
        fputcsv($fh, $headerRow);

        foreach ($notes as $note) {
            $resource = $note->resource();
            if ($resource) {
                $row = [$resource->displayTitle(), $resource->siteUrl(null, true)];
            } else {
                $row = ['', ''];
            }
            $row[] = $note->content();
            fputcsv($fh, $row);
        }

        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'text/csv; charset=utf-8');
        $response->getHeaders()->addHeaderLine('Content-Disposition', 'attachment; filename="notes.csv"');
        $response->setContent($csv);

        return $response;
    }

    public function deleteAction()
    {
        $form = $this->getForm(Form::class);
        $this->logger()->err(get_class($form));
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $response = $this->api()->delete('personalnotebook_notes', $this->params('id'));
            } else {
                $this->messenger()->addFormErrors($form);
            }

            $redirectUrl = $this->params()->fromPost('redirect_url');
            if (!$redirectUrl) {
                $redirectUrl = $this->currentSite()->siteUrl();
            }

            return $this->redirect()->toUrl($redirectUrl);
        }

        $note = $this->api()->read('personalnotebook_notes', $this->params('id'))->getContent();

        $model = new ViewModel();
        $model->setVariable('form', $form);
        $model->setVariable('note', $note);
        $model->setVariable('redirectUrl', $this->params()->fromQuery('redirect_url'));

        return $model;
    }
}
