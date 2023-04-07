<?php

namespace PersonalNotebook\Form;

use Laminas\Form\Form;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Textarea;

class NoteForm extends Form
{
    public function init()
    {
        $this->setAttribute('class', 'personal-notebook-form');
        $this->add([
            'type' => Hidden::class,
            'name' => 'o-module-personal-notebook:resource[o:id]',
        ]);

        $this->add([
            'type' => Textarea::class,
            'name' => 'o-module-personal-notebook:content',
            'attributes' => [
                'placeholder' => 'Personal notes...', // @translate
            ],
        ]);
    }
}
