<?php

namespace App\Database;

use DateTime;
use Exception;
use Iterator;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;
use RuntimeException;

class ApiKey extends Utils\LoadableEntity
{
    public string $apiKey;
    public int $userId;
    public DateTime $validSince;
    public string $userAgent;
    public string $remoteAddress;

    /**
     * @inheritDoc
     */
    public static function findById(int $id): ?object
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Gets the api key object that belongs to the key
     *
     * @param string $apiKey
     * @return ApiKey|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByApiKey(string $apiKey): ?ApiKey
    {
        $sql = "SELECT api_key, user_id, valid_since, user_agent, remote_address FROM api_key WHERE api_key = :apiKey";
        $result = self::executeStatement($sql, ['apiKey' => $apiKey]);

        if (count($result) === 0) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult(
            $result[0],
            new self(),
            ['validSince' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT)]
        );
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray(
            'api_key',
            new self(),
            [
                'validSince' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Gets all api keys for the given artist
     *
     * @param int $artistId
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByArtist(int $artistId): Iterator
    {
        $sql = 'SELECT * FROM api_key WHERE user_id = :artistId';

        $result = self::executeStatement($sql, ['artistId' => $artistId]);

        return self::hydrateMultipleResults(
            $result,
            new self(),
            [
                'validSince' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Sets the api key securely
     *
     * @throws Exception
     */
    public function setApiKey(): void
    {
        $this->apiKey = "jinya-api-token-$this->userId-" . bin2hex(random_bytes(20));
    }

    /**
     * Gets the artist belonging to the api key
     *
     * @return Artist
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getArtist(): Artist
    {
        return Artist::findById($this->userId);
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->internalCreate(
            'api_key',
            [
                'validSince' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ],
            ['id']
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $sql = 'DELETE FROM api_key WHERE api_key = :apiKey';
        self::executeStatement($sql, ['apiKey' => $this->apiKey]);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): void
    {
        $sql = 'UPDATE api_key SET valid_since = :validSince WHERE api_key = :apiKey';
        $converter = new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT);

        self::executeStatement(
            $sql,
            ['apiKey' => $this->apiKey, 'validSince' => $converter->extract($this->validSince)]
        );
    }
}