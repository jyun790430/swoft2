<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

const SUCCESS_MSG = '成功';
const FAIL_MSG    = '失敗';

const SUCCESS_STATUS = 200;
const FAIL_STATUS    = 100;

const REDIS_SOLR_INDEX_IDS = 'apvideo:solrIndex';
const REDIS_SOLR_DELETE_INDEX = 'apvideo:solrDelete';

define('RESULT_MESSAGE', [
    200 => '成功',
     100 => '失败',

]);

function setResultArray(int $status, string $message = '', array $memoAarry = []): array
{
    $resultArray = [
        'status'  => 0,
        'message' => '',
        'data'    => []
    ];

    if (empty($message)) {
        $message = RESULT_MESSAGE[$status];
    }

    $resultArray['status'] = $status;
    $resultArray['message'] = $message;

    if (count($memoAarry)) {
        unset($resultArray['data']);
    } else {
        $resultArray['data'] = $memoAarry;
    }

    return $resultArray;
}
