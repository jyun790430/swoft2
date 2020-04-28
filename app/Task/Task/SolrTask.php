<?php

declare(strict_types=1);


namespace App\Task\Task;

use Swoft\Crontab\Annotaion\Mapping\Cron;
use Swoft\Crontab\Annotaion\Mapping\Scheduled;
use Swoft\Task\Annotation\Mapping\Task;
use Swoft\Task\Annotation\Mapping\TaskMapping;
use Swoft\Db\DB;
use Swoft\Log\Helper\Log;
use Swoft\Redis\Redis;
use Throwable;
use App\Model\Entity\Videos;
use Solarium;

/**
 * Class SolrTask
 *
 * @since 2.0
 *
 * @Task(name="solrTask")
 * @Scheduled(name="solrTask")
 */
class SolrTask
{
    /**
     * Solr Client
     *
     * @var Solarium\Client
     */
    private $client;

    /**
     * Solr Server Config Setting for Solarium
     *
     * @var array
     */
    private $config;

    public function __construct()
    {
        /* Solarium */
        $this->config = [
            'endpoint' => [
                'iexo' => [
                    'host' => env('SOLR_HOST', 'localhost'),
                    'port' => env('SOLR_PORT', '8983'),
                    'path' => '/',
                    'core' => env('SOLR_CORE', 'test_core'),
                    'timeout' => 5,
                ]
            ]
        ];

        // create client
        $this->client = new Solarium\Client($this->config);
        // set http adapter
        $this->client->setAdapter('Solarium\Core\Client\Adapter\Guzzle');
    }

    public function index(): void
    {
        Log::profileStart('solr_index');

        // 分頁處理資料
        $page = 1;
        $columnPerPage = 1000;

        $videos = new Videos();

        do {
            Log::info(sprintf('page: %d', $page));
            Log::profileStart("solr_page_{$page}");
            $videoList = $videos->selectRaw("
                    videos.id   as video_id, 
                    videos.name as video_name, 
                    duration, 
                    vote_count, 
                    play_count
                    GROUP_CONCAT( tag.name ) as tags
                ")
                ->join("tag", "tag.video_id", '=', "videos.id")
                ->groupBy("videos.id")
                ->orderByDesc("videos.id")
                ->paginate($page, $columnPerPage);

            // add Solr Documents
            $this->addDocs($videoList['list']);

            Log::profileEnd("solr_page_{$page}");

            $page++;
        } while ($page <= $videoList['pageCount']);

        Log::profileEnd('solr_index');

        return;
    }

    public function indexByIds(array $chunkIds): void
    {
        Log::profileStart('solr_indexByIds');

        // 分頁處理資料
        $page = 1;

        // get videos
        $videos = new Videos();

        foreach ($chunkIds as $key => $ids) {
            Log::info(sprintf('page: %d', $page));
            Log::profileStart("solr_indexByIds_page_{$page}");
            $videoList = $videos->selectRaw("
                    videos.id   as video_id, 
                    videos.name as video_name, 
                    duration, 
                    vote_count, 
                    play_count
                    GROUP_CONCAT( tag.name ) as tags
                ")
                ->join("tag", "tag.video_id", '=', "videos.id")
                ->groupBy("videos.id")
                ->orderByDesc("videos.id")
                ->get();

            // add Solr Documents
            $this->addDocs($videoList->toArray());

            Log::profileEnd("solr_indexByIds_page_{$page}");

            $page++;
        }

        Log::profileEnd('solr_indexByIds');

        return;
    }

    /**
     * Add Solr Document
     *
     * @param array $videoList
     * @return void
     */
    protected function addDocs(array $videoList): void
    {
        try {
            // create an update instance
            $update = $this->client->createUpdate();

            // create input docs
            $docs = array();
            foreach ($videoList as $key => $row) {
                // create document
                $doc = $update->createDocument();
                $doc->tags = $row['tags'];
                $doc->video_id = $row['video_id'];
                $doc->duration = $row['duration'];
                $doc->video_name = $row['video_name'];
                $doc->vote_count = ($row['vote_count'])?:0;
                $doc->play_count = ($row['play_count'])?:0;

                // put into array
                $docs[] = $doc;
            }

            // add documents and commit to the query
            $update->addDocuments($docs);
            $update->addCommit();

            // execute update
            $this->client->update($update);

            // reset docs
            unset($docs);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
        }

        return;
    }

    /**
     * @TaskMapping()
     * @Cron("3 0/2 * * * *")
     *
     * @return void
     */
    public function addIndex(): void
    {
        try {
            // is key exists?
            if (Redis::exists(REDIS_SOLR_INDEX_IDS)) {
                // Get Video ids from Redis
                $videoIds = Redis::get(REDIS_SOLR_INDEX_IDS);

                // is ids == '0'?
                if ('0' !== $videoIds) {
                    // chunk video ids
                    $chunkIds = array_chunk(explode(',', $videoIds), 1000);

                    // index by ids
                    $this->indexByIds($chunkIds);

                    // set Redis = 0
                    Redis::set(REDIS_SOLR_INDEX_IDS, '0');

                    Log::info(sprintf('Solr addIndex: %s', $videoIds));
                } else {
                    Log::info('Solr addIndex: no data need to index!');
                }
            } else {
                // index all data
                $this->index();
                // set Redis = 0
                Redis::set(REDIS_SOLR_INDEX_IDS, '0');
                // build suggester
                $this->suggestBuild();

                Log::info('Solr addIndex: index all data finished!');
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
        }

        return;
    }

    /**
     * @TaskMapping()
     * @Cron("9 0/10 * * * *")
     *
     * @return void
     */
    public function deleteIndex(): void
    {
        try {
            // is key exists?
            if (Redis::exists(REDIS_SOLR_DELETE_INDEX)) {
                // Get Video ids from Redis
                $videoIds = Redis::get(REDIS_SOLR_DELETE_INDEX);

                // is ids == '0'?
                if ('0' !== $videoIds) {
                    $ids = explode(',', $videoIds);
                    // delete by ids
                    $this->deleteByIds($ids);

                    // set Redis = 0
                    Redis::set(REDIS_SOLR_DELETE_INDEX, '0');

                    Log::info(sprintf('Solr deleteIndex: %s', $videoIds));
                } else {
                    Log::info('Solr deleteIndex: no data need to delete!');
                }
            } else {
                // set Redis = 0
                Redis::set(REDIS_SOLR_DELETE_INDEX, '0');

                Log::info('Solr deleteIndex: initial delete index!');
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
        }

        return;
    }

    /**
     * Solr index 重建
     * 每日 5:00 執行
     *
     * @TaskMapping()
     * @Cron("0 0 5 * * *")
     *
     * @return void
     */
    public function rebuildIndex(): void
    {
        try {
            // delete all index
            if (!$this->deleteAll()) {
                return;
            }
            // rebuild index
            $this->index();
            // rebuild suggester
            $this->suggestBuild();
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
        }

        return;
    }

    protected function deleteByIds(array $ids): void
    {
        try {
            // get an update instance
            $update = $this->client->createUpdate();
            // add command to update query
            $update->addDeleteByIds($ids);
            $update->addCommit();
            // execute update
            $this->client->update($update);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        return;
    }

    protected function deleteAll(): bool
    {
        try {
            // get an update instance
            $update = $this->client->createUpdate();
            // add command to delete all
            $update->addDeleteQuery("*:*");
            $update->addCommit();
            // execute update
            $this->client->update($update);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return false;
        }

        return true;
    }

    protected function suggestBuild(): void
    {
        // suggester dictionary
        $dictionarys = array('nameSuggester', 'tagSuggester', 'nameOneWordSuggester');
        try {
            // create suggester query
            $query = $this->client->createSuggester();
            // set dictionary
            $query->setDictionary($dictionarys);
            // set build
            $query->setBuild(true);
            // excute
            $this->client->suggester($query);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        return;
    }
}