<?php
namespace common\models\structure;

use modules\department\models\Department;

/**
 * Class CollectTreeSource
 * @package common\models\structure
 */
class CollectTreeSource
{
	public $treeSource = [];

	public static function getTreeSource()
	{
		$newTree = new self;
		$departments = Department::find()->orderBy('sorting ASC')->all();

		foreach ($departments as $department) {
			$newTree->treeSource[] = new TreeFabric($department, $department->id);

			if (!empty($department->positionsTree)) {
				foreach ($department->positionsTree as $position) {
					$newTree->treeSource[] = new TreeFabric($position, $department->id);

					if (!empty($position->usersTree)) {
						foreach ($position->usersTree as $user) {
							$newTree->treeSource[] = new TreeFabric($user, $department->id);
						}
					}
				}
			}
		}

		return $newTree->treeSource;
	}
}