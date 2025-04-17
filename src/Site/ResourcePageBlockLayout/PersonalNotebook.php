<?php
namespace PersonalNotebook\Site\ResourcePageBlockLayout;

use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Site\ResourcePageBlockLayout\ResourcePageBlockLayoutInterface;
use Laminas\View\Renderer\PhpRenderer;

class PersonalNotebook implements ResourcePageBlockLayoutInterface
{
    public function getLabel() : string
    {
        return 'Personal notebook'; // @translate
    }

    public function getCompatibleResourceNames() : array
    {
        return ['items', 'media'];
    }

    public function render(PhpRenderer $view, AbstractResourceEntityRepresentation $resource) : string
    {
        return $view->partial('personal-notebook/common/personal-notebook-block', ['resource' => $resource]);
    }
}
