<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     iri="http://schema.org/Role",
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
 * )
 * @ORM\Entity
 */
class Job implements LibraryInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/name"
     * )
     * @ORM\Column(type="string", length=256, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="256")
     */
    private $translation_key;

    /**
     * id can be null until flush is done
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTranslationKey(): ?string
    {
        return $this->translation_key;
    }

    /**
     * @param mixed $translationKey
     * @return Job
     */
    public function setTranslationKey($translationKey): Job
    {
        $this->translation_key = $translationKey;

        return $this;
    }
}
