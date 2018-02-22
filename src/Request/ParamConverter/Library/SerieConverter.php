<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\Serie;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;

class SerieConverter extends AbstractConverter
{
    const NAME = 'serie';

    /**
     * {@inheritdoc}
     */
    function getEzPropsName(): array
    {
        return ['id', 'name', ];
    }

    /**
     * {@inheritdoc}
     */
    function getManyRelPropsName():array
    {
        // for instance i don't want to allow the creation of a serie with all embeded books, this is not a usual way of working
        // that's why i don't add books here
        return [];
    }

    /**
     * {@inheritdoc}
     */
    function getOneRelPropsName():array {
        return [];
    }

     /**
     * @inheritdoc
     */
    public function initFromRequest($jsonOrArray)
    {
        try {
            $entity = new Serie();
            $json = $this->checkJsonOrArray($jsonOrArray);

            $this->buildWithEzProps($json, $entity);

            $this->buildWithManyRelProps($json, $entity);

            $this->buildWithOneRelProps($json, $entity);

            $errors = $this->validator->validate($entity);

            if (count($errors)) {
                throw new ValidationException($errors);
            }

            return $entity;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $violationList = new ConstraintViolationList();
            $violation = new ConstraintViolation($e->getMessage(), null, [], null, null, null);
            $violationList->add($violation);
            throw new ValidationException($violationList,'Wrong parameter to create new Serie (generic)', 420, $e);
        }
    }
}
