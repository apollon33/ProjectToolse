<?php

namespace modules\buildingprocess\models;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use modules\document\models\Document;

class FieldWidget extends Widget
{
    /**
     * @var
     */
    public $disabled;
    /**
     * @var array
     */
    public $option;

    /**
     * @var string
     */
    public $type;

    /**
     * @var boolean
     */
    public $required;

    /**
     * @var boolean
     */
    public $hidden;

    public $document_id;

    public $document_name;

    /**
     * @var string
     */
    public $linkTemplate = <<< HTML
    <div class="col-md-12 col-sm-12 input-group" style="padding-left: 0px">
   <span class="input-group-btn">
        <div class="btn btn-primary {linkPopupOpen}">
                <i class="glyphicon glyphicon-folder-open"></i>&nbsp;  
            <span class="hidden-xs">{field}</span>
            
        </div>
    </span>
    <input id="{id}" class="field-name form-control  deal-document-name" name="{name}" value="{document_id}" type="hidden">
    <a id="link" href="/document?id={document_id}" target="_blank">{document_name}</a>
    <div class="text-danger"></div>
    </div>
HTML;



    /**
     * @return string
     */
    public function run()
    {
        return $this->renderWidget();
    }

    /**
     * @return string
     */
    private function renderWidget()
    {
        $required = $this->required ? ' required' : '';
        $hidden = empty($this->option['hidden']) ? '' : ' hidden';
        $divHeaderOptions = ['class' => 'form-group field-' . $this->option['id'] . ' ' . $required . $hidden];
        $labelsOption = ['class' => 'control-label col-sm-2 col-md-2 col-xs-12'];
        $divOptions = ['class' => 'col-md-10 col-sm-10'];
        $nameLabel = Yii::t('app', $this->option['labelName']);

        $field = Html::tag($this->type, $this->option['value'],
            array_merge($this->option, ['disabled' => $this->disabled]));

        if ($this->type === 'link') {
            if ($this->option['value']) {
                $field = strtr(
                    $this->linkTemplate, [
                    '{field}' => Yii::t('app', 'Browse …'),
                    '{name}' => $this->option['name'],
                    '{id}' => $this->option['id'],
                    '{linkPopupOpen}' => $this->disabled ? '' : 'link-popup-open',
                    '{disabled}' => $this->disabled ? 'disabled' : true,
                    '{document_id}' => $this->option['value'],
                    '{document_name}' => Document::getName($this->option['value']),
                ]);
            } else {
                $field = strtr(
                    $this->linkTemplate, [
                    '{field}' => Yii::t('app', 'Browse …'),
                    '{name}' => $this->option['name'],
                    '{id}' => $this->option['id'],
                    '{linkPopupOpen}' => $this->disabled ? '' : 'link-popup-open',
                    '{disabled}' => $this->disabled ? 'disabled' : true,
                    '{document_id}' => '',
                    '{document_name}' => '',
                ]);
            }
        }

        if (isset($this->option['type']) && $this->option['type'] == 'file') {
            if ($this->option['value']) {
                $field = Html::a(
                    Yii::t('app', Document::getName(intval($this->option['value']))),
                    ['/document', 'id' => $this->option['value']],
                    ['class' => 'profile-link', 'target' => '_blank']
                );
            } else {
                $field = Html::tag(
                        $this->type,
                        $this->option['value'],
                        array_merge($this->option, ['disabled' => $this->disabled, 'name' => 'attachmentFiles[]'])) . Html::tag($this->type, $this->option['value'],
                        array_merge($this->option, ['disabled' => $this->disabled, 'type' => 'hidden'])
                    );
            }
        }

        if (!empty($this->option['type']) && $this->option['type'] == 'checkbox') {
            $viewWidget = Html::checkboxList($this->option['name'], $this->option['value'], $this->option['option'], ['itemOptions' => ['disabled' => $this->disabled]] );
            $field = Html::tag('div', $viewWidget, ['class' => 'form-control']);
        }

        if (!empty($this->option['option']) && empty($this->option['type'])) {
            $field = Html::dropDownList($this->option['name'], $this->option['value'], $this->option['option'],
                ['class' => 'form-control', 'disabled' => $this->disabled]);
        }

        $viewWidget =
            Html::tag('div',
                Html::tag('label', $nameLabel, $labelsOption) .
                Html::tag('div', $field .
                    Html::tag('div', '', ['class' => 'text-danger'])
                    , $divOptions),
                $divHeaderOptions);

        return $viewWidget;
    }
}