<?php

namespace PersonalNotebook\Site\BlockLayout;

use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Site\BlockLayout\AbstractBlockLayout;

class PersonalNotebook extends AbstractBlockLayout
{
    public function getLabel()
    {
        return 'Personal Notebook'; // @translate
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
        SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {
        return $view->escapeHtml($view->translate('Personal Notebook'));
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $identity = $view->identity();
        if (!$identity) {
            return '';
        }

        $notes = $view->api()->search('personalnotebook_notes', ['owner_id' => $identity->getId()])->getContent();
        return $view->partial('personal-notebook/block-layout/personal-notebook', ['notes' => $notes]);
    }
}
