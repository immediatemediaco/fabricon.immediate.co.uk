<?php

namespace App\Repository;

use App\Entity\Conference;
use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Slot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slot[]    findAll()
 * @method Slot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    /** @return Slot[] */
    public function findScheduleByConference(Conference $conference): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.conference = :conference')
            ->setParameter('conference', $conference)
            ->orderBy('s.date', 'ASC')
            ->addOrderBy('s.startTime', 'ASC')
            ->addOrderBy('COALESCE(s.track, 0)', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns slots grouped by date string (Y-m-d).
     *
     * @return array<string, Slot[]>
     */
    public function findByConferenceGroupedByDay(Conference $conference): array
    {
        $slots = $this->findScheduleByConference($conference);

        $grouped = [];
        foreach ($slots as $slot) {
            $key = $slot->getDate()?->format('Y-m-d') ?? 'unknown';
            $grouped[$key][] = $slot;
        }

        return $grouped;
    }
}
