<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    /**
     * Trouve les humeurs les plus populaires avec leur nombre de playlists
     * 
     * @param int $limit Nombre maximum de résultats à retourner
     * @return array Tableau associatif avec les humeurs et leur compte
     */
    public function findMostPopularMoods(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.humeur as name, COUNT(p.id) as count')
            ->where('p.humeur IS NOT NULL')
            ->groupBy('p.humeur')
            ->orderBy('count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les playlists mises en avant (les plus populaires)
     * 
     * @return Playlist[] Tableau des playlists mises en avant
     */
    public function findFeaturedPlaylists(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.playlistSongs', 'ps')
            ->addSelect('COUNT(ps.id) as songCount')
            ->groupBy('p.id')
            ->orderBy('songCount', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les playlists par humeur
     * 
     * @param string $humeur Humeur recherchée
     * @return Playlist[] Tableau des playlists correspondantes
     */
    public function findByMood(string $humeur): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.humeur = :humeur')
            ->setParameter('humeur', $humeur)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Sauvegarde une playlist en base de données
     * 
     * @param Playlist $playlist
     * @param bool $flush Si true, exécute immédiatement la requête
     */
    public function save(Playlist $playlist, bool $flush = false): void
    {
        $this->getEntityManager()->persist($playlist);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une playlist de la base de données
     * 
     * @param Playlist $playlist
     * @param bool $flush Si true, exécute immédiatement la requête
     */
    public function remove(Playlist $playlist, bool $flush = false): void
    {
        $this->getEntityManager()->remove($playlist);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}