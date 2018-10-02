<?php

namespace common\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use common\access\AccessManager;

class Toolbar
{
    public static function toggleButton($dataProvider)
    {
        $multiPageView = $dataProvider->totalCount > $dataProvider->pagination->pageSize;
        return $multiPageView ? '{toggleData}' : null;
    }

    public static function refreshButton($caption = '')
    {
        return Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . $caption, '', [
            'class' => 'btn btn-primary refresh-link',
            'title' => Yii::t('app', 'Refresh Data'),
            'aria-label' => Yii::t('app', 'Refresh Data'),
        ]) . ' ';
    }

    public static function createButton($caption = '')
    {
        if (!Yii::$app->user->can(AccessManager::CREATE)) {
            return null;
        }

        return Html::a('<i class="glyphicon glyphicon-plus"></i> ' . $caption, ['create'], [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', $caption),
            'aria-label' => Yii::t('app', 'Add New Record'),
        ]) . ' ';
    }

    public static function createButtonMultipleEmail($caption = '', $id)
    {
        if (!Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE)) {
            return null;
        }

        return Html::a('<i class="glyphicon glyphicon-plus"></i> ' . $caption, ['create-multiple-emails', 'id' => $id], [
            'class' => 'btn btn-success pjax-modal-multiEmail multipleEmail',
            'title' => Yii::t('app', $caption),
            'aria-label' => Yii::t('app', 'Add ' . $caption),
        ]) . ' ';
    }

    public static function createButtonBiuldingProcess($caption = '')
    {
        if (!Yii::$app->user->can('buildingprocess.backend.default:' . AccessManager::CREATE)) {
            return null;
        }

        return Html::a('<i class="glyphicon glyphicon-plus"></i> ' . $caption, ['create'], [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', $caption),
            'aria-label' => Yii::t('app', 'Add New Record'),
        ]) . ' ';
    }

    public static function createButtonDeal($caption = '')
    {
        if (!Yii::$app->user->can('buildingprocess.backend.deal:' . AccessManager::CREATE)) {
            return null;
        }

        $type = Yii::$app->request->get('type');

        return Html::a('<i class="glyphicon glyphicon-plus"></i> ' . $caption, ['create', 'type' => $type], [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', $caption),
            'aria-label' => Yii::t('app', 'Add New Record'),
        ]) . ' ';
    }

    public static function paginationTrashDeal($dataProvider)
    {
        $pageSize = $dataProvider->pagination->pageSize;
        $paginationOptions = Yii::$app->params['paginationOptions'];
        $dropDownElement = Html::input('text', 'type', Yii::$app->request->get('type'), ['class' => 'hidden']);
        $dropDownElement = $dropDownElement . Html::dropDownList('pageSize', $pageSize, $paginationOptions, ['id' => 'page-size', 'class' => 'form-control']);

        return Html::tag('form', '&nbsp; ' . Yii::t('app', 'Per page') . ' ' . $dropDownElement, ['class' => 'form-inline pull-left']);
    }

    public static function createUserResume($caption = '',$user)
    {
        if (!Yii::$app->user->can(AccessManager::CREATE)) {
            return null;
        }

        return Html::a('<i class="glyphicon glyphicon-plus"></i> ' . $caption, ['create', 'id' => $user], [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', 'Add New Record'),
            'aria-label' => Yii::t('app', 'Add New Record'),
        ]) . ' ';
    }


    public static function putdownButton()
    {
        return Html::a(Yii::t('app', 'Copy Configuration'), ['putdown'], ['class' => 'btn btn-info']);
    }

    public static function privacyButton()
    {
        return Html::a(Yii::t('app', 'Month report'), ['privacy'], ['class' => 'btn btn-info']);
    }


    public static function exportButton()
    {
        return '{export}';
    }

    public static function enableButton()
    {
        return Html::a(Yii::t('app', 'Enable Selected'), ['show-list'], ['class' => 'action-list-link']);
    }

    public static function disableButton()
    {
        return Html::a(Yii::t('app', 'Disable Selected'), ['show-list', 'visible' => false], ['class' => 'action-list-link']);
    }

    public static function blockButton()
    {
        return Html::a(Yii::t('app', 'Block Selected'), ['activate-list', 'active' => false], ['class' => 'action-list-link']);
    }

    public static function unblockButton()
    {
        return Html::a(Yii::t('app', 'Unblock Selected'), ['activate-list'], ['class' => 'action-list-link']);
    }

    public static function markOpenedButton()
    {
        return Html::a(Yii::t('app', 'Mark Selected as Read'), ['open-list'], ['class' => 'action-list-link']);
    }

    public static function markNewButton()
    {
        return Html::a(Yii::t('app', 'Mark Selected as Unread'), ['open-list', 'opened' => false], ['class' => 'action-list-link']);
    }

    public static function deleteButtonUser($caption = '')
    {

        if (!Yii::$app->user->can(AccessManager::DELETE)) {
            return null;
        }

        return Html::a('<i class="glyphicon glyphicon-trash"></i> ' . $caption, ['archive-list'], [
            'class' => 'btn btn-danger action-list-link',
            'title' => Yii::t('app', 'Delete Selected'),
            'aria-label' => Yii::t('app', 'Delete Selected'),
            'data-confirm' => Yii::t('app', 'Are you sure you want to delete the selected items?'),
        ]) . ' ';
    }

    public static function deleteButton($caption = '')
    {

        if (!Yii::$app->user->can(AccessManager::DELETE)) {
            return null;
        }

        return Html::a('<i class="glyphicon glyphicon-trash"></i> ' . $caption, ['delete-list'], [
            'class' => 'btn btn-danger action-list-link',
            'title' => Yii::t('app', 'Delete Selected'),
            'aria-label' => Yii::t('app', 'Delete Selected'),
            'data-confirm' => Yii::t('app', 'Are you sure you want to delete the selected items?'),
        ]) . ' ';
    }

    public static function activateSelect()
    {
        if (!Yii::$app->user->can('core.backend.admin:'. AccessManager::UPDATE)) {
            return null;
        }
        return self::operationSelect([self::blockButton(), self::unblockButton()]);
    }

    public static function showSelect()
    {

        if (!Yii::$app->user->can(AccessManager::UPDATE)) {
            return null;
        }

        return self::operationSelect([self::enableButton(), self::disableButton()]);
    }

    public static function openSelect()
    {
        return self::operationSelect([self::markOpenedButton(), self::markNewButton()]);
    }

    public static function operationSelect($items)
    {
        return '<div class="btn-group">
            <button type="button" class="btn btn-default">' . Yii::t('app', 'More') . '</button>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="caret"></span>
            </button>'
            . Html::ul($items, [
                'item' => function($item, $index) { return Html::tag('li', $item); },
                'class' => 'dropdown-menu'
            ])
        . '</div>';
    }

    public static function basketButton()
    {
        return Html::a('<i class="glyphicon glyphicon-trash"></i>',Url::to(['trash']), ['class' => 'btn btn-info pjax-modal-trash trash']);
    }

    public  static function restoreButton($caption = ''){
        if (!Yii::$app->user->can(AccessManager::UPDATE)) {
            return null;
        }

        return Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . $caption, ['restore-list'], [
            'class' => 'btn btn-primary action-list-link',
            'title' => Yii::t('app', 'Restore Selected'),
            'aria-label' => Yii::t('app', 'Restore Selected'),
        ]) . ' ';
    }

    public  static function deleteTrashButton($caption = ''){
        if (!Yii::$app->user->can(AccessManager::DELETE)) {
            return null;
        }

        return Html::a('<i class="glyphicon glyphicon-trash"></i> ' . $caption, ['delete-list'], [
            'class' => 'btn btn-danger action-list-link',
            'title' => Yii::t('app', 'Delete Selected'),
            'aria-label' => Yii::t('app', 'Delete Selected'),
            'data-confirm' => Yii::t('app', 'Are you sure you want to delete the selected items?'),
        ]) . ' ';
    }

    public static function allUpdeleteTrashButton($caption = ''){
        if (!Yii::$app->user->can(AccessManager::DELETE)) {
            return null;
        }

        return Html::a('<i class="glyphicon glyphicon-ok-sign"></i> ' . $caption, ['all-undelete'], [
            'class' => 'btn btn-success action-list-allupdate-trash pjax-modal-trash',
            'title' => Yii::t('app', 'Delete Selected'),
            'aria-label' => Yii::t('app', 'Delete Selected'),
            'data-confirm' => Yii::t('app', 'Are you sure you want to delete the selected items?'),
        ]) . ' ';
    }

    public static function paginationSelect($dataProvider)
    {
        $pageSize = $dataProvider->pagination->pageSize;
        $paginationOptions = Yii::$app->params['paginationOptions'];
        $dropDownElement = Html::dropDownList('pageSize', $pageSize, $paginationOptions, ['id' => 'page-size', 'class' => 'form-control']);
        return Html::tag('form', '&nbsp; ' . Yii::t('app', 'Per page') . ' ' . $dropDownElement, ['class' => 'form-inline pull-left']);
    }


    public static function paginationTrashSelect($dataProvider)
    {
        $pageSize = $dataProvider->pagination->pageSize;
        $paginationOptions = Yii::$app->params['paginationOptions'];
        $dropDownElement = Html::dropDownList('pageSize', $pageSize, $paginationOptions, ['id' => 'page-size-trash', 'class' => 'form-control']);
        return Html::tag('form', '&nbsp; ' . Yii::t('app', 'Per page') . ' ' . $dropDownElement, ['class' => 'form-inline pull-left','action'=>Url::to(['trash'])]);
    }

    public static function deleteDeprtmentStructure($id)
    {
        if (!Yii::$app->user->can('department.backend.default:' . AccessManager::DELETE)) {
            return null;
        }

        return  Html::a(Yii::t('app', 'Delete this Department'),
            ['/department/'.$id.'/delete'],
            [
                'class' => 'btn btn-danger',
                'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?')
            ]);
    }

    public static function deletePositionStructure($id)
    {
        if (!Yii::$app->user->can('position.backend.default:' . AccessManager::DELETE)) {
            return null;
        }

        return  Html::a(Yii::t('app', 'Delete this Position'),
            ['/position/'.$id.'/delete'],
            [
                'class' => 'btn btn-danger',
                'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?')
            ]);
    }

    public static function month(){
        return [
            '1' => Yii::t('app', 'January'),
            '2' => Yii::t('app', 'February'),
            '3' => Yii::t('app', 'March'),
            '4' => Yii::t('app', 'April'),
            '5' => Yii::t('app', 'May'),
            '6' => Yii::t('app', 'June'),
            '7' => Yii::t('app', 'July'),
            '8' => Yii::t('app', 'August'),
            '9' => Yii::t('app', 'September'),
            '10' => Yii::t('app', 'October'),
            '11' => Yii::t('app', 'November'),
            '12' => Yii::t('app', 'December'),
        ];
    }

    public static function listRandom($arr){
        unset($arr[0]); return $arr;
    }

    public static function viewMonth($month){
        $arr=Toolbar::month();
        return $arr[$month];
    }
}
