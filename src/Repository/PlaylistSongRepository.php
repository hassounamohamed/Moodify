<?php

namespace App\Repository;

use App\Entity\PlaylistSong;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlaylistSong>
 */
class PlaylistSongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaylistSong::class);
    }

    public function save(PlaylistSong $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlaylistSong $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Ajoutez vos méthodes personnalisées ici
    public function findByPlaylistWithSongs(int $playlistId): array
    {
        return $this->createQueryBuilder('ps')
            ->join('ps.song', 's')
            ->addSelect('s')
            ->where('ps.playlist = :playlistId')
            ->setParameter('playlistId', $playlistId)
            ->orderBy('s.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}