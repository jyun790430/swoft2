<?php

declare(strict_types=1);


namespace App\Http\Controller;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;

use Swoft\Http\Message\Request;
use Swoft\Log\Helper\Log;
use Swoft\Log\Helper\CLog;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Task\Task;
use Swoft\Stdlib\Helper\ArrayHelper;
use Solarium;

/**
 * Class SolrController
 *
 * @since 2.0
 *
 * @Controller(prefix="solr")
 */
class SolrController
{

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

        // set default fetch fields
        $this->fields = [
            'id',
            'tags',
            'name'
        ];
    }

    /**
     * @RequestMapping("test")
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function test()
    {
        /* ping */
        // $pingResponse  = $this->client->ping();

        /* add index */
        // $doc = new \SolrInputDocument();
        // $doc->addField('id', '7878');
        // $doc->addField('title_cn', '极品叫床浪骚');
        // $doc->addField('tag_cn', '叫床');
        // $doc->addField('tag_cn', '极品');
        // $client->addDocument($doc);
        // $updateResopnse = $client->commit();

        /* search */
        // $query = new \SolrQuery();
        // $query->setQuery('title_cn:美图 OR tag_cn:美图');
        // $query->setStart('0');
        // $query->setRows('5');
        // // $query->setOmitHeader(true);    // 關掉 header
        // $queryResponse = $client->query($query);
        // $response = $queryResponse->getResponse();

        /* query terms use for suggestion */
        // $queryTerms = new \SolrQuery();
        // $queryTerms->setTerms(true)
        //     ->setTermsField('title_cn')
        //     ->setTermsPrefix('骚')
        //     ->setTermsLimit('6');
        // $queryResponse = $this->client->query($queryTerms);
        // $response = $queryResponse->getResponse();

        /* DisMax Search */
        // $query = new \SolrDisMaxQuery();
        // $query->setQuery('叫')
        //     ->addQueryField('title_cn', '1')
        //     ->addQueryField('tag_cn', '0.5')
        //     ->addField('title_cn');
        // //->setShowDebugInfo(true);
        // $queryResponse = $this->client->query($query);
        // $response = $queryResponse->getResponse();

        /* MoreLikeThis */
        // $query = new \SolrDisMaxQuery();
        // $query->setQuery('极品')
        //     ->addQueryField('title_cn', '1')
        //     ->addQueryField('tag_cn', '0.5')
        //     ->setStart('0')
        //     ->setRows('1')
        //     ->setMlt(true)
        //     ->setMltBoost(true)
        //     ->addMltField('title_cn')
        //     ->addMltField('tag_cn')
        //     // ->addMltQueryField('title_cn', '1')
        //     // ->addMltQueryField('tag_cn', '0.8')
        //     ->setMltMinDocFrequency('1')        // setMltMinDocFrequency & setMltMinTermFrequency must be set
        //     ->setMltMinTermFrequency('1')
        //     ->setShowDebugInfo(true);
        // $queryResponse = $client->query($query);
        // $response = $queryResponse->getResponse();

        /* suggest */
        // $param = new \SolrModifiableParams();
        // $param->add('qt', 'suggest')
        //     ->add('suggest.build', 'true')
        //     ->add('suggest.dictionary', 'mySuggester')
        //     ->add('suggest.q', '极品');
        // // ->toString();
        // $queryResponse = $this->client->query($param);
        // $response = $queryResponse->getResponse();
        // $debug = $this->client->getDebug();

        /* Solarium TEST suggester */
        $query = $this->client->createSuggester();
        $query->setQuery('极品');
        $query->setDictionary('titleSuggester');
        $query->setCount(6);
        // $query->setBuild(true);  // 用这个很耗时
        $response = $this->client->suggester($query);

        /* Solarium TEST MoreLikeThis */
        // $query = $this->client->createMoreLikeThis();
        // $query->setFields($this->fields);
        // $query->setOmitHeader(false);
        // $query->setMltFields([
        //     'title',
        //     'tag'
        // ]);
        // $query->setMinimumDocumentFrequency(1);
        // $query->setMinimumTermFrequency(1);
        // $query->setStart(0);
        // $query->setRows(1);
        // $query->setQuery('id:37359');
        // // $query->setInterestingTerms('details');
        // $query->setMatchInclude(false);
        // // $query->setBoost(true);
        // $mlt = $query->getMoreLikeThis(); // enable mlt
        // $mlt->setCount(3);
        // // $debug = $query->getDebug();
        // // $debug->setOptions(['debug' => 'all']);

        // // executes the query
        // $response = $this->client->moreLikeThis($query);
        // $mltResult = ArrayHelper::toArray($response->getMoreLikeThis()->getResult(37359));
        // $resultDoc = $this->arrangeDocs($mltResult);

        // $client = new \GuzzleHttp\Client();
        // $response = $client->request('GET', "http://10.99.106.106:8983/solr/iexo/select?q=*:*&wt=json");

        $result = array(
            //            'ping' => $pingResponse->success(),
            'query' => $response->getData(),
            // 'debug' => $debug,
            // 'result' => $resultDoc,
        );
        return $result;

        // return var_export($response->getNumFound(), true);
        // return ArrayHelper::toArray($response->getMoreLikeThis()->getResult(37359));
        // $result = $response->getMoreLikeThis();
        // return var_export($response->getMoreLikeThis(), true);
    }

    /**
     * @RequestMapping("select")
     *
     */

    public function select()
    {


        $suggester = 'titleSuggester';

        // suggester dictionary
        $dictionarys = array($suggester, 'tagSuggester');
        // create Suggester
        $query = $this->client->createSelect();
        $edismax = $query->getEDisMax();
        //$edismax->setBoostFunctions("sum(play_count,app_play_count,download_count)^0.01");
        $edismax->setQueryFields("name^1.0 tags^0.5");
        $query->setQuery('boy');
        //$query->setDictionary($dictionarys);

        // execute query
        $response = $this->client->select($query);
        // get result
        $data = $response->getData();

        CLog::info('Solr Select', [$data]);
    }

    /**
     * @RequestMapping("create")
     *
     */
    public function create()
    {
        $update = $this->client->createUpdate();

        $videoList = [
            [
                'id' => 1,
                'tags' => 'boy',
                'name' => 'What the video'
            ],
            [
                'id' => 2,
                'tags' => 'girl',
                'name' => 'Is the video'
            ]
        ];
        // create input docs
        $docs = array();
        foreach ($videoList as $key => $video) {
            // create document
            $doc = $update->createDocument();
            $doc->id = $video['id'];
            $doc->tags = $video['tags'];
            $doc->name = $video['name'];
            $docs[] = $doc;
        }

        // add documents and commit to the query
        $update->addDocuments($docs);
        $update->addCommit();

        // execute update
        $this->client->update($update);

        // reset docs
        unset($docs);

        CLog::info('Insert all', $videoList);


//        $dictionarys = array('titleSuggester', 'tagSuggester', 'titleOneWordSuggester');
//
//        // create suggester query
//        $query = $this->client->createSuggester();
//        // set dictionary
//        $query->setDictionary($dictionarys);
//        // set build
//        $query->setBuild(true);
//        // excute
//        $this->client->suggester($query);
//
//        CLog::info('Rebuild all', $videoList);
    }


    /**
     * @RequestMapping(route="index", method={RequestMethod::GET})
     *
     * @return array
     * @throws \Swoft\Task\Exception\TaskException
     */
    public function index(): array
    {
        // 投遞異步任務
        Task::async('solrTask', 'addIndex');

        // return Success!
        $result = setResultArray(SUCCESS_STATUS);
        return $result;
    }

    /**
     * @RequestMapping(route="rebuild", method={RequestMethod::GET})
     *
     * @return array
     * @throws \Swoft\Task\Exception\TaskException
     */
    public function rebuildIndex(): array
    {
        // 投遞異步任務
        Task::async('solrTask', 'rebuildIndex');

        // return Success!
        $result = setResultArray(SUCCESS_STATUS);
        return $result;
    }

    /**
     * @RequestMapping(route="delete", method={RequestMethod::GET})
     *
     * @return array
     * @throws \Swoft\Task\Exception\TaskException
     */
    public function delete(): array
    {
        // 投遞異步任務
        Task::async('solrTask', 'deleteIndex');

        // return Success!
        $result = setResultArray(SUCCESS_STATUS);
        return $result;
    }


    /**
     * @RequestMapping(route="{id}", method={RequestMethod::DELETE})
     *
     * @param int $id
     * @return array
     * @throws Throwable
     */
    public function destory(int $id): array
    {
        try {
            // get an update instance
            $update = $this->client->createUpdate();
            // id === 0 , delete all
            if (0 === $id) {
                $update->addDeleteQuery("*:*");
            } else {
                $update->addDeleteById($id);
            }
            $update->addCommit();
            // execute update
            $this->client->update($update);
        } catch (\Throwable $th) {
            $result = setResultArray(FAIL_STATUS, $th->getMessage());
            return $result;
        }

        // return Success!
        $result = setResultArray(SUCCESS_STATUS);
        return $result;
    }

    /**
     * @RequestMapping(route="auto-complete", method={RequestMethod::POST})
     *
     * @param Request $request
     * @return array
     * @throws Throwable
     */
    public function autoComplete(Request $request): array
    {
        // get inputs
        $inputs = $request->getParsedBody();

        // check input "count"
        if (empty($inputs['count'])) {
            $count = 6;
        } else {
            $count = (int) $inputs['count'];
        }

        // 判斷字數, 只有一個中文字時，使用 OneWord dictionary
        if (1 == mb_strlen($inputs['search'])) {
            $suggester = 'titleOneWordSuggester';
        } else {
            $suggester = 'titleSuggester';
        }

        // suggester dictionary
        $dictionarys = array($suggester, 'tagSuggester');
        // create Suggester
        $query = $this->client->createSuggester();
        $query->setQuery($inputs['search']);
        $query->setDictionary($dictionarys);
        $query->setCount($count);
        // execute query
        $response = $this->client->suggester($query);
        // get result
        $data = $response->getData();
        $tagSuggest = current($data['suggest']['tagSuggester']);
        $titleSuggest = current($data['suggest'][$suggester]);
        // parse result and combine it
        $suggest = array();
        $suggestCount = 0;
        $countLimit = (int) round($count / 2);
        // 放入 tag 的結果
        if (0 < $tagSuggest['numFound']) {
            foreach ($tagSuggest['suggestions'] as $key => $term) {
                $suggest[] = $term['term'];
                $suggestCount++;
                // 數量到達請求數的一半時, 跳離
                if ($countLimit === $suggestCount) {
                    break;
                }
            }
        }
        if (0 < $titleSuggest['numFound']) {
            foreach ($titleSuggest['suggestions'] as $key => $term) {
                $suggest[] = $term['term'];
                $suggestCount++;
                // 總數已達請求數，跳離
                if ($count === $suggestCount) {
                    break;
                }
            }
        }

        // set result
        $result = array(
            'result' => $suggest,
        );
        $resultArr = setResultArray(SUCCESS_STATUS, SUCCESS_MSG, $result);
        return $resultArr;
    }

    /**
     * @RequestMapping(route="suggest-build", method={RequestMethod::GET})
     *
     * @return array
     * @throws Throwable
     */
    public function suggestBuild(): array
    {
        // server config
        $config = array(
            'endpoint' => array(
                'iexo' => array(
                    'host' => env('SOLR_HOST'),
                    'port' => env('SOLR_PORT'),
                    'path' => '/',
                    'core' => 'iexo',
                    'timeout' => 30,
                )
            )
        );
        // suggester dictionary
        $dictionarys = array('titleSuggester', 'tagSuggester', 'titleOneWordSuggester');
        // create client
        $client = new Solarium\Client($config);
        // create suggester
        $query = $client->createSuggester();
        // set dictionary
        $query->setDictionary($dictionarys);
        $query->setBuild(true);
        $response = $client->suggester($query);

        // set result
        $result = array(
            'result' => $response->getData(),
        );
        $resultArr = setResultArray(SUCCESS_STATUS, SUCCESS_MSG, $result);
        return $resultArr;
    }

    /**
     * @RequestMapping(route="search", method={RequestMethod::POST})
     * @Validate(validator="SolrValidator", fields={"search"})
     *
     * @param Request $request
     * @return array
     * @throws Throwable
     */
    public function search(Request $request): array
    {
        // get inputs
        $inputs = $request->getParsedBody();

        // check columnsPerPage
        if (!empty($inputs['columnsPerPage'])) {
            $columnsPerPage = (int) $inputs['columnsPerPage'];
        } else {
            $columnsPerPage = 8;
        }
        // check page
        if (!empty($inputs['page'])) {
            $page = (int) $inputs['page'];
        } else {
            $page = 1;
        }

        /* eDisMax Search */
        // get a select query instance
        $query = $this->client->createSelect();
        // get the eDisMax component and set Boost setting
        $edismax = $query->getEDisMax();
        //$edismax->setBoostFunctions("sum(play_count,app_play_count,download_count)^0.01");
        $edismax->setBoostQuery("releasedAt:[NOW/DAY-1YEAR TO NOW/DAY]^1.5");
        $edismax->setQueryFields("title^1.0 tag^0.5");
        // set a query string
        $query->setQuery($inputs['search']);
        $query->setFields($this->fields);
        // set 分頁
        $offset = ($page - 1) * $columnsPerPage;
        $query->setStart($offset);
        $query->setRows($columnsPerPage);

        // executes the query
        $response = $this->client->select($query);
        $docs = ArrayHelper::toArray($response->getDocuments());

        // check doc result
        if (0 === count($docs)) {
            return setResultArray(NOT_FOUND_STATUS);
        }

        // 整理結果
        $resultDoc = $this->arrangeDocs($docs);

        // set result
        $result = array(
            'page'  => $page,
            'totalColumn' => $response->getNumFound(),
            'video' => $resultDoc,
        );
        if (true == env('DEV', false)) {
            $result['debug'] = $docs;
        }
        $resultArr = setResultArray(SUCCESS_STATUS, SUCCESS_MSG, $result);
        return $resultArr;
    }

    /**
     * @RequestMapping(route="guess-you-like", method={RequestMethod::POST})
     * @Validate(validator="SolrValidator", fields={"id"})
     *
     * @param Request $request
     * @return array
     * @throws Throwable
     */
    public function guessYouLike(Request $request): array
    {
        // get inputs
        $inputs = $request->getParsedBody();

        // check input "count"
        if (empty($inputs['count'])) {
            $count = 6;
        } else {
            $count = $inputs['count'];
        }

        /* Solarium MoreLikeThis */
        // get MoreLikeThis query instance
        $query = $this->client->createSelect();
        // enable mlt
        $mlt = $query->getMoreLikeThis();
        $mlt->setCount($count);
        $mlt->setFields([
            'title',
            'tag'
        ]);
        $mlt->setMinimumDocumentFrequency(1);
        $mlt->setMinimumTermFrequency(1);
        // set query parameter
        $query->setFields($this->fields);
        $query->setStart(0);
        $query->setRows(1);
        // set query string
        $queryStr = sprintf('id:%s', $inputs['id']);
        $query->setQuery($queryStr);

        // executes the query
        $response = $this->client->select($query);
        $mltResult = $response->getMoreLikeThis()->getResult($inputs['id']);

        // 判斷是否有結果, 沒結果會回傳 null
        if (!$mltResult) {
            return setResultArray(NOT_FOUND_STATUS);
        }

        // 整理結果
        $mltResult = ArrayHelper::toArray($mltResult);
        $resultDoc = $this->arrangeDocs($mltResult);

        // set result
        $result = array(
            'video' => $resultDoc,
        );
        if (true == env('DEV', false)) {
            $result['debug'] = $mltResult;
        }
        $resultArr = setResultArray(SUCCESS_STATUS, SUCCESS_MSG, $result);
        return $resultArr;
    }

}