<?php

namespace App\Service;

use App\Entity\v1\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class AbstractRestService {
    private DenormalizerInterface $denormalizer;
    private ServiceEntityRepository $repository;
    private EntityManagerInterface $entityManagerInterface;

    public function __construct(
        DenormalizerInterface $denormalizer,
        ServiceEntityRepository $repository,
        EntityManagerInterface $entityManagerInterface
    ) {
        $this->denormalizer = $denormalizer;
        $this->repository = $repository;
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function create(object $objet) {
        $this->entityManagerInterface->persist($objet);
        $this->entityManagerInterface->flush();
    }

    public function delete(object $objet) {
        $this->entityManagerInterface->remove($objet);
        $this->entityManagerInterface->flush();
    }

    /**
     * This function checks that the user making the request is from the same client as $the $clientSlug passed in parameter
     * 
     * @param string $clientSlug The client slug
     * @param User $user The current user making the request
     * 
     * @return array
     */
    public function isUserFromClient(string $clientSlug, User $user): bool {
        return $clientSlug === $user->getClient()->getSlug();
    }

    /**
     * @param array $data
     * @param string $format
     * 
     * @return object
     */
    public function hydrate(array $data, ?string $format = 'json'): object {
        return $this->denormalizer->denormalize($data, $this->repository->getClassName(), $format);
    }

    /**
     * @param array $data
     * @param array $mandatoryFields
     * 
     * @return array|false
     */
    public function getMissingFields(array $data, array $mandatoryFields): array|false {
        $missingFields = [];

        foreach ($mandatoryFields as $field) {
            if (!isset($data[$field])) {
                $missingFields[] = $field;
            }
        }

        return count($missingFields) > 0 ? $missingFields : false;
    }

    /**
     * @param array $data
     * 
     * @return array|false
     */
    public function getBlankValue(array $data): array|false {
        $blankValues = [];

        foreach ($data as $key) {
            if (empty($data[$key]) || $data[$key] === null) {
                $blankValues[] = $key;
            }
        }
        
        return count($blankValues) > 0 ? $blankValues : false;
    }

    /**
     * @param string $mail
     * 
     * @return bool
     */
    public function isMailValid(string $mail): bool {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    public function findOneBy(array $criteria) {
        return $this->repository->findOneBy($criteria);
    }
}