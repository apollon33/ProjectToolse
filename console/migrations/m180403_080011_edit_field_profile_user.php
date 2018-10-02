<?php

use yii\db\Migration;

/**
 * Class m180403_080011_edit_field_profile_user
 */
class m180403_080011_edit_field_profile_user extends Migration
{
    private $updateTableUser = 'user';
    private $skillsEnglishColumn = 'skillsEnglish';
    private $skillsSoftColumn = 'skillsSoft';
    private $certificationUserColumn = 'certificationUser';

    private $englishLevelColumn = 'englishLevel';
    private $positionAndGradeColumn = 'positionAndGrade';
    private $performanceAppraisalReviewColumn = 'performanceAppraisalReview';
    private $personalDevelopmentPlan = 'personalDevelopmentPlan';


    public function up()
    {
        if (!in_array($this->updateTableUser, $this->getDb()->schema->tableNames)) {
            return;
        }

        $this->renameColumn("{{%$this->updateTableUser}}", $this->skillsEnglishColumn, $this->englishLevelColumn);
        $this->renameColumn("{{%$this->updateTableUser}}", $this->skillsSoftColumn, $this->positionAndGradeColumn);
        $this->renameColumn(
            "{{%$this->updateTableUser}}",
            $this->certificationUserColumn,
            $this->performanceAppraisalReviewColumn
        );
        $this->addColumn(
            "{{%$this->updateTableUser}}",
            $this->personalDevelopmentPlan,
            $this->string()->defaultValue(null)
        );

    }
}
