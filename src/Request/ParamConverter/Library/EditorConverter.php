<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\Editor;
use App\Request\ParamConverter\AbstractConverter;

class EditorConverter extends AbstractConverter
{
    const NAME = 'editor';

    const RELATED_ENTITY = Editor::class;

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "editor": {
     *     "name": "Hachette"
     *   }
     * }
     */
    public function getEzPropsName(): array
    {
        return ['id', 'name', ];
    }

    /**
     * {@inheritdoc}
     */
    public function getManyRelPropsName():array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getOneRelPropsName():array
    {
        return [];
    }
}
