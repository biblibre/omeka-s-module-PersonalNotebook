<?php

namespace PersonalNotebook\Form;

use Laminas\Form\Fieldset;
use Laminas\Form\Element\Checkbox;

class SiteSettingsFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('personal-notebook');
        $this->setLabel('Personal Notebook'); // @translate

        $this->add([
            'type' => Checkbox::class,
            'name' => 'personalnotebook_show_after_item',
            'options' => [
                'label' => 'Show on item page', // @translate
            ],
        ]);

        $this->add([
            'type' => Checkbox::class,
            'name' => 'personalnotebook_show_after_media',
            'options' => [
                'label' => 'Show on media page', // @translate
            ],
        ]);
    }
}
