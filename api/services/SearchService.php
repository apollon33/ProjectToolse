<?php

namespace api\services;

use common\models\UserSearch;
use modules\document\models\DocumentSearch;
use modules\position\models\PositionSearch;
use modules\vacation\models\VacationSearch;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Class SearchService
 * @package api\services
 */
class SearchService
{
    const ENTITY_DOCUMENT_SEARCH = 'DocumentSearch';
    const ENTITY_USER_SEARCH = 'UserSearch';
    const ENTITY_VACATION_SEARCH = 'VacationSearch';
    const ENTITY_POSITION_SEARCH = 'PositionSearch';

    /**
     * @param array $params
     * @return UserSearch|DocumentSearch|PositionSearch|VacationSearch
     * @throws NotFoundHttpException
     */
    public function getSearchModelByParams(array $params)
    {
        if (array_key_exists(self::ENTITY_DOCUMENT_SEARCH, $params)) {
            $searchModel = new DocumentSearch();
        } elseif (array_key_exists(self::ENTITY_USER_SEARCH, $params)) {
            $searchModel = new UserSearch();
        } elseif (array_key_exists(self::ENTITY_VACATION_SEARCH, $params)) {
            $searchModel = new VacationSearch();
        } elseif (array_key_exists(self::ENTITY_POSITION_SEARCH, $params)) {
            $searchModel = new PositionSearch();
        } else {
            throw new NotFoundHttpException('No data entered for search');
        }

        return $searchModel;
    }

    /**
     * @param UserSearch|DocumentSearch|PositionSearch|VacationSearch|ActiveRecord $searchModel
     * @param array $params
     * @return array
     */
    public function getSearchResult(ActiveRecord $searchModel, array $params = [])
    {
        if (array_key_exists('page', $params)) {
            $params['pageSize'] = array_key_exists('pageSize', $params) ? $params['pageSize'] : $searchModel->pagination->pageSize;
            $params['page'] = 1 + (int) $params['page'] / (int) $params['pageSize'];
        }

        return $searchModel->search($params);
    }

}