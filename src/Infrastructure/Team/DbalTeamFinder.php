<?php

declare(strict_types=1);

namespace App\Infrastructure\Team;

use App\Application\Person\PersonFinderInterface;
use App\Application\Sport\SportFinderInterface;
use App\Application\Team\TeamFinderInterface;
use App\Application\Team\TeamModel;
use App\Domain\Common\ValueObject\DateTime;
use App\Domain\Sport\ValueObject\SportId;
use App\Domain\Team\ValueObject\TeamId;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DbalTeamFinder implements TeamFinderInterface
{
    private const TABLE_NAME = 'teams';

    public function __construct(
        private readonly Connection $connection,
        private readonly SportFinderInterface $sportFinder,
        private readonly PersonFinderInterface $personFinder
    ) {
    }

    public function getById(TeamId $id): TeamModel
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', (string) $id)
        ;

        $result = $query->fetchAssociative();

        if (!$result) {
            throw new NotFoundHttpException('Team not found');
        }

        return $this->createFromRow($result);
    }

    public function getAll(array $teamIds = []): array
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select('*')
            ->from(self::TABLE_NAME)
            ->orderBy('id')
        ;

        foreach ($teamIds as $n => $id) {
            $query
                ->orWhere("id = :id{$n}")
                ->setParameter("id{$n}", (string) $id)
            ;
        }

        return array_map(
            fn (array $row) => $this->createFromRow($row),
            $query->fetchAllAssociative()
        );
    }

    public function getForSport(SportId $id): array
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select('*')
            ->from(self::TABLE_NAME)
            ->orderBy('id')
            ->where('sport_id = :sport_id')
            ->setParameter('sport_id', (string) $id)
        ;

        return array_map(
            fn (array $row) => $this->createFromRow($row),
            $query->fetchAllAssociative()
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    private function createFromRow(array $row): TeamModel
    {
        $deletedAt = null;
        if (isset($row['deleted_at'])) {
            $deletedAt = DateTime::fromString($row['deleted_at']);
        }

        $teamId = TeamId::fromString($row['id']);

        $sport = $this->sportFinder->getById(SportId::fromString($row['sport_id']));
        $people = $this->personFinder->getForTeam($teamId);

        return new TeamModel(
            $teamId,
            $row['name'],
            $row['description'],
            $people,
            $sport,
            DateTime::fromString($row['created_at']),
            DateTime::fromString($row['updated_at']),
            $deletedAt
        );
    }
}
