<?php

namespace modules\document\models;


class Attachment extends Document
{
    const SCENARIO_ATTACHMENT = 'save_attachment';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [['file', 'required']]);
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_ATTACHMENT => [
                'user_id',
                'root',
                'lft',
                'rgt',
                'lvl',
                'icon_type',
                'active',
                'selected',
                'disabled',
                'readonly',
                'visible',
                'collapsed',
                'movable_u',
                'movable_d',
                'movable_l',
                'movable_r',
                'removable',
                'removable_all',
                'created_at',
                'updated_at',
                'name',
                'order',
                'document_type',
                'description',
                'file',
            ],
            'default' => [
                'user_id',
                'root',
                'lft',
                'rgt',
                'lvl',
                'icon_type',
                'active',
                'selected',
                'disabled',
                'readonly',
                'visible',
                'collapsed',
                'movable_u',
                'movable_d',
                'movable_l',
                'movable_r',
                'removable',
                'removable_all',
                'created_at',
                'updated_at',
                'name',
                'order',
                'document_type',
                'description',
            ],
        ];
    }
}
