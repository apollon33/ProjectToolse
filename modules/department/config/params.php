<?php
use modules\department\models\Department;

return [
    'params' => [
        'admin_modules' => [
            [
            	'label' => Yii::t('app', 'Departments'),
	            'url' => ['/department'],
//                'badge' => Position::find()->count()
			],
//           ['label' => Yii::t('app', 'Add Position'), 'url' => ['/position/create']],
        ],
    ],
];