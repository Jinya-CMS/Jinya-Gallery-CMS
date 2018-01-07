<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 07.01.2018
 * Time: 20:10
 */

namespace DataBundle\Services\Labels;


use DataBundle\Entity\Gallery;
use DataBundle\Entity\Label;
use Doctrine\ORM\EntityManager;
use function array_map;

class LabelService implements LabelServiceInterface
{

    /** @var EntityManager */
    private $entityManager;

    /**
     * LabelService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function getAllLabels(): array
    {
        return $this->entityManager->getRepository(Label::class)->findAll();
    }

    /**
     * @inheritdoc
     */
    public function getAllLabelsWithArtworks(): array
    {
        $labelRepo = $this->entityManager->getRepository(Label::class);

        return $labelRepo
            ->createQueryBuilder('label')
            ->select('label')
            ->join('label.artworks', 'artworks')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @inheritdoc
     */
    public function getAllLabelsWithGalleries(): array
    {
        $galleryRepo = $this->entityManager->getRepository(Gallery::class);

        return $galleryRepo
            ->createQueryBuilder('g')
            ->select('l')
            ->join('g.labels', 'l')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @inheritdoc
     */
    public function deleteLabel(string $name): void
    {
        $this->entityManager->remove($this->getLabel($name));
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getLabel(string $name): Label
    {
        return $this->entityManager->getRepository(Label::class)->findOneBy(['name' => $name]);
    }

    /**
     * @inheritdoc
     */
    public function updateLabel(Label $label): Label
    {
        $this->entityManager->flush();

        return $label;
    }

    /**
     * @inheritdoc
     */
    public function getAllLabelNames(): array
    {
        return array_map('current', $this->entityManager->getRepository(Label::class)
            ->createQueryBuilder('label')
            ->select('label.name')
            ->getQuery()
            ->getScalarResult());
    }

    /**
     * @inheritdoc
     */
    public function createMissingLabels(array $labels): void
    {
        foreach ($labels as $label) {
            if (!$this->labelExists($label)) {
                $this->addLabel($label);
            }
        }
    }

    /**
     * @param Label $label
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function labelExists(Label $label): bool
    {
        $qb = $this->entityManager->getRepository(Label::class)->createQueryBuilder('label');
        return $qb
            ->select($qb->expr()->count('label.name'))
            ->where('label.id = :id')
            ->orWhere('label.name = :name')
            ->setParameter('id', $label->getId())
            ->setParameter('name', $label->getName())
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function addLabel(string $name): Label
    {
        $label = new Label();
        $label->setName($name);
        $this->entityManager->persist($label);
        $this->entityManager->flush();

        return $label;
    }
}