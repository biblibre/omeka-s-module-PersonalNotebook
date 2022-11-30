<?php

namespace PersonalNotebook\Controller\Site;

use Laminas\Form\Form;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class NoteController extends AbstractActionController
{
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
