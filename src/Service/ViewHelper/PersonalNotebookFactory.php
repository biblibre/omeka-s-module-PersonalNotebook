<?php

namespace PersonalNotebook\Service\ViewHelper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use PersonalNotebook\View\Helper\PersonalNotebook;

class PersonalNotebookFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $formElementManager = $services->get('FormElementManager');

        $personalNotebook = new PersonalNotebook();
        $personalNotebook->setFormElementManager($formElementManager);

        return $personalNotebook;
    }
}
